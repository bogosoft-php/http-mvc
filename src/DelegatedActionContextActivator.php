<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Bogosoft\Http\Routing\IAction;
use Psr\Http\Message\ServerRequestInterface as IRequest;

/**
 * An implementation of the {@see IActionContextActivator} that delegates
 * action context activation to a {@see callable} object.
 *
 * This class cannot be inherited.
 *
 * @package Bogosoft\Http\Mvc
 */
class DelegatedActionContextActivator implements IActionContextActivator
{
    /** @var callable */
    private $delegate;

    /**
     * Create a new delegated action context activator.
     *
     * @param callable $delegate An invokable object to which action context
     *                           activation will be delegated.
     */
    function __construct(callable $delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * @inheritDoc
     */
    function activateContext(ActionContext $context, IRequest $request): ?IAction
    {
        return ($this->delegate)($context, $request);
    }
}
