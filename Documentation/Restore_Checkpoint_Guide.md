# Panduan Restore ke Checkpoint Aman - SportEventBook

Dokumen ini menjelaskan cara untuk mengembalikan aplikasi SportEventBook ke checkpoint aman yang telah dibuat pada tanggal 19 Maret 2026.

## Informasi Checkpoint

- **Tanggal Pembuatan**: 19 Maret 2026
- **Nama Tag**: `checkpoint-2026-03-19`
- **Deskripsi**: Complete analysis and promo code implementation - Safe checkpoint for development
- **Commit Hash**: `98bd733` (dapat berubah, lihat dengan `git log --oneline`)

## Daftar Isi
1. [Verifikasi Checkpoint](#verifikasi-checkpoint)
2. [Metode Restore](#metode-restore)
3. [Soft Reset (Rekomendasi)](#soft-reset-rekomendasi)
4. [Hard Reset (Hati-hati!)](#hard-reset-hati-hati)
5. [Checkout ke Branch Baru](#checkout-ke-branch-baru)
6. [Revert Commits](#revert-commits)
7. [Prosedur Pemulihan Lengkap](#prosedur-pemulihan-lengkap)

## Verifikasi Checkpoint

Untuk melihat semua tag yang tersedia:
```bash
git tag -l
```

Untuk melihat detail commit dari checkpoint:
```bash
git show checkpoint-2026-03-19
```

## Metode Restore

### Soft Reset (Rekomendasi)
Gunakan metode ini jika Anda ingin kembali ke checkpoint tapi menyimpan perubahan saat ini sebagai staged changes.

```bash
# Pastikan berada di branch yang benar
git checkout main

# Lakukan soft reset ke checkpoint
git reset --soft checkpoint-2026-03-19

# Verifikasi status
git status
```

**Keuntungan**: 
- Mengembalikan kode ke kondisi checkpoint
- Menyimpan semua perubahan yang belum di-commit
- Aman untuk eksperimen

### Hard Reset (Hati-hati!)
⚠️ **PERINGATAN**: Metode ini akan menghapus semua perubahan setelah checkpoint!

```bash
# Pastikan berada di branch yang benar
git checkout main

# Lakukan hard reset ke checkpoint
git reset --hard checkpoint-2026-03-19

# Force push jika perlu (HATI-HATI!)
git push --force-with-lease origin main
```

**Peringatan**:
- Semua perubahan setelah checkpoint akan hilang
- Gunakan hanya jika yakin
- Jika sudah di-push, perlu force push yang berisiko

### Checkout ke Branch Baru
Gunakan metode ini untuk eksplorasi dari checkpoint tanpa mengganggu branch utama.

```bash
# Buat branch baru dari checkpoint
git checkout -b recovery-branch checkpoint-2026-03-19

# Atau jika ingin kembali ke main setelah itu
git checkout main
git reset --hard recovery-branch
```

### Revert Commits
Gunakan jika hanya ingin membatalkan beberapa commit terakhir.

```bash
# Lihat commit terakhir
git log --oneline

# Revert satu commit
git revert <commit-hash>

# Revert range of commits (terbaru ke terlama)
git revert <newest-commit>^..<oldest-commit>
```

## Prosedur Pemulihan Lengkap

### Langkah 1: Backup Lokal (Opsional tapi Disarankan)
```bash
# Buat backup branch
git branch backup-before-restore-$(date +%Y%m%d)
```

### Langkah 2: Pilih Metode Restore
Berdasarkan kebutuhan Anda, pilih salah satu metode di atas.

### Langkah 3: Restore Dependencies
Setelah restore, mungkin perlu menginstall ulang dependencies:
```bash
composer install
npm install
```

### Langkah 4: Restore Database (Jika Diperlukan)
```bash
# Jika ada perubahan migrasi setelah checkpoint
php artisan migrate:refresh --seed
```

### Langkah 5: Verifikasi Aplikasi
```bash
# Jalankan aplikasi
php artisan serve

# Jalankan tests
php artisan test
```

## Tips Keamanan

1. **Selalu backup sebelum restore**
2. **Gunakan soft reset untuk eksperimen**
3. **Hindari hard reset jika sudah di-push ke remote**
4. **Gunakan branch untuk percobaan**
5. **Verifikasi aplikasi setelah restore**

## Troubleshooting

### Jika tag tidak ditemukan:
```bash
# Pull tags dari remote
git fetch --all --tags
```

### Jika repository tidak sinkron:
```bash
# Fetch semua perubahan
git fetch origin
git pull origin main
```

### Jika ada conflict:
```bash
# Resolve conflicts secara manual
git add .
git commit
```

## Catatan Penting

- Checkpoint ini mencakup semua analisis aplikasi dan implementasi seeder promo code
- Semua perubahan yang telah dicatat di `.cline_session_log.json` juga termasuk
- Pastikan memahami konsekuensi dari setiap metode restore sebelum melaksanakan
- Simpan dokumen ini di tempat yang aman untuk referensi masa depan