<?php

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IRequest;

/**
 * Represents a strategy for creating a controller.
 *
 * @package Bogosoft\Http\Mvc
 */
interface IControllerFactory
{
    /**
     * Create a new controller.
     *
     * @param  string          $class   The class name of a controller to be
     *                                  created.
     * @param  IRequest        $request The HTTP request associated with the
     *                                  controller construction event.
     * @return Controller|null          A new controller. Implementations
     *                                  SHOULD return {@see null} in the event
     *                                  that a controller with the given class
     *                                  name cannot be found.
     */
    function createController(string $class, IRequest $request): ?Controller;
}
