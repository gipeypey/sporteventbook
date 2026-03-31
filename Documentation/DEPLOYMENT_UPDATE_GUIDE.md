# Deployment Update Guide

## Flow Update Deployment

Ketika ada perubahan codingan atau konfigurasi setelah deployment, berikut adalah flow yang harus dilakukan:

---

## 📋 Overview Flow

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Code      │    │   Build &   │    │   Deploy    │    │   Verify    │
│   Change    │ →  │   Push      │ →  │   Update    │ →  │   & Test    │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
```

---

## 🔧 Jenis-Jenis Update

### 1. **Code Changes** (Perubahan Kode)
- Perubahan file PHP, Blade, JavaScript, CSS
- Menambah/mengubah logic aplikasi
- Fix bug atau fitur baru

**Flow:**
```
1. Pull code terbaru dari Git
2. Build Docker image baru dengan version tag
3. Push ke Harbor Registry
4. Update Kubernetes deployment dengan image baru
5. Kubernetes akan rolling update pods
6. Verify deployment
```

### 2. **Database Changes** (Migration)
- Menambah tabel baru
- Menambah/modifikasi kolom
- Seed data baru

**Flow:**
```
1. Buat migration file: php artisan make:migration
2. Commit migration ke Git
3. Build & push Docker image
4. Run migration job di Kubernetes
5. Verify migration berhasil
```

### 3. **Configuration Changes**
- Perubahan environment variables (.env)
- Perubahan ConfigMap atau Secret

**Flow:**
```
1. Update ConfigMap/Secret YAML
2. Apply perubahan ke Kubernetes
3. Restart pods untuk load config baru
```

### 4. **Static Assets Changes**
- Perubahan CSS, JavaScript, images
- Frontend updates

**Flow:**
```
1. Build assets: npm run build
2. Build Docker image baru
3. Push dan update deployment
```

---

## 📝 Step-by-Step Update Process

### Step 1: Pull Code Terbaru

```bash
# SSH ke VM Bastion
ssh username@bastion-ip

# Masuk ke direktori project
cd /home/username/sporteventbook

# Pull code terbaru dari Git
git pull origin main
```

### Step 2: Build Docker Image Baru

```bash
# Login ke Harbor (jika belum)
docker login harbor.your-domain.com

# Build image dengan version tag baru
# Gunakan semantic versioning: major.minor.patch
docker build -t harbor.your-domain.com/sporteventbook/app:v1.1.0 .

# Push ke Harbor
docker push harbor.your-domain.com/sporteventbook/app:v1.1.0
```

### Step 3: Update Kubernetes Deployment

**Opsi A: Manual Update**
```bash
# Update image deployment
kubectl set image deployment/laravel-app \
    laravel=harbor.your-domain.com/sporteventbook/app:v1.1.0 \
    -n sporteventbook
```

**Opsi B: Menggunakan Script**
```bash
cd k8s
./update-deployment.sh v1.1.0
```

### Step 4: Run Migration (Jika Ada)

```bash
# Apply migration job
kubectl apply -f migration-job.yaml -n sporteventbook

# Monitor migration
kubectl logs job/migrate -n sporteventbook
```

### Step 5: Monitor Rollout Status

```bash
# Monitor rolling update
kubectl rollout status deployment/laravel-app -n sporteventbook

# Check pod status
kubectl get pods -n sporteventbook

# Check logs
kubectl logs -f deployment/laravel-app -n sporteventbook
```

### Step 6: Verify Deployment

```bash
# Test aplikasi
curl http://<LOAD_BALANCER_IP>

# Check semua pods running
kubectl get pods -n sporteventbook | grep -E "laravel|nginx|queue|scheduler"
```

---

## 🚀 Quick Update Script

Saya sudah membuat script `k8s/update-deployment.sh` untuk automate update process.

### Cara Menggunakan:

```bash
cd k8s
chmod +x update-deployment.sh
./update-deployment.sh v1.1.0
```

Script akan:
1. Pull code terbaru dari Git
2. Build Docker image dengan version tag
3. Push ke Harbor
4. Update Kubernetes deployment
5. Monitor rollout status
6. Verify deployment

---

## 🔄 Rollback (Jika Ada Masalah)

Jika update menyebabkan masalah, lakukan rollback:

### Rollback Deployment

```bash
# Rollback ke versi sebelumnya
kubectl rollout undo deployment/laravel-app -n sporteventbook

# Rollback ke versi tertentu
kubectl rollout undo deployment/laravel-app:2 -n sporteventbook

# Check rollout history
kubectl rollout history deployment/laravel-app -n sporteventbook
```

### Rollback Migration (Jika Perlu)

```bash
# SSH ke bastion
kubectl run rollback --image=harbor.your-domain.com/sporteventbook/app:v1.0.0 \
    --rm -it --restart=Never -n sporteventbook \
    --env-from=configmap/app-config \
    --env-from=secret/app-secret \
    -- php artisan migrate:rollback
