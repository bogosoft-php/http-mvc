<?php

declare(strict_types=1);

namespace Bogosoft\Http\Mvc;

/**
 * A collection of route information.
 *
 * @package Bogosoft\Http\Mvc
 */
class RouteInfo
{
    /**
     * @var array Get or set an array of methods allowed for the associated
     *            route.
     */
    public array $allowedMethods = [];

    /**
     * @var ActionContext|null Get or set an associated action context.
     */
    public ?ActionContext $context = null;

    /**
     * @var int Get or set a status code.
     */
    public int $status;

    /**
     * Create a new collection of route information.
     *
     * @param int                $status         A status code.
     * @param array              $allowedMethods An array of allowed methods
     *                                           for an associated route.
     * @param ActionContext|null $context        A context within which an
     *                                           action can be created.
     */
    function __construct(int $status, array $allowedMethods = [], ActionContext $context = null)
    {
        $this->allowedMethods = $allowedMethods;
        $this->status         = $status;
        $this->context        = $context;
    }
}
