<?php

declare(strict_types=1);

namespace Tests\Actions;

use Bogosoft\Http\Routing\IAction;
use Bogosoft\Http\Routing\Results\StatusCodeResult;
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
