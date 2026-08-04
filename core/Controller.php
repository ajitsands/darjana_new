<?php
abstract class Controller {
    protected function render($view, $data = [], $layout = 'default') {
        extract($data);
        
        // Start buffering for view output
        ob_start();
        $viewFile = __DIR__ . '/../app/Views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo "View file not found: " . $view;
        }
        $content = ob_get_clean();

        // Render inside header and footer layout
        if ($layout === 'default') {
            require __DIR__ . '/../app/Views/layouts/header.php';
            echo $content;
            require __DIR__ . '/../app/Views/layouts/footer.php';
        } else {
            echo $content;
        }
    }

    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    protected function redirect($url) {
        header("Location: " . $url);
        exit;
    }
}
