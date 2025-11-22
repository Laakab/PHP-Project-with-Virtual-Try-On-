function addCategory() {
    const categoryName = document.getElementById('categoryName').value;
    const formData = new FormData();
    formData.append('categoryName', categoryName);

    fetch('controllers/CategoryController.php?action=add', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            document.getElementById('categoryName').value = '';
            // You can also refresh the categories list here if needed
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding category.');
    });
}