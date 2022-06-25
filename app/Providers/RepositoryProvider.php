<?php

namespace App\Providers;

use App\Repository\Cart\CartContract;
use Illuminate\Support\ServiceProvider;
use App\Repository\Cart\CartRepository;
use App\Repository\Product\ProductContract;
use App\Repository\Product\ProductRepository;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(CartContract::class, CartRepository::class);
        $this->app->bind(ProductContract::class, ProductRepository::class);
    }
}
