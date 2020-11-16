<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use RuntimeException;

/**
 * An exception intended to be thrown when an attempt to create a view failed
 * because the view could not be found by a given name.
 *
 * @package Bogosoft\Http\Mvc
 */
class ViewNotFoundException extends RuntimeException
{
    /**
     * Create a new view-not-found exception.
     *
     * @param string $name The name of a view that could not be resolved.
     */
    function __construct(string $name)
    {
        $message = "View not found, '$name'.";

        parent::__construct($message);
    }
}
