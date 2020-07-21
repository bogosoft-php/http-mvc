<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

/**
 * An action context that describes a controller class and action method to
 * which an HTTP request will be routed.
 *
 * @package Bogosoft\Http\Mvc
 */
class ControllerActionContext extends ActionContext
{
    static function __set_state($data)
    {
        $context = new ControllerActionContext();

        $context->controllerClass   = $data['controllerClass'];
        $context->filterDefinitions = $data['filterDefinitions'];
        $context->methodName        = $data['methodName'];

        return $context;
    }

    /**
     * @var string Get or set the name of a controller class.
     */
    public string $controllerClass;

    /**
     * @var string Get or set the name of a method on a controller.
     */
    public string $methodName;

    /**
     * @var array Get or set an array of parameters derived from an associated
     *            HTTP request.
     */
    public array $parameters = [];
}
