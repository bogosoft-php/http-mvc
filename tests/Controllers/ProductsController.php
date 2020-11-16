<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Tests\Controllers;

use Bogosoft\Http\Mvc\Controller;
use Bogosoft\Http\Routing\IActionResult;
use Bogosoft\Http\Routing\Results\StatusCodeResult;
use Tests\Repositories\IProductRepository;
use Tests\Models\Product;

class ProductsController extends Controller
{
    private IProductRepository $repository;

    function __construct(IProductRepository $repository)
    {
        $this->repository = $repository;
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
