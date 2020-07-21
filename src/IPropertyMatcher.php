<?php

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IServerRequest;
use ReflectionProperty;

/**
 * Represents a strategy for matching HTTP request data to a class property.
 *
 * @package Bogosoft\Http\Mvc
 */
interface IPropertyMatcher
{
    /**
     * Attempt to match data from a given HTTP request to a given class
     * property.
     *
     * @param  ReflectionProperty $rp      A class property.
     * @param  IServerRequest     $request An HTTP request.
     * @param  mixed              $result  The result of matching the given
     *                                     HTTP data to a class property.
     * @return bool                        A value indicating whether or not
     *                                     the current matcher succeeded.
     */
    function tryMatch(
        ReflectionProperty $rp,
        IServerRequest $request,
        &$result
        )
        : bool;
}
