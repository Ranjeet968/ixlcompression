# 📦 IxlCompression - Laravel File Compression Package

IxlCompression is a Laravel package that compresses images and PDFs before storing them. It supports **Imagick**, **Ghostscript**, **Snappy**, and **Dompdf** for optimized compression.

---

## 🚀 Installation

Install the package using **Composer**:

```sh
composer require ranjeet968/ixlcompression

If you face dependency issues, try updating:

composer update ranjeet968/ixlcompression


📂 Supported File Types

✅ Compressed Files

📄 PDFs (Compressed using Ghostscript with configurable pdf_quality setting)

🖼️ Images (jpg, jpeg, png, gif, bmp, webp, psd, tiff - Compressed using Imagick with configurable image_quality setting)

🎞️ Videos (mp4, mov, avi, mkv, webm, flv - Compressed using FFmpeg with configurable CRF setting)

⛔ Unsupported Files

doc, xls, ppt, csv (No compression applied)

🔧 Usage

1️⃣ Basic File Compression
use Ranjeet\IxlCompression\FileCompressor;

$compressedFile = FileCompressor::compress($uploadedFile);
Here, $uploadedFile should be an instance of Illuminate\Http\UploadedFile or a valid file path.


🛠 Requirements
PHP 8.0+

Laravel 8+

Imagick (for image compression)

Ghostscript (for PDF compression)

FFmpeg (for video compression)

Snappy/Dompdf (optional PDF compression)

To install required dependencies:

 For image support:
 a. example for php 8.2 : sudo apt install php8.2-imagick -y

 For PDF compression:
 b. sudo apt install ghostscript -y

 For Video compression:
 c. sudo apt install ffmpeg php8.2-ffmpeg -y


## License

This package is proprietary and not open source.

You may not copy, reuse, or distribute this code without written permission.

