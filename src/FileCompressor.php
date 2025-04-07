<?php

namespace Ranjeet\IxlCompression;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Exception;

class FileCompressor
{
    public static function compress($file)
    {
        $compressor = new self();
        return $compressor->compressFile($file);
    }

    public function compressFile($file)
    {
        // Get the current PHP version
        $phpVersion = phpversion();
        $phpMajorMinor = implode('.', array_slice(explode('.', $phpVersion), 0, 2)); // Extract major.minor version (e.g., "8.3")

        // Handle Livewire TemporaryUploadedFile dynamically
        if (class_exists('Livewire\TemporaryUploadedFile') && $file instanceof \Livewire\TemporaryUploadedFile) {
            $filePath = $file->getRealPath();
            $file = new File($filePath);
        }

        // Ensure file exists
        if (!file_exists($file->getPathname())) {
            throw new Exception("File not found: " . $file->getPathname());
        }

        // Determine file extension
        if (method_exists($file, 'getClientOriginalExtension')) {
            $fileExtension = strtolower($file->getClientOriginalExtension());
        } elseif ($file instanceof File) {
            $fileExtension = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
        } else {
            throw new Exception("Unsupported file type passed to compressFile()");
        }

        // 🔹 Handle image compression
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'psd', 'tiff'];
        if (in_array($fileExtension, $imageExtensions)) {
            if (!class_exists('Imagick')) {
                throw new \Exception("Imagick is not installed. Please install it using: sudo apt install php{$phpMajorMinor}-imagick -y");
            }
        }

        $inputPath = $file->getPathname();
        $outputPath = storage_path('app/compressed_' . Str::uuid() . '.' . $fileExtension);

        $compressedFilePath = $this->handleCompression($inputPath, $outputPath, $fileExtension);

        // 🔹 Return as an UploadedFile instance so users can use it without modifying their code
        return new UploadedFile(
            $compressedFilePath,
            $file->getClientOriginalName(),
            mime_content_type($compressedFilePath),
            null,
            true // Mark as test mode (skips some validation)
        );
    }

    private function handleCompression($inputPath, $outputPath, $fileExtension)
    {
        // Skip compression for certain document types
        if (in_array($fileExtension, ['doc', 'docx', 'xls', 'xlsx', 'csv', 'ppt', 'pptx'])) {
            copy($inputPath, $outputPath);
            return $outputPath;
        }

        if ($fileExtension === 'pdf') {
            return $this->compressPDF($inputPath, $outputPath);
        } elseif (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'psd', 'tiff', 'webp'])) {
            return $this->compressImage($inputPath, $outputPath);
        } elseif (in_array($fileExtension, ['mp4', 'avi', 'mkv', 'mov', 'flv', 'wmv', 'webm'])) {
            return $this->compressVideo($inputPath, $outputPath);
        } else {
            throw new Exception("Unsupported file type: $fileExtension");
        }
    }

    private function compressPDF($inputPath, $outputPath)
    {
        $gsCheck = shell_exec("gs --version 2>&1"); // Check if Ghostscript is available
        if (!$gsCheck) {
            throw new \Exception("Ghostscript is not installed. Please install it using: sudo apt install ghostscript");
        }

        // Try Snappy first
        if (class_exists('\Barryvdh\Snappy\PdfWrapper')) {
            return $this->compressWithSnappy($inputPath, $outputPath);
        }

        // Try Dompdf next
        if (class_exists('\Dompdf\Dompdf')) {
            return $this->compressWithDompdf($inputPath, $outputPath);
        }

        // Default fallback: Ghostscript
        return $this->compressWithGhostscript($inputPath, $outputPath);
    }

    private function compressWithSnappy($inputPath, $outputPath)
    {
        try {
            $quality = config('ixlcompression.image_quality', 60);
            $snappy = new \Barryvdh\Snappy\PdfWrapper(app('snappy.pdf'));
            $snappy->setOption('quality', $quality);
            $snappy->generate($inputPath, $outputPath);
            return $outputPath;
        } catch (Exception $e) {
            \Log::warning("Snappy compression failed: " . $e->getMessage());
            return $this->compressWithGhostscript($inputPath, $outputPath);
        }
    }

    private function compressWithDompdf($inputPath, $outputPath)
    {
        try {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml(file_get_contents($inputPath));
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            file_put_contents($outputPath, $dompdf->output());
            return $outputPath;
        } catch (Exception $e) {
            \Log::warning("Dompdf compression failed: " . $e->getMessage());
            return $this->compressWithGhostscript($inputPath, $outputPath);
        }
    }

    private function compressWithGhostscript($inputPath, $outputPath)
    {
        // Ensure Ghostscript is installed
        if (!shell_exec("command -v gs")) {
            throw new Exception("Ghostscript is not installed on this system.");
        }

        $escapedInput = escapeshellarg($inputPath);
        $escapedOutput = escapeshellarg($outputPath);

        $gsSetting = config('ixlcompression.pdf_quality', 'screen');

        $gsCommand = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/$gsSetting -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$escapedOutput $escapedInput";
        exec($gsCommand, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new Exception("Ghostscript PDF compression failed!");
        }

        return $outputPath;
    }

    private function compressImage($inputPath, $outputPath)
    {
        if (!extension_loaded('imagick') || !class_exists('Imagick')) {
            throw new Exception("Imagick extension is not installed.");
        }

        try {
            $quality = config('ixlcompression.image_quality', 60);
            $imagick = new \Imagick($inputPath);
            // Set compression based on format
            if ($imagick->getImageFormat() === 'TIFF') {
                $imagick->setImageCompression(\Imagick::COMPRESSION_LZW); // LZW compression for TIFF
            } else {
                $imagick->setImageCompressionQuality($quality);
            }
            $imagick->stripImage(); // Remove metadata to reduce size
            $imagick->writeImage($outputPath);
            $imagick->clear();
            $imagick->destroy();
        } catch (Exception $e) {
            throw new Exception("Image compression failed: " . $e->getMessage());
        }

        return $outputPath;
    }

    private function compressVideo($inputPath, $outputPath)
    {
        $phpVersion = phpversion();
        $phpMajorMinor = implode('.', array_slice(explode('.', $phpVersion), 0, 2));

        if (!shell_exec("command -v ffmpeg")) {
            throw new \Exception("FFmpeg is not installed. Please install it using: sudo apt install ffmpeg php{$phpMajorMinor}-ffmpeg");
        }

        $crf = config('ixlcompression.video_crf', 28);

        $extension = pathinfo($inputPath, PATHINFO_EXTENSION);
        $outputPath = preg_replace('/\.\w+$/', ".$extension", $outputPath);

        $command = "ffmpeg -i " . escapeshellarg($inputPath) .
            " -vcodec libx264 -crf $crf -preset medium " .
            escapeshellarg($outputPath) . " -y";

        exec($command, $output, $returnVar);

        if ($returnVar !== 0 || !file_exists($outputPath)) {
            throw new \Exception("Video compression failed using FFmpeg.");
        }

        return $outputPath;
    }
}
