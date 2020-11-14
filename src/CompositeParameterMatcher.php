<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IRequest;
use ReflectionParameter;

/**
 * A composite implementation of the {@see IParameterMatcher} contract that
 * allows multiple parameter matchers to behave as if they were a single
 * parameter matcher.
 *
 * This class cannot be inherited.
 *
 * @package Bogosoft\Http\Mvc
 */
final class CompositeParameterMatcher implements IParameterMatcher
{
    /** @var IParameterMatcher[] */
    private array $matchers;

    /**
     * Create a new composite parameter matcher.
     *
     * @param IParameterMatcher ...$matchers
     */
    function __construct(IParameterMatcher ...$matchers)
    {
        $this->matchers = $matchers;
    }

    /**
     * @inheritDoc
     */
    function tryMatch(ReflectionParameter $rp, IRequest $request, &$result): bool
    {
        foreach ($this->matchers as $matcher)
            if ($matcher->tryMatch($rp, $request, $result))
                return true;

        return false;
    }
}
