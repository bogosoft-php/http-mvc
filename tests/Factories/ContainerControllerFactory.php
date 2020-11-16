<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Tests\Factories;

use Bogosoft\Http\Mvc\Controller;
use Bogosoft\Http\Mvc\IControllerFactory;
use Psr\Container\ContainerInterface as IContainer;
use Psr\Http\Message\ServerRequestInterface as IRequest;

class ContainerControllerFactory implements IControllerFactory
{
    private IContainer $container;

    function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    function createController(string $class, IRequest $request): ?Controller
    {
        return $this->container->get($class);
    }
}
