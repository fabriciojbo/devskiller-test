<?php

namespace Tests\Feature;

use Tests\TestCase;

class CustomErrorResponseTest extends TestCase
{
    public function testRespondsWithCustomErrorResponse()
    {
        $productId = mt_rand(1000000, 100000000);
        $response = $this->get('/api/products/'.$productId);

        $response->assertExactJson([
            'error' => 'Product with given ID does not exist',
        ]);
    }
}
