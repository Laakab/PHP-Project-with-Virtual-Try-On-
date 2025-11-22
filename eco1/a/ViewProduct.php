<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products | Crowd Zero</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-body text-center py-5">
                        <h1 class="card-title text-primary mb-3">
                            <i class="fas fa-boxes me-3"></i>Product Management
                        </h1>
                        <p class="card-text text-muted fs-5">
                            Manage all your products in one place
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Controls Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8 mb-3 mb-md-0">
                                <div class="row g-2">
                                    <div class="col-md-8">
                                        <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="Search products...">
                                    </div>
                                    <div class="col-md-4">
                                        <select id="searchCondition" class="form-select form-select-lg">
                                            <option value="all">All Fields</option>
                                            <option value="ProductName">Product Name</option>
                                            <option value="CategoryName">Category</option>
                                            <option value="productColor">Color</option>
                                            <option value="Price">Price</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex flex-column flex-md-row gap-3 justify-content-md-end">
                                    <a href="AddProduct.php" class="btn btn-primary btn-lg">
                                        <i class="fas fa-plus-circle me-2"></i>Add New Product
                                    </a>
                                    <a href="AddCateGory.php" class="btn btn-success btn-lg">
                                        <i class="fas fa-tags me-2"></i>Manage Categories
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h2 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>All Products
                        </h2>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Category</th>
                                        <th>Quantity</th>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Discount</th>
                                        <th>Price</th>
                                        <th>Discounted</th>
                                        <th>Delivery</th>
                                        <th>Returns</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="productsGrid">
                                    <tr>
                                        <td colspan="12">
                                            <div class="text-center py-5">
                                                <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                                                <p class="text-muted fs-5">Loading products...</p>
                                                <small class="text-muted">Please wait while we fetch your products</small>
                                            </div>
                                        </td>
                                    </tr>
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
        function loadProducts() {
            fetch('controllers/ProductController.php?action=get_products')
                .then(response => response.json())
                .then(products => {
                    const productList = document.getElementById('productsGrid');
                    productList.innerHTML = '';

                    if (products.length === 0) {
                        const emptyRow = `
                            <tr>
                                <td colspan="12">
                                    <div class="text-center py-5">
                                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                        <p class="text-muted fs-5">No products found</p>
                                        <small class="text-muted">Add some products to get started</small>
                                    </div>
                                </td>
                            </tr>
                        `;
                        productList.innerHTML = emptyRow;
                        return;
                    }

                    products.forEach(product => {
                        const disc = product.discount / 100;
                        const dis = product.price * disc;
                        const discount = product.price - dis;
                        
                        // Determine quantity badge color
                        let quantityBadgeClass = 'bg-success';
                        if (product.quantity <= 0) {
                            quantityBadgeClass = 'bg-danger';
                        } else if (product.quantity <= 10) {
                            quantityBadgeClass = 'bg-warning';
                        }
                        
                        const row = document.createElement('tr');
                        row.className = 'product-row';
                        row.innerHTML = `
                            <td>
                                <strong>${escapeHtml(product.name)}</strong>
                            </td>
                            <td>${escapeHtml(product.description.substring(0, 50))}${product.description.length > 50 ? '...' : ''}</td>
                            <td>
                                <span class="badge bg-primary">${escapeHtml(product.category_name)}</span>
                            </td>
                            <td>
                                <span class="badge ${quantityBadgeClass}">${product.quantity}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle border" style="width: 20px; height: 20px; background-color: ${product.color};"></div>
                                    <span>${product.color}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">${escapeHtml(product.size)}</span>
                            </td>
                            <td>
                                <span class="badge ${product.discount > 0 ? 'bg-danger' : 'bg-secondary'}">
                                    ${product.discount}%
                                </span>
                            </td>
                            <td>
                                <span class="text-muted text-decoration-line-through">${product.price} RS</span>
                            </td>
                            <td>
                                <span class="fw-bold text-danger">${discount.toFixed(2)} RS</span>
                            </td>
                            <td>${product.delivery_price} RS</td>
                            <td>${product.return_days}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="editProduct(${product.id})" title="Edit Product">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteProduct(${product.id})" title="Delete Product">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                        productList.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Error loading products:', error);
                    const productList = document.getElementById('productsGrid');
                    productList.innerHTML = `
                        <tr>
                            <td colspan="12">
                                <div class="text-center py-5">
                                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                    <p class="text-danger fs-5">Error loading products</p>
                                    <small class="text-muted">Please check console for details</small>
                                </div>
                            </td>
                        </tr>
                    `;
                });
        }

        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                const formData = new FormData();
                formData.append('productId', productId);

                fetch('controllers/ProductController.php?action=delete', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ ' + data.message);
                        loadProducts();
                    } else {
                        alert('❌ ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error deleting product:', error);
                    alert('❌ An error occurred while deleting product.');
                });
            }
        }

        function editProduct(productId) {
            window.location.href = `UpdateProduct.php?id=${productId}`;
        }

        function searchProducts() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const searchCondition = document.getElementById('searchCondition').value;
            
            const rows = document.querySelectorAll('.product-row');
            
            rows.forEach(row => {
                let text = '';
                switch(searchCondition) {
                    case 'ProductName':
                        text = row.cells[0].textContent.toLowerCase();
                        break;
                    case 'CategoryName':
                        text = row.cells[2].textContent.toLowerCase();
                        break;
                    case 'productColor':
                        text = row.cells[4].textContent.toLowerCase();
                        break;
                    case 'Price':
                        text = row.cells[7].textContent.toLowerCase();
                        break;
                    default:
                        text = row.textContent.toLowerCase();
                }
                
                if (text.includes(searchInput)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Load products when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();
            
            // Add event listener for search input
            document.getElementById('searchInput').addEventListener('input', searchProducts);
            
            // Add event listener for search condition change
            document.getElementById('searchCondition').addEventListener('change', searchProducts);
        });
    </script>
</body>
</html>