// Booking challenge page - All functionality combined
document.addEventListener('DOMContentLoaded', function () {
    console.log('Booking challenge script loaded');

    // 1. DYNAMIC BACK NAVIGATION - Only override if 'from' parameter exists
    try {
        const params = new URLSearchParams(window.location.search);
        const from = params.get('from');
        const backLink = document.getElementById('backLink');

        if (backLink && from) {
            const map = {
                details: '/details',
                home: '/',
                event: '/event'
            };

            // Only override if 'from' parameter exists and has a valid mapping
            if (map[from]) {
                backLink.href = map[from];
                console.log('Back link overridden to:', map[from]);
            }
        }
    } catch (e) {
        console.log('Navigation fallback to original href');
    }

    // 2. PERSONAL INFO SECTION TOGGLE
    const personalInfoSection = document.querySelector('.bg-white.rounded-3xl.p-5');
    if (personalInfoSection) {
        const header = personalInfoSection.querySelector('.flex.items-center.justify-between');
        const arrow = header.querySelector('img[src*="arrow-circle-down"]');

        if (header && arrow) {
            // Collect all content after header (divider + form fields)
            const contentElements = [];
            let nextElement = header.nextElementSibling;

            // Collect all elements after header
            while (nextElement && nextElement.parentNode === personalInfoSection) {
                contentElements.push(nextElement);
                nextElement = nextElement.nextElementSibling;
            }

            // Initially show content so first action can be to close it
            if (contentElements.length > 0) {
                contentElements.forEach(el => {
                    el.style.display = 'block';
                });
                arrow.style.transform = 'rotate(180deg)';
            }

            // Add click event to header
            header.addEventListener('click', function () {
                if (contentElements.length === 0) return;
                const isVisible = contentElements[0].style.display !== 'none';
                if (isVisible) {
                    contentElements.forEach(el => {
                        el.style.display = 'none';
                    });
                    arrow.style.transform = 'rotate(0deg)';
                } else {
                    contentElements.forEach(el => {
                        el.style.display = 'block';
                    });
                    arrow.style.transform = 'rotate(180deg)';
                }
            });

            // Support clicking the arrow itself
            arrow.addEventListener('click', function (e) {
                e.stopPropagation();
                header.click();
            });

            header.style.cursor = 'pointer';
            arrow.style.cursor = 'pointer';
        }
    }
});
