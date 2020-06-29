<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

/**
 * An implementation of the {@see IViewFactory} contract that delegates view
 * creation to a {@see callable} object.
 *
 * The delegate is expected to be of the form:
 *
 * - fn({@see string}, {@see mixed}|{@see null}, {@see array}): {@see IView}|{@see null}
 *
 * This class cannot be inherited.
 *
 * @package Bogosoft\Http\Mvc
 */
final class DelegatedViewFactory implements IViewFactory
{
    /** @var callable */
    private $delegate;

    /**
     * Create a new delegated view factory.
     *
     * @param callable $delegate An invokable object to which view creation
     *                           will be delegated.
     */
    function __construct(callable $delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * @inheritDoc
     */
    function createView(string $name, $model, array $parameters): ?IView
    {
        return ($this->delegate)($name, $model, $parameters);
    }
}
