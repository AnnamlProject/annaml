<?php

namespace App\Providers;

use App\CompanyProfile;
use App\SettingDepartement;
use App\TaxpayersProfile;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        View::composer('*', function ($view) {
            $view->with('company', CompanyProfile::first());

            $view->with('taxpayers', TaxpayersProfile::first());


            $currentDept = SettingDepartement::where('key', 'current_department')->value('value') ?? '-';
            $view->with('currentDept', $currentDept);
        });
    }
}
