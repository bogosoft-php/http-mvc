<?php

declare(strict_types=1);

namespace Tests;

use Bogosoft\Http\Mvc\ControllerActionContext;
use Bogosoft\Http\Mvc\Controller;
use Bogosoft\Http\Mvc\ControllerAction;
use Bogosoft\Http\Mvc\ControllerActionContextResolver;
use Bogosoft\Http\Mvc\DelegatedControllerFactory;
use Bogosoft\Http\Mvc\DelegatedViewFactory;
use Bogosoft\Http\Mvc\DispatcherActionResolver;
use Bogosoft\Http\Mvc\IDispatcher;
use Bogosoft\Http\Mvc\IView;
use Bogosoft\Http\Mvc\RouteInfo;
use Bogosoft\Http\Routing\Actions\MethodNotAllowedAction;
use Bogosoft\Http\Routing\FilteredAction;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as IServerRequest;
use RuntimeException;

class DispatcherActionResolverTest extends TestCase
{
    function testReturnsControllerActionWhenDispatcherFindsNonFilteredActionContext(): void
    {
        $getController = function (string $class): ?Controller
        {
            return new class extends Controller {};
        };

        $controllers = new DelegatedControllerFactory($getController);

        $dispatcher = new class implements IDispatcher
        {
            function dispatch(IServerRequest $request): RouteInfo
            {
                $context = new ControllerActionContext();

                $context->controllerClass = 'HomeController';
                $context->methodName      = 'index';

                return new RouteInfo(IDispatcher::STATUS_FOUND, [], $context);
            }
        };

        $getView = function(string $name, $model, array $parameters): ?IView
        {
            return null;
        };

        $views = new DelegatedViewFactory($getView);

        $resolvers = [
            new ControllerActionContextResolver($controllers, $views)
        ];

        $resolver = new DispatcherActionResolver($dispatcher, $resolvers);

        $request = new ServerRequest('GET', '/');

        $action = $resolver->resolve($request);

        $this->assertInstanceOf(ControllerAction::class, $action);
    }

    function testReturnsFilteredActionWhenDispatcherFindFilteredActionContext(): void
    {
        $getController = function (string $class): ?Controller
        {
            return new class extends Controller {};
        };

        $controllers = new DelegatedControllerFactory($getController);

        $dispatcher = new class implements IDispatcher
        {
            function dispatch(IServerRequest $request): RouteInfo
            {
                $context = new ControllerActionContext();

                $context->controllerClass = 'HomeController';
                $context->filterDefinitions   = ['RequiresClaim'];
                $context->methodName      = 'index';

                return new RouteInfo(IDispatcher::STATUS_FOUND, [], $context);
            }
        };

        $getView = function(string $name, $model, array $parameters): ?IView
        {
            return null;
        };

        $views = new DelegatedViewFactory($getView);

        $resolvers = [
            new ControllerActionContextResolver($controllers, $views)
        ];

        $resolver = new DispatcherActionResolver($dispatcher, $resolvers);

        $request = new ServerRequest('GET', '/');

        $action = $resolver->resolve($request);

        $this->assertInstanceOf(FilteredAction::class, $action);
    }

    function testReturnsMethodNotAllowedWhenDispatcherIndicatesDisallowedMethod(): void
    {
        $dispatcher = new class implements IDispatcher
        {
            function dispatch(IServerRequest $request): RouteInfo
            {
                return new RouteInfo(IDispatcher::STATUS_METHOD_NOT_ALLOWED);
            }
        };

        $resolver = new DispatcherActionResolver($dispatcher, []);

        $request = new ServerRequest('GET', '/');

        $action = $resolver->resolve($request);

        $this->assertInstanceOf(MethodNotAllowedAction::class, $action);
    }

    function testReturnsNullWhenDispatcherIndicatesNotFound(): void
    {
        $dispatcher = new class implements IDispatcher
        {
            function dispatch(IServerRequest $request): RouteInfo
            {
                return new RouteInfo(IDispatcher::STATUS_NOT_FOUND);
            }
        };

        $resolver = new DispatcherActionResolver($dispatcher, []);

        $request = new ServerRequest('GET', '/');

        $action = $resolver->resolve($request);

        $this->assertNull($action);
    }

    function testThrowsRuntimeExceptionWhenDispatcherIndicatesFoundAndContextIsNull(): void
    {
        $dispatcher = new class implements IDispatcher
        {
            function dispatch(IServerRequest $request): RouteInfo
            {
                return new RouteInfo(IDispatcher::STATUS_FOUND);
            }
        };

        $resolver = new DispatcherActionResolver($dispatcher, []);

        $request = new ServerRequest('GET', '/');

        $this->expectException(RuntimeException::class);

        $resolver->resolve($request);
    }
}
