<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Attributes;

use Attribute;

/**
 * Identifies an action that supports the HTTP PUT method.
 *
 * @package Bogosoft\Http\Mvc\Attributes
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpPut extends HttpMethod
{
    /**
     * Create a new HTTP PUT attribute.
     */
    function __construct()
    {
        parent::__construct('PUT');
    }
}
