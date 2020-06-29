<?php

namespace Bogosoft\Http\Mvc;

/**
 * Represents the definition of an action filter.
 *
 * @package Bogosoft\Http\Mvc
 */
class ActionFilterDefinition
{
    /**
     * @var string Get or set the name of the class of defined action filter.
     */
    public string $class;

    /**
     * @var array Get or set an array of arguments to be passed into the
     *            constructor of an action filter class.
     */
    public array $constructorArgs = [];
}
