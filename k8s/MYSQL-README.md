# MySQL Deployment for SportEventBook

## 📁 Files Overview

| File | Description |
|------|-------------|
| `mysql-all-in-one.yaml` | **Complete manifest for MySQL deployment** |
| `mysql-secret.yaml` | Secret for MySQL credentials |
| `mysql-configmap.yaml` | MySQL configuration (my.cnf) |
| `mysql-pvc.yaml` | PersistentVolumeClaim for MySQL data |
| `mysql-deployment.yaml` | MySQL Deployment |
| `mysql-service.yaml` | MySQL Service (ClusterIP) |

---

## 🚀 Deploy MySQL

### Option 1: Deploy All-in-One (Recommended)

```bash
cd ~/sporteventbook/k8s
kubectl apply -f mysql-all-in-one.yaml
```

### Option 2: Deploy Individual Files

```bash
kubectl apply -f mysql-secret.yaml
kubectl apply -f mysql-configmap.yaml
kubectl apply -f mysql-pvc.yaml
kubectl apply -f mysql-deployment.yaml
kubectl apply -f mysql-service.yaml
```

---

## ✅ Verify MySQL Deployment

### Check Pods
```bash
kubectl get pods -n sporteventbook | grep mysql
```

### Check PVC
```bash
kubectl get pvc -n sporteventbook | grep mysql
```

### Check Service
```bash
kubectl get svc -n sporteventbook | grep mysql
```

### Check MySQL Logs
```bash
kubectl logs deployment/mysql -n sporteventbook
```

### Test MySQL Connection
```bash
# From kubectl shell
kubectl run -it --rm mysql-client --image=mysql:8.0 --restart=Never -n sporteventbook -- mysql -h mysql -u sportuser -p

# Enter password: Sp0rtEv3ntP@ss!
```

---

## 🔧 Update Laravel to Use MySQL

### Update ConfigMap
Laravel ConfigMap sudah diupdate dengan:
- `DB_HOST: mysql` (service name)
- `DB_USERNAME: sportuser`
- `DB_PASSWORD: Sp0rtEv3ntP@ss!`

### Restart Laravel Pods
```bash
kubectl rollout restart deployment/laravel-app -n sporteventbook
kubectl rollout restart deployment/queue-worker -n sporteventbook
```

### Run Migrations
```bash
kubectl exec -it deployment/laravel-app -n sporteventbook -- php artisan migrate --force
```

---

## 🔐 Default Credentials

| User | Password | Database |
|------|----------|----------|
| `root` | `MyS3cur3R00tP@ss!` | - |
| `sportuser` | `Sp0rtEv3ntP@ss!` | `sporteventbook` |

**⚠️ IMPORTANT:** Change these passwords before production!

---

## 📊 MySQL Configuration

- **Max Connections:** 200
- **InnoDB Buffer Pool:** 256M
- **Character Set:** utf8mb4
- **Timezone:** UTC (+00:00)
- **Storage:** 10Gi PVC

---

## 🗑️ Cleanup

### Remove MySQL Only
```bash
kubectl delete -f mysql-all-in-one.yaml
```

### Remove Entire Namespace
```bash
kubectl delete namespace sporteventbook --force --grace-period=0
```

---

## 🔧 Troubleshooting

### MySQL Pod Not Starting
```bash
kubectl describe pod -n sporteventbook -l app=mysql
kubectl logs deployment/mysql -n sporteventbook
```

### PVC Pending
```bash
kubectl describe pvc mysql-pvc -n sporteventbook
```

### Cannot Connect from Laravel
```bash
# Test from Laravel pod
kubectl exec -it deployment/laravel-app -n sporteventbook -- mysql -h mysql -u sportuser -p