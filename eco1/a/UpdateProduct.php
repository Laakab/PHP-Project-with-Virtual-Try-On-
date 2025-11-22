<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product | Crowd Zero</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h2 class="card-title mb-0 text-center">
                            <i class="fas fa-edit me-2"></i>Update Product
                        </h2>
                    </div>
                    <div class="card-body p-4">
                        <form id="productForm" enctype="multipart/form-data">
                            <input type="hidden" id="productId" name="productId" value="">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productName" class="form-label fw-bold">Product Name *</label>
                                    <input type="text" class="form-control form-control-lg" id="productName" name="productName" placeholder="Enter product name" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="categoryName" class="form-label fw-bold">Category *</label>
                                    <select class="form-select form-select-lg" id="categoryName" name="categoryName" required>
                                        <option value="">Select Category</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="productImage" class="form-label fw-bold">Product Image</label>
                                <input type="file" class="form-control form-control-lg" id="productImage" name="productImage" accept="image/*">
                                <div class="form-text">Choose new image (optional, Max 5MB)</div>
                                
                                <div id="currentImage" class="mt-3 d-none">
                                    <p class="fw-bold mb-2">Current Image:</p>
                                    <img id="currentImg" src="#" alt="Current Product" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                </div>
                                
                                <div id="imagePreview" class="mt-3 d-none">
                                    <p class="fw-bold mb-2">New Preview:</p>
                                    <img id="previewImg" src="#" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productColor" class="form-label fw-bold">Color</label>
                                    <input type="color" class="form-control form-control-color" id="productColor" name="productColor" value="#3498db" title="Choose product color">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="productPrice" class="form-label fw-bold">Price (RS) *</label>
                                    <input type="number" class="form-control form-control-lg" id="productPrice" name="productPrice" placeholder="Enter price" min="0" step="0.01" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productDiscount" class="form-label fw-bold">Discount (%) *</label>
                                    <input type="number" class="form-control form-control-lg" id="productDiscount" name="productDiscount" placeholder="Enter discount percentage" min="0" max="100" value="0" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="productDelveryPrice" class="form-label fw-bold">Delivery Price (RS) *</label>
                                    <input type="number" class="form-control form-control-lg" id="productDelveryPrice" name="productDelveryPrice" placeholder="Enter delivery price" min="0" step="0.01" value="0" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productQuantity" class="form-label fw-bold">Quantity *</label>
                                    <input type="number" class="form-control form-control-lg" id="productQuantity" name="productQuantity" placeholder="Enter quantity" min="1" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="srd" class="form-label fw-bold">Return Policy *</label>
                                    <select class="form-select form-select-lg" id="srd" name="srd" required>
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
                            </div>

                            <div class="mb-4">
                                <label for="productSize" class="form-label fw-bold">Size *</label>
                                <input type="text" class="form-control form-control-lg" id="productSize" name="productSize" placeholder="Enter size (e.g., S, M, L, XL)" required>
                            </div>

                            <div class="mb-4">
                                <label for="productDescription" class="form-label fw-bold">Description *</label>
                                <textarea class="form-control form-control-lg" id="productDescription" name="productDescription" placeholder="Enter product description" rows="4" required></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg py-3">
                                    <i class="fas fa-save me-2"></i> Update Product
                                </button>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6 mb-2">
                                    <a href="./AddProduct.php" class="btn btn-outline-success w-100">
                                        <i class="fas fa-plus-circle me-2"></i> Add New Product
                                    </a>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <a href="./ViewProduct.php" class="btn btn-outline-info w-100">
                                        <i class="fas fa-list me-2"></i> View All Products
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Get product ID from URL
        function getProductIdFromURL() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('id');
        }

        // Load product data
        function loadProductData() {
            const productId = getProductIdFromURL();
            
            if (!productId) {
                alert('❌ No product ID provided');
                window.location.href = './ViewProduct.php';
                return;
            }

            document.getElementById('productId').value = productId;

            fetch(`./controllers/ProductController.php?action=get_product&id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.product) {
                        populateForm(data.product);
                    } else {
                        alert('❌ ' + (data.message || 'Product not found'));
                        window.location.href = './ViewProduct.php';
                    }
                })
                .catch(error => {
                    console.error('Error loading product:', error);
                    alert('❌ Error loading product data');
                    window.location.href = './ViewProduct.php';
                });
        }

        // Populate form with product data
        function populateForm(product) {
            document.getElementById('productName').value = product.name;
            document.getElementById('productColor').value = product.color;
            document.getElementById('productQuantity').value = product.quantity;
            document.getElementById('productDescription').value = product.description;
            document.getElementById('productSize').value = product.size;
            document.getElementById('productPrice').value = product.price;
            document.getElementById('productDelveryPrice').value = product.delivery_price;
            document.getElementById('productDiscount').value = product.discount;
            document.getElementById('srd').value = product.return_days;

            // Show current image
            if (product.image) {
                document.getElementById('currentImg').src = product.image;
                document.getElementById('currentImage').classList.remove('d-none');
            }

            // Load categories and set the current category
            loadCategories(product.category_name);
        }

        // Load categories and select the current one
        function loadCategories(currentCategory = '') {
            fetch('./controllers/ProductController.php?action=get_categories')
                .then(response => response.json())
                .then(categories => {
                    const select = document.getElementById('categoryName');
                    select.innerHTML = '<option value="">Select Category</option>';
                    
                    if (categories && Array.isArray(categories) && categories.length > 0) {
                        categories.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category;
                            option.textContent = category;
                            if (category === currentCategory) {
                                option.selected = true;
                            }
                            select.appendChild(option);
                        });
                    } else {
                        select.innerHTML = '<option value="">No categories available</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading categories:', error);
                });
        }

        // Handle form submission
        document.getElementById('productForm').addEventListener('submit', function(e) {
            e.preventDefault();
            updateProduct();
        });

        // File input change handler
        document.getElementById('productImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            
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

        function updateProduct() {
            const formData = new FormData(document.getElementById('productForm'));
            const submitBtn = document.querySelector('.btn-primary');
            const originalText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Updating Product...';
            submitBtn.disabled = true;

            fetch('./controllers/ProductController.php?action=update', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    window.location.href = './ViewProduct.php';
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ An error occurred while updating product.');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        // Load product data when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadProductData();
        });
    </script>
</body>
</html>