<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Tests\Factories;

use Bogosoft\Http\Mvc\Controller;
use Bogosoft\Http\Mvc\IControllerFactory;
use Psr\Http\Message\ServerRequestInterface as IRequest;

final class EmptyControllerFactory implements IControllerFactory
{
    /**
     * @inheritDoc
     */
    function createController(string $class, IRequest $request): ?Controller
    {
        return null;
    }
}
