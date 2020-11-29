<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Tests\Actions;

use Bogosoft\Http\Mvc\IAction;
use Bogosoft\Http\Mvc\StatusCodeResult;
use Psr\Http\Message\ServerRequestInterface as IServerRequest;

class SetStatusCodeAction implements IAction
{
    function __construct(private int $code)
    {
    }

    /**
     * @inheritDoc
     */
    function execute(IServerRequest $request): StatusCodeResult
    {
        return new StatusCodeResult($this->code);
    }
}
