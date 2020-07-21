<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

/**
 * An action context that describes a view to be directly rendered against
 * an HTTP request.
 *
 * @package Bogosoft\Http\Mvc
 */
class ViewActionContext extends ActionContext
{
    static function __set_state($data)
    {
        $context = new ViewActionContext();

        $context->filterDefinitions = $data['filterDefinitions'];
        $context->parameters        = $data['parameters'];
        $context->viewName          = $data['viewName'];

        return $context;
    }

    /**
     * @var string Get or set the name of a view to be rendered.
     */
    public string $viewName;
}
