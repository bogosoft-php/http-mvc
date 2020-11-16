<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Tests;

use Bogosoft\Http\Mvc\IView;
use Bogosoft\Http\Mvc\IViewFactory;
use Bogosoft\Http\Mvc\ViewAction;
use Bogosoft\Http\Mvc\ViewResult;
use Bogosoft\Http\Routing\Results\NotFoundResult;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Bogosoft\Http\Mvc\Tests\Factories\EmptyViewFactory;

class ViewActionTest extends TestCase
{
    function testNonNullViewFromFactoryResultsInViewAction(): void
    {
        $views = new class implements IViewFactory
        {
            function createView(string $name, $model, array $parameters): ?IView
            {
                return new class implements IView
                {
                    function render($target): void
                    {
                    }
                };
            }
        };

        $action  = new ViewAction('', null, [], $views);
        $request = new ServerRequest('GET', '/');
        $result  = $action->execute($request);

        $this->assertInstanceOf(ViewResult::class, $result);
    }

    function testNullViewFromFactoryResultsInNotFoundResult(): void
    {
        $views   = new EmptyViewFactory();
        $action  = new ViewAction('', null, [], $views);
        $request = new ServerRequest('GET', '/');
        $result  = $action->execute($request);

        $this->assertInstanceOf(NotFoundResult::class, $result);
    }
}
