<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Options;
use App\Models\Company;
use App\Models\Module;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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

        $dataOption = Options::first();
        $option['name'] = $dataOption->name;
        $option['description'] = $dataOption->description;
        $option['logo'] = $dataOption->logo;

        $moduleAppServiceProvider = Module::where('induk_module', '0')->orderby('order_module', 'ASC')->get();

        View::share('moduleAppServiceProvider', $moduleAppServiceProvider);
        View::share('option', $option);
    }
}
