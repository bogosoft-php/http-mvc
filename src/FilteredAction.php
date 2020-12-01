<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Iterator;
use Psr\Http\Message\ServerRequestInterface as IRequest;

/**
 * Represents an action with zero or more action filters applied to it.
 *
 * This class cannot be inherited.
 *
 * @package Bogosoft\Http\Mvc
 */
final class FilteredAction implements IAction
{
    public ActionFilterQueue $filters;

    /**
     * Create a new filtered action.
     *
     * @param IAction  $action  An executable action.
     * @param iterable $filters Zero or more filters to be applied to the
     *                          given action upon its execution.
     */
    function __construct(
        private IAction $action,
        iterable $filters = []
        )
    {
        $this->filters = new ActionFilterQueue($filters);
    }

    /**
     * @inheritDoc
     */
    function execute(IRequest $request): mixed
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $filters = $this->filters->getIterator();

        return (new class($this->action, $filters) implements IAction
        {
            private IAction $action;
            private Iterator $filters;

            function __construct(IAction $action, Iterator $filters)
            {
                $this->action  = $action;
                $this->filters = $filters;
            }

            function execute(IRequest $request): mixed
            {
                return $this->filters->valid()
                    ? $this->next()->apply($request, $this)
                    : $this->action->execute($request);
            }

            private function next(): IActionFilter
            {
                try
                {
                    return $this->filters->current();
                }
                finally
                {
                    $this->filters->next();
                }
            }

        })->execute($request);
    }
}
