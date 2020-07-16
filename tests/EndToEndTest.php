<?php

declare(strict_types=1);

namespace Tests;

use Bogosoft\Http\Mvc\ControllerActionContext;
use Bogosoft\Http\Mvc\ActionFilterDefinition;
use Bogosoft\Http\Mvc\Controller;
use Bogosoft\Http\Mvc\ControllerActionContextResolver;
use Bogosoft\Http\Mvc\DefaultActionFilterFactory;
use Bogosoft\Http\Mvc\DispatcherActionResolver;
use Bogosoft\Http\Mvc\IControllerFactory;
use Bogosoft\Http\Mvc\IView;
use Bogosoft\Http\Mvc\IViewFactory;
use Bogosoft\Http\Routing\IActionResult;
use FastRoute\RouteCollector;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class EndToEndTest extends TestCase
{
    function testA(): void
    {
        $dispatcher = new FastRouteDispatcher(function(RouteCollector $rc): void
        {
            $context1 = new ControllerActionContext();

            $context1->controllerClass   = ProductsController::class;
            $context1->methodName        = 'index';

            $rc->addRoute('GET', '/products', $context1);

            $filter = new ActionFilterDefinition();

            $filter->class = HasUserInfoFilter::class;

            $context2 = new ControllerActionContext();

            $context2->controllerClass   = ProductsController::class;
            $context2->methodName        = 'add';
            $context2->filterDefinitions = [$filter];

            $rc->addRoute('POST', '/products/add', $context2);
        });

        $repo = new MemoryProductRepository();

        $controllers = new class($repo) implements IControllerFactory
        {
            private IProductRepository $repo;

            function __construct(IProductRepository $repo)
            {
                $this->repo = $repo;
            }

            function createController(string $class): ?Controller
            {
                if (ProductsController::class === $class)
                    return new ProductsController($this->repo);

                return null;
            }
        };

        $views = new class implements IViewFactory
        {
            function createView(string $name, $model, array $parameters): ?IView
            {
                return null;
            }
        };

        $resolvers = [
            new ControllerActionContextResolver($controllers, $views)
        ];

        $resolver = new DispatcherActionResolver($dispatcher, $resolvers);

        #
        # Ensure not found functionality works.
        #
        $request = new ServerRequest('GET', '/not/a/page');

        $action = $resolver->resolve($request);

        $this->assertNull($action);

        #
        # Ensure method not allowed functionality works.
        #
        $request = new ServerRequest('GET', '/products/add');

        $result = $resolver->resolve($request)->execute($request);

        $this->assertInstanceOf(IActionResult::class, $result);

        /** @var IActionResult $result */
        $response = $result->apply(new Response(200));

        $this->assertEquals(405, $response->getStatusCode());

        #
        # Try adding a product without user info (caught by filter).
        #
        $description = 'No description provided.';
        $name        = 'Widget 1.0';
        $price       = 1.99;

        $queryParams = [
            'description' => $description,
            'name'        => $name,
            'price'       => $price
        ];

        $uri = "/products/add";

        $request = new ServerRequest('POST', '/products/add');

        $request = $request->withQueryParams($queryParams);

        $result = $resolver->resolve($request)->execute($request);

        $this->assertInstanceOf(IActionResult::class, $result);

        /** @var IActionResult $result */
        $response = $result->apply(new Response(200));

        $this->assertEquals(401, $response->getStatusCode());

        #
        # Try adding product with user info.
        #
        $uri = "http://alice:12345678@localhost$uri";

        $request = new ServerRequest('POST', $uri);

        $request = $request->withQueryParams($queryParams);

        $result = $resolver->resolve($request)->execute($request);

        $this->assertInstanceOf(IActionResult::class, $result);

        /** @var IActionResult $result */
        $response = $result->apply(new Response(200));

        $this->assertEquals(201, $response->getStatusCode());

        #
        # Get all products.
        #
        $request = new ServerRequest('GET', '/products');

        $result = $resolver->resolve($request)->execute($request);

        $this->assertIsArray($result);

        /** @var Product[] $products */
        $products = $result;

        $this->assertCount(1, $products);

        $this->assertEquals($description, $products[0]->description);
        $this->assertEquals($name, $products[0]->name);
        $this->assertEquals($price, $products[0]->price);
    }
}
