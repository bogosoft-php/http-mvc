<?php

declare(strict_types=1);

namespace Tests\Repositories;

use Tests\Models\Product;

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
