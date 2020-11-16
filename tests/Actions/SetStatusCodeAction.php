<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Tests\Actions;

use Bogosoft\Http\Mvc\IAction;
use Bogosoft\Http\Mvc\StatusCodeResult;
use Psr\Http\Message\ServerRequestInterface as IServerRequest;

class SetStatusCodeAction implements IAction
{
    private int $code;

    function __construct(int $code)
    {
        $this->code = $code;
    }

    /**
     * @inheritDoc
     */
    function execute(IServerRequest $request)
    {
        return new StatusCodeResult($this->code);
    }
}
