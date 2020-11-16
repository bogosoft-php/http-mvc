<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Bogosoft\Http\Mvc\Serialization\ISerializer;
use Psr\Http\Message\ResponseFactoryInterface as IResponseFactory;

/**
 * A collection parameters by which the behavior of an object of the
 * {@see MvcMiddleware} class can be influenced.
 *
 * @package Bogosoft\Http\Mvc
 */
class MvcMiddlewareParameters
{
    /**
     * @var IActionContextActivator Get or set or action context activation
     *                              strategy.
     */
    public IActionContextActivator $activator;

    /**
     * @var ISerializer|null Get or set an optional default serializer.
     */
    public ?ISerializer $defaultSerializer = null;

    /**
     * @var bool Get or set a value indicating whether or not a 406 Not
     *           Acceptable HTTP response can be returned in the event that
     *           content negotiation fails.
     */
    public bool $notAcceptableEnabled = false;

    /**
     * @var IResponseFactory Get or set an HTTP response factory.
     */
    public IResponseFactory $responses;

    /**
     * @var IRouter Get or set a strategy responsible for generating action
     *              contexts from HTTP requests.
     */
    public IRouter $router;

    /**
     * @var ISerializer[] Get or set an array of serializers.
     */
    public array $serializers = [];
}
