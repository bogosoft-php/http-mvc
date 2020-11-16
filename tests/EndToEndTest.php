<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Tests;

use Bogosoft\Http\Mvc\ActionContext;
use Bogosoft\Http\Mvc\ControllerAction;
use Bogosoft\Http\Mvc\ControllerActionContext;
use Bogosoft\Http\Mvc\DefaultValueParameterMatcher;
use Bogosoft\Http\Mvc\DelegatedActionContextActivator;
use Bogosoft\Http\Mvc\DelegatedRouter;
use Bogosoft\Http\Mvc\IAction;
use Bogosoft\Http\Mvc\IControllerFactory;
use Bogosoft\Http\Mvc\IParameterMatcher;
use Bogosoft\Http\Mvc\MvcMiddleware;
use Bogosoft\Http\Mvc\MvcMiddlewareParameters;
use Bogosoft\Http\Mvc\Tests\Controllers\ProductsController;
use Bogosoft\Http\Mvc\Tests\Factories\ContainerControllerFactory;
use Bogosoft\Http\Mvc\Tests\Factories\SimpleResponseFactory;
use Bogosoft\Http\Mvc\Tests\Models\Product;
use Bogosoft\Http\Mvc\Tests\Repositories\IProductRepository;
use Bogosoft\Http\Mvc\Tests\Repositories\MemoryProductRepository;
use Bogosoft\Http\Mvc\Tests\Serializers\PhpSerializer;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface as IContainer;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\MessageInterface as IMessage;
use Psr\Http\Message\ResponseInterface as IResponse;
use Psr\Http\Message\ServerRequestInterface as IRequest;
use Psr\Http\Server\MiddlewareInterface as IMiddleware;
use Psr\Http\Server\RequestHandlerInterface as IRequestHandler;
use ReflectionNamedType;
use ReflectionParameter;
use RuntimeException;

class EndToEndTest extends TestCase
{
    private static ?IContainer $container = null;

    private static function activate(
        ActionContext $context,
        IRequest $request
        )
        : ?IAction
    {
        if (!($context instanceof ControllerActionContext))
            return null;

        $container = self::getContainer();

        return new ControllerAction(
            $container->get(IControllerFactory::class),
            $context->controllerClass,
            $context->methodName,
            self::getParameterMatcher()
            );
    }

    private static function deserializePayload(IMessage $response)
    {
        $body = $response->getBody();

        $body->rewind();

        return unserialize($body->getContents());
    }

    private static function getContainer(): IContainer
    {
        if (null !== self::$container)
            return self::$container;

        return self::$container = new class implements IContainer
        {
            private ?IProductRepository $products = null;

            public function get($id)
            {
                switch ($id)
                {
                    case IControllerFactory::class:
                        return new ContainerControllerFactory($this);
                    case IProductRepository::class:
                        return $this->products
                            ?? ($this->products = new MemoryProductRepository());
                    case ProductsController::class:
                        return new ProductsController(
                            $this->get(IProductRepository::class));
                    default:
                        throw new class
                            extends RuntimeException
                            implements NotFoundExceptionInterface {};
                }
            }

            public function has($id)
            {
                switch ($id)
                {
                    case IControllerFactory::class:
                    case IProductRepository::class:
                    case ProductsController::class:
                        return true;
                    default:
                        return false;
                }
            }
        };
    }

    private static function getDefaultRequestHandler(): IRequestHandler
    {
        return new class implements IRequestHandler
        {
            public function handle(IRequest $request): IResponse
            {
                return new Response(404);
            }
        };
    }

    private static function getParameterMatcher(): IParameterMatcher
    {
        return new class implements IParameterMatcher
        {
            function tryMatch(ReflectionParameter $rp, IRequest $request, &$result): bool
            {
                if (
                    null !== ($type = $rp->getType())
                    && $type instanceof ReflectionNamedType
                    && $type->getName() === Product::class
                    )
                {
                    $request->getBody()->rewind();

                    $result = unserialize($request->getBody()->getContents());

                    return true;
                }

                return false;
            }
        };
    }

    private static function getMvcMiddleware(): IMiddleware
    {
        $params = new MvcMiddlewareParameters();

        $params->activator = new DelegatedActionContextActivator(
            fn(ActionContext $c, IRequest $r): ?IAction =>
                self::activate($c, $r));

        $params->defaultSerializer = new PhpSerializer();

        $params->responses = new SimpleResponseFactory();

        $params->router = new DelegatedRouter(
            fn(IRequest $request): ?ActionContext =>
                self::route($request));

        return new MvcMiddleware($params);
    }

    private static function route(IRequest $request): ?ActionContext
    {
        $method = $request->getMethod();
        $path   = $request->getUri()->getPath();

        if (substr($path, 0, 9) === '/products')
            if (strlen($path) === 9 && 'GET' === $method)
                return new ControllerActionContext(
                    ProductsController::class,
                    'index'
                    );
            elseif ('POST' === $method)
                return new ControllerActionContext(
                    ProductsController::class,
                    'add'
                    );

        return null;
    }

    function testA(): void
    {
        $mvc = self::getMvcMiddleware();

        $request = new ServerRequest('GET', '/products');

        $handler = self::getDefaultRequestHandler();

        $response = $mvc->process($request, $handler);

        $this->assertEquals(200, $response->getStatusCode());

        $payload = self::deserializePayload($response);

        $this->assertIsArray($payload);
        $this->assertEmpty($payload);

        $expected = new Product();

        $expected->description = 'This is a test product';
        $expected->name        = 'Test Product A';
        $expected->price       = 9.99;

        $request = new ServerRequest(
            'POST',
            '/products',
            [],
            serialize($expected));

        $response = $mvc->process($request, $handler);

        $this->assertEquals(201, $response->getStatusCode());

        $request = new ServerRequest('GET', '/products');

        $response = $mvc->process($request, $handler);

        $this->assertEquals(200, $response->getStatusCode());

        $payload = self::deserializePayload($response);

        $this->assertIsArray($payload);
        $this->assertCount(1, $payload);

        $actual = $payload[0];

        $this->assertInstanceOf(Product::class, $actual);
        $this->assertEquals($expected->description, $actual->description);
        $this->assertEquals($expected->name, $actual->name);
        $this->assertEquals($expected->price, $actual->price);
    }
}
