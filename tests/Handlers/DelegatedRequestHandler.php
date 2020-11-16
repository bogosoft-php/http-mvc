<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Tests\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DelegatedRequestHandler implements RequestHandlerInterface
{
    /** @var callable */
    private $delegate;

    function __construct(callable $delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return ($this->delegate)($request);
    }
}
