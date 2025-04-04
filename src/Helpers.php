<?php

if (!function_exists('compressFile')) {
    function compressFile($file)
    {
        return app(Ranjeet\IxlCompression\FileCompressor::class)->compressFile($file);
    }
}
