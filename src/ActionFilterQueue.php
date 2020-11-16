<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use IteratorAggregate;

/**
 * Represents a first-in, first-out (FIFO) collection of action filters.
 *
 * @package Bogosoft\Http\Mvc
 */
class ActionFilterQueue implements IteratorAggregate
{
    private static function concat(iterable $a, iterable $b): iterable
    {
        yield from $a;
        yield from $b;
    }

    private static function iterate($singleton): iterable
    {
        yield $singleton;
    }

    private iterable $filters;

    /**
     * Create a new action filter queue.
     *
     * @param iterable $filters A sequence of filters with which to initially
     *                          populate the new queue.
     */
    function __construct(iterable $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Place an action filter on the current queue.
     *
     * @param IActionFilter $filter An action filter.
     */
    function enqueueFilter(IActionFilter $filter): void
    {
        $this->enqueueFilters(self::iterate($filter));
    }

    /**
     * Place a sequence of action filters on the current queue.
     *
     * @param iterable $filters A sequence of {@see IActionFilter} objects.
     */
    function enqueueFilters(iterable $filters): void
    {
        $this->filters = self::concat($this->filters, $filters);
    }

    /**
     * Remove an action filter from the current queue.
     *
     * Calling this action will convert the internal store of action filters
     * to an array. Avoid using this method if the intent is to store and then
     * iterate filters from a generator.
     *
     * @return IActionFilter An action filter.
     */
    function dequeueFilter(): IActionFilter
    {
        if (!is_array($this->filters))
            $this->filters = [...$this->filters];

        return array_shift($this->filters);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        yield from $this->filters;
    }
}
