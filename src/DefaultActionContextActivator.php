<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Bogosoft\Http\Routing\Actions\MethodNotAllowedAction;
use Bogosoft\Http\Routing\IAction;
use Psr\Http\Message\ServerRequestInterface as IServerRequest;

/**
 * A default action context activation strategy that will activate the
 * following derived action context types:
 *
 * - {@see ControllerActionContext}
 * - {@see MethodNotAllowedActionContext}
 * - {@see ViewActionContext}
 *
 * @package Bogosoft\Http\Mvc
 */
class DefaultActionContextActivator implements IActionContextActivator
{
    private IControllerFactory $controllers;
    private IParameterMatcher $matcher;
    private ISession $session;
    private IViewFactory $views;

    /**
     * Create a new default action context activator.
     *
     * @param IControllerFactory $controllers A strategy for creating
     *                                        controllers.
     * @param IParameterMatcher  $matcher     A strategy for matching HTTP
     *                                        request data to class method
     *                                        parameters.
     * @param IViewFactory       $views       A strategy for creating views.
     * @param ISession|null      $session     A session.
     */
    function __construct(
        IControllerFactory $controllers,
        IParameterMatcher $matcher,
        IViewFactory $views,
        ISession $session = null
        )
    {
        $this->controllers = $controllers;
        $this->matcher     = $matcher;
        $this->session     = $session ?? new DefaultSession();
        $this->views       = $views;
    }

    /**
     * @inheritDoc
     */
    function activateContext(ActionContext $context, IServerRequest $request): ?IAction
    {
        if ($context instanceof MethodNotAllowedActionContext)
            return new MethodNotAllowedAction($context->getAllowedMethods());
        elseif ($context instanceof ControllerActionContext)
            return $this->handleControllerActionContext(
                $context,
                $request,
                $this->controllers,
                $this->matcher
                );
        elseif ($context instanceof ViewActionContext)
            return $this->handleViewActionContext($context, $request, $this->views);
        else
            return null;
    }

    /**
     * Extract model data for a view from an HTTP request.
     *
     * By default, this method simply returns {@see null}.
     *
     * @param  IServerRequest $request An HTTP request from which a model
     *                                 will be extracted.
     * @return mixed|null              An extracted model.
     */
    protected function extractViewModel(IServerRequest $request)
    {
        return null;
    }

    /**
     * Extract parameters for a view from an HTTP request.
     *
     * By default, this method simply returns the result of calling the
     * {@see IServerRequest::getQueryParams()} method.
     *
     * @param  IServerRequest $request An HTTP request from which view
     *                                 parameters will be extracted.
     * @return array                   An array of view parameters.
     */
    protected function extractViewParameters(IServerRequest $request): array
    {
        return $request->getQueryParams();
    }

    /**
     * Returns a decorated controller factory that sets the HTTP request,
     * session and view factory on a controller created by the controller
     * factory passed into to the current object's constructor.
     *
     * @param  IControllerFactory $controllers The controller factory used to
     *                                         construct the current action
     *                                         context activator.
     * @return IControllerFactory
     */
    protected function getLockingControllerFactory(
        IControllerFactory $controllers
        )
        : IControllerFactory
    {
        $create = function(string $class, IServerRequest $request) use (&$controllers): ?Controller
        {
            $controller = $controllers->createController($class, $request);

            if (null === $controller)
                return null;

            if (!$controller->isLocked())
            {
                $controller->setRequest($request);
                $controller->setSession($this->session);
                $controller->setViewFactory($this->views);

                $controller->lock();
            }

            return $controller;
        };

        return new DelegatedControllerFactory($create);
    }

    /**
     * Activate a controller action context, generating a corresponding
     * controller action.
     *
     * @param  ControllerActionContext $context     A controller action
     *                                              context.
     * @param  IServerRequest          $request     An HTTP request.
     * @param  IControllerFactory      $controllers A strategy for creating
     *                                              controllers.
     * @param  IParameterMatcher       $matcher     A strategy for matching
     *                                              HTTP request data to class
     *                                              method parameters.
     * @return IAction                              A controller action.
     */
    protected function handleControllerActionContext(
        ControllerActionContext $context,
        IServerRequest $request,
        IControllerFactory $controllers,
        IParameterMatcher $matcher
        )
        : IAction
    {
        return new ControllerAction(
            $this->getLockingControllerFactory($controllers),
            $context->controllerClass,
            $context->methodName,
            $matcher
        );
    }

    /**
     * Activate a view context action, generating a corresponding view action.
     *
     * @param  ViewActionContext $context A view action context.
     * @param  IServerRequest    $request An HTTP request.
     * @param  IViewFactory      $views   A strategy for creating views.
     * @return IAction
     */
    protected function handleViewActionContext(
        ViewActionContext $context,
        IServerRequest $request,
        IViewFactory $views
        )
        : IAction
    {
        return new ViewAction(
            $context->viewName,
            $this->extractViewModel($request),
            $this->extractViewParameters($request),
            $views
        );
    }
}
