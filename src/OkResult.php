<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

/**
 * An action result that, when applied to an HTTP response, will set its
 * status code to 200 (OK).
 *
 * @package Bogosoft\Http\Mvc
 */
class OkResult extends StatusCodeResult
{
    /**
     * Create a new OK action result.
     */
    function __construct()
    {
        parent::__construct(HttpStatusCode::OK);
    }
}
