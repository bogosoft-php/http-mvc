<?php

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IRequest;
use ReflectionParameter;

/**
 * Represents a strategy for matching HTTP request data to a class method
 * parameter.
 *
 * @package Bogosoft\Http\Mvc
 */
interface IParameterMatcher
{
    /**
     * Attempt to match data from a given HTTP request to a given class
     * method parameter.
     *
     * @param  ReflectionParameter $rp      A class method parameter.
     * @param  IRequest            $request An HTTP request.
     * @param  mixed               $result  The result of matching the given
     *                                      HTTP data to a given class method
     *                                      parameter.
     * @return bool                         A value indicating whether or not
     *                                      the current matcher succeeded.
     */
    function tryMatch(
        ReflectionParameter $rp,
        IRequest $request,
        mixed &$result
        )
        : bool;
}
