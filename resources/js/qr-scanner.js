// QR Scanner JavaScript
let qrScanner = null;
let scanningInterval = null;

function getScannerElements() {
    return {
        video: document.getElementById('scanner-video'),
        canvas: document.getElementById('scanner-canvas'),
        status: document.getElementById('scanner-status')
    };
}

function startScanner() {
    const { video, canvas, status } = getScannerElements();

    // Only initialize if the video element exists on the DOM
    if (!video || !canvas || !status) {
        console.error('Scanner elements not found');
        return;
    }

    // Prevent initializing the scanner twice on the same element
    if (video.dataset.scannerActive === 'true') {
        console.log('Scanner already active, skipping initialization');
        return;
    }

    // Check if jsQR is available
    if (typeof jsQR === 'undefined') {
        status.textContent = 'Error: QR Scanner library tidak tersedia';
        status.classList.add('text-red-500');
        console.error('jsQR library not loaded');
        return;
    }

    // Check if getUserMedia is supported
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        status.textContent = 'Error: Browser tidak mendukung akses kamera';
        status.classList.add('text-red-500');
        console.error('getUserMedia not supported');
        return;
    }

    // Clean up existing scanner
    stopScanner();

    status.textContent = 'Meminta akses kamera...';
    status.classList.remove('text-red-500');

    // Request camera access with better error handling
    navigator.mediaDevices.getUserMedia({
        video: {
            facingMode: 'environment',
            width: { ideal: 1280, min: 640 },
            height: { ideal: 720, min: 480 }
        }
    })
        .then((stream) => {
            console.log('Camera stream obtained:', stream);

            video.srcObject = stream;
            video.dataset.scannerActive = 'true';

            // Wait for video to be ready
            video.onloadedmetadata = () => {
                console.log('Video metadata loaded');
                video.play()
                    .then(() => {
                        console.log('Video started playing');
                        status.textContent = 'Arahkan ke QR Code';
                        status.classList.remove('text-red-500');

                        // Start scanning loop
                        const context = canvas.getContext('2d');

                        scanningInterval = setInterval(() => {
                            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                                // Set canvas size to match video
                                canvas.width = video.videoWidth;
                                canvas.height = video.videoHeight;

                                // Draw video frame to canvas
                                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                                // Get image data for QR detection
                                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);

                                // Try to detect QR code
                                const code = jsQR(imageData.data, imageData.width, imageData.height, {
                                    inversionAttempts: "dontInvert"
                                });

                                if (code) {
                                    console.log('QR Code detected:', code.data);
                                    console.log('QR Code length:', code.data ? code.data.length : 0);
                                    console.log('QR Code type:', typeof code.data);

                                    if (code.data && code.data.trim() !== '') {
                                        stopScanner();
                                        status.textContent = 'QR Code terdeteksi! Memproses...';
                                        status.classList.add('text-green-500');

                                        // Call the Livewire method
                                        const livewireElement = document.querySelector('[wire\\:id]');
                                        if (livewireElement) {
                                            Livewire.find(livewireElement.getAttribute('wire:id')).call('verifyBooking', code.data);
                                        } else {
                                            console.error('Livewire element not found');
                                        }
                                    } else {
                                        console.warn('QR Code detected but empty or invalid data:', code);
                                    }
                                }
                            }
                        }, 200); // Reduced frequency for better performance

                        console.log('QR Scanner: Camera started successfully');
                    })
                    .catch(err => {
                        console.error('Video play error:', err);
                        status.textContent = 'Error: Tidak dapat memutar video kamera';
                        status.classList.add('text-red-500');
                    });
            };

            video.onerror = (err) => {
                console.error('Video error:', err);
                status.textContent = 'Error: Masalah dengan video kamera';
                status.classList.add('text-red-500');
            };
        })
        .catch(err => {
            console.error('Scanner error:', err);

            let errorMessage = 'Error: Kamera tidak dapat diakses';

            if (err.name === 'NotAllowedError') {
                errorMessage = 'Error: Izin kamera ditolak. Silakan berikan izin kamera di pengaturan browser.';
            } else if (err.name === 'NotFoundError') {
                errorMessage = 'Error: Kamera tidak ditemukan. Pastikan perangkat memiliki kamera.';
            } else if (err.name === 'NotSupportedError') {
                errorMessage = 'Error: Browser tidak mendukung akses kamera.';
            } else if (err.name === 'NotReadableError') {
                errorMessage = 'Error: Kamera sedang digunakan oleh aplikasi lain.';
            }

            status.textContent = errorMessage;
            status.classList.add('text-red-500');
        });
}

function stopScanner() {
    const { video } = getScannerElements();

    if (scanningInterval) {
        clearInterval(scanningInterval);
        scanningInterval = null;
    }

    if (video && video.srcObject) {
        video.srcObject.getTracks().forEach(track => track.stop());
        video.srcObject = null;
        video.dataset.scannerActive = 'false';
    }
}

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function () {
    console.log('QR Scanner JS loaded');

    // Check if we're on the QR scanner page
    if (document.getElementById('scanner-video')) {
        console.log('QR Scanner page detected, starting scanner...');
        setTimeout(startScanner, 500);
    }
});

// Event listeners for Livewire
document.addEventListener('livewire:navigated', () => {
    console.log('Livewire navigated - checking for scanner');
    // Check if scanner elements exist before starting
    if (document.getElementById('scanner-video')) {
        console.log('Starting scanner after navigation');
        setTimeout(startScanner, 500);
    }
});

// Listener for the event from the backend to restart the scanner
window.addEventListener('restart-scanner', () => {
    console.log('Restart scanner event received');
    setTimeout(startScanner, 500);
});

// Ensure the scanner stops when navigating away from the page
document.addEventListener('livewire:navigating', () => {
    console.log('Livewire navigating - stopping scanner');
    stopScanner();
});

// Additional event listener for page visibility changes
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        console.log('Page hidden - stopping scanner');
        stopScanner();
    } else if (document.getElementById('scanner-video')) {
        console.log('Page visible - restarting scanner');
        setTimeout(startScanner, 500);
    }
});

// Export for global access
window.startScanner = startScanner;
window.stopScanner = stopScanner;
