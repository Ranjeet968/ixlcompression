<?php

namespace Ranjeet\IxlCompression;

use Illuminate\Support\ServiceProvider;

class IxlCompressionServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register FileCompressor as a singleton
        $this->app->singleton(FileCompressor::class, function () {
            return new FileCompressor();
        });

        // Merge default config
        $this->mergeConfigFrom(__DIR__ . '/../config/ixlcompression.php', 'ixlcompression');
    }
    

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/ixlcompression.php' => config_path('ixlcompression.php'),
        ], 'config');
    }
    
    
}
