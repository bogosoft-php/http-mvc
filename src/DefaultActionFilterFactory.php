<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Bogosoft\Http\Routing\IActionFilter;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

/**
 * A default implementation of the {@see IActionFilterFactory} contract.
 *
 * This implementation will use a {@see ReflectionClass} object in order to
 * create a defined action filter.
 *
 * @package Bogosoft\Http\Mvc
 */
final class DefaultActionFilterFactory implements IActionFilterFactory
{
    /**
     * @inheritDoc
     *
     * @throws ReflectionException in the event that the defined class cannot
     *                             be reflected upon.
     */
    function createActionFilter(ActionFilterDefinition $definition): IActionFilter
    {
        $rc = new ReflectionClass($definition->class);

        $object = $rc->newInstanceArgs($definition->constructorArgs);

        if ($object instanceof IActionFilter)
            return $object;

        $message = sprintf('Expected instance of: \'%s\'.', IActionFilter::class);

        throw new RuntimeException($message);
    }
}
