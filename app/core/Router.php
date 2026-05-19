<?php

class Router
{
    private $routes = [];

    public function get($path, $handler, $role = 'user') {
        $this->routes['GET'][$path] = ['handler' => $handler, 'role' => $role];
    }

    public function post($path, $handler, $role = 'user') {
        $this->routes['POST'][$path] = ['handler' => $handler, 'role' => $role];
    }

    public function resolve()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $base = dirname($_SERVER['SCRIPT_NAME']);
        if ($base !== '/' && strpos($path, $base) === 0) {
            $path = substr($path, strlen($base));
        }
        $uri = $path;

        $uri = (empty($uri)) ? '/' : rtrim($uri, '/');
        if ($uri === '') $uri = '/';

        if (!isset($this->routes[$method])) {
            $this->render404();
            return;
        }

        $routes = $this->routes[$method];

        $routeList = [];
        foreach ($routes as $path => $config) {
            $paramCount = substr_count($path, '{');
            $routeList[] = [
                'path'       => $path,
                'config'     => $config,
                'paramCount' => $paramCount,
                'length'     => strlen($path),
            ];
        }

        usort($routeList, function($a, $b) {
            if ($a['paramCount'] !== $b['paramCount']) {
                return $a['paramCount'] - $b['paramCount'];
            }
            return $b['length'] - $a['length'];
        });

        foreach ($routeList as $item) {
            $route  = $item['path'];
            $config = $item['config'];

            $routePath = ($route === '/') ? '/' : rtrim($route, '/');
            $pattern   = preg_replace('/\{[a-zA-Z_]+\}/', '([^\/]+)', $routePath);
            $pattern   = "#^" . $pattern . "$#";

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                $handler = $config['handler'];
                $role    = $config['role'];

                list($controllerName, $action) = explode('@', $handler);

                $controllerFile = ROOT . "/app/controllers/$role/$controllerName.php";

                

                if (!file_exists($controllerFile)) {
                    die("❌ Không tìm thấy file: " . $controllerFile);
                }

                require_once $controllerFile;

                if (!class_exists($controllerName)) {
                    die("❌ Class không tồn tại: " . $controllerName);
                }

                $controller = new $controllerName();

                if (!method_exists($controller, $action)) {
                    die("❌ Method không tồn tại: $action trong $controllerName");
                }

                call_user_func_array([$controller, $action], $matches);
                return;
            }
        }

        $this->render404();
        exit;
    }

    private function render404() {
        header("HTTP/1.0 404 Not Found");

        $base       = APP_URL;
        $headerPath = ROOT . "/views/user/layouts/header.php";
        $viewPath   = ROOT . "/views/user/errors/404.php";
        $footerPath = ROOT . "/views/user/layouts/footer.php";

        if (file_exists($headerPath)) require_once $headerPath;
        if (file_exists($viewPath))   require_once $viewPath;
        else echo "<h1>404 - Trang không tồn tại</h1>";
        if (file_exists($footerPath)) require_once $footerPath;
        exit;
    }
}