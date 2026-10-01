<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
   public function boot(): void 
{
    // บังคับใช้ HTTPS ถ้า Request ถูกเรียกด้วย HTTPS 
    // หรือถ้ารันอยู่บนสภาพแวดล้อมที่เป็น Production
    if (Request::isSecure() || app()->environment('production')) {
        URL::forceScheme('https');
    }
}
}
