<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

/**
 * An implementation of the {@see IActionFilterFactory} contract that
 * delegates the creation of action filters to a {@see callable} object.
 *
 * The delegate is expected to be of the form:
 *
 * - fn({@see ActionFilterDefinition}): {@see IActionFilter}
 *
 * @package Bogosoft\Http\Mvc
 */
final class DelegatedActionFilterFactory implements IActionFilterFactory
{
    /** @var callable */
    private $delegate;

    /**
     * Create a new delegated action filter factory.
     *
     * @param callable $delegate An invokable object to which action filter
     *                           creation will be delegated.
     */
    function __construct(callable $delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * @inheritDoc
     */
    function createActionFilter(ActionFilterDefinition $definition): IActionFilter
    {
        return ($this->delegate)($definition);
    }
}
