<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Compression Quality
    |--------------------------------------------------------------------------
    | Controls the quality of image compression.
    | Value should be between 0 (worst) and 100 (best).
    | Recommended values:
    | - 30–50: Very high compression, smaller size, lower quality.
    | - 60–75: Balanced quality and size.
    | - 80–100: High quality, larger file size.
    */
    'image_quality' => 60,

    /*
    |--------------------------------------------------------------------------
    | PDF Compression Quality (Ghostscript Setting)
    |--------------------------------------------------------------------------
    | Controls the level of PDF compression using Ghostscript.
    | Available options:
    | - 'screen'  : Lowest quality and smallest size (e.g., email previews)
    | - 'ebook'   : Medium quality (suitable for eBook reading)
    | - 'printer' : Higher quality for print, larger size
    | - 'prepress': Best quality, for commercial printing
    | - 'default' : Balanced setting provided by Ghostscript
    */
    'pdf_quality' => 'screen',


    /*
    |--------------------------------------------------------------------------
    | Video Compression CRF (Constant Rate Factor)
    |--------------------------------------------------------------------------
    | CRF controls the quality of video compression using FFmpeg.
    | Range:
    | - 18 = visually lossless (higher file size)
    | - 23 = good balance (default)
    | - 28 = more compression, smaller size
    | - 30 = more compression, smaller size

    */
    'video_crf' => 30,

];
