<?php

namespace Tests\Feature;

use App\Services\ProductDto;
use App\Services\ProductsDataSource;
use App\Services\ProductsImporter;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

class ProductImporterTest extends TestCase
{
    public function testCommitsTransaction()
    {
        $txStarted = false;
        $txCommitted = false;

        /** @var Connection $db */
        $db = $this->app->get('db');
        $db->getEventDispatcher()->listen(TransactionBeginning::class, function () use (&$txStarted) {
            $txStarted = true;
        });
        $db->getEventDispatcher()->listen(TransactionCommitted::class, function () use (&$txCommitted) {
            $txCommitted = true;
        });

        $db->flushQueryLog();
        $db->enableQueryLog();


        $this->app->bind(ProductsDataSource::class, function () {
            return new class implements ProductsDataSource {
                public function get(int $offset)
                {
                    if ($offset === 10) {
                        return [];
                    }

                    return [
                        new ProductDto(uniqid('product name'), random_int(1, 1000)),
                        new ProductDto(uniqid('product name'), random_int(1, 1000)),
                    ];
                }
            };
        });

        $db->flushQueryLog();

        /** @var ProductsImporter $importer */
        $importer = $this->app->make(ProductsImporter::class);
        $importer->import();

        $this->assertTrue($txStarted, 'Transaction has not been opened');
        $this->assertTrue($txCommitted, 'Transaction has not been committed');
    }

    public function testRollbacksTransaction()
    {
        $txStarted = false;
        $txRolledBack = false;

        /** @var Connection $db */
        $db = $this->app->get('db');
        $db->getEventDispatcher()->listen(TransactionBeginning::class, function () use (&$txStarted) {
            $txStarted = true;
        });
        $db->getEventDispatcher()->listen(TransactionRolledBack::class, function () use (&$txRolledBack) {
            $txRolledBack = true;
        });
        $db->flushQueryLog();
        $db->enableQueryLog();

        $this->app->bind(ProductsDataSource::class, function () {
            return new class implements ProductsDataSource {
                public function get(int $offset)
                {
                    if ($offset === 10) {
                        throw new \Exception('Datasource error');
                    }

                    return [
                        new ProductDto(uniqid('product name'), random_int(1, 1000)),
                        new ProductDto(uniqid('product name'), random_int(1, 1000)),
                    ];
                }
            };
        });

        /** @var ProductsImporter $importer */
        $importer = $this->app->make(ProductsImporter::class);
        $importer->import();

        $this->assertTrue($txStarted, 'Transaction has not been opened');
        $this->assertTrue($txRolledBack, 'Transaction has not been rolled back');
    }

    public function testLogsError()
    {
        $this->instance(
            LoggerInterface::class,
            \Mockery::mock(LoggerInterface::class,
                function ($mock) {
                    $mock->shouldReceive('error')->once();
                }
            )
        );

        /** @var Connection $db */
        $this->app->bind(ProductsDataSource::class, function () {
            return new class implements ProductsDataSource {
                public function get(int $offset)
                {
                    if ($offset === 0) {
                        throw new \Exception('Datasource error');
                    }

                    return [];
                }
            };
        });

        /** @var ProductsImporter $importer */
        $importer = $this->app->make(ProductsImporter::class);
        $importer->import();
    }
}
