<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IServerRequest;
use ReflectionParameter;
use ReflectionProperty;

/**
 * A parameter matching strategy which attempts to "hydrate" a value object
 * by populating its fields (properties) with data from a given HTTP request.
 *
 * @package Bogosoft\Http\Mvc
 */
class ValueObjectParameterMatcher implements IParameterMatcher
{
    private IPropertyMatcher $propertyMatcher;

    /**
     * Create a new value object parameter matcher.
     *
     * @param IPropertyMatcher $propertyMatcher A property matcher to be used
     *                                          when hydrating a value object
     *                                          with HTTP request data.
     */
    function __construct(IPropertyMatcher $propertyMatcher)
    {
        $this->propertyMatcher = $propertyMatcher;
    }

    /**
     * @inheritDoc
     */
    function tryMatch(ReflectionParameter $rp, IServerRequest $request, &$result): bool
    {
        if (null === ($rc = $rp->getClass()))
            return false;

        $result = $rc->newInstance();

        $flags = ReflectionProperty::IS_PUBLIC;

        foreach ($rc->getProperties($flags) as $property)
            if ($this->propertyMatcher->tryMatch($property, $request, $value))
                $property->setValue($result, $value);

        return true;
    }
}
