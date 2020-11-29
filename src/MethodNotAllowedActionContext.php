<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

/**
 * An action context that represents an HTTP request using a method
 * that was not allowed.
 *
 * @package Bogosoft\Http\Mvc
 */
final class MethodNotAllowedActionContext extends ActionContext
{
    /**
     * Create a new method-not-allowed action context.
     *
     * @param string[] $allowedMethods An array of strings representing the
     *                                 names of allowed HTTP methods.
     */
    function __construct(private array $allowedMethods)
    {
    }

    /**
     * @return string[] Get an array of string representing the names of
     *                  allowed HTTP methods.
     */
    function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }
}
