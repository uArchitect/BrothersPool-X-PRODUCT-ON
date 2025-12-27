document.addEventListener('DOMContentLoaded', () => {
    const productAddForm = document.getElementById('productAddForm');

    productAddForm.addEventListener('submit', function(event) {
        event.preventDefault();
        let isValid = true;
        let errorMessage = "";
        const requiredFields = [
            'productCode',
            'category',
            'unit',
            'productName',
            'buyPrice',
            'sellPrice'
        ];

        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
                errorMessage += `${field.previousElementSibling.innerText} zorunludur.\n`;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            alert(errorMessage);
            return;
        }

        alert("Form başarıyla gönderildi!");
        productAddForm.submit();
    });

    const inputFields = document.querySelectorAll('#productAddForm input, #productAddForm select');
    inputFields.forEach(field => {
        field.addEventListener('input', () => {
            if (field.value.trim()) {
                field.classList.remove('is-invalid');
            }
        });
    });
});