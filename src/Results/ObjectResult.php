<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Results;

use Bogosoft\Http\Mvc\Serialization\ISerializer;
use Bogosoft\Http\Routing\IActionResult;
use Psr\Http\Message\ResponseInterface as IResponse;

/**
 * An implementation of the {@see IActionResult} contract that, when applied,
 * will serialize arbitrary data to an HTTP response.
 *
 * @package Bogosoft\Http\Mvc\Results
 */
class ObjectResult implements IActionResult
{
    /** @var mixed */
    private $data;
    private ISerializer $serializer;

    /**
     * Create a new object result.
     *
     * @param mixed $data Data to be serialized when the new result is applied
     *                    to an HTTP response.
     */
    function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    function apply(IResponse $response): IResponse
    {
        $output = $response->getBody();

        $this->serializer->serialize($this->data, $output);

        return $response->withHeader(
            'Content-Type',
            $this->serializer->getContentType());
    }

    /**
     * Associate a serializer with the current object result.
     *
     * @param ISerializer $serializer A serializer to be used when serializing
     *                                arbitrary data to an HTTP response.
     */
    function setSerializer(ISerializer $serializer): void
    {
        $this->serializer = $serializer;
    }
}
