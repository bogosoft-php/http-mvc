<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IServerRequest;
use ReflectionParameter;

/**
 * A parameter matching strategy that looks for the name of a given parameter
 * in the keys of a given HTTP request's query parameter collection.
 *
 * @package Bogosoft\Http\Mvc
 */
class NamedParameterQueryMatcher implements IParameterMatcher
{
    /**
     * @inheritDoc
     */
    function tryMatch(ReflectionParameter $rp, IServerRequest $request, &$result): bool
    {
        $params = $request->getQueryParams();

        if (!array_key_exists($name = $rp->getName(), $params))
            return false;

        $result = $params[$name];

        return true;
    }
}
