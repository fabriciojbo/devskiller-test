<?php

namespace Tests\Feature;

use App\Page;
use Tests\TestCase;

class CmsDatabaseTest extends TestCase
{
    public function testConnectionIsConfigured()
    {
        /** @var \Illuminate\Config\Repository $config */
        $config = $this->app->get('config');
        $connections = $config->get('database.connections');

        $this->assertArrayHasKey('cms', $connections);
        $this->assertEquals('sqlite', $connections['cms']['driver']);
        $this->assertStringEndsWith('cms_database.sqlite', $connections['cms']['database']);
    }

    public function testCanConnectToCmsDatabase()
    {
        /** @var \Illuminate\Database\DatabaseManager $connection */
        $connection = $this->app->get('db');

        $connection = $connection->reconnect('cms');
        $this->assertTrue($connection->statement('select 1;'));
    }

    public function testPageModelIsConfigured()
    {
        $page = new Page();

        $this->assertEquals('cms', $page->getConnection()->getName());
    }

    public function testPageTableExists()
    {
        $page = factory(Page::class)->create(['name' => 'home', 'content' => 'lorem ipsum']);

        $this->assertDatabaseHas(
            'pages',
            [
                'id' => $page->id,
                'name' => 'home',
                'content' => 'lorem ipsum',
            ],
            'cms'
        );
    }
}


