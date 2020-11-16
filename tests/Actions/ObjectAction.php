<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Tests\Actions;

use Bogosoft\Http\Mvc\IAction;
use Psr\Http\Message\ServerRequestInterface as IServerRequest;

class ObjectAction implements IAction
{
    /** @var mixed */
    private $data;

    function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    function execute(IServerRequest $request)
    {
       return $this->data;
    }
}
