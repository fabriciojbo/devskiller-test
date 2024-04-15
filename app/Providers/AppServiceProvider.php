<?php

namespace App\Providers;

use App\Services\ProductDto;
use App\Services\ProductsDataSource;
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
        $this->app->bind(ProductsDataSource::class, function () {
            return new class implements ProductsDataSource {
                public function get(int $offset)
                {
                    $products = [
                        0 => [
                            new ProductDto(uniqid('product name'), random_int(1, 1000)),
                            new ProductDto(uniqid('product name'), random_int(1, 1000)),
                        ],
                        2 => [
                            new ProductDto(uniqid('product name'), random_int(1, 1000)),
                            new ProductDto(uniqid('product name'), random_int(1, 1000)),
                        ],
                        4 => [],
                    ];

                    return $products[$offset] ?? [];
                }
            };
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
