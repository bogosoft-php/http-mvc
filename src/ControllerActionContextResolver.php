<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Bogosoft\Http\Routing\IAction;
use Psr\Http\Message\ServerRequestInterface as IServerRequest;

/**
 * An action context resolution strategy that will resolve controller actions
 * against the appropriate context.
 *
 * @package Bogosoft\Http\Mvc
 */
class ControllerActionContextResolver implements IActionContextResolver
{
    private IControllerFactory $controllers;
    private ISession $session;
    private IViewFactory $views;

    /**
     * Create a new controller action context resolver.
     *
     * @param IControllerFactory $controllers A strategy for creating
     *                                        controllers.
     * @param IViewFactory       $views       A strategy for creating views.
     * @param ISession|null      $session     A collection of session data.
     */
    function __construct(
        IControllerFactory $controllers,
        IViewFactory $views,
        ISession $session = null
        )
    {
        $this->controllers = $controllers;
        $this->session     = $session ?? new DefaultSession();
        $this->views       = $views;
    }

    /**
     * @inheritDoc
     */
    function resolveContext(ActionContext $context, IServerRequest $request): ?IAction
    {
        if (!($context instanceof ControllerActionContext))
            return null;

        $create = function(string $class) use (&$request): ?Controller
        {
            $controller = $this->controllers->createController($class);

            if (null === $controller)
                return null;

            $controller->setRequest($request);
            $controller->setSession($this->session);
            $controller->setViewFactory($this->views);

            $controller->lock();

            return $controller;
        };

        return new ControllerAction(
            new DelegatedControllerFactory($create),
            $context->controllerClass,
            $context->methodName,
            array_merge($request->getQueryParams(), $context->parameters)
        );
    }
}
