<?php
class Router {
    private $routes = [];

    public function add($method, $path, $controllerAction) {
        // Convert route pattern to regular expression
        // e.g., /collections/{slug} -> ^/collections/(?P<slug>[^/]+)$
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#i';

        $this->routes[] = [
            'method'     => strtoupper($method),
            'pattern'    => $pattern,
            'path'       => $path,
            'target'     => $controllerAction
        ];
    }

    public function dispatch($url, $requestMethod) {
        // Clean URL
        $url = parse_url($url, PHP_URL_PATH);
        
        // Remove subfolder prefix if hosted in subfolder e.g. /darjanafashon_new/public
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseFolder = dirname($scriptName);
        if ($baseFolder !== '/' && $baseFolder !== '\\' && strpos($url, $baseFolder) === 0) {
            $url = substr($url, strlen($baseFolder));
        }
        
        $url = '/' . trim($url, '/');
        if ($url === '//') $url = '/';

        $requestMethod = strtoupper($requestMethod);

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && preg_match($route['pattern'], $url, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                list($controllerName, $action) = explode('@', $route['target']);
                
                $controllerFile = __DIR__ . '/../app/Controllers/' . $controllerName . '.php';
                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    $controller = new $controllerName();
                    if (method_exists($controller, $action)) {
                        call_user_func_array([$controller, $action], $params);
                        return;
                    }
                }
            }
        }

        // 404 Not Found
        http_response_code(404);
        require_once __DIR__ . '/../app/Views/layouts/header.php';
        echo '<div style="padding: 100px 20px; text-align: center; font-family: sans-serif;">
                <h1 style="font-size: 48px; color: #181818;">404 Page Not Found</h1>
                <p style="color: #666; font-size: 18px;">The page you are looking for does not exist in Dar Jana Fashion.</p>
                <a href="' . (defined('BASE_URL') ? BASE_URL : '/') . '" style="display: inline-block; margin-top: 20px; padding: 12px 30px; background: #181818; color: #fff; text-decoration: none; text-transform: uppercase; letter-spacing: 0.15em; font-size: 14px;">Return to Home</a>
              </div>';
        require_once __DIR__ . '/../app/Views/layouts/footer.php';
    }
}
