<?php

declare(strict_types=1);

namespace Tests;

use Bogosoft\Http\Mvc\ActionContext;
use Bogosoft\Http\Mvc\CannotActivateActionContextException;
use Bogosoft\Http\Mvc\DelegatedActionContextActivator;
use Bogosoft\Http\Mvc\DelegatedRouter;
use Bogosoft\Http\Mvc\MvcMiddleware;
use Bogosoft\Http\Mvc\MvcMiddlewareParameters;
use Bogosoft\Http\Mvc\MvcMiddlewareParameters as Parameters;
use Bogosoft\Http\Routing\HttpStatusCode;
use Bogosoft\Http\Routing\IAction;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as IResponse;
use Psr\Http\Message\ServerRequestInterface as IRequest;
use Tests\Actions\ObjectAction;
use Tests\Actions\SetStatusCodeAction;
use Tests\Factories\SimpleResponseFactory;
use Tests\Handlers\DelegatedRequestHandler;
use Tests\Handlers\StatusCodeConfigurableRequestHandler;
use Tests\Serializers\PhpSerializer;

class MvcMiddlewareTest extends TestCase
{
    function testCallsDefaultHandlerWhenRouterReturnsNull(): void
    {
        $actions = fn(IRequest $request): ?ActionContext => null;

        $config = function(Parameters $params) use ($actions): void
        {
            $params->router = new DelegatedRouter($actions);
        };

        $mvc = MvcMiddleware::create($config);

        $request = new ServerRequest('GET', '/');

        $expected = 404;

        $handler = new DelegatedRequestHandler(
            fn(IRequest $request): IResponse => new Response($expected));

        $this->assertNull($actions($request));

        $actual = $mvc->process($request, $handler);

        $this->assertEquals($expected, $actual->getStatusCode());
    }

    function testActionResultIsProperlyAppliedToResponse(): void
    {
        $expected = HttpStatusCode::CREATED;

        $params = new Parameters();

        $params->activator = new DelegatedActionContextActivator(
            fn(ActionContext $context, IRequest $request): ?IAction =>
                new SetStatusCodeAction($expected));

        $params->responses = new class implements ResponseFactoryInterface
        {
            public function createResponse(
                int $code = 200,
                string $reasonPhrase = ''
                )
                : IResponse
            {
                return new Response($code);
            }
        };

        $params->router = new DelegatedRouter(
            fn(IRequest $request): ?ActionContext =>
                new class extends ActionContext {});

        $mvc = new MvcMiddleware($params);

        $request = new ServerRequest('GET', '/');

        $handler = new StatusCodeConfigurableRequestHandler(404);

        $response = $mvc->process($request, $handler);

        $this->assertEquals($expected, $response->getStatusCode());
    }

    function testNonActionResultIsNotAcceptableWhenNoSerializerMatchesAndNotAcceptableEnabled(): void
    {
        $salutation = 'Hello, World!';

        $params = new MvcMiddlewareParameters();

        $params->activator = new DelegatedActionContextActivator(
            fn(ActionContext $context, IRequest $request): ?IAction =>
            new ObjectAction($salutation));

        $params->notAcceptableEnabled = true;

        $params->responses = new SimpleResponseFactory();

        $params->router = new DelegatedRouter(
            fn(IRequest $request): ?ActionContext =>
            new class extends ActionContext {});

        $mvc = new MvcMiddleware($params);

        $request = new ServerRequest('GET', '/');

        /** @var IRequest $request */
        $request = $request->withHeader('Accept', 'text/xml');

        $handler = new StatusCodeConfigurableRequestHandler(200);

        $response = $mvc->process($request, $handler);

        $this->assertEquals(406, $response->getStatusCode());
    }

    function testNonActionResultIsSerializedByDefaultSerializerWhenNotAcceptableIsDisabled(): void
    {
        $salutation = 'Hello, World!';

        $expected = json_encode($salutation);

        $params = new MvcMiddlewareParameters();

        $params->activator = new DelegatedActionContextActivator(
            fn(ActionContext $context, IRequest $request): ?IAction =>
                new ObjectAction($salutation));

        $params->responses = new SimpleResponseFactory();

        $params->router = new DelegatedRouter(
            fn(IRequest $request): ?ActionContext =>
            new class extends ActionContext {});

        $mvc = new MvcMiddleware($params);

        $request = new ServerRequest('GET', '/');

        $handler = new StatusCodeConfigurableRequestHandler(200);

        $response = $mvc->process($request, $handler);

        $this->assertNotEquals(406, $response->getStatusCode());

        $body = $response->getBody();

        $body->rewind();

        $this->assertEquals($expected, $body->getContents());
    }

    function testNonActionResultIsSerializedByGivenSerializerWhenNotAcceptableIsDisabled(): void
    {
        $expected = 'Hello, World!';

        $params = new MvcMiddlewareParameters();

        $params->activator = new DelegatedActionContextActivator(
            fn(ActionContext $context, IRequest $request): ?IAction =>
            new ObjectAction($expected));

        $params->responses = new SimpleResponseFactory();

        $params->router = new DelegatedRouter(
            fn(IRequest $request): ?ActionContext =>
            new class extends ActionContext {});

        $params->serializers = [new PhpSerializer()];

        $mvc = new MvcMiddleware($params);

        $request = new ServerRequest('GET', '/');

        $handler = new StatusCodeConfigurableRequestHandler(200);

        $response = $mvc->process($request, $handler);

        $this->assertNotEquals(406, $response->getStatusCode());

        $body = $response->getBody();

        $body->rewind();

        $actual = unserialize($body->getContents());

        $this->assertEquals($expected, $actual);
    }

    function testNonActionResultSerializedByGivenSerializerWhenMatchesAcceptAndNotAcceptableIsEnabled(): void
    {
        $expected = 'Hello, World!';

        $params = new MvcMiddlewareParameters();

        $params->activator = new DelegatedActionContextActivator(
            fn(ActionContext $context, IRequest $request): ?IAction =>
            new ObjectAction($expected));

        $params->notAcceptableEnabled = true;

        $params->responses = new SimpleResponseFactory();

        $params->router = new DelegatedRouter(
            fn(IRequest $request): ?ActionContext =>
            new class extends ActionContext {});

        $serializer = new PhpSerializer();

        $params->serializers = [$serializer];

        $mvc = new MvcMiddleware($params);

        $format = 'application/octet-stream';

        $this->assertEquals($format, $serializer->getContentType());

        $request = new ServerRequest('GET', '/');

        /** @var IRequest $request */
        $request = $request->withHeader('Accept', $format);

        $handler = new StatusCodeConfigurableRequestHandler(404);

        $response = $mvc->process($request, $handler);

        $body = $response->getBody();

        $body->rewind();

        $actual = unserialize($body->getContents());

        $this->assertEquals($expected, $actual);
    }

    function testThrowsCannotActivateContextExceptionWhenContextActivatorReturnsNull(): void
    {
        $activate = fn(ActionContext $context, IRequest $request): ?IAction =>
            null;

        $generate = fn(IRequest $request): ?ActionContext =>
            new class extends ActionContext {};

        $config = function(Parameters $params) use ($activate, $generate): void
        {
            $params->activator = new DelegatedActionContextActivator($activate);
            $params->router    = new DelegatedRouter($generate);
        };

        $mvc = MvcMiddleware::create($config);

        $handler = new StatusCodeConfigurableRequestHandler(404);

        $request = new ServerRequest('GET', '/');

        $this->expectException(CannotActivateActionContextException::class);

        $mvc->process($request, $handler);
    }
}
