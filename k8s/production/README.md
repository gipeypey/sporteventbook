# Production Deployment - SportEventBook

## Overview

Folder ini berisi Kubernetes manifests untuk deployment production SportEventBook dengan konfigurasi:

| Component | Configuration |
|-----------|--------------|
| **Database** | NDB MySQL (192.168.2.61) |
| **Storage** | NUS Object Storage (S3-compatible) |
| **Cache/Queue** | Redis |
| **Ingress** | Traefik |
| **Domain** | sport.bercalab.my.id |

---

## 📁 File Structure

```
k8s/production/
├── README.md                 # File ini
├── deploy.sh                 # Script deployment otomatis
├── app-configmap.yaml        # ConfigMap aplikasi
├── app-secret.yaml           # Secret (credentials)
├── app-deployment.yaml       # Laravel application
├── nginx-deployment.yaml     # Nginx deployment
├── nginx-service.yaml        # Nginx service
├── ingress.yaml              # Traefik ingress
└── namespace.yaml            # Namespace definition
```

---

## 🚀 Quick Start

### Prerequisites

1. **Akses ke NKP Cluster**
   ```bash
   export KUBECONFIG=/path/to/kubeconfig.yaml
   kubectl cluster-info
   ```

2. **Docker & Harbor Access**
   ```bash
   docker login harbor.your-domain.com
   ```

3. **NDB MySQL Ready**
   - Database `sporteventbook` sudah dibuat
   - User `sporteventbook` dengan proper grants

4. **NUS Object Storage Ready**
   - Bucket `sporteventbook-assets` sudah dibuat
   - Access credentials sudah digenerate

### Deployment Steps

#### Opsi A: Menggunakan Script (Recommended)

```bash
cd k8s/production
chmod +x deploy.sh
./deploy.sh v1.0.0
```

Script akan:
1. Create namespace
2. Create secrets
3. Deploy ConfigMap
4. Deploy Redis
5. Build & push Docker image
6. Deploy aplikasi
7. Deploy Nginx
8. Deploy Ingress
9. Run migrations
10. Deploy queue worker & scheduler

#### Opsi B: Manual

```bash
# 1. Create namespace
kubectl apply -f namespace.yaml

# 2. Create secrets (edit values terlebih dahulu)
kubectl apply -f app-secret.yaml

# 3. Deploy ConfigMap
kubectl apply -f app-configmap.yaml

# 4. Deploy Redis
kubectl apply -f ../redis-deployment.yaml

# 5. Build & push image
docker build -t harbor.your-domain.com/sporteventbook/app:v1.0.0 ../../
docker push harbor.your-domain.com/sporteventbook/app:v1.0.0

# 6. Deploy aplikasi
kubectl apply -f app-deployment.yaml

# 7. Deploy Nginx
kubectl apply -f nginx-deployment.yaml
kubectl apply -f nginx-service.yaml

# 8. Deploy Ingress
kubectl apply -f ingress.yaml

# 9. Run migrations
kubectl apply -f ../migration-job.yaml

# 10. Deploy queue worker & scheduler
kubectl apply -f ../queue-worker-deployment.yaml
kubectl apply -f ../scheduler-cronjob.yaml
```

---

## 🔧 Configuration

### Update app-secret.yaml

Edit file `app-secret.yaml` dengan credentials Anda:

```yaml
stringData:
  APP_KEY: "base64:YourAppKey32Characters=="
  DB_PASSWORD: "YourNDBMySQLPassword"
  AWS_ACCESS_KEY_ID: "YourNUSAccessKey"
  AWS_SECRET_ACCESS_KEY: "YourNUSSecretKey"
  MIDTRANS_SERVER_KEY: "YourMidtransServerKey"
  MIDTRANS_CLIENT_KEY: "YourMidtransClientKey"
```

### Update app-configmap.yaml

Edit jika perlu mengubah konfigurasi:

```yaml
data:
  APP_URL: "https://sport.bercalab.my.id"
  REDIS_HOST: "redis"
  FILESYSTEM_DRIVER: "s3"
```

### Update ingress.yaml

Jika ingin mengubah domain atau menambah routes:

```yaml
spec:
  rules:
  - host: sport.bercalab.my.id  # Update domain disini
```

---

## 📊 Scaling

### Horizontal Pod Autoscaler

Untuk auto-scaling berdasarkan CPU/Memory:

```bash
kubectl autoscale deployment laravel-app \
    --namespace=sporteventbook \
    --cpu-percent=80 \
    --min=3 \
    --max=10
```

### Manual Scaling

```bash
# Scale up
kubectl scale deployment laravel-app --replicas=5 -n sporteventbook

# Scale down
kubectl scale deployment laravel-app --replicas=2 -n sporteventbook
```

---

## 🔍 Monitoring

### Check Status

