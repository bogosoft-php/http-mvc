<?php

namespace Tests\Repositories;

use Tests\Models\Product;

interface IProductRepository
{
    function add(Product $product): void;

    function getAll(): iterable;
}
