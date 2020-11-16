<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc\Serialization;

/**
 * Represents a media format (MIME type) as described by
 * {@see https://tools.ietf.org/html/rfc7231#section-5.3.2 HTTP 1.1 spec}.
 *
 * @package Bogosoft\Http\Mvc\Formatting
 */
class RequestedMediaFormat
{
    /**
     * Parse a string representing a single requested media format.
     *
     * @param  string      $entry A string representing a single media format.
     * @return RequestedMediaFormat        The result of parsing the given string.
     */
    static function parse(string $entry): RequestedMediaFormat
    {
        $format = new RequestedMediaFormat();

        $components = explode(';', $entry);

        list ($format->type, $format->subtype) =
            explode('/', array_shift($components));

        $format->acceptParams = [];

        foreach ($components as $component)
        {
            list ($name, $value) = explode('=', $component);

            $name  = trim($name);
            $value = trim($value);

            if ('q' === $name)
                $format->qualityFactor = floatval($value);
            else
                $format->acceptParams[$name] = $value;
        }

        return $format;
    }

    /**
     * Parse a string representing a collection of media formats.
     *
     * @param  string        $entries A string representing a collection of
     *                                media formats.
     * @return RequestedMediaFormat[]          An array of parsed and ranked requested
     *                                media formats.
     */
    static function parseAndRankAll(string $entries): array
    {
        $formats = array_map(
            self::class . '::parse',
            array_map('trim', explode(',', $entries))
            );

        usort($formats, function(
            RequestedMediaFormat $a,
            RequestedMediaFormat $b
            )
            : int
        {
            $x = intval($a->qualityFactor * 1000000);
            $y = intval($b->qualityFactor * 1000000);

            $x += '*' === $a->type ? 10000 : 100000;
            $y += '*' === $b->type ? 10000 : 100000;

            $x += '*' === $a->subtype ? 100 : 1000;
            $y += '*' === $b->subtype ? 100 : 1000;

            $x += count($a->acceptParams);
            $y += count($b->acceptParams);

            return $y - $x;
        });

        return $formats;
    }

    /**
     * @var string[] Get or set an array of accept-params for the current
     * media format.
     */
    public array $acceptParams = [];

    /**
     * @var float Get or set the quality factor of the current media format.
     */
    public float $qualityFactor = 1;

    /** @var string Get or set the type of the current media format. */
    public string $type;

    /** @var string Get or set the subtype of the current media format. */
    public string $subtype;

    /**
     * @return string Serialize the current media format to a string.
     */
    function __toString(): string
    {
        $params = ';q=' . $this->qualityFactor;

        foreach ($this->acceptParams as $name => $value)
            $params .= ";$name=$value";

        return $this->type . '/' . $this->subtype . $params;
    }
}
