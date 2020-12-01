<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Attributes;

use Attribute;

/**
 * Indicates that an action supports the HTTP HEAD method.
 *
 * @package Bogosoft\Http\Mvc\Attributes
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpHead extends HttpMethod
{
    /**
     * Create a new HTTP HEAD method attribute.
     */
    function __construct()
    {
        parent::__construct('HEAD');
    }
}
