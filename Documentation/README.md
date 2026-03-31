# SportEventBook Documentation

Dokumentasi lengkap untuk deployment dan management aplikasi SportEventBook di Nutanix Kubernetes Platform (NKP).

---

## 📚 Documentation Index

### 🏁 Getting Started

| Document | Description |
|----------|-------------|
| [DOCUMENTATION.md](DOCUMENTATION.md) | Dokumentasi utama aplikasi SportEventBook |
| [INFRASTRUCTURE_SETUP.md](INFRASTRUCTURE_SETUP.md) | Setup infrastruktur (NDB MySQL + NUS Object Storage) |
| [KUBERNETES_DEPLOYMENT.md](KUBERNETES_DEPLOYMENT.md) | Deployment guide ke Kubernetes |

### 🚀 Deployment

| Document | Description |
|----------|-------------|
| [PRODUCTION_DEPLOYMENT_CHECKLIST.md](PRODUCTION_DEPLOYMENT_CHECKLIST.md) | Checklist lengkap untuk production deployment |
| [DEPLOYMENT_UPDATE_GUIDE.md](DEPLOYMENT_UPDATE_GUIDE.md) | Guide untuk update deployment |

### 🔧 Operations

| Document | Description |
|----------|-------------|
| [GUIDE_ADD_IMAGES.md](GUIDE_ADD_IMAGES.md) | Guide menambahkan gambar ke aplikasi |
| [Restore_Checkpoint_Guide.md](Restore_Checkpoint_Guide.md) | Guide restore dari checkpoint/backup |

---

## 📋 Quick Navigation

### Untuk Deployment Pertama Kali

1. **Setup Infrastructure** → [INFRASTRUCTURE_SETUP.md](INFRASTRUCTURE_SETUP.md)
   - Setup NDB MySQL
   - Setup NUS Object Storage
   - Configure credentials

2. **Deploy to Kubernetes** → [KUBERNETES_DEPLOYMENT.md](KUBERNETES_DEPLOYMENT.md)
   - Build Docker image
   - Create Kubernetes resources
   - Run migrations

3. **Verify Deployment** → [PRODUCTION_DEPLOYMENT_CHECKLIST.md](PRODUCTION_DEPLOYMENT_CHECKLIST.md)
   - Pre-deployment checklist
   - Deployment steps
   - Post-deployment verification

### Untuk Update Deployment

1. **Update Code** → [DEPLOYMENT_UPDATE_GUIDE.md](DEPLOYMENT_UPDATE_GUIDE.md)
   - Build new image
   - Push to registry
   - Update deployment

2. **Run Migrations** (jika ada)
   - Apply migration job
   - Verify migration

3. **Verify** → [PRODUCTION_DEPLOYMENT_CHECKLIST.md](PRODUCTION_DEPLOYMENT_CHECKLIST.md)
   - Check pod status
   - Test application

---

## 🏗️ Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│              Nutanix Kubernetes Platform (NKP)                   │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │              Traefik Ingress Controller                  │    │
│  │              sport.bercalab.my.id                        │    │
│  └─────────────────────────────────────────────────────────┘    │
│                              │                                   │
│         ┌────────────────────┼────────────────────┐             │
│         │                    │                    │             │
│  ┌──────▼──────┐     ┌──────▼──────┐     ┌──────▼──────┐       │
│  │   Laravel   │     │    Redis    │     │   NUS Obj   │       │
│  │   App (x3)  │     │   Cache     │     │   Storage   │       │
│  └──────┬──────┘     └─────────────┘     └──────┬──────┘       │
│         │                                        │              │
└─────────┼────────────────────────────────────────┼──────────────┘
          │                                        │
    ┌─────▼─────────┐                      ┌──────▼──────┐
    │   NDB MySQL   │                      │   Harbor    │
    │  192.168.2.61 │                      │  Registry   │
    └───────────────┘                      └─────────────┘
