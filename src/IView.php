<?php

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\StreamInterface as IStream;

/**
 * Represents a strategy for rendering content.
 *
 * @package Bogosoft\Http\Mvc
 */
interface IView
{
    /**
     * Render the current view to a given target stream.
     *
     * @param IStream $target A target stream to which the current view is to
     *                        be rendered.
     */
    function render(IStream $target): void;
}
