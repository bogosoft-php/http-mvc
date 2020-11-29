<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Bogosoft\Http\Mvc;

use Psr\Http\Message\ServerRequestInterface as IRequest;
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
    /**
     * Create a new controller action.
     *
     * @param IControllerFactory $controllers A factory from which controllers
     *                                        can be created.
     * @param string             $class       The name of a controller class.
     * @param string             $method      The name of a method to be
     *                                        invoked on a controller.
     * @param IParameterMatcher  $matcher     A parameter matching strategy.
     */
    function __construct(
        private IControllerFactory $controllers,
        private string $class,
        private string $method,
        private IParameterMatcher $matcher
        )
    {
    }

    /**
     * @inheritDoc
     */
    function execute(IRequest $request): mixed
    {
        $controller = $this->controllers->createController($this->class, $request);

        if (null === $controller)
            return new NotFoundResult();

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

        $params = $rm->getParameters();

        foreach ($params as $rp)
            if ($this->matcher->tryMatch($rp, $request, $result))
                $args[] = $result;

        if (count($args) < count($params))
            return new BadRequestResult();

        return $rm->invokeArgs($controller, $args);
    }
}
