# SportEventBook Kubernetes Deployment

## 📁 Files Overview

| File | Description |
|------|-------------|
| `all-in-one.yaml` | **Complete manifest for DKP Kommander dashboard deployment** |
| `deploy-ndb.sh` | Main deployment script for CLI deployment with NDB MySQL |
| `app-configmap-ndb.yaml` | ConfigMap for application configuration |
| `app-secret-ndb.yaml` | Secret for sensitive data (DB credentials, API keys) |
| `app-deployment.yaml` | Laravel application deployment |
| `app-storage-pvc.yaml` | PersistentVolumeClaim for storage (Nutanix Volumes) |
| `laravel-service.yaml` | Service for Laravel FPM (Nginx upstream) |
| `migration-job.yaml` | Kubernetes Job for database migrations |
| `nginx-deployment.yaml` | Nginx reverse proxy deployment |
| `nginx-service.yaml` | LoadBalancer service for Nginx |
| `queue-worker-deployment.yaml` | Laravel queue worker deployment |
| `redis-deployment.yaml` | Redis deployment for cache/session |
| `scheduler-cronjob.yaml` | CronJob for Laravel scheduler |

## 🚀 Pre-requisites

1. Access to Nutanix Kubernetes Platform (DKP Kommander)
2. Docker installed for building images
3. Access to Harbor registry (`registry.bercalab.my.id`)
4. NDB MySQL instance created and connection details available

---

## 📋 Option 1: Deploy via GitOps (Recommended for Production)

### Step 1: Fill GitOps Repository Form

Di DKP Kommander Dashboard, saat menambahkan GitOps Repository:

| Field | Isi |
|-------|-----|
| **Name** | `sporteventbook` |
| **Repository URL** | `https://github.com/gipeypey/sporteventbook.git` |
| **Git Ref Type** | `Branch` |
| **Branch Name** | `main` (atau nama branch Anda) |
| **Path** | `k8s` |
| **Primary Git Secret** | `None` (jika repo public) |

### Step 2: Update Values di kustomization.yaml

Edit `k8s/kustomization.yaml` dengan nilai sebenarnya:

```yaml
patches:
  - patch: |-
      - op: replace
        path: /data/DB_HOST
        value: "mysql-host.example.com"  # ← NDB MySQL host
      - op: replace
        path: /data/DB_DATABASE
        value: "sporteventbook"          # ← Database name
      - op: replace
        path: /data/DB_USERNAME
        value: "sportuser"               # ← DB username
    target:
      kind: ConfigMap
      name: app-config

  - patch: |-
      - op: replace
        path: /stringData/DB_PASSWORD
        value: "your-actual-password"    # ← DB password
      - op: replace
        path: /stringData/APP_KEY
        value: "base64:your-app-key"     # ← php artisan key:generate
    target:
      kind: Secret
      name: app-secret
```

### Step 3: Commit dan Push

```bash
git add k8s/
git commit -m "Add k8s manifests for GitOps deployment"
git push origin main
```

### Step 4: Flux Akan Auto-Deploy

Flux CD akan otomatis sync dalam 1-2 menit. Check status:
```bash
kubectl get kustomizations -A
kubectl get pods -n sporteventbook
```

### Step 5: Create Secrets (Setelah Deploy)

**Opsi A: Gunakan Script**
```bash
cd k8s
chmod +x create-secrets.sh
./create-secrets.sh
```

Script akan:
- Generate APP_KEY otomatis
- Prompt untuk DB_PASSWORD
- Prompt untuk MIDTRANS keys (optional)
- Create Kubernetes Secret

**Opsi B: Manual dengan kubectl**
```bash
# Generate APP_KEY
php artisan key:generate --show

# Create secret
kubectl create secret generic app-secret -n sporteventbook \
    --from-literal=APP_KEY="base64:xxxxxxxxx" \
    --from-literal=DB_PASSWORD="your-password" \
    --from-literal=MIDTRANS_SERVER_KEY="xxx" \
    --from-literal=MIDTRANS_CLIENT_KEY="xxx"
```

**Opsi C: Via DKP Kommander UI**
1. Navigate ke **Secrets** di namespace sporteventbook
2. Click **Create Secret**
3. Tambah keys: APP_KEY, DB_PASSWORD, MIDTRANS_*
4. Save

### Step 6: Restart Pods

```bash
kubectl rollout restart deployment -n sporteventbook
```

---

## 📋 Option 2: Deploy via Dashboard (Manual YAML Upload)

### Step 1: Prepare Manifests

1. Open `k8s/all-in-one.yaml`
2. Update the following values:
   - **ConfigMap**: DB_HOST, DB_DATABASE, DB_USERNAME
   - **Secret (app-secret)**: APP_KEY, DB_PASSWORD, MIDTRANS keys
   - **Secret (harbor-secret)**: Harbor password
   - **nginx-service**: Update LoadBalancer IP if needed

### Step 2: Delete Old Namespace (if exists)

From Kommander kubectl shell or local terminal:
```bash
kubectl delete namespace sporteventbook --force --grace-period=0
```

### Step 3: Deploy via Dashboard

1. Login to **DKP Kommander Dashboard**
2. Navigate to **Applications** → **Deploy Application**
3. Select **"Upload YAML"** or **"Paste YAML"**
4. Upload/paste entire content of `k8s/all-in-one.yaml`
5. Click **Deploy**

### Step 4: Verify Deployment

From Kommander UI or kubectl:
```bash
kubectl get pods -n sporteventbook
kubectl get pvc -n sporteventbook
kubectl get svc -n sporteventbook
```

### Step 5: Run Migrations

From Kommander kubectl shell:
```bash
kubectl exec -it deployment/laravel-app -n sporteventbook -- php artisan migrate --force
```

---

## 📋 Option 3: Deploy via CLI

### 1. Delete Old Namespace (if exists)

```bash
kubectl delete namespace sporteventbook --force --grace-period=0
```

### 2. Run Deployment Script

```bash
cd k8s
chmod +x deploy-ndb.sh
./deploy-ndb.sh
```

Script akan meminta:
- Harbor password (untuk push image)
- NDB MySQL connection details:
  - Host/IP
  - Port (default: 3306)
  - Database name
  - Username
  - Password

### 3. Verify Deployment

```bash
# Check all pods are running
kubectl get pods -n sporteventbook

# Check PVC is bound
kubectl get pvc -n sporteventbook

# Check services
kubectl get svc -n sporteventbook
```

### 4. Run Migrations (if not auto-run)

```bash
kubectl exec -it deployment/laravel-app -n sporteventbook -- php artisan migrate --force
```

## 🔧 Troubleshooting

### PVC Pending

Jika PVC status `Pending`, check:
```bash
kubectl describe pvc app-storage-pvc -n sporteventbook
```



### Pods CrashLoopBackOff

Check logs:
```bash
kubectl logs deployment/laravel-app -n sporteventbook
kubectl logs deployment/nginx -n sporteventbook
```

### Image Pull Error

Verify Harbor credentials:
```bash
kubectl get secret harbor-secret -n sporteventbook -o yaml
```

Test pull manually:
```bash
docker pull registry.bercalab.my.id/sporteventbook/app:latest
```

## 🗑️ Cleanup

Delete entire namespace:
```bash
kubectl delete namespace sporteventbook --force --grace-period=0
```

## 📝 Notes

- Storage class: `nutanix-volume` (default)

- Access mode: `ReadWriteOnce` (single node)
- Default replicas: 1 (due to RWO storage)