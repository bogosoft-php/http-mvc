<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Tests\Controllers;

use Bogosoft\Http\Mvc\Controller;
use Bogosoft\Http\Mvc\IActionResult;
use Bogosoft\Http\Mvc\StatusCodeResult;
use Bogosoft\Http\Mvc\Tests\Repositories\IProductRepository;
use Bogosoft\Http\Mvc\Tests\Models\Product;

class ProductsController extends Controller
{
    function __construct(private IProductRepository $repository)
    {
    }

    function add(Product $product): IActionResult
    {
        $this->repository->add($product);

        return new StatusCodeResult(201);
    }

    function index(): array
    {
        return [...$this->repository->getAll()];
    }
}
