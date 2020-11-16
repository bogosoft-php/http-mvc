<?php

declare(strict_types=1);

namespace Tests\Handlers;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface as IResponse;
use Psr\Http\Message\ServerRequestInterface as IRequest;
use Psr\Http\Server\RequestHandlerInterface as IRequestHandler;

final class StatusCodeConfigurableRequestHandler implements IRequestHandler
{
    private int $code;

    function __construct(int $code)
    {
        $this->code = $code;
    }

    /**
     * @inheritDoc
     */
    function handle(IRequest $request): IResponse
    {
        return new Response($this->code);
    }
}
