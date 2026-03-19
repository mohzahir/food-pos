<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // أضف هذا السطر هنا
use App\Models\PurchaseItem;
use App\Observers\PurchaseItemObserver;
use App\Models\SaleItem;
use App\Observers\SaleItemObserver;

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
        Schema::defaultStringLength(191);
        PurchaseItem::observe(PurchaseItemObserver::class);
        SaleItem::observe(SaleItemObserver::class);
    }
}
