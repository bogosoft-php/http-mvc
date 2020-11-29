<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Tests\Actions;

use Bogosoft\Http\Mvc\IAction;
use Psr\Http\Message\ServerRequestInterface as IServerRequest;

class ObjectAction implements IAction
{
    function __construct(private mixed $data)
    {
    }

    /**
     * @inheritDoc
     */
    function execute(IServerRequest $request): mixed
    {
       return $this->data;
    }
}
