<?php
declare(strict_types=1);

class Router {
    private array $routes = [];

    public function get(string $key, callable $handler): void { $this->routes["GET:$key"] = $handler; }
    public function post(string $key, callable $handler): void { $this->routes["POST:$key"] = $handler; }

    public function run(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $r = $_GET['r'] ?? 'dashboard';

        $k = "$method:$r";
        if (!isset($this->routes[$k])) {
            http_response_code(404);
            echo "Rota não encontrada.";
            return;
        }
        ($this->routes[$k])();
    }
}
