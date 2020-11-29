<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IRequest;

/**
 * An action that, when executed, will generate a method not allowed result.
 *
 * @package Bogosoft\Http\Mvc
 */
class MethodNotAllowedAction implements IAction
{
    /**
     * Create a new method not allowed action.
     *
     * @param string[] $allowed An array of allowed methods.
     */
    function __construct(private array $allowed)
    {
    }

    /**
     * @inheritDoc
     */
    function execute(IRequest $request): MethodNotAllowedResult
    {
        return new MethodNotAllowedResult($this->allowed);
    }
}
