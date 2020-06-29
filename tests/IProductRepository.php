<?php

namespace Tests;

interface IProductRepository
{
    function add(Product $product): void;

    function getAll(): iterable;
}
