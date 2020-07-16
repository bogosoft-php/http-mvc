<?php

declare(strict_types=1);

namespace Tests;

use Bogosoft\Http\Mvc\Controller;
use Bogosoft\Http\Mvc\IControllerFactory;

final class EmptyControllerFactory implements IControllerFactory
{
    /**
     * @inheritDoc
     */
    function createController(string $class): ?Controller
    {
        return null;
    }
}
