<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Attributes;

use Attribute;

/**
 * Identifies an endpoint for an HTTP request.
 *
 * @package Bogosoft\Http\Mvc\Attributes
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Route
{
    /**
     * Create a new route attribute.
     *
     * @param string  $path A path to associate with a class or class method.
     * @param ?string $name An optional name for the new path.
     */
    function __construct(
        public string $path,
        public ?string $name = null
        )
    {
    }
}
