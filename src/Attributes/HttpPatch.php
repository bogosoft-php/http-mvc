<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Attributes;

use Attribute;

/**
 * Indicates that an action supports the HTTP PATCH method.
 *
 * @package Bogosoft\Http\Mvc\Attributes
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpPatch extends HttpMethod
{
    /**
     * Create a new HTTP PATCH method attribute.
     */
    function __construct()
    {
        parent::__construct('PATCH');
    }
}
