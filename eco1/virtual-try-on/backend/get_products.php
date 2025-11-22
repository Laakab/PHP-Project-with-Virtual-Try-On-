<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Include config to get DB connection and models
// Assuming this file is in virtual-try-on/backend/
// and config is in e/config.php
// virtual-try-on/backend/ -> ../../e/config.php
require_once '../../e/config.php';

try {
    $productModel = getProductModel();
    $products = $productModel->getAllProducts();

    // Sort by category to group them in the frontend
    usort($products, function($a, $b) {
        return strcmp($a['category_name'] ?? '', $b['category_name'] ?? '');
    });

    $processedProducts = array_map(function($p) {
        $image = $p['image'];
        
        // Fix image path for virtual-try-on frontend
        // Frontend is in virtual-try-on/frontend/
        // Images are usually in eco1/a/uploads/
        // We want path relative to frontend: ../../a/uploads/
        
        // Use the shared helper to get the path relative to 'e/'
        $formatted = format_image_url($image);
        
        // Now convert it to be relative to 'virtual-try-on/frontend/'
        // We are in virtual-try-on/frontend/
        // We need to go to e/ which is ../../e/
        
        if (strpos($formatted, 'http') === 0) {
            $image = $formatted;
        } else {
            // $formatted is something like "images/foo.png" or "../a/uploads/foo.png"
            // We prepend ../../e/ to make it valid from frontend
            $image = '../../e/' . $formatted;
        }
        
        return [
            'id' => $p['id'],
            'name' => $p['name'],
            'image' => $image,
            'category' => $p['category_name'] ?? 'Other'
        ];
    }, $products);

    echo json_encode($processedProducts);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
