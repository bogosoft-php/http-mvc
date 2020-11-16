<?php

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IServerRequest;

/**
 * A strategy for filtering either the input to, or output from, an action.
 *
 * @package Bogosoft\Http\Routing
 */
interface IActionFilter
{
    /**
     * @param  IServerRequest $request An HTTP request to be filtered.
     * @param  IAction        $action  An action to be called in the event
     *                                 that no filters return an action result.
     * @return mixed                   The result of filtering the given HTTP
     *                                 request.
     */
    function apply(IServerRequest $request, IAction $action);
}
