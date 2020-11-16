<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

/**
 * A base implementation of the {@see IAction} contract providing common
 * utility functionality.
 *
 * @package Bogosoft\Http\Routing
 */
abstract class ActionBase implements IAction
{
    /**
     * Generate a method not allowed action result.
     *
     * @param  array         $allowed Methods that were acceptable to the
     *                                associated HTTP request.
     * @return IActionResult          An action result.
     */
    protected function notAllowed(array $allowed): IActionResult
    {
        return new MethodNotAllowedResult($allowed);
    }
}
