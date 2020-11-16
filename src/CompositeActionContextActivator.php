<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IRequest;

/**
 * A composite implementation of the {@see IActionContextActivator} contract
 * that allows multiple action context activators to behave as if they were a
 * single action context activator.
 *
 * @package Bogosoft\Http\Mvc
 */
final class CompositeActionContextActivator implements IActionContextActivator
{
    /** @var IActionContextActivator[]  */
    private array $activators;

    /**
     * Create a new composite action context activator.
     *
     * @param IActionContextActivator ...$activators Zero or more action
     *                                               context activators from
     *                                               which to form the
     *                                               composite.
     */
    function __construct(IActionContextActivator ...$activators)
    {
        $this->activators = $activators;
    }

    /**
     * @inheritDoc
     */
    function activateContext(ActionContext $context, IRequest $request): ?IAction
    {
        foreach ($this->activators as $activator)
            if (null !== ($action = $activator->activateContext($context, $request)))
                return $action;

        return null;
    }
}
