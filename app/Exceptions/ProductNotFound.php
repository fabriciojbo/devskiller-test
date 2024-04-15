<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class ProductNotFound extends \Exception
{
    public $productId;

    public static function withId(int $id): self
    {
        $exception = new self('Product with given ID does not exist');
        $exception->productId = $id;

        return $exception;
    }
}
