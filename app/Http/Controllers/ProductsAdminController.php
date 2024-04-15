<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostProductRequest;
use App\Http\Resources\ProductResource;
use App\Product;

class ProductsAdminController extends Controller
{
    public function post(PostProductRequest $request)
    {
        $product = new Product();

        $product->save($request->validated());

        return new ProductResource($product);
    }
}
