<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Tests\Serializers;

use Bogosoft\Http\Mvc\Serialization\ISerializer;
use Bogosoft\Http\Mvc\Serialization\RequestedMediaFormat;
use Psr\Http\Message\StreamInterface as IStream;

class PhpSerializer implements ISerializer
{
    /**
     * @inheritDoc
     */
    function canSerialize(RequestedMediaFormat $format): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    function getContentType(): string
    {
        return 'application/octet-stream';
    }

    /**
     * @inheritDoc
     */
    function serialize($data, IStream $output): void
    {
        $output->write(serialize($data));
    }
}
