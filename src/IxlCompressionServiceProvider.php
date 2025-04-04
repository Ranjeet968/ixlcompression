<?php

namespace Ranjeet\IxlCompression;

use Illuminate\Support\ServiceProvider;

class IxlCompressionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(FileCompressor::class, function () {
            return new FileCompressor();
        });
    }

    public function boot()
    {
        // No boot actions needed
    }
}
