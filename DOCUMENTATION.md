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
- **News**: Berita dan artikel terbaru (baru)
- **Sponsor**: Sponsor dan partner (baru)
- **Runner**: Data runner untuk ranking system (baru)

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

#### 7. Homepage Redesign (UTMB World Style) - Planned
- Full-width desktop layout (mengubah dari mobile-first)
- Navbar horizontal dengan dropdown menu
- Hero section dengan background HD
- Events carousel (3-column grid)
- Ranking section (Top 3 Runners - Men & Women)
- Latest News section
- Registration CTA section
- Sponsors grid
- Multi-column footer

#### 8. Ranking System - Planned
- Top 3 World ranking untuk Men dan Women
- Filter berdasarkan distance (20K, 50K, 100K, 100M)
- UTMB Index-style scoring

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

## Testing
- Feature tests untuk booking expiry
- Feature tests untuk Midtrans callback
- Feature tests untuk promo code functionality
- Payment status enum tests

---

## Deployment
- Environment configuration (.env.example)
- Database migration ready
- Queue worker support
- File storage configuration

---

## Teknologi yang Digunakan
- **Backend**: Laravel 11.x
- **Frontend**: Tailwind CSS, JavaScript vanilla, Swiper.js
- **Admin Panel**: Filament PHP
- **Payment Gateway**: Midtrans
- **Database**: MySQL
- **Queue**: Redis/Database
- **File Storage**: Local/Cloud storage

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

## Kontribusi
Aplikasi ini dirancang untuk mudah dikembangkan dan dimaintain dengan struktur kode yang bersih dan dokumentasi yang lengkap.