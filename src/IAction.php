<?php

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IRequest;

/**
 * Represents an action executable against an HTTP request.
 *
 * @package Bogosoft\Http\Mvc
 */
interface IAction
{
    /**
     * Execute the current action against an HTTP request.
     *
     * @param  IRequest $request An HTTP request against which the current
     *                           action will be executed.
     * @return mixed             The result of executing the current action
     *                           against the given HTTP request.
     */
    function execute(IRequest $request);
}
