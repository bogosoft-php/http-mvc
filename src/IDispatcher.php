<?php

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IServerRequest;

/**
 * Represents a strategy for converting an HTTP request into an action
 * context.
 *
 * @package Bogosoft\Http\Mvc
 */
interface IDispatcher
{
    const STATUS_NOT_FOUND          = 0;
    const STATUS_FOUND              = 1;
    const STATUS_METHOD_NOT_ALLOWED = 2;

    /**
     * Dispatch an HTTP request.
     *
     * @param  IServerRequest $request An HTTP request.
     * @return RouteInfo               Routing information associated with the
     *                                 given HTTP request.
     */
    function dispatch(IServerRequest $request): RouteInfo;
}
