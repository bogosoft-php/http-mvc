<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Attributes;

use Attribute;

/**
 * Identifies an action that supports the HTTP GET method.
 *
 * @package Bogosoft\Http\Mvc\Attributes
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpGet extends HttpMethod
{
    /**
     * Create a new HTTP GET attribute.
     */
    function __construct()
    {
        parent::__construct('GET');
    }
}
