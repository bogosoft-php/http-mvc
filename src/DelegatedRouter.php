<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IRequest;

/**
 * An implementation of the {@see IRouter} contract that delegates action
 * context generation to a {@see callable} object.
 *
 * This class cannot be inherited.
 *
 * @package Bogosoft\Http\Mvc
 */
final class DelegatedRouter implements IRouter
{
    /** @var callable */
    private $delegate;

    /**
     * Create a new delegated router.
     *
     * @param callable $delegate An invokable object to which action context
     *                           generation will be delegated.
     */
    function __construct(callable $delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * @inheritDoc
     */
    function route(IRequest &$request): ?ActionContext
    {
        return ($this->delegate)($request);
    }
}
