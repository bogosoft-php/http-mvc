<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ResponseInterface as IResponse;

/**
 * An implementation of the {@see IActionResult} contract that delegates
 * the application of the result of an action to a {@see callable} object.
 *
 * The delegate is expected to be of the form:
 *
 * - fn({@see IResponse}): {@see IResponse}
 *
 * This class cannot be inherited.
 *
 * @package Bogosoft\Http\Mvc
 */
final class DelegatedActionResult implements IActionResult
{
    /** @var callable */
    private $delegate;

    /**
     * Create a new delegated action result.
     *
     * @param callable $delegate An invokable object to which the application
     *                           of the result of an action to an HTTP
     *                           response will be delegated.
     */
    function __construct(callable $delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * @inheritDoc
     */
    function apply(IResponse $response): IResponse
    {
        return ($this->delegate)($response);
    }
}
