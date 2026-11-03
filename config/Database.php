<?php
class Database {
    private string $host = "localhost";
    private string $db_name = "shop_db";
    private string $username = "root";
    private string $password = "";
    private ?PDO $conn = null;

    public function getConnection(): ?PDO {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $exception) {
            http_response_code(500);
            echo json_encode(["message" => "Erreur de connexion : " . $exception->getMessage()]);
            exit;
        }
        return $this->conn;
    }
}
