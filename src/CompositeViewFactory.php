<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

/**
 * A composite implementation of the {@see IViewFactory} contract that allows
 * multiple view factories to behave as if they were a single view factory.
 *
 * During creation, each view factory will be called in sequence. The first
 * factory to return a non-null result wins, short-circuiting iteration and
 * immediately returning the new view.
 *
 * @package Bogosoft\Http\Mvc
 */
final class CompositeViewFactory implements IViewFactory
{
    private iterable $factories;

    /**
     * Create a new composite view factory.
     *
     * @param iterable $factories A sequence of factories from which the new
     *                            composite view factory will be composed.
     */
    function __construct(iterable $factories)
    {
        $this->factories = $factories;
    }

    /**
     * @inheritDoc
     */
    function createView(string $name, $model, array $parameters): ?IView
    {
        /** @var IView $view */
        $view = null;

        /** @var IViewFactory $factory */
        foreach ($this->factories as $factory)
            if (null !== ($view = $factory->createView($name, $model, $parameters)))
                break;

        return $view;
    }
}