```

---

## 📊 Update Checklist

Sebelum update, pastikan:

- [ ] Code sudah di-test di local/staging
- [ ] Migration sudah di-test dan aman
- [ ] Backup database sudah dilakukan
- [ ] Version tag sudah sesuai (semantic versioning)
- [ ] Harbor registry sudah di-login
- [ ] Tim sudah di-notify tentang maintenance window (jika perlu)

Setelah update:

- [ ] Semua pods running successfully
- [ ] Tidak ada error di logs
- [ ] Aplikasi bisa diakses
- [ ] Fitur baru/fix sudah bekerja
- [ ] Database migration berhasil
- [ ] Queue worker berjalan normal
- [ ] Scheduler berjalan normal

---

## 🎯 Best Practices

### 1. **Gunakan Version Tag**
Selalu gunakan version tag untuk Docker image:
```bash
# ✅ Good
harbor.your-domain.com/sporteventbook/app:v1.1.0
harbor.your-domain.com/sporteventbook/app:v1.1.1

# ❌ Bad (hindari :latest untuk production)
harbor.your-domain.com/sporteventbook/app:latest
```

### 2. **Blue-Green Deployment (Optional)**
Untuk zero-downtime deployment:
```bash
# Deploy versi baru dengan label berbeda
kubectl apply -f app-deployment-green.yaml -n sporteventbook

# Test versi baru
# ...

# Switch traffic
kubectl patch svc nginx-service -n sporteventbook \
    -p '{"spec":{"selector":{"version":"green"}}}'
```

### 3. **Canary Deployment**
Release bertahap untuk minimize risk:
```bash
# 10% traffic ke versi baru
# Monitor metrics
# Increase ke 50%
# Full rollout jika aman
```

### 4. **Automated CI/CD (Recommended)**
Setup GitHub Actions atau GitLab CI untuk automate:
```yaml
# Contoh GitHub Actions
name: Deploy to Kubernetes
on:
  push:
    branches: [main]
jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Build and Push
        run: |
          docker build -t harbor.your-domain.com/sporteventbook/app:${{ github.sha }} .
          docker push harbor.your-domain.com/sporteventbook/app:${{ github.sha }}
      - name: Deploy to K8s
        run: |
          kubectl set image deployment/laravel-app laravel=harbor.your-domain.com/sporteventbook/app:${{ github.sha }} -n sporteventbook
```

---

## 🔍 Monitoring & Troubleshooting

### Check Deployment Status
```bash
kubectl get deployment laravel-app -n sporteventbook
kubectl describe deployment laravel-app -n sporteventbook
```

### Check Pod Logs
```bash
# All logs
kubectl logs -f deployment/laravel-app -n sporteventbook

# Specific pod
kubectl logs -f <pod-name> -n sporteventbook

# Previous pod (before restart)
kubectl logs -f <pod-name> -n sporteventbook --previous
```

### Check Events
```bash
kubectl get events -n sporteventbook --sort-by='.lastTimestamp'
```

### Debug Pod
```bash
# Exec ke pod
kubectl exec -it <pod-name> -n sporteventbook -- bash

# Check environment
kubectl exec -it <pod-name> -n sporteventbook -- env

# Test database connection
kubectl exec -it <pod-name> -n sporteventbook -- php artisan tinker
>>> DB::connection()->getPdo();
```

---

## 📈 Metrics to Monitor

Setelah update, monitor:

1. **Pod Status** - Semua pods harus running
2. **CPU/Memory Usage** - Tidak ada resource leak
3. **Error Rate** - Tidak ada peningkatan error
4. **Response Time** - Performance tetap optimal
5. **Database Connections** - Connection pool sehat
6. **Queue Size** - Queue tidak menumpuk

```bash
# Resource usage
kubectl top pods -n sporteventbook

# Check queue size (jika ada monitoring)
kubectl exec -it deployment/queue-worker -n sporteventbook -- \
    php artisan queue:status
```

---

## 📞 Emergency Contacts

Jika ada masalah saat update:

1. **Rollback Immediately** - Jangan tunggu terlalu lama
2. **Check Logs** - Identifikasi root cause
3. **Notify Team** - Informasikan stakeholder
4. **Document Issue** - Untuk pembelajaran

---

## 📚 Related Documentation

- [KUBERNETES_DEPLOYMENT.md](KUBERNETES_DEPLOYMENT.md) - Deployment guide lengkap
- [DOCUMENTATION.md](DOCUMENTATION.md) - Dokumentasi aplikasi
- [Troubleshooting Guide](TROUBLESHOOTING.md) - Guide troubleshooting

---

## FAQ

**Q: Berapa lama update deployment?**
A: Biasanya 2-5 menit tergantung ukuran image dan jumlah replica.

**Q: Apakah deployment menyebabkan downtime?**
A: Tidak, Kubernetes melakukan rolling update secara otomatis.

**Q: Bagaimana jika migration gagal?**
A: Rollback deployment dan rollback migration, lalu fix migration.

**Q: Apakah perlu restart queue worker?**
A: Ya, queue worker akan otomatis restart saat deployment update.

**Q: Bagaimana cara tau versi yang sedang running?**
A: `kubectl get deployment laravel-app -n sporteventbook -o jsonpath='{.spec.template.spec.containers[0].image}'`