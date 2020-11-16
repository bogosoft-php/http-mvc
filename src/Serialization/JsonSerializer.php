<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Serialization;

use Psr\Http\Message\StreamInterface as IStream;

/**
 * An implementation of the {@see ISerializer} contract that can serialize
 * arbitrary data as a JSON-encoded string.
 *
 * This serializer utilizes the {@see json_encode()} function.
 *
 * @package Bogosoft\Http\Mvc\Serialization
 */
class JsonSerializer implements ISerializer
{
    private int $options;

    /**
     * Create a new JSON serializer.
     *
     * Use values from the
     * {@see https://www.php.net/manual/en/json.constants.php JSON Constants}
     * page when setting options.
     *
     * @param int $options A bitmask of options.
     *
     */
    function __construct(int $options = 0)
    {
        $this->options = $options;
    }

    /**
     * @inheritDoc
     */
    function canSerialize(RequestedMediaFormat $format): bool
    {
        $range = $format->type . '/' . $format->subtype;

        return 'application/json' === $range
            || 'application/*' === $range
            || '*/*' === $range;
    }

    /**
     * @inheritDoc
     */
    function getContentType(): string
    {
        return 'application/json';
    }

    /**
     * @inheritDoc
     */
    function serialize($data, IStream $output): void
    {
        $output->write(json_encode($data));
    }
}
