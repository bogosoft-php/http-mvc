<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IRequest;

/**
 * An implementation of the {@see IAction} contract that creates a view result
 * when executed.
 *
 * If a view cannot be created from the internal view factory, a
 * {@see NotFoundResult} will be returned instead.
 *
 * @package Bogosoft\Http\Mvc
 */
class ViewAction implements IAction
{
    /**
     * Create a new view action.
     *
     * @param string       $name       The name of a view to create.
     * @param mixed        $model      A model to be projected through the
     *                                 created view.
     * @param array        $parameters A collection or parameters as key-value
     *                                 pairs.
     * @param IViewFactory $views      A strategy for creating views.
     */
    function __construct(
        private string $name,
        private mixed $model,
        private array $parameters,
        private IViewFactory $views
        )
    {
    }

    /**
     * @inheritDoc
     */
    function execute(IRequest $request): NotFoundResult|ViewResult
    {
        $view = $this->views->createView($this->name, $this->model, $this->parameters);

        return null === $view
            ? new NotFoundResult()
            : new ViewResult($view);
    }
}
