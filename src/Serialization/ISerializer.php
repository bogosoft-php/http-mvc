<?php

namespace Bogosoft\Http\Mvc\Serialization;

use Psr\Http\Message\StreamInterface as IStream;

/**
 * Represents a strategy for serializing data to an output stream.
 *
 * @package Bogosoft\Http\Mvc\Serialization
 */
interface ISerializer
{
    /**
     * Get a value indicating whether or not the current serializer can
     * serialize data in a given format.
     *
     * @param  RequestedMediaFormat $format A media format.
     * @return bool                True if the current serializer can
     *                             serialize data in the given format; false
     *                             otherwise.
     */
    function canSerialize(RequestedMediaFormat $format): bool;

    /**
     * Get the content type of data serialized by the current serializer.
     *
     * @return string A media format (MIME type).
     */
    function getContentType(): string;

    /**
     * Serialize given data to a given output stream.
     *
     * @param mixed   $data   Data to be serializer.
     * @param IStream $output An output stream to which data will be
     *                        serialized.
     */
    function serialize($data, IStream $output): void;
}
