<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Attributes;

use Attribute;

/**
 * Identifies an action that supports specific HTTP request methods.
 *
 * @package Bogosoft\Http\Mvc\Attributes
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpMethod
{
    /** @var string[] */
    public array $methods;

    /**
     * Create a new HTTP method attribute.
     *
     * @param string ...$methods A sequence of HTTP request method names.
     */
    function __construct(string ...$methods)
    {
        $this->methods = $methods;
    }
}
