<?php
require_once __DIR__ . '/Database.php';

class OfferModel {
    private $db;
    private $table = "offers";

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function createOffer($offerData) {
        try {
            $query = "INSERT INTO {$this->table} 
                     (title, product_id, description, discount, image_path, 
                      start_date, end_date, status) 
                     VALUES (:title, :product_id, :description, :discount, :image_path, 
                             :start_date, :end_date, :status)";
            
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(":title", $offerData['title']);
            $stmt->bindParam(":product_id", $offerData['product_id']);
            $stmt->bindParam(":description", $offerData['description']);
            $stmt->bindParam(":discount", $offerData['discount']);
            $stmt->bindParam(":image_path", $offerData['image_path']);
            $stmt->bindParam(":start_date", $offerData['start_date']);
            $stmt->bindParam(":end_date", $offerData['end_date']);
            $stmt->bindParam(":status", $offerData['status']);
            
            if ($stmt->execute()) {
                return [
                    "success" => true, 
                    "message" => "Offer created successfully!",
                    "offer_id" => $this->db->lastInsertId()
                ];
            } else {
                return ["success" => false, "message" => "Failed to create offer."];
            }
        } catch(PDOException $e) {
            return ["success" => false, "message" => "Database error: " . $e->getMessage()];
        }
    }

    public function getAllOffers() {
        try {
            $query = "SELECT o.*, p.name as product_name 
                     FROM {$this->table} o 
                     LEFT JOIN products p ON o.product_id = p.id 
                     ORDER BY o.created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Get All Offers Error: " . $e->getMessage());
            return [];
        }
    }

    public function getOfferById($id) {
        try {
            $query = "SELECT o.*, p.name as product_name 
                     FROM {$this->table} o 
                     LEFT JOIN products p ON o.product_id = p.id 
                     WHERE o.id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Get Offer By ID Error: " . $e->getMessage());
            return null;
        }
    }

    public function updateOffer($id, $offerData) {
        try {
            if (isset($offerData['image_path'])) {
                // Update with new image
                $query = "UPDATE {$this->table} 
                         SET title = :title, product_id = :product_id, description = :description, 
                             discount = :discount, image_path = :image_path, start_date = :start_date, 
                             end_date = :end_date, status = :status, updated_at = NOW() 
                         WHERE id = :id";
            } else {
                // Update without changing image
                $query = "UPDATE {$this->table} 
                         SET title = :title, product_id = :product_id, description = :description, 
                             discount = :discount, start_date = :start_date, 
                             end_date = :end_date, status = :status, updated_at = NOW() 
                         WHERE id = :id";
            }
            
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":title", $offerData['title']);
            $stmt->bindParam(":product_id", $offerData['product_id']);
            $stmt->bindParam(":description", $offerData['description']);
            $stmt->bindParam(":discount", $offerData['discount']);
            $stmt->bindParam(":start_date", $offerData['start_date']);
            $stmt->bindParam(":end_date", $offerData['end_date']);
            $stmt->bindParam(":status", $offerData['status']);
            
            if (isset($offerData['image_path'])) {
                $stmt->bindParam(":image_path", $offerData['image_path']);
            }
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Update Offer Error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteOffer($id) {
        try {
            // First get the image path to delete the file
            $offer = $this->getOfferById($id);
            
            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $result = $stmt->execute();
            
            // Delete image file if offer was deleted successfully
            if ($result && $offer && !empty($offer['image_path']) && file_exists($offer['image_path'])) {
                unlink($offer['image_path']);
            }
            
            return $result;
        } catch(PDOException $e) {
            error_log("Delete Offer Error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteExpiredOffers() {
        try {
            $query = "DELETE FROM {$this->table} WHERE end_date < CURDATE()";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $deletedCount = $stmt->rowCount();
            return $deletedCount;
        } catch(PDOException $e) {
            error_log("Delete Expired Offers Error: " . $e->getMessage());
            return 0;
        }
    }

    public function getActiveOffers() {
        try {
            $query = "SELECT o.*, p.name as product_name 
                     FROM {$this->table} o 
                     LEFT JOIN products p ON o.product_id = p.id 
                     WHERE o.status = 'active' 
                     AND o.start_date <= CURDATE() 
                     AND o.end_date >= CURDATE() 
                     ORDER BY o.created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }
}
?>