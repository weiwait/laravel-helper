<?php

namespace Weiwait\Helper;


use Illuminate\Support\ServiceProvider;

class WeiwaitServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/lang' => resource_path('lang'),
        ], 'weiwait-helper');
    }
}
