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

📄 PDFs (Compressed using Ghostscript, Snappy, or Dompdf)

🖼️ Images (jpg, jpeg, png, gif, bmp, webp - Compressed using Imagick)

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

Snappy/Dompdf (optional PDF compression)

To install required dependencies:
sudo apt install php$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")-imagick -y
sudo apt install ghostscript -y


📜 License
MIT License © 2024 Ranjeet
