<?php

namespace Bogosoft\Http\Mvc;

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
     * @param  string          $class The class name of a controller to be
     *                                created.
     * @return Controller|null        A new controller. Implementations SHOULD
     *                                return {@see null} in the event that a
     *                                controller with the given class name
     *                                cannot be found.
     */
    function createController(string $class): ?Controller;
}
