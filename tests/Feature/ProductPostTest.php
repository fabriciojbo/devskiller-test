<?php

namespace Tests\Feature;

use App\Product;
use App\User;
use Illuminate\Testing\TestResponse;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ProductPostTest extends TestCase
{
    public function testDenyNonAdminUserAccess()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $response = $this->postJson('/api/products', [
            'name' => 'iPhone 10',
            'price' => 1000,
        ]);

        $response->assertStatus(403);
    }

    public function testSuccessfulPost()
    {
        $user = factory(User::class)->state('admin')->create();
        Passport::actingAs($user);

        $response = $this->postJson('/api/products', [
            'name' => 'iPhone 10',
            'price' => 1000,
        ]);

        $response->assertStatus(201);

        $id = $response->json('data.id');

        $product = Product::find($id);
        $this->assertResponseContainsProduct($response, $product);
        $this->assertEquals('iPhone 10', $product->name);
        $this->assertEquals(1000, $product->price);
    }

    /**
     * @dataProvider validationDataProvider
     */
    public function testValidation(array $invalidData, string $invalidParameter)
    {
        $user = factory(User::class)->state('admin')->create();
        Passport::actingAs($user);

        $validData = [
            'name' => 'iPhone 10',
            'price' => 1000,
        ];
        $data = array_merge($validData, $invalidData);

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([$invalidParameter]);
    }

    public function validationDataProvider()
    {
        return [
            [['name' => null], 'name'],
            [['price' => null], 'price'],
            [['price' => 0], 'price'],
        ];
    }

    private function assertResponseContainsProduct(TestResponse $response, Product $product): void
    {
        $response->assertJson([
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
            ],
        ]);
    }
}
