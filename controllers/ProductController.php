<?php
require_once __DIR__ . '/../models/Product.php';

class ProductController {
    private Product $product;

    public function __construct(PDO $db) {
        $this->product = new Product($db);
    }

    public function processRequest(string $method, ?int $id): void {
        header("Content-Type: application/json; charset=UTF-8");

        switch ($method) {
            case 'GET':
                if ($id) {
                    $item = $this->product->getById($id);
                    if ($item) {
                        echo json_encode($item);
                    } else {
                        http_response_code(404);
                        echo json_encode(["message" => "Produit introuvable"]);
                    }
                } else {
                    echo json_encode($this->product->getAll());
                }
                break;

            case 'POST':
                $data = json_decode(file_get_contents("php://input"), true);
                if (!empty($data['name']) && isset($data['price'])) {
                    if ($this->product->create($data['name'], (float)$data['price'])) {
                        http_response_code(201);
                        echo json_encode(["message" => "Produit créé avec succès"]);
                    } else {
                        http_response_code(500);
                        echo json_encode(["message" => "Impossible de créer le produit"]);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(["message" => "Données incomplètes (nom et prix requis)"]);
                }
                break;

            case 'PUT':
                if (!$id) {
                    http_response_code(400);
                    echo json_encode(["message" => "ID requis pour la mise à jour"]);
                    return;
                }
                $data = json_decode(file_get_contents("php://input"), true);
                if (!empty($data['name']) && isset($data['price'])) {
                    if ($this->product->update($id, $data['name'], (float)$data['price'])) {
                        echo json_encode(["message" => "Produit mis à jour"]);
                    } else {
                        http_response_code(500);
                        echo json_encode(["message" => "Erreur lors de la mise à jour"]);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(["message" => "Données incomplètes"]);
                }
                break;

            case 'DELETE':
                if (!$id) {
                    http_response_code(400);
                    echo json_encode(["message" => "ID requis pour la suppression"]);
                    return;
                }
                if ($this->product->delete($id)) {
                    echo json_encode(["message" => "Produit supprimé"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["message" => "Erreur lors de la suppression"]);
                }
                break;

            default:
                http_response_code(405);
                header("Allow: GET, POST, PUT, DELETE");
                echo json_encode(["message" => "Méthode non autorisée"]);
                break;
        }
    }
}
