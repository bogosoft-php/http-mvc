<?php

declare(strict_types=1);

namespace Tests;

use Bogosoft\Http\Mvc\Controller;
use Bogosoft\Http\Routing\IActionResult;
use Bogosoft\Http\Routing\Results\StatusCodeResult;

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
