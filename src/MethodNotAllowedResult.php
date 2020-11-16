<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ResponseInterface as IResponse;

/**
 * An action result that will modify an HTTP response to indicate that the
 * request HTTP method was not allowed.
 *
 * @package Bogosoft\Http\Mvc
 */
class MethodNotAllowedResult extends StatusCodeResult
{
    private array $allowed;

    /**
     * Create a new method not allowed action result.
     *
     * @param array $allowed An array of acceptable methods.
     */
    function __construct(array $allowed)
    {
        parent::__construct(405);

        $this->allowed = $allowed;
    }

    /**
     * @inheritDoc
     */
    function apply(IResponse $response): IResponse
    {
        $methods = implode(' ', $this->allowed);

        return parent::apply($response)->withHeader('Allow', $methods);
    }

    /**
     * Get an array of names corresponding to allowed HTTP request methods.
     *
     * @return array An array of HTTP request method names.
     */
    function getAllowedMethods(): array
    {
        return $this->allowed;
    }
}
