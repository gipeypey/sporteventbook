# Dokumentasi Aplikasi SportEventBook

## Deskripsi Umum
SportEventBook adalah platform booking event olahraga yang komprehensif dengan sistem pembayaran, manajemen booking, dan multi-panel admin menggunakan Filament. Aplikasi ini dibangun dengan Laravel dan menggunakan berbagai teknologi modern untuk memberikan pengalaman booking yang lancar.

## Struktur Aplikasi

### Model Utama
- **User**: Manajemen pengguna dan otentikasi (Admin, Venue Owner, Runner)
- **Event**: Informasi acara olahraga (judul, tanggal, harga, kategori, venue)
- **Booking**: Proses booking dengan status dan riwayat
- **Venue**: Lokasi tempat penyelenggaraan event
- **PromoCode**: Sistem diskon dengan berbagai jenis
- **Setting**: Konfigurasi aplikasi
- **Withdrawal**: Manajemen penarikan dana
- **EventCategory**: Kategori event (Trail Running, Road Race, dll)
- **EventPrize**: Hadiah tambahan untuk event
- **BookingStatusHistory**: Riwayat perubahan status booking

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

#### 3. Multi-Panel Admin (Filament)
- **Admin Panel**: Dashboard statistik booking, CRUD lengkap untuk semua entitas
- **Venue Owner Panel**: Manajemen venue dan event
- **Runner Panel** (Planned): Dashboard untuk runner/peserta
  - Lihat history booking
  - Lihat upcoming events
  - Edit profile
  - Download tiket

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

#### 7. Event Management
- CRUD event dengan form terstruktur (Informasi Event, Detail Event, Pendaftaran & Harga, Hadiah Tambahan)
- Upload gambar event dengan editor (crop, rotate, aspect ratio)
- Status event (Open, Closed, Ended)
- Winner management untuk event yang sudah selesai

#### 8. Image Upload & Display
- Upload gambar event ke `public/assets/images/events/`
- Upload gambar hadiah ke `public/assets/images/event-prizes/`
- Image editor dengan aspect ratio presets (16:9, 4:3, 1:1)
- Auto-generate image URL dengan fallback ke placeholder
- Support untuk multiple image path formats

---

## Design System

### Color Palette (Purple & Gold Theme)

**Primary Colors:**
- Primary: `#7B2CBF` (Deep Purple)
- Secondary: `#1A1A2E` (Dark Navy)
- Accent: `#FFD700` (Gold)

**Neutral Colors:**
- Background: `#FFFFFF` (White)
- Background Alt: `#F5F5F5` (Light Gray)
- Text Dark: `#1A1A2E` (Dark Navy)
- Text Light: `#FFFFFF` (White)
- Text Muted: `#9BA4A6` (Gray)

**Status Colors:**
- Success: `#10B981` (Green)
- Warning: `#F59E0B` (Amber)
- Error: `#EF4444` (Red)
- Info: `#3B82F6` (Blue)

### Typography
- Font Family: Inter, system-ui, sans-serif
- Headings: Bold (700)
- Body: Regular (400)
- Small: Regular (400)

---

## Database Structure

### Migration Files

#### Existing Tables:
- `create_users_table.php` - User management
- `create_events_table.php` - Event information
- `create_bookings_table.php` - Booking records
- `create_promo_codes_table.php` - Promo code management
- `create_booking_status_histories_table.php` - Status history tracking
- `create_venues_table.php` - Venue information
- `create_event_categories_table.php` - Event categories
- `create_event_prizes_table.php` - Event prizes
- `create_settings_table.php` - Application settings
- `create_withdrawals_table.php` - Withdrawal management

#### New Tables (Planned):
- `create_news_table.php` - News and articles
  - id, title, slug, excerpt, content, image, author_id, published_at, is_published, timestamps
- `create_sponsors_table.php` - Sponsors and partners
  - id, name, logo, url, tier, is_active, order, timestamps
- `create_runners_table.php` - Runner profiles for ranking
  - id, name, photo, country, gender, utmb_index_20k, utmb_index_50k, utmb_index_100k, utmb_index_100m, is_active, timestamps

### Seeder Files
- `EventSeeder.php` - Sample events
- `VenueSeeder.php` - Sample venues
- `AdditionalPromoCodeSeeder.php` - Sample promo codes
- `EventPrizeSeeder.php` - Event prizes
- `NewsSeeder.php` (Planned) - Sample news articles
- `SponsorSeeder.php` (Planned) - Sample sponsors
- `RunnerSeeder.php` (Planned) - Sample runner rankings

---

## File Structure & Storage

### Image Storage Configuration

