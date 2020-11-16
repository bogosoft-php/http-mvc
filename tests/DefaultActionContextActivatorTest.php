<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Tests;

use Bogosoft\Http\Mvc\ActionContext;
use Bogosoft\Http\Mvc\ControllerAction;
use Bogosoft\Http\Mvc\ControllerActionContext;
use Bogosoft\Http\Mvc\DefaultActionContextActivator;
use Bogosoft\Http\Mvc\DefaultValueParameterMatcher;
use Bogosoft\Http\Mvc\ViewAction;
use Bogosoft\Http\Mvc\ViewActionContext;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Bogosoft\Http\Mvc\Tests\Factories\EmptyControllerFactory;
use Bogosoft\Http\Mvc\Tests\Factories\EmptyViewFactory;

class DefaultActionContextActivatorTest extends TestCase
{
    function testControllerActionContextResolvesToControllerAction(): void
    {
        $context = new ControllerActionContext();

        $context->controllerClass   = '';
        $context->filterDefinitions = [];
        $context->methodName        = '';
        $context->parameters        = [];

        $controllers = new EmptyControllerFactory();
        $views       = new EmptyViewFactory();
        $matcher     = new DefaultValueParameterMatcher();
        $resolver    = new DefaultActionContextActivator($controllers, $matcher, $views);
        $activator   = new ServerRequest('GET', '/');
        $action      = $resolver->activateContext($context, $activator);

        $this->assertInstanceOf(ControllerAction::class, $action);
    }

    function testNonControllerActionContextReturnsNullUponResolution(): void
    {
        $context = new class extends ActionContext {};

        $this->assertNotInstanceOf(ControllerActionContext::class, $context);

        $controllers = new EmptyControllerFactory();
        $views       = new EmptyViewFactory();
        $matcher     = new DefaultValueParameterMatcher();
        $activator   = new DefaultActionContextActivator($controllers, $matcher, $views);
        $request     = new ServerRequest('GET', '/');
        $action      = $activator->activateContext($context, $request);

        $this->assertNull($action);
    }

    function testNonViewActionContextReturnsNullWhenResolved(): void
    {
        $context = new class extends ActionContext {};

        $this->assertNotInstanceOf(ViewActionContext::class, $context);

        $controllers = new EmptyControllerFactory();
        $request     = new ServerRequest('GET', '/');
        $views       = new EmptyViewFactory();
        $matcher     = new DefaultValueParameterMatcher();
        $activator   = new DefaultActionContextActivator($controllers, $matcher, $views);
        $action      = $activator->activateContext($context, $request);

        $this->assertNull($action);
    }

    function testViewActionContextResolvesToViewAction(): void
    {
        $context = new ViewActionContext();

        $context->filterDefinitions = [];
        $context->viewName          = '/home/index';

        $controllers = new EmptyControllerFactory();
        $request     = new ServerRequest('GET', '/');
        $views       = new EmptyViewFactory();
        $matcher     = new DefaultValueParameterMatcher();
        $activator   = new DefaultActionContextActivator($controllers, $matcher, $views);
        $action      = $activator->activateContext($context, $request);

        $this->assertInstanceOf(ViewAction::class, $action);
    }
}
