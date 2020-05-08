<?php

namespace Snowdog\DevTest\Component;

use FastRoute\RouteCollector;

class RouteRepository
{
    private static $instance = null;
    private $routes = [];
    const HTTP_METHOD = 'http_method';
    const ROUTE = 'route';
    const CLASS_NAME = 'class_name';
    const METHOD_NAME = 'method_name';
    const USER_RESTRICTION = 'user_restriction';

    /**
     * @return RouteRepository
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function registerRoute($httpMethod, $route, $className, $methodName, $userRestriction = null)
    {
        $instance = self::getInstance();
        $instance->addRoute($httpMethod, $route, $className, $methodName, $userRestriction);
    }

    public function __invoke(RouteCollector $r)
    {
        foreach ($this->routes as $route) {
            $r->addRoute(
                $route[self::HTTP_METHOD],
                $route[self::ROUTE],
                [
                    $route[self::CLASS_NAME],
                    $route[self::METHOD_NAME],
                    $route[self::USER_RESTRICTION]
                ]
            );
        }
    }

    private function addRoute($httpMethod, $route, $className, $methodName, $userRestriction)
    {
        $this->routes[] = [
            self::HTTP_METHOD => $httpMethod,
            self::ROUTE => $route,
            self::CLASS_NAME => $className,
            self::METHOD_NAME => $methodName,
            self::USER_RESTRICTION => $userRestriction
        ];
    }
}
