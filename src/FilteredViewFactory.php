<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use ArrayIterator;
use Iterator;

/**
 * An implementation of the {@see IViewFactory} contract that processes
 * a view request through a series of filters before optionally calling
 * a source view factory.
 *
 * The source view factory will only be called in the event that a filter
 * does not preempt it by directly returning a {@see IView} itself. Any
 * filter that directly returns a view effectively short-circuits the
 * pipeline, ensuring that no downstream filters or the source view factory
 * are ever called.
 *
 * @package Bogosoft\Http\Mvc
 */
final class FilteredViewFactory implements IViewFactory
{
    /**
     * Create a new filtered view factory.
     *
     * @param IViewFactory $source  A source view factory.
     * @param iterable     $filters A sequence of {@see IViewFilter} objects.
     */
    function __construct(
        private IViewFactory $source,
        private iterable $filters
        )
    {
    }

    /**
     * @inheritDoc
     */
    function createView(string $name, $model, array $parameters): ?IView
    {
        $filters = is_array($this->filters)
            ? new ArrayIterator($this->filters)
            : $this->filters;

        return (new class($this->source, $filters) implements IViewFactory
        {
            private Iterator $filters;
            private IViewFactory $source;

            function __construct(IViewFactory $source, Iterator $filters)
            {
                $this->filters = $filters;
                $this->source  = $source;
            }

            /**
             * @inheritDoc
             */
            function createView(string $name, $model, array $parameters): ?IView
            {
                return $this->filters->valid()
                    ? $this->next()->apply($name, $model, $parameters, $this)
                    : $this->source->createView($name, $model, $parameters);
            }

            private function next(): IViewFilter
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

        })->createView($name, $model, $parameters);
    }
}
