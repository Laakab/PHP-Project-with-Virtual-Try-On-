<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories | Crowd Zero</title>
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
                            <i class="fas fa-tags me-3"></i>Category Management
                        </h1>
                        <p class="card-text text-muted fs-5">
                            Manage all your product categories in one place
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
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="input-group">
                                    <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="Search categories...">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-search"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex flex-column flex-md-row gap-3 justify-content-md-end">
                                    <a href="AddCateGory.php" class="btn btn-primary btn-lg">
                                        <i class="fas fa-plus-circle me-2"></i>Add New Category
                                    </a>
                                    <a href="ViewProduct.php" class="btn btn-success btn-lg">
                                        <i class="fas fa-boxes me-2"></i>View Products
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h2 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>All Categories
                        </h2>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Category Name</th>
                                        <th>Created Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="categoriesGrid">
                                    <tr>
                                        <td colspan="4">
                                            <div class="text-center py-5">
                                                <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                                                <p class="text-muted fs-5">Loading categories...</p>
                                                <small class="text-muted">Please wait while we fetch your categories</small>
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

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Edit Category
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editCategoryForm">
                    <div class="modal-body">
                        <input type="hidden" id="editCategoryId" name="categoryId">
                        <div class="mb-3">
                            <label for="editCategoryName" class="form-label fw-bold">Category Name *</label>
                            <input type="text" class="form-control form-control-lg" id="editCategoryName" name="categoryName" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load categories when page loads
        function loadCategories() {
            console.log('Loading categories...');
            
            fetch('controllers/CategoryController.php?action=get')
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(categories => {
                    console.log('Categories data:', categories);
                    const categoriesGrid = document.getElementById('categoriesGrid');
                    categoriesGrid.innerHTML = '';

                    if (!categories || categories.length === 0) {
                        const emptyRow = `
                            <tr>
                                <td colspan="4">
                                    <div class="text-center py-5">
                                        <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                        <p class="text-muted fs-5">No categories found</p>
                                        <small class="text-muted">Add some categories to get started</small>
                                    </div>
                                </td>
                            </tr>
                        `;
                        categoriesGrid.innerHTML = emptyRow;
                        return;
                    }

                    categories.forEach(category => {
                        const createdDate = category.created_at ? new Date(category.created_at).toLocaleDateString() : 'N/A';
                        
                        const row = document.createElement('tr');
                        row.className = 'category-row';
                        row.innerHTML = `
                            <td class="fw-bold">${category.id}</td>
                            <td>
                                <strong>${escapeHtml(category.name)}</strong>
                            </td>
                            <td>${createdDate}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="openEditModal(${category.id}, '${escapeHtml(category.name)}')" title="Edit Category">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteCategory(${category.id}, '${escapeHtml(category.name)}')" title="Delete Category">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                        categoriesGrid.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Error loading categories:', error);
                    const categoriesGrid = document.getElementById('categoriesGrid');
                    categoriesGrid.innerHTML = `
                        <tr>
                            <td colspan="4">
                                <div class="text-center py-5">
                                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                    <p class="text-danger fs-5">Error loading categories</p>
                                    <small class="text-muted">Please check console for details</small>
                                </div>
                            </td>
                        </tr>
                    `;
                });
        }

        // Open edit modal
        function openEditModal(categoryId, categoryName) {
            document.getElementById('editCategoryId').value = categoryId;
            document.getElementById('editCategoryName').value = categoryName;
            const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
            modal.show();
        }

        // Handle edit form submission
        document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            updateCategory();
        });

        // Update category
        function updateCategory() {
            const categoryId = document.getElementById('editCategoryId').value;
            const categoryName = document.getElementById('editCategoryName').value.trim();

            if (!categoryName) {
                alert('Please enter a category name');
                return;
            }

            const formData = new FormData();
            formData.append('categoryId', categoryId);
            formData.append('categoryName', categoryName);

            fetch('controllers/CategoryController.php?action=update', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    bootstrap.Modal.getInstance(document.getElementById('editCategoryModal')).hide();
                    loadCategories(); // Refresh the categories list
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error updating category:', error);
                alert('❌ An error occurred while updating category.');
            });
        }

        // Delete category
        function deleteCategory(categoryId, categoryName) {
            if (confirm(`Are you sure you want to delete the category "${categoryName}"? This action cannot be undone.`)) {
                const formData = new FormData();
                formData.append('categoryId', categoryId);

                fetch('controllers/CategoryController.php?action=delete', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ ' + data.message);
                        loadCategories(); // Refresh the categories list
                    } else {
                        alert('❌ ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error deleting category:', error);
                    alert('❌ An error occurred while deleting category.');
                });
            }
        }

        // Search functionality
        function searchCategories() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('.category-row');
            
            rows.forEach(row => {
                const categoryName = row.cells[1].textContent.toLowerCase();
                
                if (categoryName.includes(searchInput)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Utility function to escape HTML
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Load categories when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadCategories();
            
            // Add event listener for search input
            document.getElementById('searchInput').addEventListener('input', searchCategories);
        });
    </script>
</body>
</html>