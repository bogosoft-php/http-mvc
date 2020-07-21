<?php

declare(strict_types=1);

namespace Tests;

use Bogosoft\Http\Mvc\Controller;
use Bogosoft\Http\Mvc\IControllerFactory;
use Psr\Http\Message\ServerRequestInterface as IServerRequest;

final class EmptyControllerFactory implements IControllerFactory
{
    /**
     * @inheritDoc
     */
    function createController(string $class, IServerRequest $request): ?Controller
    {
        return null;
    }
}