```

---

## 📁 Project Structure

```
sporteventbook/
├── app/                    # Laravel application code
├── config/                 # Laravel configuration
├── database/               # Migrations & seeders
├── resources/              # Views, CSS, JS
├── routes/                 # Route definitions
├── k8s/                    # Kubernetes manifests
│   ├── production/         # Production deployment files
│   │   ├── app-configmap.yaml
│   │   ├── app-secret.yaml
│   │   ├── app-deployment.yaml
│   │   ├── nginx-deployment.yaml
│   │   ├── nginx-service.yaml
│   │   ├── ingress.yaml
│   │   ├── namespace.yaml
│   │   ├── deploy.sh
│   │   └── README.md
│   ├── redis-deployment.yaml
│   ├── migration-job.yaml
│   ├── queue-worker-deployment.yaml
│   └── scheduler-cronjob.yaml
├── Documentation/          # This folder
│   ├── README.md           # Documentation index
│   ├── DOCUMENTATION.md    # Main documentation
│   ├── INFRASTRUCTURE_SETUP.md
│   ├── KUBERNETES_DEPLOYMENT.md
│   ├── DEPLOYMENT_UPDATE_GUIDE.md
│   └── PRODUCTION_DEPLOYMENT_CHECKLIST.md
└── Dockerfile              # Container image definition
```

---

## 🔑 Key Configuration

### Environment Variables

| Variable | Description | Example |
|----------|-------------|---------|
| APP_URL | Application URL | https://sport.bercalab.my.id |
| DB_HOST | Database host | 192.168.2.61 |
| DB_DATABASE | Database name | sporteventbook |
| DB_USERNAME | Database user | sporteventbook |
| DB_PASSWORD | Database password | ******** |
| AWS_ACCESS_KEY_ID | NUS access key | AKIAIOSFODNN7EXAMPLE |
| AWS_SECRET_ACCESS_KEY | NUS secret key | ******** |
| AWS_BUCKET | S3 bucket name | sporteventbook-assets |

### Kubernetes Resources

| Resource | CPU Request | Memory Request | Replicas |
|----------|-------------|----------------|----------|
| Laravel App | 200m | 256Mi | 3 |
| Nginx | 50m | 64Mi | 2 |
| Redis | 100m | 128Mi | 1 |
| Queue Worker | 100m | 256Mi | 2 |

---

## 🚀 Quick Commands

### Deployment

```bash
# Deploy to production
cd k8s/production
./deploy.sh v1.0.0
```

### Monitoring

```bash
# Check all pods
kubectl get pods -n sporteventbook

# View logs
kubectl logs -f deployment/laravel-app -n sporteventbook

# Resource usage
kubectl top pods -n sporteventbook
```

### Update

```bash
# Update deployment
kubectl set image deployment/laravel-app \
    laravel=harbor.your-domain.com/sporteventbook/app:v1.1.0 \
    -n sporteventbook
```

### Rollback

```bash
# Rollback to previous version
kubectl rollout undo deployment/laravel-app -n sporteventbook
```

---

## 📞 Support & Contacts

### Internal Resources

- **DevOps Team**: devops@bercalab.my.id
- **App Owner**: [Contact]
- **DBA Team**: [Contact]

### External Resources

- **Nutanix Documentation**: https://portal.nutanix.com
- **Laravel Documentation**: https://laravel.com/docs
- **Kubernetes Documentation**: https://kubernetes.io/docs

---

## 📝 Document Changelog

| Date | Document | Change |
|------|----------|--------|
| 2026-03-27 | All | Initial comprehensive documentation |
| 2026-03-27 | INFRASTRUCTURE_SETUP.md | Created for NDB + NUS setup |
| 2026-03-27 | PRODUCTION_DEPLOYMENT_CHECKLIST.md | Created checklist |
| 2026-03-27 | README.md | Created documentation index |

---

## ✅ Checklist Usage

Untuk setiap deployment, gunakan checklist berikut:

1. [Pre-Deployment Checklist](PRODUCTION_DEPLOYMENT_CHECKLIST.md#pre-deployment-checklist)
2. [Deployment Checklist](PRODUCTION_DEPLOYMENT_CHECKLIST.md#deployment-checklist)
3. [Post-Deployment Verification](PRODUCTION_DEPLOYMENT_CHECKLIST.md#post-deployment-verification)

---

## 🎯 Next Steps

Setelah membaca dokumentasi ini:

1. **Setup Infrastructure** → Mulai dari [INFRASTRUCTURE_SETUP.md](INFRASTRUCTURE_SETUP.md)
2. **Deploy Application** → Lanjut ke [KUBERNETES_DEPLOYMENT.md](KUBERNETES_DEPLOYMENT.md)
3. **Verify Deployment** → Gunakan [PRODUCTION_DEPLOYMENT_CHECKLIST.md](PRODUCTION_DEPLOYMENT_CHECKLIST.md)

---

**Last Updated**: March 27, 2026
**Version**: 1.0.0