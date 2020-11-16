<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Iterator;
use Psr\Http\Message\ServerRequestInterface as IServerRequest;

/**
 * Represents an action with zero or more action filters applied to it.
 *
 * This class cannot be inherited.
 *
 * @package Bogosoft\Http\Mvc
 */
final class FilteredAction implements IAction
{
    private IAction $action;
    public ActionFilterQueue $filters;

    /**
     * Create a new filtered action.
     *
     * @param IAction  $action  An executable action.
     * @param iterable $filters Zero or more filters to be applied to the
     *                          given action upon its execution.
     */
    function __construct(IAction $action, iterable $filters = [])
    {
        $this->action  = $action;
        $this->filters = new ActionFilterQueue($filters);
    }

    /**
     * @inheritDoc
     */
    function execute(IServerRequest $request)
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

            function execute(IServerRequest $request)
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
