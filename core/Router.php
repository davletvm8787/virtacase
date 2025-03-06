<?php

namespace Core;

class Router {
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $uri, string $controllerAction) {
        $this->routes['GET'][$this->parseUri($uri)] = $controllerAction;
    }

    public function middleware(string $uri, callable $middleware) {
        $this->middlewares[$this->parseUri($uri)] = $middleware;
    }

    public function dispatch(string $requestUri, string $requestMethod) {
        $parsedUri = $this->parseUri($requestUri);

        // Middleware 
        if (isset($this->middlewares[$parsedUri])) {
            call_user_func($this->middlewares[$parsedUri]);
        }

        // Route eşleşmesini kontrol ediyorum 
        foreach ($this->routes[$requestMethod] as $route => $controllerAction) {
            if (preg_match($route, $parsedUri, $matches)) {
                array_shift($matches); // İlk elemanı kaldırıyorum  (tam eşleşme)

                list($controllerName, $method) = explode('@', $controllerAction);

                $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';
                if (!file_exists($controllerFile)) {
                    http_response_code(404);
                    echo "Controller $controllerName not found.";
                    return;
                }

                require_once $controllerFile;
                $controllerClass = "\\App\\Controllers\\$controllerName";

                if (!class_exists($controllerClass)) {
                    http_response_code(500);
                    echo "Controller class $controllerClass not found.";
                    return;
                }

                $controller = new $controllerClass();

                if (!method_exists($controller, $method)) {
                    http_response_code(500);
                    echo "Method $method not found in controller $controllerClass.";
                    return;
                }

                // Metodu çağırıyorum ve parametreleri iletıyorum
                echo call_user_func_array([$controller, $method], $matches);
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }

    private function parseUri(string $uri): string {
        $uri = trim(parse_url($uri, PHP_URL_PATH), '/');
        return "/^" . str_replace(['{id}', '{\d+}'], ['(\d+)', '(\d+)'], preg_quote($uri, '/')) . "$/";
    }
}
