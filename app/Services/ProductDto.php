<?php

namespace App\Services;


class ProductDto
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var int
     */
    public $price;

    public function __construct(string $name, int $price)
    {
        $this->name = $name;
        $this->price = $price;
    }
}
