<?php

namespace App\Services;


use App\Product;
use Psr\Log\LoggerInterface;
use Illuminate\Support\Facades\DB;

class ProductsImporter
{
    protected $logger;

    /**
     * @var ProductsDataSource
     */
    private $dataSource;

    public function __construct(LoggerInterface $logger, ProductsDataSource $dataSource)
    {
        $this->logger = $logger;
        $this->dataSource = $dataSource;
    }

    /**
     * @return int[] Created product ids
     */
    public function import()
    {

        try {
            // Start a database transaction
            DB::beginTransaction();

            $ids = [];
            $offset = 0;

            do {
                $products = $this->dataSource->get($offset);

                foreach ($products as $productDto) {
                    $product = new Product(['name' => $productDto->name, 'price' => $productDto->price]);
                    $product->save();

                    $ids[] = $product->id;
                }

                $offset += count($products);
            } while (!empty($products));

            DB::commit();

            return $ids;
        } catch (\Exception $e) {
            // Rollback the transaction if an exception occurs
            DB::rollBack();

            // Log the error with the offset
            $this->logger->error('An error occurred during import.', ['offset' => $offset]);

            // Rethrow the exception to propagate it further if needed
            throw $e;
        }

    }
}
