<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use InstagramAPI\Instagram;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Instagram::class, function () {
            Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
            $instagram = new Instagram(false, false, [
                'storage' => 'file',
                'basefolder' => storage_path('app/instagram/sessions'),
            ]);

            return $instagram;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
