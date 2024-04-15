<?php

namespace Tests\Feature;

use App\Services\ViewCounter;
use Tests\TestCase;

class ViewCounterTest extends TestCase
{
    private $redis;
    private $counter;

    public function setUp(): void
    {
        parent::setUp();

        $this->redis = $this->app->get('redis')->connection();
        $this->redis->flushDB();
        $this->counter = new ViewCounter($this->redis);
    }

    public function testStartsNewCounterWithOne()
    {
        $this->counter->increment('some-page-id');

        $this->assertEquals(1, $this->redis->get('page-views:some-page-id'));
    }

    public function testIncrementsViewsNumber()
    {
        $this->counter->increment('some-page-id');
        $this->counter->increment('some-page-id');
        $this->counter->increment('some-page-id');

        $this->assertEquals(3, $this->redis->get('page-views:some-page-id'));
    }

    public function testResetsCounter()
    {
        $this->counter->increment('some-page-id');

        $this->counter->reset('some-page-id');

        $this->assertEquals(0, $this->redis->get('page-views:some-page-id'));
    }

    public function testCountsViews()
    {
        $this->counter->increment('some-page-id');
        $this->counter->increment('some-page-id');

        $this->assertEquals(2, $this->counter->count('some-page-id'));
    }
}


