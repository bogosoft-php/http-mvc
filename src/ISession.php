<?php

namespace Bogosoft\Http\Mvc;

/**
 * Represents a session.
 *
 * @package Bogosoft\Http\Mvc
 */
interface ISession
{
    /**
     * Get a value by its key from the current session.
     *
     * @param  string     $key     The key of a value to retrieve.
     * @param  mixed|null $default A value to be returned if the given key
     *                             has no associated value registered with
     *                             the current session.
     * @return mixed|null          The value associated with the given key
     *                             or the default value if no value could be
     *                             located.
     */
    function get(string $key, $default = null);

    /**
     * Regenerate the current session.
     */
    function regenerate(): void;

    /**
     * Set a given value by a given key in the current session.
     *
     * @param string $key   A key by which the given value can be referenced.
     * @param mixed  $value A value.
     */
    function set(string $key, $value): void;
}
