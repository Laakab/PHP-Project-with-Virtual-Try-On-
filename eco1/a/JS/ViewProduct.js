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
                            <div class="empty-state">
                                <i class="fas fa-box-open"></i>
                                <p>No products found</p>
                                <small>Add some products to get started</small>
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
                
                const row = document.createElement('tr');
                row.className = 'product-row';
                row.innerHTML = `
                    <td data-label="Name">${product.name}</td>
                    <td data-label="Description">${product.description}</td>
                    <td data-label="Category"><span class="status status-active">${product.category_name}</span></td>
                    <td data-label="Quantity">${product.quantity}</td>
                    <td data-label="Color">
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <div style="width: 20px; height: 20px; background-color: ${product.color}; border-radius: 3px; border: 1px solid #ddd;"></div>
                            ${product.color}
                        </div>
                    </td>
                    <td data-label="Size">${product.size}</td>
                    <td data-label="Discount">${product.discount}%</td>
                    <td data-label="Price">
                        <span class="price-original">${product.price} RS</span>
                    </td>
                    <td data-label="Discounted">
                        <span class="price-discounted">${discount.toFixed(2)} RS</span>
                    </td>
                    <td data-label="Delivery">${product.delivery_price} RS</td>
                    <td data-label="Returns">${product.return_days}</td>
                    <td data-label="Actions">
                        <div class="action-buttons">
                            <button class="action-btn edit" onclick="editProduct(${product.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete" onclick="deleteProduct(${product.id})" title="Delete">
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
                        <div class="empty-state error">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>Error loading products</p>
                            <small>Please check console for details</small>
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
                loadProducts(); // Reload the products list
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
    // Redirect to UpdateProduct.php with the product ID
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

// Load products when the page loads
document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
    
    // Add event listener for search input
    document.getElementById('searchInput').addEventListener('input', searchProducts);
});