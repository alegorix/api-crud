<?php
class Product {
    private PDO $conn;
    private string $table = "products";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function getAll(): array {
        $query = "SELECT * FROM {$this->table} ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(string $name, float $price): bool {
        $query = "INSERT INTO {$this->table} (name, price) VALUES (:name, :price)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':name', htmlspecialchars(strip_tags($name)));
        $stmt->bindValue(':price', $price);
        return $stmt->execute();
    }

    public function update(int $id, string $name, float $price): bool {
        $query = "UPDATE {$this->table} SET name = :name, price = :price WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':name', htmlspecialchars(strip_tags($name)));
        $stmt->bindValue(':price', $price);
        return $stmt->execute();
    }

    public function delete(int $id): bool {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
