<?php

namespace App\Console\Commands;

use App\Product;
use App\Services\ProductsImporter;
use Illuminate\Console\Command;

class RefreshDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh databases';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('db:wipe');
        if (config('database.connections.cms')) {
            $this->call('db:wipe', ['--database' => 'cms']);
        }
        $this->call('migrate:fresh', ['--env' => 'testing']);
        $this->call('db:seed');
        $this->call('passport:install');

        return 0;
    }
}
