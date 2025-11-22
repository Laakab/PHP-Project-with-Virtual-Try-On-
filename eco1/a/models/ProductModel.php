<?php
require_once __DIR__ . '/Database.php';

class ProductModel {
    private $db;
    private $table = "products";

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function addProduct($productData) {
        try {
            $query = "INSERT INTO {$this->table} 
                     (name, category_id, image, color, quantity, description, size, 
                      price, delivery_price, return_days, discount, created_at) 
                     VALUES (:name, :category_id, :image, :color, :quantity, :description, 
                             :size, :price, :delivery_price, :return_days, :discount, NOW())";
            
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(":name", $productData['name']);
            $stmt->bindParam(":category_id", $productData['category_id']);
            $stmt->bindParam(":image", $productData['image']);
            $stmt->bindParam(":color", $productData['color']);
            $stmt->bindParam(":quantity", $productData['quantity']);
            $stmt->bindParam(":description", $productData['description']);
            $stmt->bindParam(":size", $productData['size']);
            $stmt->bindParam(":price", $productData['price']);
            $stmt->bindParam(":delivery_price", $productData['delivery_price']);
            $stmt->bindParam(":return_days", $productData['return_days']);
            $stmt->bindParam(":discount", $productData['discount']);
            
            if ($stmt->execute()) {
                return ["success" => true, "message" => "Product added successfully!"];
            } else {
                return ["success" => false, "message" => "Failed to add product."];
            }
        } catch(PDOException $e) {
            return ["success" => false, "message" => "Database error: " . $e->getMessage()];
        }
    }

    public function getAllProducts() {
        try {
            $query = "SELECT p.*, c.name as category_name 
                     FROM {$this->table} p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     ORDER BY p.created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }
       public function getProductById($productId) {
        try {
            $query = "SELECT p.*, c.name as category_name 
                     FROM {$this->table} p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     WHERE p.id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $productId);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }
    public function updateProduct($productData) {
        try {
            // If new image is provided, update image field too
            if (isset($productData['image'])) {
                $query = "UPDATE {$this->table} 
                         SET name = :name, category_id = :category_id, image = :image, 
                             color = :color, quantity = :quantity, description = :description, 
                             size = :size, price = :price, delivery_price = :delivery_price, 
                             return_days = :return_days, discount = :discount, updated_at = NOW()
                         WHERE id = :id";
            } else {
                $query = "UPDATE {$this->table} 
                         SET name = :name, category_id = :category_id, 
                             color = :color, quantity = :quantity, description = :description, 
                             size = :size, price = :price, delivery_price = :delivery_price, 
                             return_days = :return_days, discount = :discount, updated_at = NOW()
                         WHERE id = :id";
            }
            
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(":id", $productData['id']);
            $stmt->bindParam(":name", $productData['name']);
            $stmt->bindParam(":category_id", $productData['category_id']);
            $stmt->bindParam(":color", $productData['color']);
            $stmt->bindParam(":quantity", $productData['quantity']);
            $stmt->bindParam(":description", $productData['description']);
            $stmt->bindParam(":size", $productData['size']);
            $stmt->bindParam(":price", $productData['price']);
            $stmt->bindParam(":delivery_price", $productData['delivery_price']);
            $stmt->bindParam(":return_days", $productData['return_days']);
            $stmt->bindParam(":discount", $productData['discount']);
            
            // Bind image parameter only if new image is provided
            if (isset($productData['image'])) {
                $stmt->bindParam(":image", $productData['image']);
            }
            
            if ($stmt->execute()) {
                return ["success" => true, "message" => "Product updated successfully!"];
            } else {
                return ["success" => false, "message" => "Failed to update product."];
            }
        } catch(PDOException $e) {
            return ["success" => false, "message" => "Database error: " . $e->getMessage()];
        }
    }
     public function deleteProduct($productId) {
        try {
            // First get the product to delete its image file
            $product = $this->getProductById($productId);
            
            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $productId);
            
            if ($stmt->execute()) {
                // Delete the image file if it exists
                if ($product && !empty($product['image']) && file_exists($product['image'])) {
                    unlink($product['image']);
                }
                return ["success" => true, "message" => "Product deleted successfully!"];
            } else {
                return ["success" => false, "message" => "Failed to delete product."];
            }
        } catch(PDOException $e) {
            return ["success" => false, "message" => "Database error: " . $e->getMessage()];
        }
    }
}

?>