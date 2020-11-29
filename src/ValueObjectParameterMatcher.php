<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IRequest;
use ReflectionClass;
use ReflectionNamedType;
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
    private static function getClass(ReflectionParameter $rp): ?ReflectionClass
    {
        return null !== ($rt = $rp->getType())
            && $rt instanceof ReflectionNamedType
            ? new ReflectionClass($rt->getName())
            : null;
    }

    /**
     * Create a new value object parameter matcher.
     *
     * @param IPropertyMatcher $propertyMatcher A property matcher to be used
     *                                          when hydrating a value object
     *                                          with HTTP request data.
     */
    function __construct(private IPropertyMatcher $propertyMatcher)
    {
    }

    /**
     * @inheritDoc
     */
    function tryMatch(ReflectionParameter $rp, IRequest $request, &$result): bool
    {
        if (null === ($rc = self::getClass($rp)))
            return false;

        $result = $rc->newInstance();

        $flags = ReflectionProperty::IS_PUBLIC;

        foreach ($rc->getProperties($flags) as $property)
            if ($this->propertyMatcher->tryMatch($property, $request, $value))
                $property->setValue($result, $value);

        return true;
    }
}
