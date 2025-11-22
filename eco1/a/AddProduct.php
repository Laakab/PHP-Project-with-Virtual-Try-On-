<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | Crowd Zero</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h2 class="card-title mb-0 text-center">
                            <i class="fas fa-cube me-2"></i>Add New Product
                        </h2>
                    </div>
                    <div class="card-body p-4">
                        <form id="productForm" enctype="multipart/form-data">
                            <div class="row">
                                <!-- Product Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="productName" class="form-label fw-semibold">
                                        Product Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" 
                                           id="productName" name="productName" 
                                           placeholder="Enter product name" required>
                                </div>

                                <!-- Category -->
                                <div class="col-md-6 mb-3">
                                    <label for="categoryName" class="form-label fw-semibold">
                                        Category <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="categoryName" name="categoryName" required>
                                        <option value="">Select Category</option>
                                        <!-- Categories will be loaded dynamically -->
                                    </select>
                                </div>

                                <!-- Product Image -->
                                <div class="col-12 mb-3">
                                    <label for="productImage" class="form-label fw-semibold">
                                        Product Image <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="file" class="form-control" 
                                               id="productImage" name="productImage" 
                                               accept="image/*" required>
                                        <span class="input-group-text">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </span>
                                    </div>
                                    <div class="form-text">Maximum file size: 5MB</div>
                                    
                                    <!-- Image Preview -->
                                    <div id="imagePreview" class="mt-3 text-center d-none">
                                        <img id="previewImg" src="#" alt="Preview" 
                                             class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                </div>

                                <!-- Color -->
                                <div class="col-md-6 mb-3">
                                    <label for="productColor" class="form-label fw-semibold">Color</label>
                                    <input type="color" class="form-control form-control-color" 
                                           id="productColor" name="productColor" value="#3498db" 
                                           title="Choose product color">
                                </div>

                                <!-- Price -->
                                <div class="col-md-6 mb-3">
                                    <label for="productPrice" class="form-label fw-semibold">
                                        Price (RS) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">RS</span>
                                        <input type="number" class="form-control" 
                                               id="productPrice" name="productPrice" 
                                               placeholder="Enter price" min="0" step="0.01" required>
                                    </div>
                                </div>

                                <!-- Discount -->
                                <div class="col-md-6 mb-3">
                                    <label for="productDiscount" class="form-label fw-semibold">
                                        Discount (%) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" 
                                               id="productDiscount" name="productDiscount" 
                                               placeholder="Enter discount percentage" 
                                               min="0" max="100" value="0" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>

                                <!-- Delivery Price -->
                                <div class="col-md-6 mb-3">
                                    <label for="productDelveryPrice" class="form-label fw-semibold">
                                        Delivery Price (RS) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">RS</span>
                                        <input type="number" class="form-control" 
                                               id="productDelveryPrice" name="productDelveryPrice" 
                                               placeholder="Enter delivery price" 
                                               min="0" step="0.01" value="0" required>
                                    </div>
                                </div>

                                <!-- Quantity -->
                                <div class="col-md-6 mb-3">
                                    <label for="productQuantity" class="form-label fw-semibold">
                                        Quantity <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" 
                                           id="productQuantity" name="productQuantity" 
                                           placeholder="Enter quantity" min="1" required>
                                </div>

                                <!-- Return Policy -->
                                <div class="col-md-6 mb-3">
                                    <label for="srd" class="form-label fw-semibold">
                                        Return Policy <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="srd" name="srd" required>
                                        <option value="">Select Return Days</option>
                                        <option value="1 Days Return">1 Day Return</option>
                                        <option value="2 Days Return">2 Days Return</option>
                                        <option value="3 Days Return">3 Days Return</option>
                                        <option value="4 Days Return">4 Days Return</option>
                                        <option value="5 Days Return">5 Days Return</option>
                                        <option value="7 Days Return">7 Days Return</option>
                                        <option value="No Return">No Return</option>
                                    </select>
                                </div>

                                <!-- Size -->
                                <div class="col-md-6 mb-3">
                                    <label for="productSize" class="form-label fw-semibold">
                                        Size <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" 
                                           id="productSize" name="productSize" 
                                           placeholder="Enter size (e.g., S, M, L, XL)" required>
                                </div>

                                <!-- Description -->
                                <div class="col-12 mb-4">
                                    <label for="productDescription" class="form-label fw-semibold">
                                        Description <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="productDescription" 
                                              name="productDescription" 
                                              placeholder="Enter product description" 
                                              rows="4" required></textarea>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-plus-circle me-2"></i>Add Product
                                </button>
                            </div>

                            <!-- Navigation Links -->
                            <div class="row text-center">
                                <div class="col-md-6 mb-2">
                                    <a href="./AddCateGory.php" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-tags me-2"></i>Manage Categories
                                    </a>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <a href="./ViewProduct.php" class="btn btn-outline-info w-100">
                                        <i class="fas fa-search me-2"></i>View Products
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle form submission
        document.getElementById('productForm').addEventListener('submit', function(e) {
            e.preventDefault();
            addProduct();
        });

        // File input change handler with preview
        document.getElementById('productImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            // Show image preview
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            } else {
                document.getElementById('imagePreview').classList.add('d-none');
            }
        });

        function addProduct() {
            const formData = new FormData(document.getElementById('productForm'));
            const submitBtn = document.querySelector('.btn-success');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding Product...';
            submitBtn.disabled = true;

            fetch('controllers/ProductController.php?action=add', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    resetForm();
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ An error occurred while adding product. Please check console for details.');
            })
            .finally(() => {
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        function loadCategories() {
            console.log('Loading categories...');
            
            fetch('controllers/ProductController.php?action=get_categories')
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Categories data:', data);
                    
                    const select = document.getElementById('categoryName');
                    select.innerHTML = '<option value="">Select Category</option>';
                    
                    if (data && Array.isArray(data) && data.length > 0) {
                        data.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category;
                            option.textContent = category;
                            select.appendChild(option);
                        });
                        console.log('Categories loaded successfully');
                    } else {
                        console.warn('No categories found or invalid response:', data);
                        select.innerHTML = '<option value="">No categories available. Please add categories first.</option>';
                        alert('⚠️ No categories found. Please add categories first from Manage Categories.');
                    }
                })
                .catch(error => {
                    console.error('Error loading categories:', error);
                    const select = document.getElementById('categoryName');
                    select.innerHTML = '<option value="">Error loading categories</option>';
                    alert('❌ Error loading categories: ' + error.message);
                });
        }

        function resetForm() {
            document.getElementById('productForm').reset();
            document.getElementById('productColor').value = '#3498db';
            document.getElementById('imagePreview').classList.add('d-none');
        }

        // Load categories when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, loading categories...');
            loadCategories();
        });
    </script>
</body>

</html>