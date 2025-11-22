<?php
require_once 'models/Database.php';
require_once 'models/OfferModel.php';

class OfferCleanup {
    private $offerModel;

    public function __construct() {
        $this->offerModel = new OfferModel();
    }

    public function cleanupExpiredOffers() {
        try {
            $deletedCount = $this->offerModel->deleteExpiredOffers();
            
            // Log the cleanup activity
            error_log("Offer Cleanup: Deleted {$deletedCount} expired offers at " . date('Y-m-d H:i:s'));
            
            return [
                "success" => true,
                "deleted_count" => $deletedCount,
                "timestamp" => date('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            error_log("Offer Cleanup Error: " . $e->getMessage());
            return ["success" => false, "error" => $e->getMessage()];
        }
    }
}

// Run cleanup
$cleanup = new OfferCleanup();
$result = $cleanup->cleanupExpiredOffers();

if (php_sapi_name() === 'cli') {
    // CLI execution (for cron jobs)
    if ($result['success']) {
        echo "Cleanup completed: {$result['deleted_count']} offers deleted\n";
    } else {
        echo "Cleanup failed: {$result['error']}\n";
    }
} else {
    // Web execution
    header('Content-Type: application/json');
    echo json_encode($result);
}
?>