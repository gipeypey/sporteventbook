# Panduan Troubleshooting Deployment NKP (Nutanix Kubernetes Platform) - SportEventBook

Dokumen ini berisi catatan komprehensif mengenai berbagai rintangan *deployment* dan solusi yang diterapkan saat mendeploy aplikasi SportEventBook ke Nutanix Kubernetes Platform (NKP) via Flux CD (GitOps).

Jika di kemudian hari kluster di-reset atau dipindah, pastikan untuk membaca dokumen ini agar tidak mengulangi kesalahan/error yang sama.

---

## 1. Masalah Cache Runtime vs Build-time (Docker & K8s ConfigMap)

### Gejala
Job `migrate` atau `queue-worker` mengalami error koneksi database yang mengarah ke `database.sqlite`, padahal ConfigMap Kubernetes sudah menyuntikkan `DB_CONNECTION=mysql` dan IP `DB_HOST` milik NDB.

### Penyebab
Di dalam `Dockerfile` bawaan sebelumnya, terdapat eksekusi `RUN php artisan optimize`. Perintah ini mem-build *cache* konfigurasi Laravel pada saat pembuatan *Image Docker*. Karena pada saat pembuatan image tidak ada koneksi/informasi ke NDB, Laravel menyimpan *fallback* konfigurasi lokal (`sqlite`) ke dalam cache.
Ketika dijalankan di K8s, Laravel tetap membaca cache (SQLite) dan sama sekali mengabaikan seluruh `envFrom` dari ConfigMap yang di-inject oleh K8s.

### Solusi
Hapus `php artisan optimize` dari `Dockerfile` dan ganti dengan *cache* parsial yang tidak mengunci konfigurasi jaringan:
```dockerfile
# JANGAN GUNAKAN INI DI DOCKERFILE K8S:
# RUN php artisan optimize

# GUNAKAN INI:
RUN php artisan event:cache && php artisan route:cache && php artisan view:cache
```

---

## 2. Hilangnya Ekstensi Redis di PHP (Class 'Redis' Not Found)

### Gejala
Pod `queue-worker` tiba-tiba mengalami status `Error` dan `CrashLoopBackOff`. Jika dilihat dari lognya (`kubectl logs <nama-pod-worker>`), muncul pesan fatal:
`Class "Redis" not found in PhpRedisConnector.php`

### Penyebab
Aplikasi ini dikonfigurasi dengan `REDIS_CLIENT: phpredis` (ekstensi *native* PHP untuk Redis). Namun, `Dockerfile` berbasis `php:8.3-fpm` bawaan belum menyertakan ekstensi tersebut.

### Solusi
Tambahkan proses kompilasi ekstensi Redis (`pecl install redis`) ke dalam `Dockerfile` pada langkah instalasi ekstensi PHP:
```dockerfile
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl && \
    pecl install redis && docker-php-ext-enable redis
```
*Jangan lupa melakukan Build ulang image Docker dan menembaknya (Push) ke Harbor setiap ada perubahan relasi bahasa seperti ini.*

---

## 3. Worker CrashLoopBackOff Misterius Tanpa Output Log (Anti-Pattern K8s)

### Gejala
Pod `queue-worker` kembali masuk dalam siklus *CrashLoopBackOff* yang terus-menerus. Anehnya, saat mengecek log, perintah `kubectl logs` menampilkan hasil kosong.

### Penyebab
Perintah yang dieksekusi oleh Pod Queue Worker di dalam file manifest Kubernetes (`all-in-one.yaml`) diakhiri oleh argumen berbahaya: `--stop-when-empty`.
Di K8s, tipe _Deployment_ mengharapkan agar *container* selalu menyala (*Daemon*). Jika antrean kosong, Laravel langsung "mematikan" dirinya (*exit code 0*). Karena ia mati, K8s menganggapnya sebagai *Crash* / malfungsi dan langsung merestartnya terus-menerus.

