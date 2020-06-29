<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

/**
 * A straight-forward implementation of the {@see ISession} contract.
 *
 * This class cannot be inherited.
 *
 * @package Bogosoft\Http\Mvc
 */
final class DefaultSession implements ISession
{
    private bool $deleteOnRegen;

    /**
     * Create a new default session.
     *
     * @param bool $deleteOnRegen A value indicating whether or not old
     *                            session data should be deleted when a
     *                            session has been regenerated.
     */
    function __construct(bool $deleteOnRegen = false)
    {
        $this->deleteOnRegen = $deleteOnRegen;
    }

    /**
     * @inheritDoc
     */
    function get(string $key, $default = null)
    {
        return array_key_exists($key, $_SESSION)
            ? $_SESSION[$key]
            : $default;
    }

    /**
     * @inheritDoc
     */
    function regenerate(): void
    {
        session_regenerate_id($this->deleteOnRegen);
    }

    /**
     * @inheritDoc
     */
    function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }
}