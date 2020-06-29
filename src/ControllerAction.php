<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Bogosoft\Http\Mvc;

use Bogosoft\Http\Routing\IAction;
use Bogosoft\Http\Routing\Results\BadRequestResult;
use Bogosoft\Http\Routing\Results\NotFoundResult;
use Psr\Http\Message\ServerRequestInterface as IServerRequest;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * An action that, when executed, invokes a method of a controller.
 *
 * @package Bogosoft\Http\Mvc
 */
class ControllerAction implements IAction
{
    private string $class;
    private IControllerFactory $controllers;
    private string $method;
    private array $parameters;

    /**
     * Create a new controller action.
     *
     * @param IControllerFactory $controllers A factory from which controllers
     *                                        can be created.
     * @param string     $class               The name of a controller class.
     * @param string     $method              The name of a method to be
     *                                        invoked on a controller.
     * @param array      $parameters          A collection of parameters as an
     *                                        array of key-value pairs.
     */
    function __construct(
        IControllerFactory $controllers,
        string $class,
        string $method,
        array $parameters
        )
    {
        $this->class       = $class;
        $this->controllers = $controllers;
        $this->method      = $method;
        $this->parameters  = $parameters;
    }

    /**
     * @inheritDoc
     */
    function execute(IServerRequest $request)
    {
        $controller = $this->controllers->createController($this->class);

        $rc = new ReflectionClass($controller);

        /** @var ReflectionMethod $rm */
        $rm = null;

        try
        {
            $rm = $rc->getMethod($this->method);
        }
        catch (ReflectionException $re)
        {
            return new NotFoundResult();
        }

        $args = [];

        $params = array_merge($request->getQueryParams(), $this->parameters);

        foreach ($rm->getParameters() as $rp)
            if (array_key_exists($name = $rp->getName(), $params))
                $args[] = $params[$name];
            elseif (null !== ($rc = $rp->getClass()))
                $args[] = $this->resolveObject($rc, $params);
            elseif ($rp->isDefaultValueAvailable())
                $args[] = $rp->getDefaultValue();
            else
                return new BadRequestResult();

        return $rm->invokeArgs($controller, $args);
    }

    private function resolveObject(ReflectionClass $rc, array &$parameters)
    {
        $argument = $rc->newInstance();

        foreach ($rc->getProperties() as $rp)
            if (array_key_exists($name = $rp->getName(), $parameters))
                $rp->setValue($argument, $parameters[$name]);

        return $argument;
    }
}
