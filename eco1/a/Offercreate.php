<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Offers | Crowd Zero</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4 text-primary">Create New Product Offer</h1>
                
                <div id="statusMessage" class="alert d-none mb-4"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="card-title h4 mb-4"><?php echo isset($_GET['edit']) ? 'Edit Offer' : 'New Offer'; ?></h2>
                        
                        <form id="offerForm" enctype="multipart/form-data">
                            <?php if (isset($_GET['edit'])): ?>
                                <input type="hidden" name="offerId" value="<?php echo $_GET['edit']; ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="offerTitle" class="form-label required">Offer Title</label>
                                <input type="text" class="form-control" id="offerTitle" name="offerTitle" placeholder="Enter offer title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="offerProduct" class="form-label required">Product</label>
                                <select class="form-select" id="offerProduct" name="offerProduct" required>
                                    <option value="">Select a product</option>
                                    <!-- Products will be loaded here -->
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="offerDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="offerDescription" name="offerDescription" placeholder="Enter offer description" rows="3"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="offerDiscount" class="form-label required">Discount (%)</label>
                                <input type="number" class="form-control" id="offerDiscount" name="offerDiscount" min="1" max="100" placeholder="Enter discount percentage" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="offerImage" class="form-label required">Offer Image</label>
                                <input type="file" class="form-control" id="offerImage" name="offerImage" accept="image/*" required>
                                <div class="mt-2">
                                    <img id="offerImagePreview" class="img-thumbnail d-none" alt="Image Preview" style="max-width: 200px; max-height: 150px;">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="offerStartDate" class="form-label required">Start Date</label>
                                    <input type="date" class="form-control" id="offerStartDate" name="offerStartDate" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="offerEndDate" class="form-label required">End Date</label>
                                    <input type="date" class="form-control" id="offerEndDate" name="offerEndDate" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="offerStatus" class="form-label required">Status</label>
                                <select class="form-select" id="offerStatus" name="offerStatus" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="pending" selected>Pending</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-success w-100" id="saveOfferBtn">
                                <i class="fas fa-plus-circle"></i> Save Offer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="card-title h4 mb-4">Existing Offers</h2>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Title</th>
                                        <th>Product</th>
                                        <th>Discount</th>
                                        <th>Dates</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="offersTableBody">
                                    <!-- Offers will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentEditingId = null;

        // Image preview functionality
        document.getElementById('offerImage').addEventListener('change', function() {
            const file = this.files[0];
            const preview = document.getElementById('offerImagePreview');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                }
                
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('d-none');
            }
        });
        
        // Load products for dropdown
        function loadProducts() {
            fetch('controllers/OfferController.php?action=get_products')
                .then(response => response.json())
                .then(products => {
                    const productSelect = document.getElementById('offerProduct');
                    productSelect.innerHTML = '<option value="">Select a product</option>';
                    
                    products.forEach(product => {
                        const option = document.createElement('option');
                        option.value = product.id;
                        option.textContent = product.name;
                        productSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading products:', error);
                    showStatus('Error loading products', 'alert-danger');
                });
        }
        
        // Form submission
        document.getElementById('offerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            saveOffer();
        });

        function saveOffer() {
            const formData = new FormData(document.getElementById('offerForm'));
            const submitBtn = document.getElementById('saveOfferBtn');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            submitBtn.disabled = true;

            const url = currentEditingId 
                ? `controllers/OfferController.php?action=update&id=${currentEditingId}`
                : 'controllers/OfferController.php?action=create';

            if (currentEditingId) {
                formData.append('offerId', currentEditingId);
            }

            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStatus('✅ ' + data.message, 'alert-success');
                    resetForm();
                    loadOffers();
                } else {
                    showStatus('❌ ' + data.message, 'alert-danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showStatus('❌ An error occurred while saving offer', 'alert-danger');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }
        
        // Load existing offers
        function loadOffers() {
            fetch('controllers/OfferController.php?action=get_offers')
                .then(response => response.json())
                .then(offers => {
                    const tableBody = document.getElementById('offersTableBody');
                    tableBody.innerHTML = '';
                    
                    if (offers.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No offers found</td></tr>';
                        return;
                    }
                    
                    offers.forEach(offer => {
                        const row = document.createElement('tr');
                        
                        // Format dates
                        const startDate = new Date(offer.start_date).toLocaleDateString();
                        const endDate = new Date(offer.end_date).toLocaleDateString();
                        
                        // Status badge class
                        const statusClass = offer.status === 'active' ? 'badge bg-success' : 
                                          offer.status === 'pending' ? 'badge bg-warning' : 'badge bg-secondary';
                        
                        row.innerHTML = `
                            <td>${offer.title}</td>
                            <td>${offer.product_name || 'Unknown Product'}</td>
                            <td><span class="badge bg-danger">${offer.discount}% OFF</span></td>
                            <td>${startDate} - ${endDate}</td>
                            <td><span class="${statusClass}">${offer.status.charAt(0).toUpperCase() + offer.status.slice(1)}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary edit-btn" data-id="${offer.id}">Edit</button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${offer.id}">Delete</button>
                            </td>
                        `;
                        
                        tableBody.appendChild(row);
                    });
                    
                    // Add event listeners to action buttons
                    document.querySelectorAll('.edit-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const offerId = this.getAttribute('data-id');
                            editOffer(offerId);
                        });
                    });
                    
                    document.querySelectorAll('.delete-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const offerId = this.getAttribute('data-id');
                            if (confirm('Are you sure you want to delete this offer?')) {
                                deleteOffer(offerId);
                            }
                        });
                    });
                })
                .catch(error => {
                    console.error('Error loading offers:', error);
                    showStatus('Error loading offers', 'alert-danger');
                });
        }
        
        // Edit offer
        function editOffer(offerId) {
            fetch(`controllers/OfferController.php?action=get_offer&id=${offerId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const offer = data.data;
                        document.getElementById('offerTitle').value = offer.title;
                        document.getElementById('offerProduct').value = offer.product_id;
                        document.getElementById('offerDescription').value = offer.description || '';
                        document.getElementById('offerDiscount').value = offer.discount;
                        document.getElementById('offerStartDate').value = offer.start_date;
                        document.getElementById('offerEndDate').value = offer.end_date;
                        document.getElementById('offerStatus').value = offer.status;
                        
                        // Show existing image preview
                        if (offer.image_path) {
                            const preview = document.getElementById('offerImagePreview');
                            preview.src = `../${offer.image_path}`;
                            preview.classList.remove('d-none');
                        }
                        
                        // Change save button to update
                        const saveBtn = document.getElementById('saveOfferBtn');
                        saveBtn.innerHTML = '<i class="fas fa-edit"></i> Update Offer';
                        saveBtn.className = 'btn btn-warning w-100';
                        currentEditingId = offerId;
                        
                        // Scroll to form
                        document.querySelector('.card').scrollIntoView({ behavior: 'smooth' });
                    } else {
                        showStatus('❌ ' + data.message, 'alert-danger');
                    }
                })
                .catch(error => {
                    console.error('Error loading offer:', error);
                    showStatus('Error loading offer', 'alert-danger');
                });
        }
        
        // Delete offer
        function deleteOffer(offerId) {
            const formData = new FormData();
            formData.append('id', offerId);

            fetch('controllers/OfferController.php?action=delete', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStatus('✅ ' + data.message, 'alert-success');
                    loadOffers();
                } else {
                    showStatus('❌ ' + data.message, 'alert-danger');
                }
            })
            .catch(error => {
                console.error('Error deleting offer:', error);
                showStatus('Error deleting offer', 'alert-danger');
            });
        }
        
        // Reset form
        function resetForm() {
            document.getElementById('offerForm').reset();
            document.getElementById('offerImagePreview').classList.add('d-none');
            document.getElementById('offerStatus').value = 'pending';
            
            const saveBtn = document.getElementById('saveOfferBtn');
            saveBtn.innerHTML = '<i class="fas fa-plus-circle"></i> Save Offer';
            saveBtn.className = 'btn btn-success w-100';
            currentEditingId = null;
            
            // Set default dates
            setDefaultDates();
        }

        function setDefaultDates() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('offerStartDate').value = today;
            
            // Set end date to 30 days from now by default
            const monthLater = new Date();
            monthLater.setDate(monthLater.getDate() + 30);
            document.getElementById('offerEndDate').value = monthLater.toISOString().split('T')[0];
        }
        
        function showStatus(message, type) {
            const statusElement = document.getElementById('statusMessage');
            statusElement.textContent = message;
            statusElement.className = `alert ${type}`;
            statusElement.classList.remove('d-none');

            setTimeout(() => {
                statusElement.classList.add('d-none');
            }, 5000);
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            setDefaultDates();
            loadProducts();
            loadOffers();
            
            // Auto cleanup expired offers on page load
            fetch('controllers/OfferController.php?action=cleanup_expired')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.deleted_count > 0) {
                        console.log(`Cleaned up ${data.deleted_count} expired offers`);
                    }
                })
                .catch(error => {
                    console.error('Cleanup error:', error);
                });
        });
    </script>
</body>
</html>