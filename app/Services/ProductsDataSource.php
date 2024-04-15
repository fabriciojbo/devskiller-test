<?php

namespace App\Services;


interface ProductsDataSource
{
    /**
     * @param int $offset
     * @return ProductDto[]
     */
    public function get(int $offset);
}
