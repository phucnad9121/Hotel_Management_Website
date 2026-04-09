<?php

class ApiRouter {
    private $request;
    private $response;
    private $routes = [];

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }

    public function get($pattern, $handler) {
        $this->add('GET', $pattern, $handler);
    }

    public function post($pattern, $handler) {
        $this->add('POST', $pattern, $handler);
    }

    public function put($pattern, $handler) {
        $this->add('PUT', $pattern, $handler);
    }

    public function delete($pattern, $handler) {
        $this->add('DELETE', $pattern, $handler);
    }

    public function options($pattern, $handler) {
        $this->add('OPTIONS', $pattern, $handler);
    }

    public function add($method, $pattern, $handler) {
        $pattern = $this->normalizePath($pattern);
        list($regex, $params) = $this->compilePattern($pattern);

        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'regex' => $regex,
            'params' => $params,
            'handler' => $handler,
        ];
    }

    public function run() {
        if ($this->request->method() === 'OPTIONS') {
            $this->response->noContent();
            return;
        }

        try {
            $path = $this->normalizePath($this->request->path());
            $method = strtoupper($this->request->method());

            foreach ($this->routes as $route) {
                if ($route['method'] !== $method) {
                    continue;
                }

                if (!preg_match($route['regex'], $path, $matches)) {
                    continue;
                }

                $params = [];
                foreach ($route['params'] as $name) {
                    if (isset($matches[$name])) {
                        $params[$name] = urldecode($matches[$name]);
                    }
                }

                $this->request->setRouteParams($params);
                $this->dispatch($route['handler'], $params);
                return;
            }

            $this->response->json([
                'success' => false,
                'message' => 'Route not found',
            ], 404);
        } catch (Throwable $e) {
            $this->response->json([
                'success' => false,
                'message' => 'Server error',
                'errors' => [
                    'detail' => $e->getMessage(),
                ],
            ], 500);
        }
    }

    private function dispatch($handler, array $params) {
        if (is_callable($handler)) {
            call_user_func($handler, $this->request, $this->response, $params);
            return;
        }

        if (!is_string($handler) || strpos($handler, '@') === false) {
            throw new Exception('Invalid route handler.');
        }

        list($controllerName, $method) = explode('@', $handler, 2);
        $controllerName = ucfirst(trim($controllerName));
        $controllerFile = __DIR__ . '/../../Application/Controllers/' . $controllerName . '.php';
        $controllerClass = 'Controllers' . $controllerName;

        if (!file_exists($controllerFile)) {
            throw new Exception('Controller file not found: ' . $controllerFile);
        }

        require_once $controllerFile;

        if (!class_exists($controllerClass)) {
            throw new Exception('Controller class not found: ' . $controllerClass);
        }

        $controller = new $controllerClass($this->request, $this->response);
        if (!method_exists($controller, $method)) {
            throw new Exception('Controller method not found: ' . $controllerClass . '@' . $method);
        }

        $reflection = new ReflectionMethod($controller, $method);
        if ($reflection->getNumberOfParameters() > 0) {
            $controller->$method($params);
        } else {
            $controller->$method();
        }
    }

    private function compilePattern($pattern) {
        $params = [];
        $tokenized = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}|:([a-zA-Z_][a-zA-Z0-9_]*)/', function ($matches) use (&$params) {
            $name = $matches[1] ?: $matches[2];
            $params[] = $name;
            return '__PARAM__' . $name . '__';
        }, $pattern);

        $regex = preg_quote($tokenized, '#');
        foreach ($params as $name) {
            $placeholder = preg_quote('__PARAM__' . $name . '__', '#');
            $regex = str_replace($placeholder, '(?P<' . $name . '>[^/]+)', $regex);
        }

        return ['#^' . $regex . '$#', $params];
    }

    private function normalizePath($path) {
        $path = '/' . ltrim((string) $path, '/');
        $path = preg_replace('#/+#', '/', $path);
        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/');
        }
        return $path;
    }
}
