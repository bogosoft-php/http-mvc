<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IServerRequest;
use ReflectionProperty;

/**
 * A named property matching strategy that looks for the name of a given
 * property in the keys of an HTTP request's query parameter collection.
 *
 * @package Bogosoft\Http\Mvc
 */
class NamedPropertyQueryMatcher implements IPropertyMatcher
{
    /**
     * @inheritDoc
     */
    function tryMatch(ReflectionProperty $rp, IServerRequest $request, &$result): bool
    {
        $params = $request->getQueryParams();

        if (!array_key_exists($name = $rp->getName(), $params))
            return false;

        $result = $params[$name];

        return true;
    }
}
