<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Bogosoft\Http\Routing\IActionResult;
use Psr\Http\Message\ResponseInterface as IResponse;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * An implementation of the {@see IActionResult} contract that renders a view
 * to the output of an HTTP response.
 *
 * @package Bogosoft\Http\Mvc
 */
class ViewResult implements IActionResult
{
    private StreamFactoryInterface $streams;
    private IView $view;

    /**
     * Create a new view result.
     *
     * @param IView $view A view to be rendered to the output of an HTTP
     *                    response.
     */
    function __construct(IView $view)
    {
        $this->view = $view;
    }

    /**
     * @inheritDoc
     */
    function apply(IResponse $response): IResponse
    {
        $stream = $response->getBody()->detach();

        $this->view->render($stream);

        $body = $this->streams->createStreamFromResource($stream);

        return $response->withBody($body);
    }
}
