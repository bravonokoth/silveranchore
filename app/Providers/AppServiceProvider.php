<?php

namespace App\Providers;
use App\Models\Order;
use App\Observers\OrderObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    protected $policies = [
    Notification::class => NotificationPolicy::class,
];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Order::observe(OrderObserver::class);
    }
}
