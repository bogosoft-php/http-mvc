<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IRequest;
use RuntimeException;
use Throwable;

/**
 * An exception intended to be thrown when a non-null action context cannot be
 * activated and converted into an action.
 *
 * @package Bogosoft\Http\Mvc
 */
class CannotActivateActionContextException extends RuntimeException
{
    private IRequest $request;

    /**
     * Create a new exception related to being unable to activate an action
     * context.
     *
     * @param IRequest       $request  The HTTP request for which a
     *                                 corresponding action context could not
     *                                 be activated.
     * @param int            $code     An optional exception code.
     * @param Throwable|null $previous An optional previous exception.
     */
    function __construct(
        IRequest $request,
        int $code = 0,
        Throwable $previous = null
        )
    {
        $path = $request->getUri()->getPath();

        $message = "Cannot resolve action context for path: '$path'.";

        parent::__construct($message, $code, $previous);

        $this->request = $request;
    }

    /**
     * @return IRequest Get the HTTP request associated with the current
     *                  exception.
     */
    function getRequest(): IRequest
    {
        return $this->request;
    }
}
