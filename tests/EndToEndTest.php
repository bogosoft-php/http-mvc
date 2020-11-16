<?php

declare(strict_types=1);

namespace Tests;

use Bogosoft\Http\Mvc\ActionContext;
use Bogosoft\Http\Mvc\ControllerActionContext;
use Bogosoft\Http\Mvc\ActionFilterDefinition;
use Bogosoft\Http\Mvc\Controller;
use Bogosoft\Http\Mvc\DefaultActionContextActivator;
use Bogosoft\Http\Mvc\DefaultActionFilterFactory;
use Bogosoft\Http\Mvc\IActionContextActivator;
use Bogosoft\Http\Mvc\IControllerFactory;
use Bogosoft\Http\Mvc\IView;
use Bogosoft\Http\Mvc\IViewFactory;
use Bogosoft\Http\Mvc\MethodNotAllowedActionContext;
use Bogosoft\Http\Mvc\MvcActionResolver;
use Bogosoft\Http\Mvc\NamedPropertyQueryMatcher;
use Bogosoft\Http\Mvc\ValueObjectParameterMatcher;
use Bogosoft\Http\Routing\IActionResult;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as IRequest;
use Tests\ActionFilters\HasUserInfoFilter;
use Tests\Controllers\ProductsController;
use Tests\Models\Product;
use Tests\Repositories\IProductRepository;
use Tests\Repositories\MemoryProductRepository;

class EndToEndTest extends TestCase
{
    function testA(): void
    {
        $repo = new MemoryProductRepository();

        $controllers = new class($repo) implements IControllerFactory
        {
            private IProductRepository $repo;

            function __construct(IProductRepository $repo)
            {
                $this->repo = $repo;
            }

            function createController(string $class, IRequest $request): ?Controller
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

        $matcher = new ValueObjectParameterMatcher(
            new NamedPropertyQueryMatcher()
        );

        $activator = new DefaultActionContextActivator($controllers, $matcher, $views);

        $resolver = new class($activator) extends MvcActionResolver
        {
            function __construct(IActionContextActivator $activator)
            {
                $filters = new DefaultActionFilterFactory();

                parent::__construct($activator, $filters);
            }

            /**
             * @inheritDoc
             */
            protected function getActionContext(IRequest $request): ?ActionContext
            {
                $method = strtolower($request->getMethod());
                $path   = strtolower($request->getUri()->getPath());

                if ('/products' === $path)
                {
                    if ('get' !== $method)
                        return new MethodNotAllowedActionContext(['GET']);

                    $context = new ControllerActionContext();

                    $context->controllerClass = ProductsController::class;
                    $context->methodName      = 'index';

                    return $context;
                }
                elseif ('/products/add' === $path)
                {
                    if ('post' !== $method)
                        return new MethodNotAllowedActionContext(['POST']);

                    $filter = new ActionFilterDefinition();

                    $filter->class = HasUserInfoFilter::class;

                    $context = new ControllerActionContext();

                    $context->controllerClass   = ProductsController::class;
                    $context->filterDefinitions = [$filter];
                    $context->methodName        = 'add';

                    return $context;
                }
                else
                {
                    return null;
                }
            }
        };

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
