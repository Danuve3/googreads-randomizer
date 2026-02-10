<?php

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/src/Response.php';
require_once __DIR__ . '/src/Auth.php';
require_once __DIR__ . '/src/CsvParser.php';
require_once __DIR__ . '/src/BookRepository.php';
require_once __DIR__ . '/src/GenreCache.php';
require_once __DIR__ . '/src/GenreFetcher.php';

header('Content-Type: application/json; charset=utf-8');

// CORS for same-origin (useful for dev)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Public endpoint: login
if ($action === 'login' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $password = $input['password'] ?? '';
    if (!$password) {
        Response::error('Contraseña requerida');
    }
    Response::success(Auth::login($password));
}

// All other endpoints require auth
Auth::requireAuth();

switch ($action) {
    case 'stats':
        if ($method !== 'GET') Response::error('Método no permitido', 405);
        $repo = new BookRepository();
        Response::success($repo->stats());
        break;

    case 'random':
        if ($method !== 'GET') Response::error('Método no permitido', 405);
        $repo = new BookRepository();
        $book = $repo->random($_GET);
        if (!$book) {
            Response::error('No se encontraron libros con esos filtros', 404);
        }
        Response::success($book);
        break;

    case 'filters':
        if ($method !== 'GET') Response::error('Método no permitido', 405);
        $repo = new BookRepository();
        Response::success($repo->filters());
        break;

    case 'import':
        if ($method !== 'POST') Response::error('Método no permitido', 405);
        if (!isset($_FILES['csv'])) {
            Response::error('No se recibió ningún archivo');
        }
        $books = CsvParser::parseUpload($_FILES['csv']);
        Response::success([
            'count' => count($books),
            'message' => count($books) . ' libros importados correctamente',
        ]);
        break;

    case 'fetch-genres':
        if ($method !== 'POST') Response::error('Método no permitido', 405);
        $repo = new BookRepository();
        $books = $repo->getBooks();
        if (empty($books)) {
            Response::error('No hay libros importados');
        }
        $result = GenreFetcher::processBooks($books);
        Response::success($result);
        break;

    case 'genre-progress':
        if ($method !== 'GET') Response::error('Método no permitido', 405);
        $repo = new BookRepository();
        $books = $repo->getBooks();
        Response::success(GenreFetcher::getProgress($books));
        break;

    default:
        Response::error('Acción no encontrada', 404);
}
