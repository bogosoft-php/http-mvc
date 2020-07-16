<?php

declare(strict_types=1);

namespace Tests;

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
