<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Bogosoft\Http\Routing\IAction;
use Bogosoft\Http\Routing\Results\NotFoundResult;
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
    /** @var mixed */
    private $model;
    private string $name;
    private array $parameters;
    private IViewFactory $views;

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
    function __construct(string $name, $model, array $parameters, IViewFactory $views)
    {
        $this->model      = $model;
        $this->name       = $name;
        $this->parameters = $parameters;
        $this->views      = $views;
    }

    /**
     * @inheritDoc
     */
    function execute(IRequest $request)
    {
        $view = $this->views->createView($this->name, $this->model, $this->parameters);

        return null === $view
            ? new NotFoundResult()
            : new ViewResult($view);
    }
}
