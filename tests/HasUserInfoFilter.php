<?php

declare(strict_types=1);

namespace Tests;

use Bogosoft\Http\Routing\IAction;
use Bogosoft\Http\Routing\IActionFilter;
use Bogosoft\Http\Routing\Results\StatusCodeResult;
use Psr\Http\Message\ServerRequestInterface as IServerRequest;

class HasUserInfoFilter implements IActionFilter
{
    /**
     * @inheritDoc
     */
    function apply(IServerRequest $request, IAction $action)
    {
        $userInfo = $request->getUri()->getUserInfo();

        if ('' !== $userInfo)
            return $action->execute($request);

        return new class extends StatusCodeResult
        {
            public function __construct()
            {
                parent::__construct(401);
            }
        };
    }
}
