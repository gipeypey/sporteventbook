// Immediately execute when script loads
console.log('Payment.js loaded');

// Wait for window load to ensure all elements and scripts are fully loaded
window.addEventListener('load', function() {
    console.log('Window loaded, initializing promo code functionality...');
    
    // Small delay to ensure bookingData is available
    setTimeout(function() {
        // Check if bookingData is available
        if (!window.bookingData) {
            console.error('window.bookingData is not available');
            return;
        }

        console.log('Booking data found:', window.bookingData);

        // Function to initialize promo code functionality
        function initPromoCode() {
            // Promo code elements
            const promoCodeInput = document.getElementById('promo-code');
            const applyPromoBtn = document.getElementById('apply-promo-btn');
            const removePromoBtn = document.getElementById('remove-promo-btn');
            const promoMessage = document.getElementById('promo-message');
            const promoDiscountRow = document.getElementById('promo-discount-row');
            const promoCodeName = document.getElementById('promo-code-name');
            const promoDiscountAmount = document.getElementById('promo-discount-amount');
            const grandTotalElement = document.getElementById('grand-total');
            
            // Debug logging
            console.log('Elements found:', {
                promoCodeInput: !!promoCodeInput,
                applyPromoBtn: !!applyPromoBtn,
                removePromoBtn: !!removePromoBtn,
                promoMessage: !!promoMessage,
                promoDiscountRow: !!promoDiscountRow,
                grandTotalElement: !!grandTotalElement,
                bookingData: !!window.bookingData
            });

            if (!promoCodeInput || !applyPromoBtn || !grandTotalElement) {
                console.error('Required elements not found');
                console.log('Elements:', {
                    promoCodeInput: promoCodeInput,
                    applyPromoBtn: applyPromoBtn,
                    grandTotalElement: grandTotalElement
                });
                return;
            }

            // Store original values from PHP
            // These represent the base amounts without any discount applied
            const baseSubTotal = window.bookingData.subTotal;
            const baseTax = window.bookingData.tax;
            const baseInsurance = window.bookingData.insurance;
            const currentGrandTotal = window.bookingData.grandTotal;

            console.log('Base values (without discount):', { baseSubTotal, baseTax, baseInsurance });
            console.log('Current grand total (may include discount):', currentGrandTotal);

            // Function to update grand total display
            function updateGrandTotal(total) {
                console.log('Updating grand total to:', total);
                grandTotalElement.textContent = 'Rp ' + Math.max(0, total).toLocaleString('id-ID');
            }

            // Function to apply promo code
            async function applyPromoCode(code) {
                console.log('Applying promo code:', code);
                try {
                    const response = await fetch(window.bookingData.applyPromoUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ promo_code: code })
                    });

                    console.log('Response status:', response.status);
                    const result = await response.json();
                    console.log('API result:', result);

                    if (result.success) {
                        // Show success message
                        promoMessage.textContent = result.message;
                        promoMessage.className = 'text-sm font-semibold text-green-600';
                        promoMessage.classList.remove('hidden');

                        // Show discount row
                        promoDiscountRow.style.display = 'flex';
                        promoCodeName.textContent = code;
                        promoDiscountAmount.textContent = '-Rp ' + (result.discount || 0).toLocaleString('id-ID');

                        // Calculate new total with the applied discount
                        const discount = result.discount || 0;
                        const newTotal = baseSubTotal + baseTax + baseInsurance - discount;
                        console.log('New total calculated:', newTotal);
                        updateGrandTotal(newTotal);

                        // Show remove button
                        removePromoBtn.classList.remove('hidden');

                        // Disable input and apply button
                        promoCodeInput.disabled = true;
                        applyPromoBtn.disabled = true;
                    } else {
                        // Show error message
                        promoMessage.textContent = result.error || 'Invalid promo code';
                        promoMessage.className = 'text-sm font-semibold text-red-500';
                        promoMessage.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Error applying promo code:', error);
                    promoMessage.textContent = 'Error applying promo code';
                    promoMessage.className = 'text-sm font-semibold text-red-500';
                    promoMessage.classList.remove('hidden');
                }
            }

            // Function to remove promo code
            async function removePromoCode() {
                console.log('Removing promo code');
                try {
                    const response = await fetch(window.bookingData.removePromoUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    console.log('Remove response status:', response.status);
                    const result = await response.json();
                    console.log('Remove API result:', result);

                    if (result.success) {
                        // Reset to base total (without any discount)
                        const baseTotal = baseSubTotal + baseTax + baseInsurance;
                        console.log('Resetting to base total:', baseTotal);
                        updateGrandTotal(baseTotal);

                        // Hide discount row
                        promoDiscountRow.style.display = 'none';

                        // Hide message
                        promoMessage.classList.add('hidden');

                        // Hide remove button
                        removePromoBtn.classList.add('hidden');

                        // Enable input and clear it
                        promoCodeInput.disabled = false;
                        promoCodeInput.value = '';
                        applyPromoBtn.disabled = false;
                    }
                } catch (error) {
                    console.error('Error removing promo code:', error);
                }
            }

            // Apply promo button click handler
            if (applyPromoBtn) {
                console.log('Adding click listener to apply button');
                applyPromoBtn.removeEventListener('click', applyPromoCodeHandler); // Remove any existing listener
                applyPromoBtn.addEventListener('click', applyPromoCodeHandler);
            }

            // Click handler function
            function applyPromoCodeHandler(e) {
                e.preventDefault(); // Prevent any default form submission
                console.log('Apply button clicked');
                const code = promoCodeInput.value.trim();
                if (code) {
                    applyPromoCode(code);
                } else {
                    promoMessage.textContent = 'Please enter a promo code';
                    promoMessage.className = 'text-sm font-semibold text-red-500';
                    promoMessage.classList.remove('hidden');
                }
            }

            // Enter key support for promo code input
            if (promoCodeInput) {
                promoCodeInput.removeEventListener('keypress', handleEnterKeyPress); // Remove any existing listener
                promoCodeInput.addEventListener('keypress', handleEnterKeyPress);
            }

            // Enter key handler function
            function handleEnterKeyPress(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    console.log('Enter key pressed');
                    const code = promoCodeInput.value.trim();
                    if (code) {
                        applyPromoCode(code);
                    } else {
                        promoMessage.textContent = 'Please enter a promo code';
                        promoMessage.className = 'text-sm font-semibold text-red-500';
                        promoMessage.classList.remove('hidden');
                    }
                }
            }

            // Remove promo button click handler
            if (removePromoBtn) {
                removePromoBtn.removeEventListener('click', removePromoCodeHandler); // Remove any existing listener
                removePromoBtn.addEventListener('click', removePromoCodeHandler);
            }

            // Remove handler function
            function removePromoCodeHandler(e) {
                e.preventDefault();
                console.log('Remove button clicked');
                removePromoCode();
            }

            // File upload preview (non-functional)
            const fileInput = document.getElementById('proof-upload');
            const fileLabel = document.getElementById('file-label');
            const filePreview = document.getElementById('file-preview');
            const fileName = document.getElementById('file-name');

            if (fileInput) {
                fileInput.removeEventListener('change', handleFileChange); // Remove any existing listener
                fileInput.addEventListener('change', handleFileChange);
            }

            // File change handler
            function handleFileChange(e) {
                const file = e.target.files[0];
                if (file) {
                    fileName.textContent = file.name;
                    fileLabel.textContent = 'File selected';
                    filePreview.classList.remove('hidden');
                } else {
                    fileLabel.textContent = 'Add an attachment';
                    filePreview.classList.add('hidden');
                }
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
        }

        // Initialize immediately
        initPromoCode();

        // Also initialize again after a very short delay to ensure everything is ready
        setTimeout(initPromoCode, 100);
    }, 100); // Delay to ensure bookingData is available
});
