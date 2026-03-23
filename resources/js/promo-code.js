// Promo Code Functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Promo code script loaded');
    
    // Wait a bit to ensure bookingData is available
    setTimeout(function() {
        if (!window.bookingData) {
            console.error('Booking data not available');
            return;
        }
        
        console.log('Booking data found:', window.bookingData);
        
        const promoCodeInput = document.getElementById('promo-code');
        const applyPromoBtn = document.getElementById('apply-promo-btn');
        const removePromoBtn = document.getElementById('remove-promo-btn');
        const promoMessage = document.getElementById('promo-message');
        const promoDiscountRow = document.getElementById('promo-discount-row');
        const promoCodeName = document.getElementById('promo-code-name');
        const promoDiscountAmount = document.getElementById('promo-discount-amount');
        const grandTotalElement = document.getElementById('grand-total');
        
        if (!promoCodeInput || !applyPromoBtn || !grandTotalElement) {
            console.error('Required elements not found');
            return;
        }
        
        console.log('All promo elements found');
        
        const baseSubTotal = window.bookingData.subTotal;
        const baseTax = window.bookingData.tax;
        const baseInsurance = window.bookingData.insurance;
        
        function updateGrandTotal(total) {
            grandTotalElement.textContent = 'Rp ' + Math.max(0, total).toLocaleString('id-ID');
        }
        
        async function applyPromoCode(code) {
            try {
                const response = await fetch(window.bookingData.applyPromoUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ promo_code: code })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    promoMessage.textContent = result.message;
                    promoMessage.className = 'text-sm font-semibold text-green-600';
                    promoMessage.classList.remove('hidden');
                    
                    promoDiscountRow.style.display = 'flex';
                    promoCodeName.textContent = code;
                    promoDiscountAmount.textContent = '-Rp ' + (result.discount || 0).toLocaleString('id-ID');
                    
                    const discount = result.discount || 0;
                    const newTotal = baseSubTotal + baseTax + baseInsurance - discount;
                    updateGrandTotal(newTotal);
                    
                    removePromoBtn.classList.remove('hidden');
                    promoCodeInput.disabled = true;
                    applyPromoBtn.disabled = true;
                } else {
                    promoMessage.textContent = result.error || 'Invalid promo code';
                    promoMessage.className = 'text-sm font-semibold text-red-500';
                    promoMessage.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error:', error);
                promoMessage.textContent = 'Error applying promo code';
                promoMessage.className = 'text-sm font-semibold text-red-500';
                promoMessage.classList.remove('hidden');
            }
        }
        
        async function removePromoCode() {
            try {
                const response = await fetch(window.bookingData.removePromoUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    const baseTotal = baseSubTotal + baseTax + baseInsurance;
                    updateGrandTotal(baseTotal);
                    
                    promoDiscountRow.style.display = 'none';
                    promoMessage.classList.add('hidden');
                    removePromoBtn.classList.add('hidden');
                    promoCodeInput.disabled = false;
                    promoCodeInput.value = '';
                    applyPromoBtn.disabled = false;
                }
            } catch (error) {
                console.error('Error removing promo:', error);
            }
        }
        
        // Add event listeners
        applyPromoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Apply button clicked!');
            const code = promoCodeInput.value.trim();
            if (code) {
                applyPromoCode(code);
            } else {
                promoMessage.textContent = 'Please enter a promo code';
                promoMessage.className = 'text-sm font-semibold text-red-500';
                promoMessage.classList.remove('hidden');
            }
        });
        
        promoCodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const code = promoCodeInput.value.trim();
                if (code) {
                    applyPromoCode(code);
                }
            }
        });
        
        removePromoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            removePromoCode();
        });
        
        console.log('Promo code functionality initialized');
    }, 100);
});