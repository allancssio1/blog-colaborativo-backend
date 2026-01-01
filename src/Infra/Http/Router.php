<?php

namespace App\Infra\Http;

class Router
{
    private array $routes = [];

    public function post(string $path, callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }
    public function get(string $path, callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function put(string $path, callable $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, callable $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, callable $handler): void
    {
        $this->routes[] = [
          'method' => $method,
          'path' => $path,
          'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $path, &$params): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->pathMatches($route['path'], $path, $params)) {
                call_user_func_array($route['handler'], $params);
                return;
            }
        }
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }

    private function pathMatches(string $pattern, string $path, &$params): bool
    {
        $pattern = preg_replace('/{([^}]+)}/', '(?P<$1>[^/]+)', $pattern);
        $pattern = "#^{$pattern}$#";

        if (preg_match($pattern, $path, $matches)) {
            $params = array_filter($matches, fn ($key) => !is_numeric($key), ARRAY_FILTER_USE_KEY);
            return true;
        }

        return false;
    }
}
