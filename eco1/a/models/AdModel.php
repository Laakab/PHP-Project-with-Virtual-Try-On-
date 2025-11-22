<?php
// AdModel.php - Updated with new methods
require_once __DIR__ . '/Database.php';

class AdModel {
    private $db;
    private $table = "ads";

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function createAd($adData) {
        try {
            $query = "INSERT INTO {$this->table} 
                     (title, description, phone, email, company_name, link, image_path, 
                      start_datetime, end_datetime, status) 
                     VALUES (:title, :description, :phone, :email, :company_name, :link, 
                             :image_path, :start_datetime, :end_datetime, :status)";
            
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(":title", $adData['title']);
            $stmt->bindParam(":description", $adData['description']);
            $stmt->bindParam(":phone", $adData['phone']);
            $stmt->bindParam(":email", $adData['email']);
            $stmt->bindParam(":company_name", $adData['company_name']);
            $stmt->bindParam(":link", $adData['link']);
            $stmt->bindParam(":image_path", $adData['image_path']);
            $stmt->bindParam(":start_datetime", $adData['start_datetime']);
            $stmt->bindParam(":end_datetime", $adData['end_datetime']);
            $stmt->bindParam(":status", $adData['status']);
            
            if ($stmt->execute()) {
                return [
                    "success" => true, 
                    "message" => "Advertisement created successfully!",
                    "ad_id" => $this->db->lastInsertId()
                ];
            } else {
                return ["success" => false, "message" => "Failed to create advertisement."];
            }
        } catch(PDOException $e) {
            return ["success" => false, "message" => "Database error: " . $e->getMessage()];
        }
    }

    public function updateAd($adData) {
        try {
            if (isset($adData['image_path'])) {
                // Update with new image
                $query = "UPDATE {$this->table} SET 
                         title = :title, 
                         description = :description, 
                         phone = :phone, 
                         email = :email, 
                         company_name = :company_name, 
                         link = :link, 
                         image_path = :image_path, 
                         start_datetime = :start_datetime, 
                         end_datetime = :end_datetime, 
                         status = :status,
                         updated_at = NOW()
                         WHERE id = :id";
            } else {
                // Update without changing image
                $query = "UPDATE {$this->table} SET 
                         title = :title, 
                         description = :description, 
                         phone = :phone, 
                         email = :email, 
                         company_name = :company_name, 
                         link = :link, 
                         start_datetime = :start_datetime, 
                         end_datetime = :end_datetime, 
                         status = :status,
                         updated_at = NOW()
                         WHERE id = :id";
            }
            
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(":id", $adData['id']);
            $stmt->bindParam(":title", $adData['title']);
            $stmt->bindParam(":description", $adData['description']);
            $stmt->bindParam(":phone", $adData['phone']);
            $stmt->bindParam(":email", $adData['email']);
            $stmt->bindParam(":company_name", $adData['company_name']);
            $stmt->bindParam(":link", $adData['link']);
            $stmt->bindParam(":start_datetime", $adData['start_datetime']);
            $stmt->bindParam(":end_datetime", $adData['end_datetime']);
            $stmt->bindParam(":status", $adData['status']);
            
            if (isset($adData['image_path'])) {
                $stmt->bindParam(":image_path", $adData['image_path']);
            }
            
            if ($stmt->execute()) {
                return [
                    "success" => true, 
                    "message" => "Advertisement updated successfully!"
                ];
            } else {
                return ["success" => false, "message" => "Failed to update advertisement."];
            }
        } catch(PDOException $e) {
            return ["success" => false, "message" => "Database error: " . $e->getMessage()];
        }
    }

    public function getAdById($adId) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $adId);
            $stmt->execute();
            
            $ad = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($ad) {
                return [
                    "success" => true,
                    "ad" => $ad
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "Advertisement not found"
                ];
            }
        } catch(PDOException $e) {
            return ["success" => false, "message" => "Database error: " . $e->getMessage()];
        }
    }

    public function deleteAd($adId) {
        try {
            // First get the ad to delete the image file
            $ad = $this->getAdById($adId);
            
            $query = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $adId);
            
            if ($stmt->execute()) {
                // Delete the associated image file
                if ($ad['success'] && isset($ad['ad']['image_path']) && file_exists($ad['ad']['image_path'])) {
                    unlink($ad['ad']['image_path']);
                }
                
                return [
                    "success" => true, 
                    "message" => "Advertisement deleted successfully!"
                ];
            } else {
                return ["success" => false, "message" => "Failed to delete advertisement."];
            }
        } catch(PDOException $e) {
            return ["success" => false, "message" => "Database error: " . $e->getMessage()];
        }
    }

    public function getAllAds() {
        try {
            $query = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    public function getActiveAds() {
        try {
            $query = "SELECT * FROM {$this->table} 
                     WHERE status = 'active' 
                     AND start_datetime <= NOW() 
                     AND end_datetime >= NOW() 
                     ORDER BY created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    public function deleteExpiredAds() {
        try {
            // Get expired ads to delete their images
            $querySelect = "SELECT image_path FROM {$this->table} WHERE end_datetime < NOW()";
            $stmtSelect = $this->db->prepare($querySelect);
            $stmtSelect->execute();
            $expiredAds = $stmtSelect->fetchAll(PDO::FETCH_ASSOC);
            
            // Delete the ads
            $query = "DELETE FROM {$this->table} WHERE end_datetime < NOW()";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            // Delete associated image files
            foreach ($expiredAds as $ad) {
                if (!empty($ad['image_path']) && file_exists($ad['image_path'])) {
                    unlink($ad['image_path']);
                }
            }
            
            $deletedCount = $stmt->rowCount();
            return $deletedCount;
        } catch(PDOException $e) {
            error_log("Delete Expired Ads Error: " . $e->getMessage());
            return 0;
        }
    }

    public function updateAdStatus($adId, $status) {
        try {
            $query = "UPDATE {$this->table} SET status = :status, updated_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":id", $adId);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Update Ad Status Error: " . $e->getMessage());
            return false;
        }
    }
}
?>