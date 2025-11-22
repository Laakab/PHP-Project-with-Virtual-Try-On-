function addProduct() {
    const formData = new FormData(document.getElementById('productForm'));

    fetch('controllers/ProductController.php?action=add', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
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
        alert('❌ An error occurred while adding product.');
    });
}

function loadCategories() {
    fetch('controllers/ProductController.php?action=get_categories')
        .then(response => response.json())
        .then(categories => {
            const select = document.getElementById('categoryName');
            select.innerHTML = '<option value="">Select Category</option>';
            
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category;
                option.textContent = category;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading categories:', error);
        });
}

function resetForm() {
    document.getElementById('productForm').reset();
    document.getElementById('fileLabelText').textContent = 'Choose product image';
    document.getElementById('productColor').value = '#3498db';
}

// File input change handler
document.getElementById('productImage').addEventListener('change', function(e) {
    const fileName = e.target.files[0] ? e.target.files[0].name : 'Choose product image';
    document.getElementById('fileLabelText').textContent = fileName;
});

// Load categories when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
});