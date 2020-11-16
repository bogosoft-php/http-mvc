<?php

namespace Bogosoft\Http\Mvc;

/**
 * Represents a strategy for creating action filters.
 *
 * @package Bogosoft\Http\Mvc
 */
interface IActionFilterFactory
{
    /**
     * Create an action filter from a given class name.
     *
     * @param  ActionFilterDefinition An action filter definition.
     * @return IActionFilter          A new action filter.
     */
    function createActionFilter(ActionFilterDefinition $definition): IActionFilter;
}
