<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IRequest;

/**
 * An action that, when executed, will generate a result indicating that the
 * requested resource could not be found.
 *
 * @package Bogosoft\Http\Mvc
 */
class NotFoundAction implements IAction
{
    /**
     * @inheritDoc
     */
    function execute(IRequest $request): NotFoundResult
    {
        return new NotFoundResult();
    }
}
