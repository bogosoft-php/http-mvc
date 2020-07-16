<?php

namespace Bogosoft\Http\Mvc;

use Bogosoft\Http\Routing\IAction;
use Psr\Http\Message\ServerRequestInterface as IServerRequest;

/**
 * Represents a strategy for resolving an action context into an executable
 * action.
 *
 * @package Bogosoft\Http\Mvc
 */
interface IActionContextResolver
{
    /**
     * Attempt to resolve an action from a given action context and HTTP
     * request.
     *
     * @param  ActionContext  $context An action context.
     * @param  IServerRequest $request An HTTP request.
     * @return IAction|null            The result of attempting to resolve an
     *                                 action from the given action context
     *                                 HTTP request. Implementors SHOULD
     *                                 return {@see null} if the current
     *                                 resolver cannot resolve an action from
     *                                 the given arguments.
     */
    function resolveContext(ActionContext $context, IServerRequest $request): ?IAction;
}
