<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Bogosoft\Http\Session\ISession;
use RuntimeException;

/**
 * An MVC controller which adds additional, MVC-specific functionality to
 * the class.
 *
 * @package Bogosoft\Http\Mvc
 */
abstract class MvcController extends Controller
{
    private ISession $session;
    private IViewFactory $views;

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
     * Set session data on the current controller.
     *
     * @param ISession $session Session data.
     *
     * @throws RuntimeException if the controller has already been locked.
     */
    function setSession(ISession $session): void
    {
        if ($this->isLocked())
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
        if ($this->isLocked())
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
