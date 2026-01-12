<x-filament-panels::page>
    <style>
        #reader {
            width: 100% !important;
            max-width: 500px !important;
            height: auto !important;
            margin: 0 auto;
        }

        #reader video {
            width: 100% !important;
            height: auto !important;
            border-radius: 0.5rem;
        }

        #reader__scan_region {
            border-radius: 0.5rem !important;
        }

        #reader__dashboard {
            background: transparent !important;
        }

        #reader__dashboard_section {
            background: transparent !important;
        }

        #reader__camera_permission_button {
            background: rgb(234 88 12) !important;
            color: white !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.375rem !important;
            border: none !important;
        }

        /* Result Card Styles */
        .result-card-success {
            background: rgb(240 253 244);
            border: 1px solid rgb(34 197 94);
            border-radius: 0.5rem;
            padding: 1.5rem;
        }

        .dark .result-card-success {
            background: rgba(34, 197, 94, 0.1);
            border-color: rgba(34, 197, 94, 0.2);
        }

        .result-card-error {
            background: rgb(254 242 242);
            border: 1px solid rgb(239 68 68);
            border-radius: 0.5rem;
            padding: 1.5rem;
        }

        .dark .result-card-error {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.2);
        }

        .result-icon-success {
            width: 3rem;
            height: 3rem;
            margin: 0 auto;
            color: rgb(34 197 94);
        }

        .result-icon-error {
            width: 3rem;
            height: 3rem;
            margin: 0 auto;
            color: rgb(239 68 68);
        }

        .result-title-success {
            font-size: 1.125rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1rem;
            color: rgb(21 128 61);
        }

        .dark .result-title-success {
            color: rgb(134 239 172);
        }

        .result-title-error {
            font-size: 1.125rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1rem;
            color: rgb(153 27 27);
        }

        .dark .result-title-error {
            color: rgb(252 165 165);
        }

        .booking-info {
            margin-top: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .booking-info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgb(229 231 235);
        }

        .dark .booking-info-row {
            border-bottom-color: rgb(55 65 81);
        }

        .booking-info-row:last-child {
            border-bottom: none;
        }

        .booking-label {
            font-size: 0.875rem;
            color: rgb(107 114 128);
        }

        .dark .booking-label {
            color: rgb(156 163 175);
        }

        .booking-value {
            font-weight: 500;
            color: rgb(17 24 39);
        }

        .dark .booking-value {
            color: rgb(243 244 246);
        }
    </style>

    <div class="space-y-6">
        <!-- Scanner Container -->
        <div id="scanner-container" style="background: rgb(31 41 55); border-radius: 0.5rem; padding: 1.5rem;">
            <div style="display: flex; flex-direction: column; align-items: center; gap: 1.5rem;">
                <h2 style="font-size: 1.25rem; font-weight: 600; color: white; text-align: center;">
                    Scan QR Code Ticket
                </h2>

                <!-- QR Scanner -->
                <div id="reader"
                    style="width: 100%; max-width: 500px; border: 2px solid rgb(75 85 99); border-radius: 0.5rem; overflow: hidden;">
                </div>

                <!-- Manual Input -->
                <div style="width: 100%; max-width: 500px; margin-top: 1rem;">
                    <label for="manual-code"
                        style="display: block; font-size: 0.875rem; font-weight: 500; color: rgb(209 213 219); margin-bottom: 0.5rem;">
                        Atau Masukkan Kode Manual
                    </label>
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" id="manual-code" placeholder="Contoh: BK0001"
                            style="flex: 1; padding: 0.5rem 0.75rem; border: 1px solid rgb(75 85 99); border-radius: 0.375rem; background: rgb(55 65 81); color: white;" />
                        <button onclick="validateManualCode()"
                            style="padding: 0.5rem 1rem; background: rgb(234 88 12); color: white; border-radius: 0.375rem; border: none; cursor: pointer; font-weight: 500;"
                            onmouseover="this.style.background='rgb(194 65 12)'"
                            onmouseout="this.style.background='rgb(234 88 12)'">
                            Validate
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Result Display -->
        <div id="result-container" style="display: none;">
            <div id="result-card" style="background: rgb(31 41 55); border-radius: 0.5rem; padding: 1.5rem;">
                <!-- Result will be dynamically inserted here -->
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
        <script>
            let html5QrCode;
            let isScanning = false;

            // Initialize QR Scanner
            function initializeScanner() {
                html5QrCode = new Html5Qrcode("reader");

                const config = {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    },
                    aspectRatio: 1.0
                };

                html5QrCode.start({
                        facingMode: "environment"
                    },
                    config,
                    onScanSuccess,
                    onScanError
                ).catch(err => {
                    console.error("Unable to start scanning", err);
                    showNotification('Error', 'Unable to access camera. Please check permissions.', 'danger');
                });
            }

            function onScanSuccess(decodedText, decodedResult) {
                if (isScanning) return;
                isScanning = true;

                // Stop scanner temporarily
                html5QrCode.pause();

                // Validate the ticket
                validateTicket(decodedText);
            }

            function onScanError(error) {
                // Ignore scan errors (they occur frequently during scanning)
            }

            function validateTicket(code) {
                // Get browser timezone
                const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

                // Call Livewire method to validate ticket
                @this.call('validateTicket', code, timezone).then(result => {
                    displayResult(result);
                });
            }

            function validateManualCode() {
                const code = document.getElementById('manual-code').value.trim();

                if (!code) {
                    showNotification('Error', 'Please enter a ticket code', 'danger');
                    return;
                }

                validateTicket(code);
                document.getElementById('manual-code').value = '';
            }

            function displayResult(result) {
                const container = document.getElementById('result-container');
                const card = document.getElementById('result-card');
                const scannerContainer = document.getElementById('scanner-container');

                // Icon based on success/failure
                let iconClass = result.success ? 'result-icon-success' : 'result-icon-error';
                let icon = result.success ?
                    `<svg class="${iconClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>` :
                    `<svg class="${iconClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;

                let bookingInfo = '';
                if (result.booking) {
                    bookingInfo = `
                        <div class="booking-info">
                            <div class="booking-info-row">
                                <span class="booking-label">Code:</span>
                                <span class="booking-value">${result.booking.code}</span>
                            </div>
                            <div class="booking-info-row">
                                <span class="booking-label">Name:</span>
                                <span class="booking-value">${result.booking.name}</span>
                            </div>
                            <div class="booking-info-row">
                                <span class="booking-label">Event:</span>
                                <span class="booking-value">${result.booking.event_name}</span>
                            </div>
                            <div class="booking-info-row">
                                <span class="booking-label">Venue:</span>
                                <span class="booking-value">${result.booking.venue_name}</span>
                            </div>
                            <div class="booking-info-row">
                                <span class="booking-label">Payment:</span>
                                <span class="booking-value">${result.booking.payment_status}</span>
                            </div>
                            ${result.checked_in_at ? `
                            <div class="booking-info-row">
                                <span class="booking-label">Checked In:</span>
                                <span class="booking-value">${result.checked_in_at}</span>
                            </div>
                            ` : ''}
                        </div>
                    `;
                }

                let cardClass = result.success ? 'result-card-success' : 'result-card-error';
                let titleClass = result.success ? 'result-title-success' : 'result-title-error';

                card.innerHTML = `
                    <div class="${cardClass}">
                        <div style="margin-bottom: 1rem;">
                            ${icon}
                        </div>
                        <h3 class="${titleClass}">
                            ${result.message}
                        </h3>
                        ${bookingInfo}
                        <div style="margin-top: 1.5rem; text-align: center;">
                            <button onclick="scanAgain()"
                                style="padding: 0.75rem 2rem; background: rgb(234 88 12); color: white; border-radius: 0.5rem; border: none; cursor: pointer; font-weight: 600; font-size: 1rem;"
                                onmouseover="this.style.background='rgb(194 65 12)'"
                                onmouseout="this.style.background='rgb(234 88 12)'">
                                Scan Ticket Lain
                            </button>
                        </div>
                    </div>
                `;

                // Hide scanner and show result
                scannerContainer.style.display = 'none';
                container.style.display = 'block';

                // Show notification
                showNotification(
                    result.success ? 'Success' : 'Error',
                    result.message,
                    result.success ? 'success' : 'danger'
                );
            }

            function scanAgain() {
                // Refresh halaman untuk reset scanner
                window.location.reload();
            }

            function showNotification(title, message, type) {
                window.dispatchEvent(new CustomEvent('notify', {
                    detail: {
                        title,
                        message,
                        type
                    }
                }));
            }

            // Initialize scanner when page loads
            document.addEventListener('DOMContentLoaded', function() {
                initializeScanner();
            });

            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                if (html5QrCode) {
                    html5QrCode.stop();
                }
            });
        </script>
    @endpush
</x-filament-panels::page>
