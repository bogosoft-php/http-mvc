<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Bogosoft\Http\Routing\IAction;
use Psr\Http\Message\ServerRequestInterface as IServerRequest;

/**
 * An action context resolution strategy that will resolve view actions
 * against an appropriate action context.
 *
 * @package Bogosoft\Http\Mvc
 */
class ViewActionContextResolver implements IActionContextResolver
{
    private IViewFactory $views;

    /**
     * Create a new view action context resolver.
     *
     * @param IViewFactory $views A strategy for creating views.
     */
    function __construct(IViewFactory $views)
    {
        $this->views = $views;
    }

    /**
     * When overridden in a derived class, extracts model data from a given
     * HTTP request.
     *
     * Be default, this method simply returns {@see null}.
     *
     * @param  IServerRequest $request An HTTP request from which model data
     *                                 will be extracted.
     * @return mixed|null              The result of extracting model data
     *                                 from the given HTTP request.
     */
    protected function extractViewModel(IServerRequest $request)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    function resolveContext(ActionContext $context, IServerRequest $request): ?IAction
    {
        if (!($context instanceof ViewActionContext))
            return null;

        return new ViewAction(
            $context->viewName,
            $this->extractViewModel($request),
            array_merge($request->getQueryParams(), $context->parameters),
            $this->views
            );
    }
}
