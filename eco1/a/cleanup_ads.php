<?php
require_once 'models/Database.php';
require_once 'models/AdModel.php';

class AdCleanup {
    private $adModel;

    public function __construct() {
        $this->adModel = new AdModel();
    }

    public function cleanupExpiredAds() {
        try {
            $deletedCount = $this->adModel->deleteExpiredAds();
            
            // Log the cleanup activity
            error_log("Ad Cleanup: Deleted {$deletedCount} expired ads at " . date('Y-m-d H:i:s'));
            
            return [
                "success" => true,
                "deleted_count" => $deletedCount,
                "timestamp" => date('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            error_log("Ad Cleanup Error: " . $e->getMessage());
            return ["success" => false, "error" => $e->getMessage()];
        }
    }
}

// Run cleanup
$cleanup = new AdCleanup();
$result = $cleanup->cleanupExpiredAds();

if (php_sapi_name() === 'cli') {
    // CLI execution (for cron jobs)
    if ($result['success']) {
        echo "Cleanup completed: {$result['deleted_count']} ads deleted\n";
    } else {
        echo "Cleanup failed: {$result['error']}\n";
    }
} else {
    // Web execution
    header('Content-Type: application/json');
    echo json_encode($result);
}
?>