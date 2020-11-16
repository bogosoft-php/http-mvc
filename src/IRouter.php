<?php

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IRequest;

/**
 * Represents a strategy for converting an HTTP request into an action
 * context.
 *
 * @package Bogosoft\Http\Mvc
 */
interface IRouter
{
    /**
     * Convert a given HTTP request into an action context.
     *
     * @param  IRequest           $request An HTTP request.
     * @return ActionContext|null          The result of attempting to convert
     *                                     the given HTTP request into an
     *                                     action context. Implementations
     *                                     SHOULD return {@see null} if the
     *                                     given HTTP request cannot be
     *                                     converted into an action context.
     */
    function route(IRequest $request): ?ActionContext;
}
