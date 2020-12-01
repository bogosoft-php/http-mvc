<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Bogosoft\Http\Session\ISession;
use Psr\Http\Message\ServerRequestInterface as IRequest;
use RuntimeException;

/**
 * A partial implementation of a controller containing utility functionality.
 *
 * @package Bogosoft\Http\Mvc
 */
abstract class Controller
{
    private bool $locked = false;
    private IRequest $request;

    /**
     * Indicate that a request for a resource was not correctly formed.
     *
     * @return BadRequestResult An action result.
     */
    protected function badRequest(): BadRequestResult
    {
        return new BadRequestResult();
    }

    /**
     * Get the HTTP request associated with the current controller.
     *
     * @return IRequest An HTTP request.
     */
    protected function getRequest(): IRequest
    {
        return $this->request;
    }

    /**
     * @return bool Get a value indicating whether or not the current
     *              controller is locked against member mutation.
     */
    function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * Lock the current controller against modification of certain members.
     */
    function lock(): void
    {
        $this->locked = true;
    }

    /**
     * Indicate that a request for a resource could not be found.
     *
     * @return NotFoundResult An action result.
     */
    protected function notFound(): NotFoundResult
    {
        return new NotFoundResult();
    }

    /**
     * Indicate that the requested resource was found.
     *
     * @return OkResult
     */
    protected function ok(): OkResult
    {
        return new OkResult();
    }

    /**
     * Associate an HTTP request with the current controller.
     *
     * @param IRequest $request An HTTP request.
     *
     * @throws RuntimeException if the controller has already been locked.
     */
    function setRequest(IRequest $request): void
    {
        if ($this->locked)
            throw new RuntimeException('Controller is locked.');

        $this->request = $request;
    }
}
