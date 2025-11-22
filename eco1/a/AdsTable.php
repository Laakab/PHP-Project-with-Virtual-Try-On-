<?php
// showAds.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Ads | Crowd Zero</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="text-center text-primary fw-bold mb-4">Manage Advertisements</h1>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-ad me-2"></i>All Advertisements
                            </h3>
                            <a href="Addscreate.php" class="btn btn-success btn-lg">
                                <i class="fas fa-plus-circle me-2"></i>Create New Ad
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0" id="adsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Company</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="adsTableBody">
                                    <!-- Ads will be loaded here via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this advertisement? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let adsData = [];
        let adToDelete = null;

        // Load ads when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadAds();
        });

        function loadAds() {
            fetch('controllers/AdController.php?action=get_ads')
                .then(response => response.json())
                .then(data => {
                    adsData = data;
                    renderAdsTable(data);
                })
                .catch(error => {
                    console.error('Error loading ads:', error);
                    showAlert('Error loading advertisements', 'danger');
                });
        }

        function renderAdsTable(ads) {
            const tbody = document.getElementById('adsTableBody');
            
            if (ads.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted fs-5">No advertisements found</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = ads.map(ad => `
                <tr>
                    <td class="fw-bold">${ad.id}</td>
                    <td>
                        ${ad.image_path ? 
                            `<img src="${ad.image_path}" alt="${ad.title}" class="img-thumbnail" style="width: 80px; height: 60px; object-fit: cover;" onerror="this.style.display='none'">` : 
                            '<i class="fas fa-image text-muted fa-2x"></i>'
                        }
                    </td>
                    <td>
                        <strong class="d-block">${ad.title}</strong>
                        ${ad.description ? `<small class="text-muted">${ad.description.substring(0, 50)}...</small>` : ''}
                    </td>
                    <td>${ad.company_name || '<span class="text-muted">-</span>'}</td>
                    <td>${ad.email || '<span class="text-muted">-</span>'}</td>
                    <td>${ad.phone || '<span class="text-muted">-</span>'}</td>
                    <td><small class="text-nowrap">${formatDateTime(ad.start_datetime)}</small></td>
                    <td><small class="text-nowrap">${formatDateTime(ad.end_datetime)}</small></td>
                    <td>
                        ${getStatusBadge(ad.status)}
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="editAd(${ad.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="confirmDelete(${ad.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function getStatusBadge(status) {
            const statusClasses = {
                'active': 'bg-success',
                'pending': 'bg-warning',
                'inactive': 'bg-secondary'
            };
            
            const statusText = status.charAt(0).toUpperCase() + status.slice(1);
            return `<span class="badge ${statusClasses[status]}">${statusText}</span>`;
        }

        function formatDateTime(dateTimeString) {
            const date = new Date(dateTimeString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }

        function editAd(adId) {
            // Redirect to create page with edit mode
            window.location.href = `Addscreate.php?edit=${adId}`;
        }

        function confirmDelete(adId) {
            adToDelete = adId;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (adToDelete) {
                deleteAd(adToDelete);
            }
        });

        function deleteAd(adId) {
            const formData = new FormData();
            formData.append('adId', adId);

            fetch('controllers/AdController.php?action=delete_ad', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Advertisement deleted successfully!', 'success');
                    loadAds(); // Reload the table
                    bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                } else {
                    showAlert('Error deleting advertisement: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error deleting advertisement', 'danger');
            });
        }

        function showAlert(message, type) {
            // Create alert element
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Insert at top of container
            document.querySelector('.container-fluid').insertBefore(alertDiv, document.querySelector('.row').nextSibling);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentElement) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    </script>
</body>
</html>