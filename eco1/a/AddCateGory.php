<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category | Crowd Zero</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h2 class="card-title mb-0 text-center">
                            <i class="fas fa-tags me-2"></i>Add New Category
                        </h2>
                    </div>
                    <div class="card-body p-4">
                        <form id="categoryForm">
                            <div class="mb-3">
                                <label for="categoryName" class="form-label fw-semibold">Category Name</label>
                                <input type="text" class="form-control form-control-lg" 
                                       id="categoryName" name="categoryName" 
                                       placeholder="Enter category name" required>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-plus-circle me-2"></i>Add Category
                                </button>
                            </div>
                            
                            <div class="text-center">
                                <a href="./AddProduct.php" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-right me-2"></i>Go to Add Products
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Categories List -->
                <div class="card shadow mt-4">
                    <div class="card-header bg-info text-white">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>Existing Categories
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="categoriesContainer" class="row g-2">
                            <!-- Categories will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle form submission with AJAX
        document.getElementById('categoryForm').addEventListener('submit', function(e) {
            e.preventDefault();
            addCategory();
        });

        function addCategory() {
            const categoryName = document.getElementById('categoryName').value.trim();
            
            if (!categoryName) {
                alert('Please enter a category name');
                return;
            }

            const formData = new FormData();
            formData.append('categoryName', categoryName);

            fetch('controllers/CategoryController.php?action=add', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    document.getElementById('categoryName').value = '';
                    loadCategories(); // Refresh categories list
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ An error occurred while adding category. Please check console for details.');
            });
        }

        function loadCategories() {
            fetch('controllers/CategoryController.php?action=get')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(categories => {
                    const container = document.getElementById('categoriesContainer');
                    if (categories && categories.length > 0) {
                        container.innerHTML = categories.map(cat => 
                            `<div class="col-12 col-sm-6 col-md-4">
                                <div class="card border-success mb-2">
                                    <div class="card-body py-2">
                                        <h6 class="card-title mb-1">${cat.name}</h6>
                                        <small class="text-muted">ID: ${cat.id}</small>
                                    </div>
                                </div>
                            </div>`
                        ).join('');
                    } else {
                        container.innerHTML = `
                            <div class="col-12">
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle me-2"></i>No categories found.
                                </div>
                            </div>`;
                    }
                })
                .catch(error => {
                    console.error('Error loading categories:', error);
                    document.getElementById('categoriesContainer').innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-danger text-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>Error loading categories
                            </div>
                        </div>`;
                });
        }

        // Load categories when page loads
        document.addEventListener('DOMContentLoaded', loadCategories);
    </script>
</body>

</html>