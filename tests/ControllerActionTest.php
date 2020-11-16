<?php

declare(strict_types=1);

namespace Tests;

use Bogosoft\Http\Mvc\Controller;
use Bogosoft\Http\Mvc\ControllerAction;
use Bogosoft\Http\Mvc\DelegatedControllerFactory;
use Bogosoft\Http\Mvc\IControllerFactory;
use Bogosoft\Http\Mvc\NamedParameterQueryMatcher;
use Bogosoft\Http\Mvc\NamedPropertyQueryMatcher;
use Bogosoft\Http\Mvc\ValueObjectParameterMatcher;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as IRequest;
use Tests\Models\OperandCollection;

class ControllerActionTest extends TestCase
{
    function testReturns400WhenIncompleteParameterListIsGiven(): void
    {
        $factory = new class implements IControllerFactory
        {
            function createController(string $class, IRequest $request): ?Controller
            {
                return new class extends Controller
                {
                    function create(string $username, string $password)
                    {
                        return true;
                    }
                };
            }
        };

        $matcher = new NamedParameterQueryMatcher();

        $action = new ControllerAction($factory, '', 'create', $matcher);

        $request = new ServerRequest('POST', '/');

        $request = $request->withQueryParams(['username' => 'Alice']);

        $result = $action->execute($request);

        $response = new Response(200);

        $response = $result->apply($response);

        $this->assertEquals(400, $response->getStatusCode());
    }

    function testReturns404WhenAssociatedMethodDoesNotExist(): void
    {
        $create = function(string $class): Controller
        {
            return new class extends Controller {};
        };

        $factory = new DelegatedControllerFactory($create);

        $matcher = new NamedParameterQueryMatcher();

        $action = new ControllerAction($factory, '', 'nothing', $matcher);

        $result = $action->execute(new ServerRequest('GET', '/'));

        $response = new Response(200);

        $response = $result->apply($response);

        $this->assertEquals(404, $response->getStatusCode());
    }

    function testReturnsExpectedResultWhenActionMethodWithComplexParameterIsFound(): void
    {
        $operand1 = 2;
        $operand2 = 2;
        $expected = $operand1 + $operand2;

        $create = function(string $class): Controller
        {
            return new class extends Controller
            {
                function add(OperandCollection $operands)
                {
                    return $operands->operand1 + $operands->operand2;
                }
            };
        };

        $factory = new DelegatedControllerFactory($create);

        $matcher = new ValueObjectParameterMatcher(
            new NamedPropertyQueryMatcher()
        );

        $action = new ControllerAction($factory, '', 'add', $matcher);

        $request = new ServerRequest('GET', '/');

        $request = $request->withQueryParams([
            'operand1' => $operand1,
            'operand2' => $operand2
        ]);

        $actual = $action->execute($request);

        $this->assertEquals($expected, $actual);
    }

    function testReturnsExpectedResultWhenActionMethodWithMultipleParametersIsFound(): void
    {
        $format   = 'Good %s, %s!';
        $subject  = 'World!';
        $time     = 'evening';
        $expected = sprintf($format, $time, $subject);

        $controller = new class($format) extends Controller implements IControllerFactory
        {
            private string $format;

            function __construct(string $format)
            {
                $this->format = $format;
            }

            function createController(string $class, IRequest $request): ?Controller
            {
                return $this;
            }

            function index(string $subject, string $time): string
            {
                return sprintf($this->format, $time, $subject);
            }
        };

        $request = new ServerRequest('GET', '/');

        $request = $request->withQueryParams([
            'subject' => $subject,
            'time'    => $time
        ]);

        $matcher = new NamedParameterQueryMatcher();

        $action = new ControllerAction($controller, '', 'index', $matcher);

        $actual = $action->execute($request);

        $this->assertEquals($expected, $actual);
    }

    function testReturnsExpectedResultWhenActionMethodWithParameterIsFound(): void
    {
        $format    = 'Hello, %s!';
        $subject   = 'Bob';
        $expected  = sprintf($format, $subject);

        $controller = new class($format) extends Controller implements IControllerFactory
        {
            private string $format;

            function __construct(string $format)
            {
                $this->format = $format;
            }

            function index(string $name): string
            {
                return sprintf($this->format, $name);
            }

            /**
             * @inheritDoc
             */
            function createController(string $class, IRequest $request): ?Controller
            {
                return $this;
            }
        };

        $request = new ServerRequest('GET', '/');

        $request = $request->withQueryParams(['name' => 'Bob']);

        $matcher = new NamedParameterQueryMatcher();

        $action = new ControllerAction($controller, '', 'index', $matcher);

        $actual = $action->execute($request);

        $this->assertEquals($expected, $actual);
    }

    function testReturnsExpectedResultWhenParameterlessActionMethodIsFound(): void
    {
        $expected = 'Hello, World!';

        $controller = new class($expected) extends Controller implements IControllerFactory
        {
            private string $message;

            function __construct(string $message)
            {
                $this->message = $message;
            }

            function index(): string
            {
                return $this->message;
            }

            /**
             * @inheritDoc
             */
            function createController(string $class, IRequest $request): ?Controller
            {
                return $this;
            }
        };

        $matcher = new NamedParameterQueryMatcher();

        $action = new ControllerAction($controller, '', 'index', $matcher);

        $actual = $action->execute(new ServerRequest('GET', '/'));

        $this->assertEquals($expected, $actual);
    }
}