```bash
# All pods
kubectl get pods -n sporteventbook

# All services
kubectl get svc -n sporteventbook

# All ingresses
kubectl get ingress -n sporteventbook

# Resource usage
kubectl top pods -n sporteventbook
```

### View Logs

```bash
# Laravel app logs
kubectl logs -f deployment/laravel-app -n sporteventbook

# Nginx logs
kubectl logs -f deployment/nginx -n sporteventbook

# Queue worker logs
kubectl logs -f deployment/queue-worker -n sporteventbook
```

### Debug Pod

```bash
# Exec ke pod
kubectl exec -it <pod-name> -n sporteventbook -- bash

# Test database connection
kubectl exec -it <pod-name> -n sporteventbook -- \
    php artisan tinker --execute="DB::connection()->getPdo();"
```

---

## 🔄 Update Deployment

### Update Code

```bash
# Build new image
docker build -t harbor.your-domain.com/sporteventbook/app:v1.1.0 ../../
docker push harbor.your-domain.com/sporteventbook/app:v1.1.0

# Update deployment
kubectl set image deployment/laravel-app \
    laravel=harbor.your-domain.com/sporteventbook/app:v1.1.0 \
    -n sporteventbook

# Monitor rollout
kubectl rollout status deployment/laravel-app -n sporteventbook
```

### Update Configuration

```bash
# Update secret
kubectl apply -f app-secret.yaml -n sporteventbook

# Update configmap
kubectl apply -f app-configmap.yaml -n sporteventbook

# Restart pods untuk load config baru
kubectl rollout restart deployment/laravel-app -n sporteventbook
```

---

## 🚨 Troubleshooting

### Pod Tidak Running

```bash
# Describe pod
kubectl describe pod <pod-name> -n sporteventbook

# Check logs
kubectl logs <pod-name> -n sporteventbook

# Check events
kubectl get events -n sporteventbook --sort-by='.lastTimestamp'
```

### Database Connection Error

```bash
# Test connection dari dalam cluster
kubectl run mysql-test --image=mysql:8.0 --rm -it -n sporteventbook \
    --env="MYSQL_PWD=YourPassword" \
    -- mysql -h 192.168.2.61 -u sporteventbook -e "SELECT 1;"
```

### S3 Upload Error

```bash
# Check credentials di secret
kubectl get secret app-secret -n sporteventbook -o jsonpath='{.data.AWS_ACCESS_KEY_ID}' | base64 -d
kubectl get secret app-secret -n sporteventbook -o jsonpath='{.data.AWS_SECRET_ACCESS_KEY}' | base64 -d

# Test NUS connection
kubectl run aws-test --image=amazon/aws-cli --rm -it -n sporteventbook \
    --env="AWS_ACCESS_KEY_ID=xxx" \
    --env="AWS_SECRET_ACCESS_KEY=xxx" \
    -- aws s3 ls s3://sporteventbook-assets \
        --endpoint-url http://<nus-server-ip>:8080
```

### Ingress Tidak Working

```bash
# Check ingress
kubectl describe ingress sporteventbook-ingress -n sporteventbook

# Check Traefik logs
kubectl logs -n traefik-system -l app.kubernetes.io/name=traefik --tail=100
```

---

## 🔒 Security Best Practices

1. **Rotate Secrets Berkala**
   ```bash
   kubectl create secret generic app-secret \
       --from-literal=DB_PASSWORD="NewPassword123!" \
       -n sporteventbook \
       --dry-run=client -o yaml | kubectl apply -f -
   ```

2. **Network Policies**
   ```bash
   # Restrict pod-to-pod communication
   kubectl apply -f network-policy.yaml -n sporteventbook
   ```

3. **Pod Security Context**
   Sudah configured di deployment manifests dengan:
   - Non-root user
   - Read-only root filesystem
   - No privilege escalation

4. **TLS/SSL**
   - Pastikan TLS certificate configured di Ingress
   - Gunakan cert-manager untuk auto-renewal

---

## 📊 Resource Limits

| Component | CPU Request | CPU Limit | Memory Request | Memory Limit |
|-----------|-------------|-----------|----------------|--------------|
| Laravel App | 200m | 500m | 256Mi | 512Mi |
| Nginx | 50m | 100m | 64Mi | 128Mi |
| Redis | 100m | 200m | 128Mi | 256Mi |
| Queue Worker | 100m | 300m | 256Mi | 512Mi |

---

## 📞 Support

Untuk pertanyaan atau issue:
1. Check logs: `kubectl logs -f deployment/laravel-app -n sporteventbook`
2. Check events: `kubectl get events -n sporteventbook`
3. Hubungi tim DevOps

---

## 📚 Related Documentation

- [Infrastructure Setup](../../Documentation/INFRASTRUCTURE_SETUP.md)
- [Kubernetes Deployment](../../Documentation/KUBERNETES_DEPLOYMENT.md)
- [Update Deployment](../../Documentation/DEPLOYMENT_UPDATE_GUIDE.md)