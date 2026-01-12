document.addEventListener('DOMContentLoaded', function () {
    // Promo code UI (non-functional)
    const promoCodeInput = document.getElementById('promo-code');
    const promoMessage = document.getElementById('promo-message');
    const promoDiscountRow = document.getElementById('promo-discount-row');
    const promoCodeName = document.getElementById('promo-code-name');
    const promoDiscountAmount = document.getElementById('promo-discount-amount');
    const grandTotal = document.getElementById('grand-total');

    // Show fake promo message when user types
    if (promoCodeInput) {
        promoCodeInput.addEventListener('input', function () {
            const code = this.value.trim();
            if (code) {
                promoMessage.textContent = 'Promo code feature coming soon!';
                promoMessage.className = 'text-sm font-semibold text-yellow-400';
                promoMessage.classList.remove('hidden');
            } else {
                promoMessage.classList.add('hidden');
            }
        });
    }

    // File upload preview (non-functional)
    const fileInput = document.getElementById('proof-upload');
    const fileLabel = document.getElementById('file-label');
    const filePreview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');

    if (fileInput) {
        fileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                fileName.textContent = file.name;
                fileLabel.textContent = 'File selected';
                filePreview.classList.remove('hidden');
            } else {
                fileLabel.textContent = 'Add an attachment';
                filePreview.classList.add('hidden');
            }
        });
    }

    // Remove any form validation that prevents submission
    const confirmPaymentBtn = document.getElementById('confirm-payment-btn');
    const confirmPaymentCheckbox = document.getElementById('confirm-payment');

    if (confirmPaymentBtn && confirmPaymentCheckbox) {
        // Remove any disabled state and allow form submission
        confirmPaymentBtn.disabled = false;
        confirmPaymentBtn.classList.remove('opacity-50', 'cursor-not-allowed');

        // Ensure checkbox starts unchecked
        confirmPaymentCheckbox.checked = false;
    }
});
