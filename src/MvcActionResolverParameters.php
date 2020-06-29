<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

/**
 * A collection of MVC action resolver parameters.
 *
 * @package Bogosoft\Http\Mvc
 */
class MvcActionResolverParameters
{
    /**
     * @var IControllerFactory Get or set a controller factor.
     */
    public IControllerFactory $controllers;

    /**
     * @var IDispatcher Get or set an HTTP request dispatcher.
     */
    public IDispatcher $dispatcher;

    /**
     * @var IActionFilterFactory Get or set an action filter factory.
     */
    public IActionFilterFactory $filters;

    /**
     * @var ISession|null Get or set session data.
     */
    public ?ISession $session = null;

    /**
     * @var IViewFactory Get or set a view factory.
     */
    public IViewFactory $views;
}