**Config Filesystem (`config/filesystems.php`):**
```php
'public' => [
    'driver' => 'local',
    'root' => public_path('assets/images'),
    'url' => '/assets/images',
    'visibility' => 'public',
],
```

### Image Directories:
- `public/assets/images/events/` - Event thumbnails
- `public/assets/images/event-prizes/` - Prize images

### Event Model - Image URL Accessor

Model Event memiliki accessor `image_url` yang otomatis resolve berbagai format path:
```php
// Di Event.php
public function getImageUrlAttribute(): ?string
{
    if (!$this->image) {
        return null;
    }

    // Check if it's a full URL
    if (filter_var($this->image, FILTER_VALIDATE_URL)) {
        return $this->image;
    }

    // Check various path formats...
    // Returns asset() URL or null
}
```

**Penggunaan di Blade:**
```blade
<img src="{{ $event->image_url }}" alt="{{ $event->title }}">
```

---

## Testing
- Feature tests untuk booking expiry
- Feature tests untuk Midtrans callback
- Feature tests untuk promo code functionality
- Payment status enum tests

---

## Teknologi yang Digunakan
- **Backend**: Laravel 11.x
- **Frontend**: Tailwind CSS, JavaScript vanilla, Swiper.js
- **Admin Panel**: Filament PHP
- **Payment Gateway**: Midtrans
- **Database**: MySQL
- **Queue**: Redis/Database
- **File Storage**: Local (public disk)

---

## Bug Fixes & Updates

### Maret 2026

#### 1. Fix: Event Image Upload Path
**Masalah:** Gambar event tidak muncul karena path storage salah.

**Solusi:**
- Ubah `config/filesystems.php` root ke `public_path('assets/images')`
- Update EventForm dengan helper text yang jelas
- Tambahkan accessor `getImageUrlAttribute()` di Event model
- Update semua view untuk menggunakan `$event->image_url`

**Files Modified:**
- `config/filesystems.php`
- `app/Filament/Resources/EventResource/Schemas/EventForm.php`
- `app/Models/Event.php`
- `resources/views/events/show.blade.php`
- `resources/views/events/index.blade.php`
- `resources/views/components/events-carousel.blade.php`

#### 2. Fix: Status Badge Position
**Masalah:** Badge "Open Registration" menutupi logo event di halaman show.

**Solusi:** Pindahkan badge dari `top-4 left-4` ke `bottom-6 left-6`

**Files Modified:**
- `resources/views/events/show.blade.php`

#### 3. Fix: Available Distances Display
**Masalah:** Section "Available Distances" menampilkan nama hadiah instead of distance.

**Solusi:** Extract distance dari Event Category name menggunakan regex pattern `/(\d+K)/i`

**Files Modified:**
- `resources/views/events/show.blade.php`

#### 4. Fix: Remove Redundant Location Field
**Masalah:** Field "Location" di Event Information redundan dengan "Venue".

**Solusi:** Hapus field "Location" dari Event Information card.

**Files Modified:**
- `resources/views/events/show.blade.php`

---

## Roadmap Pengembangan

### Phase 1: Homepage Redesign
- [ ] Membuat migration untuk news, sponsors, runners tables
- [ ] Membuat models dan seeders
- [ ] Membuat navbar component dengan dropdown
- [ ] Membuat hero section dengan background HD
- [ ] Membuat events carousel component
- [ ] Membuat ranking component (Top 3 Men/Women)
- [ ] Membuat news component
- [ ] Membuat registration CTA component
- [ ] Membuat sponsors component
- [ ] Membuat footer component
- [ ] Update HomeController
- [ ] Update home.blade.php

### Phase 2: Runner Dashboard (Filament)
- [ ] Setup Filament panel untuk Runner
- [ ] Dashboard untuk melihat booking history
- [ ] Dashboard untuk melihat upcoming events
- [ ] Profile management untuk runner
- [ ] Download tiket dari dashboard

### Phase 3: Additional Features
- [ ] UTMB Index-style scoring system
- [ ] Leaderboard ranking
- [ ] Achievement badges
- [ ] Performance statistics

---

## Deployment

### Local Development
1. Clone repository
2. `composer install`
3. `npm install && npm run build`
4. Copy `.env.example` ke `.env`
5. `php artisan key:generate`
6. `php artisan migrate --seed`
7. `php artisan storage:link`
8. `php artisan serve`

### Production Deployment (Nutanix Kubernetes)
Lihat: [Kubernetes Deployment Guide](KUBERNETES_DEPLOYMENT.md)

---

## Kontribusi
Aplikasi ini dirancang untuk mudah dikembangkan dan dimaintain dengan struktur kode yang bersih dan dokumentasi yang lengkap.