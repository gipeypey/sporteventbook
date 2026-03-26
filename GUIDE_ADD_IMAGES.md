# 📸 Panduan Menambahkan Gambar ke SportEventBook

## 📁 Struktur Folder Gambar

Semua gambar disimpan di folder: `public/assets/images/`

```
public/assets/images/
├── events/          # Gambar untuk event
├── news/            # Gambar untuk berita
├── runners/         # Gambar untuk runner/atlet
├── sponsors/        # Logo sponsor
├── hero/            # Gambar hero section
└── [root]           # Gambar umum (running.png, dll)
```

---

## 🖼️ Cara Menambahkan Gambar Event

### Metode 1: Upload via Admin Panel (Recommended)
1. Login ke admin panel (`/admin`)
2. Navigasi ke **Events** → **Create Event** atau **Edit Event**
3. Upload gambar di field **Image**
4. Gambar akan otomatis tersimpan di `public/assets/images/events/`

### Metode 2: Manual Upload
1. Siapkan gambar dengan format: JPG, PNG, atau WebP
2. Rename file dengan format: `nama-event.jpg` (gunakan huruf kecil dan tanda strip)
3. Simpan di folder: `public/assets/images/events/`
4. Update database event dengan path gambar:
   ```sql
   UPDATE events SET image = 'assets/images/events/nama-event.jpg' WHERE id = 1;
   ```

### Ukuran Gambar yang Disarankan:
- **Event Cards**: 800x600px atau 4:3 ratio
- **File size**: Max 500KB untuk loading cepat
- **Format**: JPG atau WebP

---

## 📰 Cara Menambahkan Gambar News

### Metode 1: Via Database
1. Simpan gambar di `public/assets/images/news/`
2. Update field `image` di tabel `news`:
   ```sql
   UPDATE news SET image = 'assets/images/news/nama-berita.jpg' WHERE id = 1;
   ```

### Metode 2: Via Admin Panel
1. Login ke admin panel
2. Navigasi ke **News** → **Create News**
3. Upload gambar langsung dari form

### Ukuran Gambar yang Disarankan:
- **News Cards**: 600x400px atau 3:2 ratio
- **File size**: Max 300KB
- **Format**: JPG, PNG, atau WebP

---

## 🏃 Cara Menambahkan Gambar Runner

1. Simpan foto runner di `public/assets/images/runners/`
2. Format file: `nama-runner.jpg`
3. Update database runners:
   ```sql
   UPDATE runners SET photo = 'nama-runner.jpg' WHERE id = 1;
   ```

### Ukuran Gambar yang Disarankan:
- **Profile Photo**: 200x200px (square)
- **File size**: Max 100KB
- **Format**: JPG atau PNG

---

## 🏢 Cara Menambahkan Logo Sponsor

1. Simpan logo di `public/assets/images/sponsors/`
2. Format file: `nama-perusahaan.png` (PNG dengan background transparan)
3. Update database sponsors:
   ```sql
   UPDATE sponsors SET logo = 'nama-perusahaan.png' WHERE id = 1;
   ```

### Ukuran Gambar yang Disarankan:
- **Tier 1**: 300x150px
- **Tier 2**: 200x100px
- **Tier 3**: 150x75px
- **Format**: PNG dengan background transparan

---

## 🎨 Cara Menambahkan Gambar Hero

1. Simpan gambar di `public/assets/images/hero/`
2. Update file `resources/views/components/hero.blade.php`:
   ```blade
   <img src="{{ asset('assets/images/hero/nama-gambar.jpg') }}" alt="Hero">
   ```

### Ukuran Gambar yang Disarankan:
- **Hero Image**: 1200x800px atau lebih besar
- **File size**: Max 1MB
- **Format**: JPG atau WebP

---

## 🔧 Troubleshooting

### Gambar Tidak Muncul?

1. **Cek path file**
   - Pastikan file ada di folder yang benar
   - Cek spelling (huruf besar/kecil sensitif)

2. **Cek permission**
   ```bash
   chmod 755 public/assets/images
   chmod 644 public/assets/images/*
   ```

3. **Clear cache**
   ```bash
   php artisan cache:clear
   php artisan view:clear
   npm run build
   ```

4. **Hard refresh browser**
   - Windows: `Ctrl + Shift + R`
   - Mac: `Cmd + Shift + R`

### Gambar Broken/Pixelated?

1. Gunakan gambar dengan resolusi yang cukup
2. Kompres gambar tanpa mengurangi kualitas (TinyPNG, Squoosh)
3. Gunakan format WebP untuk kualitas lebih baik dengan ukuran lebih kecil

---

## 🛠️ Tools yang Berguna

| Tool | Fungsi |
|------|--------|
| [TinyPNG](https://tinypng.com/) | Kompres PNG/JPG |
| [Squoosh](https://squoosh.app/) | Convert & kompres gambar |
| [Canva](https://canva.com/) | Edit & resize gambar |
| [Remove.bg](https://remove.bg/) | Hapus background (untuk logo) |

---

## 📋 Checklist Upload Gambar

- [ ] Gambar sudah di folder yang benar
- [ ] Nama file menggunakan huruf kecil dan strip (-)
- [ ] Ukuran file sudah optimal (< 500KB)
- [ ] Format file sesuai (JPG/PNG/WebP)
- [ ] Database sudah diupdate dengan path yang benar
- [ ] Sudah clear cache
- [ ] Sudah hard refresh browser

---

## 💡 Tips

1. **Gunakan WebP** untuk kualitas lebih baik dengan ukuran lebih kecil
2. **Lazy loading** sudah otomatis aktif untuk gambar yang tidak terlihat
3. **Alt text** penting untuk SEO dan accessibility
4. **Backup** gambar secara berkala
5. **Konsisten** dengan ukuran dan format untuk tampilan yang rapi

---

## 📞 Butuh Bantuan?

Jika ada masalah, cek:
1. Browser console untuk error messages
2. Laravel logs di `storage/logs/laravel.log`
3. Network tab di DevTools untuk melihat request gambar