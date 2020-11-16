<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Bogosoft\Http\Mvc\Serialization\ISerializer;
use Bogosoft\Http\Mvc\Serialization\JsonSerializer;
use Bogosoft\Http\Mvc\Serialization\RequestedMediaFormat;
use Bogosoft\Http\Routing\IActionResult;
use Bogosoft\Http\Routing\Results\StatusCodeResult;
use Psr\Http\Message\ResponseInterface as IResponse;
use Psr\Http\Message\ServerRequestInterface as IRequest;
use Psr\Http\Server\MiddlewareInterface as IMiddleware;
use Psr\Http\Server\RequestHandlerInterface as IRequestHandler;

/**
 * A Model-View-Controller (MVC) middleware component.
 *
 * @package Bogosoft\Http\Mvc
 */
class MvcMiddleware implements IMiddleware
{
    /**
     * Create a new MVC middleware component using a callback to configure
     * a collection of parameters. The parameter collection will be passed
     * to the constructor of the current class.
     *
     * The callback is expected to be of the form:
     *
     * - fn({@see MvcMiddlewareParameters}): {@see void}
     *
     * @param  callable      $config A callback that returns {@see void} and
     *                               accepts a single parameter of type,
     *                               {@see MvcMiddlewareParameters}.
     * @return MvcMiddleware         A new MVC middleware component.
     */
    static function create(callable $config): MvcMiddleware
    {
        $params = new MvcMiddlewareParameters();

        $config($params);

        return new MvcMiddleware($params);
    }

    private MvcMiddlewareParameters $params;

    /**
     * Create a new MVC middleware component.
     *
     * @param MvcMiddlewareParameters $params A collection of parameters by
     *                                        which the behavior of the new
     *                                        MVC middleware component can be
     *                                        influenced.
     */
    function __construct(MvcMiddlewareParameters $params)
    {
        $this->params = $params;
    }

    private function handleObjectResult(
        ObjectResult $result,
        IRequest $request
        )
        : IActionResult
    {
        /** @var ISerializer $serializer */
        $serializer = null;

        $acceptHeader = $request->hasHeader('Accept')
            ? trim($request->getHeaderLine('Accept'))
            : '*/*';

        $formats = RequestedMediaFormat::parseAndRankAll($acceptHeader);

        foreach ($formats as $format)
            foreach ($this->params->serializers as $candidate)
                if ($candidate->canSerialize($format))
                {
                    $serializer = $candidate;

                    break 2;
                }

        if (null === $serializer)
            if ($this->params->notAcceptableEnabled)
                return new StatusCodeResult(406);
            else
                $serializer = $this->params->defaultSerializer;

        $result->setSerializer($serializer ?? new JsonSerializer());

        return $result;
    }

    /**
     * @inheritDoc
     *
     * @throws CannotActivateActionContextException
     */
    function process(IRequest $request, IRequestHandler $handler): IResponse
    {
        if (null === ($context = $this->params->router->route($request)))
            return $handler->handle($request);

        $action = $this
            ->params
            ->activator
            ->activateContext($context, $request);

        if (null === $action)
            throw new CannotActivateActionContextException($request);

        $result = $action->execute($request);

        if (!($result instanceof IActionResult))
            $result = new ObjectResult($result);

        if ($result instanceof ObjectResult)
            $result = $this->handleObjectResult($result, $request);

        $response = $this->params->responses->createResponse();

        return $result->apply($response);
    }
}
