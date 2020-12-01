<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Attributes;

use Attribute;

/**
 * Identifies an action that supports the HTTP POST method.
 *
 * @package Bogosoft\Http\Mvc\Attributes
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpPost extends HttpMethod
{
    /**
     * Create a new HTTP POST attribute.
     */
    function __construct()
    {
        parent::__construct('POST');
    }
}
