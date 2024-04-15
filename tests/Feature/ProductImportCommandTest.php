<?php

namespace Tests\Feature;

use App\Services\ProductDto;
use App\Services\ProductsDataSource;
use App\Services\ProductsImporter;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Connection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Tests\TestCase;

class ProductImportCommandTest extends TestCase
{
    public function testCommandExists()
    {
        $this->setUpDataSource([]);

        $this->artisan('products:import');

        $this->assertTrue(true);
    }

    public function testOutputsNumberOfImportedProducts()
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

        $this->setUpDataSource($products);

        $this->artisan('products:import')
            ->expectsOutput('Imported products: 4');
    }

    public function testOutputsImportedProducts()
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

        $this->setUpDataSource($products);

        $command = $this->artisan('products:import');

        /** @var ProductDto $product */
        foreach (collect($products)->flatten()->all() as $product) {
            $command = $command->expectsOutput(
                sprintf('Name: "%s", price: "%d"', $product->name, $product->price)
            );
        }
    }

    private function setUpDataSource(array $products)
    {
        $this->app->bind(ProductsDataSource::class, function () use ($products) {
            return new class($products) implements ProductsDataSource {
                private $products;

                public function __construct($products)
                {
                    $this->products = $products;
                }

                public function get(int $offset)
                {
                    return $this->products[$offset] ?? [];
                }
            };
        });
    }
}
