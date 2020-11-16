<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IServerRequest;

/**
 * An implementation of the {@see IAction} contract that delegates action
 * execution to a {@see callable} object.
 *
 * The delegate is expected to be of the form:
 *
 * - fn({@see IServerRequest}): {@see mixed}
 *
 * This class cannot be inherited.
 *
 * @package Bogosoft\Http\Routing
 */
final class DelegatedAction implements IAction
{
    /** @var callable */
    private $delegate;

    /**
     * Create a new delegated action.
     *
     * @param callable $delegate An invokable object to which action execution
     *                           will be delegated.
     */
    function __construct(callable $delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * @inheritDoc
     */
    function execute(IServerRequest $request)
    {
        return ($this->delegate)($request);
    }
}
