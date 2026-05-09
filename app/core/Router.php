<?php

class Router
{
    private $routes = [];

    // Đăng ký route GET (Mặc định role là user)
    public function get($path, $handler, $role = 'user') {
        $this->routes['GET'][$path] = ['handler' => $handler, 'role' => $role];
    }

    // Đăng ký route POST
    public function post($path, $handler, $role = 'user') {
        $this->routes['POST'][$path] = ['handler' => $handler, 'role' => $role];
    }

    public function resolve()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if (isset($_GET['url']) && $_GET['url'] !== '') {
            $uri = '/' . trim($_GET['url'], '/');
            

        } else {
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

            $base = dirname($_SERVER['SCRIPT_NAME']);
            if ($base !== '/' && strpos($path, $base) === 0) {
                $path = substr($path, strlen($base));
            }

            $uri = $path;
        }

        // Chuẩn hóa - giữ '/' nếu rỗng
        $uri = (empty($uri) || $uri === '') ? '/' : rtrim($uri, '/');
    
        if ($uri === '') $uri = '/';

        

        if (!isset($this->routes[$method])) {
            $this->render404();
            return;
        }

        // ✅ Sort: route nào ít {param} hơn thì ưu tiên match trước
        $routes = $this->routes[$method];
        uksort($routes, function($a, $b) {

        // số lượng param
        $countA = substr_count($a, '{');
        $countB = substr_count($b, '{');

        // ưu tiên route ít param hơn
        if ($countA !== $countB) {
            return $countA - $countB;
        }

        // nếu bằng param:
        // route dài hơn ưu tiên trước
        return strlen($b) - strlen($a);
    });

        foreach ($routes as $route => $config) {

            $routePath = ($route === '/') ? '/' : rtrim($route, '/');

            $pattern = preg_replace('/\{[a-zA-Z]+\}/', '([^\/]+)', $routePath);
            $pattern = "#^" . $pattern . "$#";


            if (preg_match($pattern, $uri, $matches)) {

                array_shift($matches);

                $handler = $config['handler'];
                $role = $config['role'];

                list($controllerName, $action) = explode('@', $handler);

                $controllerFile = ROOT . "/app/controllers/$role/$controllerName.php";

                if (file_exists($controllerFile)) {
                    require_once $controllerFile;

                    if (class_exists($controllerName)) {
                        $controller = new $controllerName();

                        if (method_exists($controller, $action)) {
                            call_user_func_array([$controller, $action], $matches);
                            return;
                        }
                        die("Method không tồn tại");
                    }
                    die("Class không tồn tại");
                }
                die("Không tìm thấy controller: " . $controllerFile);
            }
        }

        $this->render404();
        exit;
    }



    // Hàm hiển thị giao diện 404 có đầy đủ Header/Footer
    private function render404() {
        header("HTTP/1.0 404 Not Found");
        
        // Tạo biến $base cho Header/Footer
        $base = APP_URL; 
        
        $headerPath = ROOT . "/views/user/layouts/header.php";
        $viewPath   = ROOT . "/views/user/errors/404.php";
        $footerPath = ROOT . "/views/user/layouts/footer.php";

        if (file_exists($headerPath)) require_once $headerPath;
        if (file_exists($viewPath)) require_once $viewPath;
        else echo "<h1>404 - Trang không tồn tại</h1>";
        if (file_exists($footerPath)) require_once $footerPath;
        exit;
    }
}
