<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Bogosoft\Http\Routing\Actions\MethodNotAllowedAction;
use Bogosoft\Http\Routing\Actions\NotFoundAction;
use Bogosoft\Http\Routing\FilteredAction;
use Bogosoft\Http\Routing\IAction;
use Bogosoft\Http\Routing\IActionResolver;
use Bogosoft\Http\Routing\Results\NotFoundResult;
use Psr\Http\Message\ServerRequestInterface as IServerRequest;
use RuntimeException;

/**
 * An implementation of the {@see IActionResolver} contract that relies on
 * a dispatcher to generate route information against an incoming HTTP
 * request. An appropriate action will be generated against the resulting
 * route information.
 *
 * @package Bogosoft\Http\Mvc
 */
class DispatcherActionResolver implements IActionResolver
{
    private IDispatcher $dispatcher;
    private IActionFilterFactory $filters;
    private iterable $resolvers;

    /**
     * Create a new dispatcher action resolver.
     *
     * @param IDispatcher               $dispatcher A strategy for generating
     *                                              route information against
     *                                              an incoming HTTP request.
     * @param iterable                  $resolvers  A sequence of
     *                                              {@see IActionContextResolver}
     *                                              objects.
     * @param IActionFilterFactory|null $filters    A strategy for creating
     *                                              action filters.
     */
    function __construct(
        IDispatcher $dispatcher,
        iterable $resolvers,
        IActionFilterFactory $filters = null
        )
    {
        $this->dispatcher = $dispatcher;
        $this->filters    = $filters ?? new DefaultActionFilterFactory();
        $this->resolvers  = $resolvers;
    }

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    private function getActionFilters(ActionContext $context): iterable
    {
        foreach ($context->filterDefinitions as $definition)
            yield $this->filters->createActionFilter($definition);
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

        $info = $this->dispatcher->dispatch($request);

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

        $action = null;

        /** @var IActionContextResolver $resolver */
        foreach ($this->resolvers as $resolver)
            if (null !== ($action = $resolver->resolveContext($context, $request)))
                break;

        if (null === $action)
            return new NotFoundAction();

        if (count($context->filterDefinitions) > 0)
        {
            $filters = $this->getActionFilters($context);

            $action = new FilteredAction($action, $filters);
        }

        return $action;
    }
}
