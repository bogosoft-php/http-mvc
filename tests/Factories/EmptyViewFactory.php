<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Tests\Factories;

use Bogosoft\Http\Mvc\IView;
use Bogosoft\Http\Mvc\IViewFactory;

final class EmptyViewFactory implements IViewFactory
{
    /**
     * @inheritDoc
     */
    function createView(string $name, $model, array $parameters): ?IView
    {
        return null;
    }
}
