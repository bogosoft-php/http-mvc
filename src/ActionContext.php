<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

/**
 * Represents a collection of information as a context within which an action
 * can be created.
 *
 * @package Bogosoft\Http\Mvc
 */
abstract class ActionContext
{
    /**
     * @var ActionFilterDefinition[] Get or set an array of action filter
     *                               definitions.
     */
    public array $filterDefinitions = [];
}
