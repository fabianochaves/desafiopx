<?php

// Configura exibição de erros
ini_set('display_errors', '0'); // Não exibe diretamente no navegador
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

// Captura warnings/notices/deprecated como exceção
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Captura exceções não tratadas
set_exception_handler(function ($e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'erro',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => explode("\n", $e->getTraceAsString()),
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
});

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\Cargas;

$method = $_SERVER['REQUEST_METHOD'];
$route = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$uri = explode('/', $route);

header('Content-Type: application/json');

try {
    if ($method === 'POST' && $route === 'carga') {
        (new Cargas)->registrar();
        return;
    }

    http_response_code(404);
    echo json_encode(['error' => 'Rota não encontrada']);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'erro',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => explode("\n", $e->getTraceAsString()),
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}