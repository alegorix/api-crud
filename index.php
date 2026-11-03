<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/controllers/ProductController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriParts = explode('/', trim($uri, '/'));

// Si on accède à la racine, afficher la vue HTML
if (empty($uriParts[0]) || end($uriParts) === 'index.php') {
    include __DIR__ . '/views/index.html';
    exit;
}

// Router pour l'API : /api/products ou /api/products/{id}
$apiIndex = array_search('api', $uriParts);
if ($apiIndex !== false && isset($uriParts[$apiIndex + 1]) && $uriParts[$apiIndex + 1] === 'products') {
    $id = isset($uriParts[$apiIndex + 2]) && is_numeric($uriParts[$apiIndex + 2]) ? (int)$uriParts[$apiIndex + 2] : null;

    $database = new Database();
    $db = $database->getConnection();

    $controller = new ProductController($db);
    $controller->processRequest($_SERVER['REQUEST_METHOD'], $id);
    exit;
}

// Renvoyer les assets statiques s'ils sont demandés
if (preg_match('/\.css$/', $uri)) {
    header("Content-Type: text/css");
    readfile(__DIR__ . '/views/style.css');
    exit;
}

http_response_code(404);
echo json_encode(["message" => "Route introuvable"]);
