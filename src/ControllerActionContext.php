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
        return new ControllerActionContext(
            $data['controllerClass'],
            $data['methodName'],
            $data['filterDefinitions']
            );
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
     * Create a new controller action context.
     *
     * @param string $controllerClass   A controller class name.
     * @param string $methodName        The name of an action method defined
     *                                  on the given class.
     * @param array  $filterDefinitions An array of action filter definitions.
     */
    function __construct(
        string $controllerClass,
        string $methodName,
        array $filterDefinitions = []
        )
    {
        $this->controllerClass   = $controllerClass;
        $this->filterDefinitions = $filterDefinitions;
        $this->methodName        = $methodName;
    }
}
