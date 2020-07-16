<?php
namespace Lyndon\Route\Action;

use Illuminate\Http\Request;
use Illuminate\Container\Container;
use Lyndon\Route\Exceptions\RouteException;

/**
 * Class ActionRunner
 * @package Lyndon\Route\Action
 */
class ActionRunner
{
    const TAG = 'ActionRunner';

    /**
     * 运行Action类
     *
     * @param string $clazz
     * @param Request $request
     * @return mixed
     * @throws RouteException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function run($clazz, Request $request)
    {
        if (! class_exists($clazz)) {
            throw new RouteException(sprintf(
                self::TAG . ' run(), the action "%s" not exists.',
                $clazz
            ));
        }

        $instance = Container::getInstance()->make($clazz);
        if (! $instance instanceof AbstractAction) {
            throw new RouteException(sprintf(
                self::TAG . ' run(), the action "%s" is not instanceof AbstractAction.',
                $clazz
            ));
        }

        $method = strtoupper($request->method());
        if (! in_array($method, $instance->getMethods())) {
            throw new RouteException(self::TAG, sprintf(
                'Request\'s method "%s" be not supported, action "%s"\'s supported methods "%s"',
                $method, $instance->getName(), implode(',', $instance->getMethods())
            ));
        }

        return $instance->onRun($request);
    }
}