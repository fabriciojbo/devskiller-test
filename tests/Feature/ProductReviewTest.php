<?php

namespace Tests\Feature;

use App\Product;
use App\ProductReview;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ProductReviewTest extends TestCase
{
    public function testDenyGuestAccess()
    {
        $product = factory(Product::class)->create();

        $response = $this->postJson($this->postUrl($product->id), [
            'review' => 5,
            'comment' => 'Lorem ipsum',
        ]);

        $response->assertStatus(401);
    }

    public function testSuccessfulPost()
    {
        $user = factory(User::class)->state('admin')->create();
        $product = factory(Product::class)->create();
        Passport::actingAs($user);

        $response = $this->postJson($this->postUrl($product->id), [
            'review' => 5,
            'comment' => 'Lorem ipsum',
        ]);

        $response->assertStatus(201);
        $id = $response->json('data.id');
        $review = ProductReview::find($id);

        $response->assertJson([
            'data' => [
                'id' => $review->id,
                'review' => $review->review,
                'comment' => $review->comment,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
            ],
        ]);
        $this->assertEquals(5, $review->review);
        $this->assertEquals('Lorem ipsum', $review->comment);
        $this->assertEquals($product->id, $review->product->id);
        $this->assertEquals($user->id, $review->user->id);
    }

    /**
     * @dataProvider validationDataProvider
     */
    public function testValidation(array $invalidData, string $invalidParameter)
    {
        $product = factory(Product::class)->create();
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $validData = [
            'review' => 5,
            'comment' => 'Lorem ipsum',
        ];
        $data = array_merge($validData, $invalidData);

        $response = $this->postJson($this->postUrl($product->id), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([$invalidParameter]);
    }

    public function validationDataProvider()
    {
        return [
            [['review' => 0], 'review'],
            [['review' => null], 'review'],
            [['comment' => null], 'comment'],
        ];
    }

    private function postUrl(int $productId)
    {
        return sprintf('/api/products/%d/reviews', $productId);
    }
}
