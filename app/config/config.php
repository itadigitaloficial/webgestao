<?php
function env(string $key, string $default = ''): string {
    static $data = null;
    if ($data === null) {
        $data = [];
        $path = __DIR__ . '/../../.env';
        if (file_exists($path)) {
            foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
                [$k, $v] = explode('=', $line, 2);
                $k = trim($k);
                $v = trim($v);
                $v = trim($v, "\"'");
                $data[$k] = $v;
            }
        }
    }
    return $data[$key] ?? $default;
}

define('APP_NAME', env('APP_NAME', 'Sistema'));
define('APP_URL', env('APP_URL', 'http://localhost'));
define('APP_ENV', env('APP_ENV', 'local'));
