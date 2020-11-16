<?php

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ResponseInterface as IResponse;

/**
 * Represents the result of executing an action against an HTTP request.
 *
 * @package Bogosoft\Http\Routing
 */
interface IActionResult
{
    /**
     * Apply the current action result to a given HTTP response.
     *
     * @param  IResponse $response An HTTP response to which the current
     *                             action is to be applied.
     * @return IResponse           An HTTP response to which the current
     *                             action has been applied.
     */
    function apply(IResponse $response): IResponse;
}
