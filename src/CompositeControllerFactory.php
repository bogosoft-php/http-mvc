<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IServerRequest;

/**
 * A composite implementation of the {@see IControllerFactory} that allows
 * multiple controller factories to behave as if they were a single controller
 * factory.
 *
 * During creation, each controller factory will be called in sequence. The
 * first factory to return a non-null value wins, short-circuiting iteration
 * and immediately returning the new controller.
 *
 * This class cannot be inherited.
 *
 * @package Bogosoft\Http\Mvc
 */
final class CompositeControllerFactory implements IControllerFactory
{
    private iterable $factories;

    /**
     * Create a new composite controller factory.
     *
     * @param iterable $factories A sequence of factories from which to form
     *                            the composite.
     */
    function __construct(iterable $factories)
    {
        $this->factories = $factories;
    }

    /**
     * @inheritDoc
     */
    function createController(string $class, IServerRequest $request): ?Controller
    {
        /** @var Controller $controller */
        $controller = null;

        /** @var IControllerFactory $factory */
        foreach ($this->factories as $factory)
            if (null !== ($controller = $factory->createController($class, $request)))
                break;

        return $controller;
    }
}
