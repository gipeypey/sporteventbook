# Dokumentasi Aplikasi SportEventBook

## Deskripsi Umum
SportEventBook adalah platform booking event olahraga yang komprehensif dengan sistem pembayaran, manajemen booking, dan admin panel menggunakan Filament. Aplikasi ini dibangun dengan Laravel dan menggunakan berbagai teknologi modern untuk memberikan pengalaman booking yang lancar.

## Struktur Aplikasi

### Model Utama
- **User**: Manajemen pengguna dan otentikasi
- **Event**: Informasi acara olahraga (judul, tanggal, harga, kategori, venue)
- **Booking**: Proses booking dengan status dan riwayat
- **Venue**: Lokasi tempat penyelenggaraan event
- **PromoCode**: Sistem diskon dengan berbagai jenis
- **Setting**: Konfigurasi aplikasi
- **Withdrawal**: Manajemen penarikan dana

### Fitur Utama

#### 1. Booking System
- Multi-step booking flow (detail peserta → pembayaran → konfirmasi)
- Session-based booking untuk menyimpan data sementara
- Validasi data dan pengecekan kapasitas
- Generate invoice PDF otomatis

#### 2. Payment Integration
- Integrasi dengan Midtrans sebagai payment gateway
- Callback handling untuk status pembayaran
- Email konfirmasi pembayaran
- Retry payment untuk pembayaran yang gagal

#### 3. Admin Panel (Filament)
- Dashboard statistik booking
- CRUD lengkap untuk semua entitas
- Export data booking (PDF, Excel, CSV)
- QR Code scanner untuk check-in manual
- Theme toggle (light/dark mode)

#### 4. Promo Codes
- Sistem diskon dengan dua jenis:
  - Percentage: Diskon dalam persentase
  - Fixed: Diskon dalam jumlah tetap
- Minimum amount requirement
- Usage limit dan expiration dates
- Active/inactive status

#### 5. Reporting & Exports
- Export booking dalam berbagai format (PDF, Excel, CSV)
- Filter berdasarkan status dan tanggal
- Dashboard statistik real-time

#### 6. Email Notifications
- Booking confirmation email
- Digital ticket delivery
- Payment reminder emails
- Template email yang responsif

## Perbaikan Fungsi Apply Promo Code

### Masalah Awal
Tombol Apply promo code tidak merespon saat diklik, meskipun endpoint API berfungsi dengan baik.

### Analisis Masalah
- File JavaScript `payment.js` terlalu kompleks dan memiliki potensi konflik
- Event listener tidak dipasang dengan benar
- Timing issue antara loading booking data dan inisialisasi JavaScript
- Potensi duplikasi event listener

### Solusi yang Diterapkan

#### 1. Pembuatan File JavaScript Terpisah
File baru `resources/js/promo-code.js` dibuat dengan fokus eksklusif pada fungsi promo code:

```javascript
// Promo Code Functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Promo code script loaded');
    
    // Wait a bit to ensure bookingData is available
    setTimeout(function() {
        if (!window.bookingData) {
            console.error('Booking data not available');
            return;
        }
        
        // ... implementasi fungsi apply/remove promo code
    }, 100);
});
```

#### 2. Update Layout Reference
File `resources/views/layouts/main.blade.php` diupdate untuk menggunakan file baru:
```html
<script src="{{ asset('assets/js/promo-code.js') }}" defer></script>
```

#### 3. Implementasi Fungsi Promo Code
- **Apply Promo Code**: Mengirim POST request ke `/bookings/{slug}/apply-promo`
- **Remove Promo Code**: Mengirim POST request ke `/bookings/{slug}/remove-promo`
- **Update UI**: Menampilkan diskon, menghitung ulang total, menampilkan pesan status

#### 4. Error Handling
- Validasi input promo code
- Penanganan error API
- Feedback visual untuk pengguna

### Endpoint API
- `POST /bookings/{slug}/apply-promo` - Menerapkan promo code
- `POST /bookings/{slug}/remove-promo` - Menghapus promo code

### Validasi Promo Code
- Cek keberadaan dan status aktif
- Cek minimum amount
- Cek usage limit
- Cek expiration date
- Cek apakah promo code sudah digunakan

## Database Structure

### Migration Files
- `create_users_table.php` - User management
- `create_events_table.php` - Event information
- `create_bookings_table.php` - Booking records
- `create_promo_codes_table.php` - Promo code management
- `create_booking_status_histories_table.php` - Status history tracking

### Seeder Files
- `EventSeeder.php` - Sample events
- `VenueSeeder.php` - Sample venues
- `AdditionalPromoCodeSeeder.php` - Sample promo codes
- `EventPrizeSeeder.php` - Event prizes

## Testing
- Feature tests untuk booking expiry
- Feature tests untuk Midtrans callback
- Feature tests untuk promo code functionality
- Payment status enum tests

## Deployment
- Environment configuration (.env.example)
- Database migration ready
- Queue worker support
- File storage configuration

## Teknologi yang Digunakan
- **Backend**: Laravel 11.x
- **Frontend**: Tailwind CSS, JavaScript vanilla
- **Admin Panel**: Filament PHP
- **Payment Gateway**: Midtrans
- **Database**: MySQL
- **Queue**: Redis/Database
- **File Storage**: Local/Cloud storage

## Kontribusi
Aplikasi ini dirancang untuk mudah dikembangkan dan dimaintain dengan struktur kode yang bersih dan dokumentasi yang lengkap.