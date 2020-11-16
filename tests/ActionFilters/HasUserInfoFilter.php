<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Tests\ActionFilters;

use Bogosoft\Http\Mvc\IAction;
use Bogosoft\Http\Mvc\IActionFilter;
use Bogosoft\Http\Mvc\StatusCodeResult;
use Psr\Http\Message\ServerRequestInterface as IRequest;

class HasUserInfoFilter implements IActionFilter
{
    /**
     * @inheritDoc
     */
    function apply(IRequest $request, IAction $action)
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
