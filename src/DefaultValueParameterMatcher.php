<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IRequest;
use ReflectionParameter;

/**
 * A parameter matching strategy that matches against the default value of a
 * given class method parameter.
 *
 * This class cannot be inherited.
 *
 * @package Bogosoft\Http\Mvc
 */
final class DefaultValueParameterMatcher implements IParameterMatcher
{
    /**
     * @inheritDoc
     */
    function tryMatch(ReflectionParameter $rp, IRequest $request, &$result): bool
    {
        if (!$rp->isDefaultValueAvailable())
            return false;

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $rp->getDefaultValue();

        return true;
    }
}
