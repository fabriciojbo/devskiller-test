<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostProductReviewRequest;
use App\Http\Resources\ProductReviewResource;
use App\Product;
use App\ProductReview;
use Illuminate\Support\Facades\Auth;

class ProductReviewsController extends Controller
{
    public function post(int $productId, PostProductReviewRequest $request)
    {
        $review = new ProductReview();
        $product = Product::findOrFail($productId);

        $review->product()->associate($product);
        $review->user()->associate(Auth::user());
        $review->save($request->validated());

        return new ProductReviewResource($review);
    }
}
