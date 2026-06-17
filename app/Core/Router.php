<?php

namespace App\Core;

/**
 * Router — Simple URL dispatcher
 *
 * Supports static paths and :param placeholders.
 * Middleware closures are executed before the controller action.
 *
 * Usage (in public/index.php):
 *   $router->get('/admin/users', [AdminController::class, 'users'], [$requireAdmin]);
 *   $router->post('/login',      [AuthController::class,  'login']);
 *   $router->dispatch();
 */
class Router
{
    /** @var array<int, array{method:string, path:string, handler:array, middleware:array}> */
    private array $routes = [];

    public function get(string $path, array $handler, array $middleware = []): self
    {
        $this->add('GET', $path, $handler, $middleware);
        return $this;
    }

    public function post(string $path, array $handler, array $middleware = []): self
    {
        $this->add('POST', $path, $handler, $middleware);
        return $this;
    }

    private function add(string $method, string $path, array $handler, array $middleware): void
    {
        $this->routes[] = compact('method', 'path', 'handler', 'middleware');
    }

    /** Match the current request and dispatch to the correct controller */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = $this->currentUri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            if ($this->match($route['path'], $uri, $params)) {
                // Run middleware stack
                foreach ($route['middleware'] as $mw) {
                    $mw();
                }
                // Instantiate controller and call action
                [$class, $action] = $route['handler'];
                (new $class())->$action($params);
                return;
            }
        }

        // No route matched — 404
        http_response_code(404);
        if (file_exists(VIEW_PATH . '/errors/404.php')) {
            require VIEW_PATH . '/errors/404.php';
        } else {
            echo '<h1>404 — Page Not Found</h1>';
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** Strip BASE_PATH prefix and normalise the URI */
    private function currentUri(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

        // Remove BASE_PATH prefix (e.g. /comclz)
        $base = rtrim(BASE_PATH, '/');
        if ($base !== '' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }

        $uri = '/' . ltrim($uri, '/');

        return $uri === '' ? '/' : $uri;
    }

    /**
     * Try to match a route pattern against the current URI.
     * :param placeholders capture single path segments.
     *
     * @param string     $pattern  Route definition (e.g. '/user/:id')
     * @param string     $uri      Actual request URI
     * @param array|null $params   Captured :param values (by reference)
     */
    private function match(string $pattern, string $uri, ?array &$params): bool
    {
        $params  = [];
        $regex   = preg_replace('/:[a-zA-Z_][a-zA-Z0-9_]*/', '([^/]+)', $pattern);
        $regex   = '@^' . $regex . '$@';

        if (preg_match($regex, $uri, $matches)) {
            array_shift($matches);   // remove full-match entry
            $params = $matches;
            return true;
        }
        return false;
    }
}
