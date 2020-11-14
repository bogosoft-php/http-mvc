<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IRequest;
use ReflectionProperty;

/**
 * A composite implementation of the {@see IPropertyMatcher} contract that
 * allows multiple property matchers to behave as if they were a single
 * property matcher.
 *
 * @package Bogosoft\Http\Mvc
 */
final class CompositePropertyMatcher implements IPropertyMatcher
{
    /** @var IPropertyMatcher[] */
    private array $matchers;

    /**
     * Create a new composite property matcher.
     *
     * @param IPropertyMatcher ...$matchers Zero or more property matchers
     *                                      from which to form the composite.
     */
    function __construct(IPropertyMatcher ...$matchers)
    {
        $this->matchers = $matchers;
    }

    /**
     * @inheritDoc
     */
    function tryMatch(ReflectionProperty $rp, IRequest $request, &$result): bool
    {
        foreach ($this->matchers as $matcher)
            if ($matcher->tryMatch($rp, $request, $result))
                return true;

        return false;
    }
}
