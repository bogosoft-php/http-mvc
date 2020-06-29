<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Bogosoft\Http\Routing\IActionResult;
use Bogosoft\Http\Routing\Results\BadRequestResult;
use Bogosoft\Http\Routing\Results\NotFoundResult;
use Psr\Http\Message\ServerRequestInterface as IServerRequest;
use RuntimeException;

/**
 * A partial implementation of a controller containing utility functionality.
 *
 * @package Bogosoft\Http\Mvc
 */
abstract class Controller
{
    private bool $locked = false;
    private IServerRequest $request;
    private ISession $session;
    private IViewFactory $views;

    /**
     * Indicate that a request for a resource was not correctly formed.
     *
     * @return IActionResult An action result.
     */
    protected function badRequest(): IActionResult
    {
        return new BadRequestResult();
    }

    /**
     * Get the HTTP request associated with the current controller.
     *
     * @return IServerRequest An HTTP request.
     */
    protected function getRequest(): IServerRequest
    {
        return $this->request;
    }

    /**
     * Get the session associated with the current controller.
     *
     * @return ISession Session data.
     */
    protected function getSession(): ISession
    {
        return $this->session;
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
     * @return IActionResult An action result.
     */
    protected function notFound(): IActionResult
    {
        return new NotFoundResult();
    }

    /**
     * Associate an HTTP request with the current controller.
     *
     * @param IServerRequest $request An HTTP request.
     *
     * @throws RuntimeException if the controller has already been locked.
     */
    function setRequest(IServerRequest $request): void
    {
        if ($this->locked)
            throw new RuntimeException('Controller is locked.');

        $this->request = $request;
    }

    /**
     * Set session data on the current controller.
     *
     * @param ISession $session Session data.
     *
     * @throws RuntimeException if the controller has already been locked.
     */
    function setSession(ISession $session): void
    {
        if ($this->locked)
            throw new RuntimeException('Controller is locked.');

        $this->session = $session;
    }

    /**
     * Associate a view factory with the current controller.
     *
     * @param IViewFactory $views A view factory.
     *
     * @throws RuntimeException if the controller has already been locked.
     */
    function setViewFactory(IViewFactory $views): void
    {
        if ($this->locked)
            throw new RuntimeException('Controller is locked.');

        $this->views = $views;
    }

    /**
     * Render a view by its name.
     *
     * @param  string     $name       The name of a view to be rendered.
     * @param  mixed|null $model      A model object to be projected through
     *                                a view.
     * @param  array      $parameters An array of parameters as key-value
     *                                pairs.
     * @return ViewResult             A new view result.
     *
     * @throws ViewNotFoundException when the given name cannot be resolved
     *                               to a view.
     */
    protected function view(string $name, $model = null, array $parameters = []): ViewResult
    {
        $view = $this->views->createView($name, $model, $parameters);

        if (null === $view)
            throw new ViewNotFoundException($name);

        return new ViewResult($view);
    }
}
