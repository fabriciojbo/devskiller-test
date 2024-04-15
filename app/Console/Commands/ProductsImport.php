<?php

namespace App\Console\Commands;

use App\Services\ProductsImporter;
use Illuminate\Console\Command;

class ProductsImport extends Command
{
    protected ProductsImporter $productsImporter;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ProductsImporter $productsImporter)
    {
        parent::__construct();
        $this->productsImporter = $productsImporter;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting product import...');

        // Start the import process
        $importedProducts = $this->productsImporter->import();

        // Output information about imported products
        $this->line('Imported products: ' . count($importedProducts));

        foreach ($importedProducts as $product) {
            $this->line('Name: "' . $product->name . '", price: "' . $product->price . '"');
        }

        $this->info('Product import completed.');

        return Command::SUCCESS;
    }
}
