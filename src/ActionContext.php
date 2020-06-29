<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

/**
 * Represents a collection of information as a context within which an action
 * can be created.
 *
 * @package Bogosoft\Http\Mvc
 */
class ActionContext
{
    /**
     * @var string Get or set the name of a controller class.
     */
    public string $controllerClass;

    /**
     * @var ActionFilterDefinition[] Get or set an array of action filter
     *                               definitions.
     */
    public iterable $filterDefinitions = [];

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
