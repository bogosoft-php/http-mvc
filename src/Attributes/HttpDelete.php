<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Attributes;

use Attribute;

/**
 * Identifies an action that supports the HTTP DELETE method.
 *
 * @package Bogosoft\Http\Mvc\Attributes
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpDelete extends HttpMethod
{
    /**
     * Create a new HTTP delete method attribute.
     */
    function __construct()
    {
        parent::__construct('DELETE');
    }
}
