<?php

declare(strict_types=1);

namespace Tests;

use Bogosoft\Http\Mvc\ActionContext;
use Bogosoft\Http\Mvc\ControllerAction;
use Bogosoft\Http\Mvc\ControllerActionContext;
use Bogosoft\Http\Mvc\ControllerActionContextResolver;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class ControllerActionContextResolverTest extends TestCase
{
    function testNonControllerActionContextReturnsNullUponResolution(): void
    {
        $context = new class extends ActionContext {};

        $this->assertNotInstanceOf(ControllerActionContext::class, $context);

        $controllers = new EmptyControllerFactory();
        $views       = new EmptyViewFactory();
        $resolver    = new ControllerActionContextResolver($controllers, $views);
        $request     = new ServerRequest('GET', '/');
        $action      = $resolver->resolveContext($context, $request);

        $this->assertNull($action);
    }

    function testControllerActionContextResolvesToControllerAction(): void
    {
        $context = new ControllerActionContext();

        $context->controllerClass   = '';
        $context->filterDefinitions = [];
        $context->methodName        = '';
        $context->parameters        = [];

        $controllers = new EmptyControllerFactory();
        $views       = new EmptyViewFactory();
        $resolver    = new ControllerActionContextResolver($controllers, $views);
        $request     = new ServerRequest('GET', '/');
        $action      = $resolver->resolveContext($context, $request);

        $this->assertInstanceOf(ControllerAction::class, $action);
    }
}
