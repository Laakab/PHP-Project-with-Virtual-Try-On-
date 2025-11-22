<?php
// Addscreate.php - Updated with edit functionality
session_start();

// Check if we're in edit mode
$isEditMode = isset($_GET['edit']);
$adId = $isEditMode ? $_GET['edit'] : null;
$adData = null;

if ($isEditMode && $adId) {
    // Fetch ad data for editing
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/controllers/AdController.php?action=get_ad&adId=" . $adId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    if ($result['success']) {
        $adData = $result['ad'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEditMode ? 'Edit Ad' : 'Create Ads'; ?> | Crowd Zero</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h1 class="card-title text-center mb-4 text-primary">
                            <?php echo $isEditMode ? 'Edit Advertisement' : 'Create New Advertisement'; ?>
                        </h1>

                        <div id="statusMessage" class="alert d-none"></div>

                        <form id="adForm" enctype="multipart/form-data">
                            <?php if ($isEditMode): ?>
                                <input type="hidden" id="adId" name="adId" value="<?php echo $adId; ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="adsTitle" class="form-label required">Ad Title</label>
                                <input type="text" class="form-control" id="adsTitle" name="adsTitle" 
                                       placeholder="Enter advertisement title" 
                                       value="<?php echo $adData ? htmlspecialchars($adData['title']) : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="adsDescription" class="form-label required">Description</label>
                                <textarea class="form-control" id="adsDescription" name="adsDescription" 
                                          placeholder="Enter advertisement description" rows="4" required><?php echo $adData ? htmlspecialchars($adData['description']) : ''; ?></textarea>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           placeholder="Enter phone number"
                                           value="<?php echo $adData ? htmlspecialchars($adData['phone']) : ''; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           placeholder="Enter email address"
                                           value="<?php echo $adData ? htmlspecialchars($adData['email']) : ''; ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="companyName" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="companyName" name="companyName" 
                                       placeholder="Enter company name"
                                       value="<?php echo $adData ? htmlspecialchars($adData['company_name']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label for="link" class="form-label required">Link URL</label>
                                <input type="text" class="form-control" id="link" name="link" 
                                       placeholder="https://example.com" 
                                       value="<?php echo $adData ? htmlspecialchars($adData['link']) : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="adsImage" class="form-label <?php echo $isEditMode ? '' : 'required'; ?>">Ad Image</label>
                                <?php if ($isEditMode && $adData && $adData['image_path']): ?>
                                    <div class="mb-2">
                                        <strong>Current Image:</strong>
                                        <img src="<?php echo $adData['image_path']; ?>" alt="Current Ad Image" class="img-thumbnail mt-2 d-block" style="max-width: 300px; max-height: 200px;">
                                    </div>
                                    <small class="text-muted">Upload a new image only if you want to change the current one.</small>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="adsImage" name="adsImage" accept="image/*" <?php echo $isEditMode ? '' : 'required'; ?>>
                                <div class="mt-2">
                                    <img id="imagePreview" class="img-thumbnail d-none" alt="Image Preview" style="max-width: 300px; max-height: 200px;">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="startDateTime" class="form-label required">Start Date/Time</label>
                                    <input type="datetime-local" class="form-control" id="startDateTime" name="startDateTime" 
                                           value="<?php echo $adData ? date('Y-m-d\TH:i', strtotime($adData['start_datetime'])) : ''; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="endDateTime" class="form-label required">End Date/Time</label>
                                    <input type="datetime-local" class="form-control" id="endDateTime" name="endDateTime"
                                           value="<?php echo $adData ? date('Y-m-d\TH:i', strtotime($adData['end_datetime'])) : ''; ?>" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="status" class="form-label required">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active" <?php echo $adData && $adData['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="pending" <?php echo !$adData || $adData['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="inactive" <?php echo $adData && $adData['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn <?php echo $isEditMode ? 'btn-warning' : 'btn-success'; ?> btn-lg" id="submitBtn">
                                    <i class="fas <?php echo $isEditMode ? 'fa-save' : 'fa-plus-circle'; ?>"></i> 
                                    <?php echo $isEditMode ? 'Update Advertisement' : 'Create Advertisement'; ?>
                                </button>
                                
                                <?php if ($isEditMode): ?>
                                    <a href="showAds.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to Ads
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const isEditMode = <?php echo $isEditMode ? 'true' : 'false'; ?>;
        const adId = <?php echo $adId ? $adId : 'null'; ?>;

        // Image preview functionality
        document.getElementById('adsImage').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.getElementById('imagePreview');
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            }
        });

        // Form submission
        document.getElementById('adForm').addEventListener('submit', function (e) {
            e.preventDefault();
            if (isEditMode) {
                updateAd();
            } else {
                createAd();
            }
        });

        function createAd() {
            const formData = new FormData(document.getElementById('adForm'));
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Advertisement...';
            submitBtn.disabled = true;

            fetch('controllers/AdController.php?action=create', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStatus('✅ ' + data.message, 'alert-success');
                    document.getElementById('adForm').reset();
                    document.getElementById('imagePreview').classList.add('d-none');
                    if (!isEditMode) {
                        setDefaultDatetimes();
                    }
                } else {
                    showStatus('❌ ' + data.message, 'alert-danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showStatus('❌ An error occurred while creating advertisement.', 'alert-danger');
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        function updateAd() {
            const formData = new FormData(document.getElementById('adForm'));
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating Advertisement...';
            submitBtn.disabled = true;

            fetch('controllers/AdController.php?action=update', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStatus('✅ ' + data.message, 'alert-success');
                    // Don't reset form in edit mode
                } else {
                    showStatus('❌ ' + data.message, 'alert-danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showStatus('❌ An error occurred while updating advertisement.', 'alert-danger');
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        function showStatus(message, type) {
            const statusElement = document.getElementById('statusMessage');
            statusElement.textContent = message;
            statusElement.className = 'alert ' + type;
            statusElement.classList.remove('d-none');

            // Hide after 5 seconds
            setTimeout(() => {
                statusElement.classList.add('d-none');
            }, 5000);
        }

        function setDefaultDatetimes() {
            const now = new Date();
            const timezoneOffset = now.getTimezoneOffset() * 60000;
            const localISOTime = (new Date(now - timezoneOffset)).toISOString().slice(0, 16);

            document.getElementById('startDateTime').value = localISOTime;

            // Set end time to 7 days from now by default
            const weekLater = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000);
            const weekLaterISOTime = (new Date(weekLater - timezoneOffset)).toISOString().slice(0, 16);
            document.getElementById('endDateTime').value = weekLaterISOTime;
        }

        // Set default datetime values when page loads (only for create mode)
        document.addEventListener('DOMContentLoaded', function () {
            if (!isEditMode) {
                setDefaultDatetimes();
                
                // Auto cleanup expired ads on page load
                fetch('controllers/AdController.php?action=cleanup_expired')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.deleted_count > 0) {
                            console.log(`Cleaned up ${data.deleted_count} expired ads`);
                        }
                    })
                    .catch(error => {
                        console.error('Cleanup error:', error);
                    });
            }
        });
    </script>
</body>
</html>