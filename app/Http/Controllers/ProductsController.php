<?php

namespace App\Http\Controllers;

use App\Exceptions\ProductNotFound;
use App\Http\Resources\ProductResource;
use App\Product;

class ProductsController extends Controller
{
    public function get(int $id): ProductResource
    {
        $product = Product::find($id);

        if ($product === null) {
            throw ProductNotFound::withId($id);
        }

        return new ProductResource($product);
    }
}
