<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ResponseInterface as IResponse;

/**
 * A generic action result that, when applied to an HTTP response, sets
 * a status code.
 *
 * @package Bogosoft\Http\Mvc
 */
class StatusCodeResult implements IActionResult
{
    /**
     * Create a new status code action result.
     *
     * @param int $code A status code to be applied to an HTTP response.
     */
    function __construct(private int $code)
    {
    }

    /**
     * @inheritDoc
     */
    function apply(IResponse $response): IResponse
    {
        return $response->withStatus($this->code);
    }

    /**
     * Get the HTTP status code assigned to the current action result.
     *
     * @return int An integer HTTP status code.
     */
    function getStatusCode(): int
    {
        return $this->code;
    }
}
