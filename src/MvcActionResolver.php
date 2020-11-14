<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Bogosoft\Http\Routing\FilteredAction;
use Bogosoft\Http\Routing\IAction;
use Bogosoft\Http\Routing\IActionResolver;
use Psr\Http\Message\ServerRequestInterface as IRequest;

/**
 * An MVC-based action resolution strategy.
 *
 * This action resolver attempts to locate an {@see ActionContext} associated
 * with a given HTTP request, after which, an action will be created from the
 * context.
 *
 * @package Bogosoft\Http\Mvc
 */
abstract class MvcActionResolver implements IActionResolver
{
    private IActionContextActivator $activator;
    private IActionFilterFactory $filters;

    /**
     * Create a new MVC-based action resolver.
     *
     * @param IActionContextActivator $activator A strategy for activating
     *                                           action contexts.
     * @param IActionFilterFactory    $filters   A strategy for creating
     *                                           action filters.
     */
    protected function __construct(
        IActionContextActivator $activator,
        IActionFilterFactory $filters
        )
    {
        $this->activator = $activator;
        $this->filters   = $filters;
    }

    /**
     * When overridden in a derived class, applies a sequence of global
     * action filters AFTER any context-specific action filters.
     *
     * By default, this method returns an empty sequence.
     *
     * @return iterable A sequence of {@see ActionFilterDefinition} objects.
     */
    protected function appendGlobalActionFilters(): iterable
    {
        /** @noinspection PhpDeprecationInspection */
        return $this->getGlobalActionFilters();
    }

    /**
     * When overridden in a derived class, generates an action context against
     * a given HTTP request.
     *
     * @param  IRequest           $request An HTTP request against which an
     *                                     action context will be generated.
     * @return ActionContext|null          An action context. Implementations
     *                                     SHOULD return {@see null} if an
     *                                     action context could not be
     *                                     generated against the given HTTP
     *                                     request.
     */
    protected abstract function getActionContext(IRequest $request): ?ActionContext;

    /**
     * @param  ActionContext $context An action context.
     * @return iterable               An {@see ActionFilterDefinition}
     *                                sequence.
     */
    private function getActionFilters(ActionContext $context): iterable
    {
        yield from $this->prependGlobalActionFilters();

        foreach ($context->filterDefinitions as $definition)
            yield $this->filters->createActionFilter($definition);

        yield from $this->appendGlobalActionFilters();
    }

    /**
     * When overridden in a derived class, gets additional, global action
     * filters that will be applied after context-specific action filters.
     *
     * By default, this method returns nothing.
     *
     * @return iterable A sequence of {@see ActionFilterDefinition} objects.
     *
     * @deprecated Use {@see MvcActionResolver::appendGlobalActionFilters()}
     *             instead.
     */
    protected function getGlobalActionFilters(): iterable
    {
        yield from [];
    }

    /**
     * When overridden in a derived class, applies a sequence of global
     * action filters BEFORE any context-specific action filters.
     *
     * By default, this method returns an empty sequence.
     *
     * @return iterable A sequence of {@see ActionFilterDefinition} objects.
     */
    protected function prependGlobalActionFilters(): iterable
    {
        yield from [];
    }

    /**
     * @inheritDoc
     */
    function resolve(IRequest $request): ?IAction
    {
        $context = $this->getActionContext($request);

        if (null === $context)
            return null;

        $action = $this->activator->activateContext($context, $request);

        if (null === $action)
            throw new CannotActivateActionContextException($request);

        if (count($context->filterDefinitions) > 0)
        {
            $filters = $this->getActionFilters($context);

            $action = new FilteredAction($action, $filters);
        }

        return $action;
    }
}
