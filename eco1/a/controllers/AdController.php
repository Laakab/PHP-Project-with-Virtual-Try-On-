<?php
// AdController.php - Updated with delete functionality
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if model files exist
if (!file_exists('../models/AdModel.php')) {
    die(json_encode(["success" => false, "message" => "AdModel.php not found"]));
}
if (!file_exists('../models/Database.php')) {
    die(json_encode(["success" => false, "message" => "Database.php not found"]));
}

require_once '../models/Database.php';
require_once '../models/AdModel.php';

class AdController {
    private $adModel;

    public function __construct() {
        try {
            $this->adModel = new AdModel();
        } catch (Exception $e) {
            error_log("Controller Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Controller initialization failed"]);
            exit;
        }
    }

    public function createAd() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Get form data
                $title = trim($_POST['adsTitle'] ?? '');
                $description = trim($_POST['adsDescription'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $companyName = trim($_POST['companyName'] ?? '');
                $link = trim($_POST['link'] ?? '');
                $startDateTime = $_POST['startDateTime'] ?? '';
                $endDateTime = $_POST['endDateTime'] ?? '';
                $status = $_POST['status'] ?? 'pending';

                // Validate required fields
                if (empty($title) || empty($description) || empty($link) || 
                    empty($startDateTime) || empty($endDateTime)) {
                    echo json_encode(["success" => false, "message" => "Please fill all required fields!"]);
                    return;
                }

                // Validate dates
                if (strtotime($endDateTime) <= strtotime($startDateTime)) {
                    echo json_encode(["success" => false, "message" => "End date/time must be after start date/time!"]);
                    return;
                }

                // Handle image upload
                $imagePath = $this->handleImageUpload();
                if (!$imagePath) {
                    echo json_encode(["success" => false, "message" => "Please upload a valid image file!"]);
                    return;
                }

                // Prepare ad data
                $adData = [
                    'title' => $title,
                    'description' => $description,
                    'phone' => $phone,
                    'email' => $email,
                    'company_name' => $companyName,
                    'link' => $link,
                    'image_path' => $imagePath,
                    'start_datetime' => date('Y-m-d H:i:s', strtotime($startDateTime)),
                    'end_datetime' => date('Y-m-d H:i:s', strtotime($endDateTime)),
                    'status' => $status
                ];

                $result = $this->adModel->createAd($adData);
                
                // Delete uploaded image if ad creation failed
                if (!$result['success'] && file_exists($imagePath)) {
                    unlink($imagePath);
                }
                
                echo json_encode($result);

            } else {
                echo json_encode(["success" => false, "message" => "Invalid request method"]);
            }
        } catch (Exception $e) {
            error_log("Create Ad Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
        }
    }

    public function updateAd() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $adId = $_POST['adId'] ?? '';
                
                if (empty($adId)) {
                    echo json_encode(["success" => false, "message" => "Ad ID is required!"]);
                    return;
                }

                // Get form data
                $title = trim($_POST['adsTitle'] ?? '');
                $description = trim($_POST['adsDescription'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $companyName = trim($_POST['companyName'] ?? '');
                $link = trim($_POST['link'] ?? '');
                $startDateTime = $_POST['startDateTime'] ?? '';
                $endDateTime = $_POST['endDateTime'] ?? '';
                $status = $_POST['status'] ?? 'pending';

                // Validate required fields
                if (empty($title) || empty($description) || empty($link) || 
                    empty($startDateTime) || empty($endDateTime)) {
                    echo json_encode(["success" => false, "message" => "Please fill all required fields!"]);
                    return;
                }

                // Validate dates
                if (strtotime($endDateTime) <= strtotime($startDateTime)) {
                    echo json_encode(["success" => false, "message" => "End date/time must be after start date/time!"]);
                    return;
                }

                // Handle image upload (optional for update)
                $imagePath = $this->handleImageUpload();
                
                // Prepare ad data
                $adData = [
                    'id' => $adId,
                    'title' => $title,
                    'description' => $description,
                    'phone' => $phone,
                    'email' => $email,
                    'company_name' => $companyName,
                    'link' => $link,
                    'start_datetime' => date('Y-m-d H:i:s', strtotime($startDateTime)),
                    'end_datetime' => date('Y-m-d H:i:s', strtotime($endDateTime)),
                    'status' => $status
                ];

                // Only update image path if a new image was uploaded
                if ($imagePath) {
                    $adData['image_path'] = $imagePath;
                }

                $result = $this->adModel->updateAd($adData);
                
                echo json_encode($result);

            } else {
                echo json_encode(["success" => false, "message" => "Invalid request method"]);
            }
        } catch (Exception $e) {
            error_log("Update Ad Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Server error: " . $e->getMessage()]);
        }
    }

    public function getAdById() {
        try {
            $adId = $_GET['adId'] ?? '';
            
            if (empty($adId)) {
                echo json_encode(["success" => false, "message" => "Ad ID is required!"]);
                return;
            }

            $ad = $this->adModel->getAdById($adId);
            echo json_encode($ad);
        } catch (Exception $e) {
            error_log("Get Ad By ID Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Server error"]);
        }
    }

    public function deleteAd() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $adId = $_POST['adId'] ?? '';
                
                if (empty($adId)) {
                    echo json_encode(["success" => false, "message" => "Ad ID is required!"]);
                    return;
                }

                $result = $this->adModel->deleteAd($adId);
                echo json_encode($result);
            }
        } catch (Exception $e) {
            error_log("Delete Ad Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Server error"]);
        }
    }

    private function handleImageUpload() {
        if (!isset($_FILES['adsImage']) || $_FILES['adsImage']['error'] !== UPLOAD_ERR_OK) {
            return null; // Return null instead of false for optional updates
        }

        $file = $_FILES['adsImage'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        // Check file type and size
        if (!in_array($file['type'], $allowedTypes) || $file['size'] > $maxSize) {
            return false;
        }

        // Create uploads directory if not exists
        $uploadDir = '../uploads/ads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate unique filename
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
        $filePath = $uploadDir . $fileName;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return $filePath;
        }

        return false;
    }

    public function getAds() {
        try {
            $ads = $this->adModel->getAllAds();
            echo json_encode($ads);
        } catch (Exception $e) {
            error_log("Get Ads Error: " . $e->getMessage());
            echo json_encode([]);
        }
    }

    public function getActiveAds() {
        try {
            $ads = $this->adModel->getActiveAds();
            echo json_encode($ads);
        } catch (Exception $e) {
            error_log("Get Active Ads Error: " . $e->getMessage());
            echo json_encode([]);
        }
    }

    public function cleanupExpiredAds() {
        try {
            $deletedCount = $this->adModel->deleteExpiredAds();
            echo json_encode([
                "success" => true, 
                "message" => "Cleaned up {$deletedCount} expired ads",
                "deleted_count" => $deletedCount
            ]);
        } catch (Exception $e) {
            error_log("Cleanup Expired Ads Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Cleanup failed"]);
        }
    }

    public function updateAdStatus() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $adId = $_POST['adId'] ?? '';
                $status = $_POST['status'] ?? '';
                
                if (empty($adId) || empty($status)) {
                    echo json_encode(["success" => false, "message" => "Ad ID and status are required!"]);
                    return;
                }

                $success = $this->adModel->updateAdStatus($adId, $status);
                if ($success) {
                    echo json_encode(["success" => true, "message" => "Ad status updated successfully!"]);
                } else {
                    echo json_encode(["success" => false, "message" => "Failed to update ad status"]);
                }
            }
        } catch (Exception $e) {
            error_log("Update Ad Status Error: " . $e->getMessage());
            echo json_encode(["success" => false, "message" => "Server error"]);
        }
    }
}

// Handle requests
try {
    if (isset($_GET['action'])) {
        $controller = new AdController();
        
        switch ($_GET['action']) {
            case 'create':
                $controller->createAd();
                break;
            case 'update':
                $controller->updateAd();
                break;
            case 'get_ad':
                $controller->getAdById();
                break;
            case 'get_ads':
                $controller->getAds();
                break;
            case 'get_active_ads':
                $controller->getActiveAds();
                break;
            case 'cleanup_expired':
                $controller->cleanupExpiredAds();
                break;
            case 'update_status':
                $controller->updateAdStatus();
                break;
            case 'delete_ad':
                $controller->deleteAd();
                break;
            default:
                echo json_encode(["success" => false, "message" => "Invalid action"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "No action specified"]);
    }
} catch (Exception $e) {
    error_log("Global Error: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "System error occurred"]);
}
?>