<?php

namespace App\Router;

use App\Core\Request;
use App\Router\Attributes\Route as RouteAttribute;
use Symfony\Component\Filesystem\Path;
use App\Router\Exceptions\HttpNotFoundException;

class Router
{
    /**
     * The list of generated routes
     */
    private array $_routes = [];
    private array $_namedRoutes = [];

    public function __construct(
        private readonly Request $_request,
        private readonly string $_projectDir
    ) {
    }

    /**
     * Add the list of controllers to the router
     *
     * @param string $controllerClass The controller class to register
     *
     * @return void
     */
    private function _registerControllers(string $controllerClass): void
    {
        $class = new \ReflectionClass($controllerClass);
        $routeAttributes = $class->getAttributes(RouteAttribute::class);
        $prefix = "";
        $httpMethods = [];
        $namePrefix = "";

        if (!empty($routeAttributes)) {
            $instance = $routeAttributes[0]->newInstance();
            $prefix = $instance->path;
            $httpMethods = $instance->methods;
            $namePrefix = $instance->name;
        }

        foreach ($class->getMethods() as $method) {
            $attributes = $method->getAttributes(RouteAttribute::class);
            if (empty($attributes)) {
                continue;
            }

            foreach ($attributes as $attribute) {

                $route = $attribute->newInstance();

                foreach (
                    array_unique(
                        array_map(
                            fn ($httpMethod) => strtolower($httpMethod),
                            [...$httpMethods, ...$route->methods]
                        )
                    ) as $methodName
                ) {
                    $path = $prefix . $route->path;
                    $name = $route->name;
                    $methodName = strtolower($methodName);

                    if (!isset($this->_routes[$methodName])) {
                        $this->_routes[$methodName] = [];
                    }

                    if ($name) {
                        $this->_namedRoutes[$namePrefix . $name] = $path;
                    }

                    $this->_routes[$methodName][] = new Route(
                        path: $path,
                        callback: $method->getClosure($class->newInstance()),
                        requirements: $route->requirements
                    );
                }

            }
        }
    }

    /**
     * Get the list of all controllers in the project in the directory src/Controller
     *
     * @return array<string>
     */
    private function _getControllers(): array
    {
        $controllers = [];
        $path = Path::canonicalize($this->_projectDir . '/src/Controller');
        $files = glob($path . "/*.php");
        $files = array_filter(
            $files,
            function ($file) {
                return is_file($file) && basename($file) !== 'AbstractController.php';
            }
        );

        foreach ($files as $file) {
            $filename = basename($file, '.php');
            $classname = "App\\Controller\\" . ucfirst($filename);

            if (class_exists($classname)) {
                $controllers[] = $classname;
            }
        }

        return $controllers;
    }

    /**
     * Resolves the current request by matching it against the registered routes.
     *
     * @return void
     */
    public function handleRequest(): void
    {
        $controllers = $this->_getControllers();

        foreach ($controllers as $controller) {
            $this->_registerControllers($controller);
        }

        foreach ($this->_routes[$this->_request->getMethod()] ?? [] as $route) {
            if ($route->match($this->_request->getPathUri())) {
                $route->call();
                return;
            }
        }

        throw new HttpNotFoundException(
            "No route found for " . $this->_request->getMethod() . " " . $this->_request->getPathUri()
        );
    }

}
