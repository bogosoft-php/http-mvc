<?php

namespace Bogosoft\Http\Mvc;

/**
 * Represents a strategy for creating views.
 *
 * @package Bogosoft\Http\Mvc
 */
interface IViewFactory
{
    /**
     * Have the current factory create a view.
     *
     * @param  string     $name       The name of a view.
     * @param  mixed      $model      A model to be projected through the new
     *                                view.
     * @param  array      $parameters An array of view parameters as key-value
     *                                pairs.
     * @return IView|null             A new view. Implementations SHOULD return
     *                                {@see null} if a view could not be
     *                                created from the given parameters.
     */
    function createView(string $name, mixed $model, array $parameters): ?IView;
}
