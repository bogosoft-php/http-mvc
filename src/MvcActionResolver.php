<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Bogosoft\Http\Mvc\MvcActionResolverParameters as Parameters;
use Bogosoft\Http\Routing\Actions\MethodNotAllowedAction;
use Bogosoft\Http\Routing\FilteredAction;
use Bogosoft\Http\Routing\IAction;
use Bogosoft\Http\Routing\IActionResolver;
use Psr\Http\Message\ServerRequestInterface as IServerRequest;
use ReflectionException;
use RuntimeException;

/**
 * An implementation of the {@see IActionResolver} contract that attempts
 * to resolve an action as a method on a {@see Controller} from a given
 * HTTP request.
 *
 * @package Bogosoft\Http\Mvc
 */
class MvcActionResolver implements IActionResolver
{
    private Parameters $parameters;

    /**
     * Create a new MVC action resolver.
     *
     * @param Parameters $parameters A collection of parameters by which the
     *                               behavior of the new MVC action resolver
     *                               can be influenced.
     */
    function __construct(Parameters $parameters)
    {
        $this->parameters = $parameters;
    }

    private function createController(string $class): ?Controller
    {
        return $this->parameters->controllers->createController($class);
    }

    private function dispatch(IServerRequest $request): RouteInfo
    {
        return $this->parameters->dispatcher->dispatch($request);
    }

    private function getActionFilters(array $filterDefinitions): iterable
    {
        $factory = $this->parameters->filters;

        foreach ($filterDefinitions as $definition)
            yield $factory->createActionFilter($definition);
    }

    /**
     * @inheritDoc
     */
    function resolve(IServerRequest $request): ?IAction
    {
        #
        # Dispatch the given HTTP request.
        #
        $allowed = [];

        $info = $this->dispatch($request);

        if (IDispatcher::STATUS_NOT_FOUND === $info->status)
            return null;
        elseif (IDispatcher::STATUS_METHOD_NOT_ALLOWED === $info->status)
            return new MethodNotAllowedAction($allowed);

        if (null === ($context = $info->context))
        {
            $message = sprintf(
                'Null context received for path: \'%s\'.',
                $request->getUri()->getPath()
            );

            throw new RuntimeException($message);
        }

        $factory = new class($this->parameters, $request) implements IControllerFactory
        {
            private Parameters $params;
            private IServerRequest $request;

            function __construct(Parameters $params, IServerRequest $request)
            {
                $this->params  = $params;
                $this->request = $request;
            }

            function createController(string $class): ?Controller
            {
                $controller = $this->params->controllers->createController($class);

                $controller->setRequest($this->request);
                $controller->setSession($this->params->session ?? new DefaultSession());
                $controller->setViewFactory($this->params->views);

                return $controller;
            }
        };

        $parameters = array_merge($request->getQueryParams(), $context->parameters);

        $action = new ControllerAction(
            $factory,
            $context->controllerClass,
            $context->methodName,
            $parameters
            );

        if (count($context->filterDefinitions) > 0)
        {
            $action = new FilteredAction($action);

            $filters = $this->getActionFilters($context->filterDefinitions);

            $action->appendFilters($filters);
        }

        return $action;
    }
}
