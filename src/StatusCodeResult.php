<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ResponseInterface as IResponse;

/**
 * A generic action result that, when applied to an HTTP response, sets
 * a status code.
 *
 * @package Bogosoft\Http\Routing\Results
 */
class StatusCodeResult implements IActionResult
{
    private int $code;

    /**
     * Create a new status code action result.
     *
     * @param int $code A status code to be applied to an HTTP response.
     */
    function __construct(int $code)
    {
        $this->code = $code;
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
