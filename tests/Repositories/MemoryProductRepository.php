<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Tests\Repositories;

use Bogosoft\Http\Mvc\Tests\Models\Product;

final class MemoryProductRepository implements IProductRepository
{
    private array $products = [];

    function add(Product $product): void
    {
        $this->products[] = $product;
    }

    function getAll(): iterable
    {
        yield from $this->products;
    }
}
