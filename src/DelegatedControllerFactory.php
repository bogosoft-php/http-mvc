<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IServerRequest;

/**
 * An implementation of the {@see IControllerFactory} that delegates
 * controller creation to a {@see callable} object.
 *
 * The delegate is expected to be of the form:
 *
 * - fn({@see string}, {@see IServerRequest}): {@see Controller}|{@see null}
 *
 * This class cannot be inherited.
 *
 * @package Bogosoft\Http\Mvc
 */
final class DelegatedControllerFactory implements IControllerFactory
{
    /** @var callable */
    private $delegate;

    /**
     * Create a new delegated controller factory.
     *
     * @param callable $delegate An invokable object to which controller
     *                           creation will be delegated.
     */
    function __construct(callable $delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * @inheritDoc
     */
    function createController(string $class, IServerRequest $request): ?Controller
    {
        return ($this->delegate)($class, $request);
    }
}
