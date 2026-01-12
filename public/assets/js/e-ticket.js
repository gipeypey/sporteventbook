// E-ticket page dropdown functionality
document.addEventListener('DOMContentLoaded', function () {
    console.log('E-ticket dropdown script loaded');

    // Find the dropdown section with arrow icon (bottom card specifically)
    const dropdownSection = document.querySelector('.bg-white.rounded-3xl.p-5:last-of-type');
    if (dropdownSection) {
        const header = dropdownSection.querySelector('.flex.items-center.justify-between');
        const arrow = header.querySelector('img[src*="arrow-circle-down"]');

        if (header && arrow) {
            console.log('Found dropdown header and arrow');

            // Find the content to toggle (everything after the header)
            const contentElements = [];
            let nextElement = header.nextElementSibling;

            // Collect all elements after header
            while (nextElement && nextElement.parentNode === dropdownSection) {
                contentElements.push(nextElement);
                nextElement = nextElement.nextElementSibling;
            }

            console.log('Found content elements:', contentElements.length);

            // Create a wrapper div to maintain layout
            const wrapper = document.createElement('div');
            wrapper.className = 'dropdown-content-wrapper';
            wrapper.style.display = 'block'; // Show by default

            // Move all content into wrapper
            contentElements.forEach(el => {
                wrapper.appendChild(el);
            });

            // Insert wrapper after header
            header.parentNode.insertBefore(wrapper, header.nextSibling);

            // Add click event to header
            header.addEventListener('click', function () {
                console.log('E-ticket header clicked!');
                const isVisible = wrapper.style.display !== 'none';

                if (isVisible) {
                    // Hide content
                    wrapper.style.display = 'none';
                    arrow.style.transform = 'rotate(0deg)';
                } else {
                    // Show content
                    wrapper.style.display = 'block';
                    arrow.style.transform = 'rotate(180deg)';
                }
            });

            header.style.cursor = 'pointer';
        }
    }
});
