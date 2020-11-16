<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ResponseInterface as IResponse;

/**
 * An implementation of the {@see IActionResult} contract that renders a view
 * to the output of an HTTP response.
 *
 * @package Bogosoft\Http\Mvc
 */
class ViewResult implements IActionResult
{
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
        $this->view->render($response->getBody());

        return $response->withHeader('Content-Type', 'text/html');
    }
}
