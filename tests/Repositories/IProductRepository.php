<?php

namespace Bogosoft\Http\Mvc\Tests\Repositories;

use Bogosoft\Http\Mvc\Tests\Models\Product;

interface IProductRepository
{
    function add(Product $product): void;

    function getAll(): iterable;
}