### Solusi
Hapus argumen `--stop-when-empty` dari spesifikasi Command di file deployment K8s, sehingga Worker akan terus IDLE (tidur) menunggu ada antrean masuk tanpa harus bunuh diri.
```yaml
# Pada file all-in-one.yaml / queue-worker-deployment:
        command:
        - php
        - artisan
        - queue:work
        - --queue=database,default
        - --sleep=3
        # JANGAN GUNAKAN ARGUMEN INI -> - --stop-when-empty
```

---

## 4. Kegagalan Liveness Probe (Nginx & Laravel Terus-menerus Tersendat)

### Gejala
Sebagian besar Pod (terutama `nginx` dan `laravel-app`) umurnya tidak akan pernah lebih dari hitungan menit, angka `RESTARTS` terus bertambah, dan ujungnya berujung pada *CrashLoopBackOff*. Nginx sering komplain dengan error `502 Bad Gateway`.

### Penyebab
Kubernetes memiliki sensor nyawa bernama `livenessProbe` dan `readinessProbe` yang cacat pengaturan:
1. **Laravel-App:** K8s mengecek port `80` (menggunakan jalur web `httpGet`). Padahal, kontainer Laravel kita memakai mesin `php-fpm` (FastCGI) yang portnya hanya buka di `9000`. Probe ini selalu gagal (Connection Refused), K8s mengira Laravel mati, dan terus membunuhnya.
2. **Nginx:** K8s mengecek rute HTTP `/healthz`. Nginx mengalihkannya ke Laravel, tapi karena tidak ada rute spesifik bawaan di Laravel, K8s akan menerima *404 Not Found* atau *502* (jika laravel sedang restart), yang juga mengartikan bahwa Probe K8s ini gagal—Nginx pun ditewaskan berkali-kali.

### Solusi
Ubah seluruh mekanisme pengecekan dari tipe aplikasi web (`httpGet`) menjadi pengecekan port tingkat *socket* (`tcpSocket`). K8s cukup mengecek apakah port jaringan terbuka tanpa memedulikan status HTTP.

```yaml
# Fix pada laravel-app (Gunakan port 9000 tcp)
        livenessProbe:
          tcpSocket:
            port: 9000
# Fix pada Nginx (Gunakan port 80 tcp)
        livenessProbe:
          tcpSocket:
            port: 80
```

---

## 5. Storage / PVC Stuck (Error: Bad value kVolumes for StorageType)

### Gejala
Pod `laravel-app` menolak didirikan dan nyangkut selamanya pada status `Pending`. PVC (`app-storage-pvc`) juga nyangkut di status *Pending* tanpa pernah memanggil Provisioner.
Pesan Error (saat `describe pvc`):
`failed to provision volume ... rpc error: code = InvalidArgument desc = Bad value kVolumes for StorageType parameter, valid values are NutanixVolumes...`

### Penyebab
Nutanix CSI menolak mentah-mentah definisi parameter lawas `kVolumes`. Ia meminta spesifikasi ejaan baru yaitu `NutanixVolumes` di dalam file pendirian `StorageClass` Anda.
Kesalahan masa lalu mencantumkan *annotation* tambahan *csi.nutanix.com/storage-type: kVolumes* di PVC, yang mengunci kesalahan tersebut secara *immutable* (tidak bisa diubah kecuali di-delete dan buat baru).

### Solusi
1. Bersihkan *annotation* yang menyangkut-pautkan kata *kVolumes* dari segala _manifest_ PVC (`app-storage-pvc.yaml` dsb).
2. Hapus *StorageClass* lama: `kubectl delete sc nutanix-volume-fixed`.
3. Buat ulang StorageClass baru dengan deklarasi `StorageType: NutanixVolumes`.
4. Hapus dan buat ulang PVC (K8s / GitOps) agar PVC langsung dikaitkan oleh parameter *StorageClass* yang telah dibenahi.

---
*Dokumentasi ini ditulis sebagai ringkasan sukses dari proses debugging integrasi GitOps di NKP pada April 2026.*
