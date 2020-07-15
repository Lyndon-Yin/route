<?php
namespace Lyndon\Route\Action;

use Illuminate\Http\Request;
use Lyndon\Route\Exceptions\RouteException;

/**
 * Class Path4Router
 * @package Lyndon\Route\Action
 */
class Path4Router
{
    const TAG = 'Path4Router';

    /**
     * 路由参数个数：包括接口类型
     */
    const SEGMENTS_NUM = 4;

    /**
     * Actions目录
     */
    const ACTIONS_DIRECTORY = 'App\\Http\\Controllers';

    /**
     * 分析路由，并执行Action
     *
     * @param Request $request
     * @return array|string
     */
    public static function route(Request $request)
    {
        try {
            $segments = self::analyzeUri($request->segments());
            $action     = array_pop($segments);
            $controller = array_pop($segments);
            $module     = array_pop($segments);
            $appType    = array_pop($segments);

            $actionName = self::actionName($appType, $module, $controller, $action);

            return ActionRunner::run($actionName, $request);
        } catch (\Exception $e) {
            return sprintf(
                "%s in %s file at %s line\r%s",
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString()
            );
        }
    }

    /**
     * 获取Action类名，包括包名
     *
     * @param string $appType
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return string
     * @throws RouteException
     */
    public static function actionName($appType, $module, $controller, $action)
    {
        if (($appType = trim($appType)) === '') {
            throw new RouteException(self::TAG . ' actionName(), unable to find the requested appType empty.');
        }

        if (($module = trim($module)) === '') {
            throw new RouteException(self::TAG . ' actionName(), unable to find the requested module empty.');
        }

        if (($controller = trim($controller)) === '') {
            throw new RouteException(self::TAG . ' actionName(), unable to find the requested controller empty.');
        }

        if (($action = trim($action)) === '') {
            throw new RouteException(self::TAG . ' actionName(), unable to find the requested action empty.');
        }

        return self::ACTIONS_DIRECTORY . '\\' . $appType . '\\' . $module . '\\' . $controller . '\\' . $action;
    }

    /**
     * 分析路由，获取接口类型、Module名、Controller名、Action名
     *
     * @param array $segments
     * @return array
     * @throws RouteException
     */
    public static function analyzeUri($segments)
    {
        if (! is_array($segments)) {
            throw new RouteException(self::TAG . ' analyzeUri(), the requested segments not array.');
        }

        $num = count($segments);
        if ($num !== self::SEGMENTS_NUM) {
            throw new RouteException(sprintf(
                self::TAG . ' analyzeUri(), the requested segments count "%d" wrong, must be "%d".',
                $num, self::SEGMENTS_NUM
            ));
        }

        $appType    = array_shift($segments);
        $module     = array_shift($segments);
        $controller = array_shift($segments);
        $action     = array_shift($segments);

        RouterParams::setAppType($appType);
        RouterParams::setModule($module);
        RouterParams::setController($controller);
        RouterParams::setAction($action);

        return [
            RouterParams::getAppType(true),
            RouterParams::getModule(true),
            RouterParams::getController(true),
            RouterParams::getAction(true)
        ];
    }
}