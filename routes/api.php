<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/products/{id}', 'ProductsController@get');
Route::post('/products/{id}/reviews', 'ProductReviewsController@post');


Route::prefix('admin')->middleware(['auth:api', 'auth.admin'])
    ->group(function () {
    Route::post('/products', 'ProductsAdminController@post');
});
