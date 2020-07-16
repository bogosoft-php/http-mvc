<?php

declare(strict_types=1);

namespace Tests;

use Bogosoft\Http\Mvc\ActionContext;
use Bogosoft\Http\Mvc\ViewAction;
use Bogosoft\Http\Mvc\ViewActionContext;
use Bogosoft\Http\Mvc\ViewActionContextResolver;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class ViewActionContextResolverTest extends TestCase
{
    function testNonViewActionContextReturnsNullWhenResolved(): void
    {
        $context = new class extends ActionContext {};

        $this->assertNotInstanceOf(ViewActionContext::class, $context);

        $request  = new ServerRequest('GET', '/');
        $views    = new EmptyViewFactory();
        $resolver = new ViewActionContextResolver($views);
        $action   = $resolver->resolveContext($context, $request);

        $this->assertNull($action);
    }

    function testViewActionContextResolvesToViewAction(): void
    {
        $context = new ViewActionContext();

        $context->filterDefinitions = [];
        $context->parameters        = [];
        $context->viewName          = '/home/index';

        $request  = new ServerRequest('GET', '/');
        $views    = new EmptyViewFactory();
        $resolver = new ViewActionContextResolver($views);
        $action   = $resolver->resolveContext($context, $request);

        $this->assertInstanceOf(ViewAction::class, $action);
    }
}
