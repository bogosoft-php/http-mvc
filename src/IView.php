<?php

namespace Bogosoft\Http\Mvc;

/**
 * Represents a strategy for rendering content.
 *
 * @package Bogosoft\Http\Mvc
 */
interface IView
{
    /**
     * Render the current view to a given target resource.
     *
     * @param resource $target A target to which the current view is to be
     *                         rendered.
     */
    function render($target): void;
}
