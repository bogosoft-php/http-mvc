<?php

namespace Bogosoft\Http\Mvc;

/**
 * Represents a strategy for filtering either the arguments sent to a view
 * factory or the view created by a view factory.
 *
 * @package Bogosoft\Http\Mvc
 */
interface IViewFilter
{
    /**
     * Apply the current filter to a view creation request.
     *
     * @param  string       $name    The name of a view.
     * @param  mixed|null   $model   An optional model object.
     * @param  array        $params  An array of parameters as key-value pairs.
     * @param  IViewFactory $factory A view factory.
     * @return IView|null            The result of filtering a view creation
     *                               request.
     */
    function apply(
        string $name,
        mixed $model,
        array $params,
        IViewFactory $factory
        )
        : ?IView;
}
