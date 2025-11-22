<?php
require_once __DIR__ . '/Database.php';

class CategoryModel {
    private $db;
    private $table = "categories";

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function addCategory($categoryName) {
        try {
            // Check if category already exists
            $checkQuery = "SELECT id FROM {$this->table} WHERE name = :name";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(":name", $categoryName);
            $checkStmt->execute();

            if ($checkStmt->rowCount() > 0) {
                return ["success" => false, "message" => "Category already exists!"];
            }

            // Insert new category
            $query = "INSERT INTO {$this->table} (name, created_at) VALUES (:name, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":name", $categoryName);
            
            if ($stmt->execute()) {
                return ["success" => true, "message" => "Category added successfully!"];
            } else {
                return ["success" => false, "message" => "Failed to add category."];
            }
        } catch(PDOException $e) {
            return ["success" => false, "message" => "Database error: " . $e->getMessage()];
        }
    }

public function getAllCategories() {
    try {
        $query = "SELECT id, name, created_at FROM {$this->table} ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return [];
    }
}

    public function getCategoryByName($name) {
        try {
            $query = "SELECT id FROM {$this->table} WHERE name = :name";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":name", $name);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['id'] : null;
        } catch(PDOException $e) {
            return null;
        }
    }
    // Add these methods to the CategoryModel class

public function updateCategory($categoryId, $newName) {
    try {
        // Check if new name already exists (excluding current category)
        $checkQuery = "SELECT id FROM {$this->table} WHERE name = :name AND id != :id";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(":name", $newName);
        $checkStmt->bindParam(":id", $categoryId);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            return ["success" => false, "message" => "Category name already exists!"];
        }

        // Update category
        $query = "UPDATE {$this->table} SET name = :name, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":name", $newName);
        $stmt->bindParam(":id", $categoryId);
        
        if ($stmt->execute()) {
            return ["success" => true, "message" => "Category updated successfully!"];
        } else {
            return ["success" => false, "message" => "Failed to update category."];
        }
    } catch(PDOException $e) {
        return ["success" => false, "message" => "Database error: " . $e->getMessage()];
    }
}

public function deleteCategory($categoryId) {
    try {
        // First check if category is being used by any products
        $checkQuery = "SELECT COUNT(*) as product_count FROM products WHERE category_id = :id";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(":id", $categoryId);
        $checkStmt->execute();
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($result['product_count'] > 0) {
            return ["success" => false, "message" => "Cannot delete category. It is being used by products."];
        }

        // Delete category
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $categoryId);
        
        if ($stmt->execute()) {
            return ["success" => true, "message" => "Category deleted successfully!"];
        } else {
            return ["success" => false, "message" => "Failed to delete category."];
        }
    } catch(PDOException $e) {
        return ["success" => false, "message" => "Database error: " . $e->getMessage()];
    }
}
}
?>