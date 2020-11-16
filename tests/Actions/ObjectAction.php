<?php

declare(strict_types=1);

namespace Tests\Actions;

use Bogosoft\Http\Routing\IAction;
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
