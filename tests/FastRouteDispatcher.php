<?php

declare(strict_types=1);

namespace Tests;

use Bogosoft\Http\Mvc\IDispatcher;
use Bogosoft\Http\Mvc\RouteInfo;
use FastRoute\Dispatcher;
use Psr\Http\Message\ServerRequestInterface as IServerRequest;
use function FastRoute\simpleDispatcher;

class FastRouteDispatcher implements IDispatcher
{
    private Dispatcher $dispatcher;

    function __construct(callable $collector)
    {
        $this->dispatcher = simpleDispatcher($collector);
    }

    /**
     * @inheritDoc
     */
    function dispatch(IServerRequest $request): RouteInfo
    {
        $method = $request->getMethod();
        $path   = $request->getUri()->getPath();
        $result = $this->dispatcher->dispatch($method, $path);

        $info = new RouteInfo($result[0]);

        if (Dispatcher::METHOD_NOT_ALLOWED === $result[0])
            $info->allowedMethods = $result[1];

        if (Dispatcher::FOUND === $result[0])
        {
            $info->context = $result[1];
            $info->context->parameters = $result[2];
        }

        return $info;
    }
}
