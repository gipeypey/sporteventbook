# Nutanix Kubernetes Platform (NKP) Deployment Guide

## Prerequisites

Sebelum memulai deployment, pastikan Anda memiliki:

1. **Nutanix Kubernetes Platform (NKP)** - Cluster Kubernetes yang sudah running
2. **Harbor Registry** - Container registry untuk menyimpan Docker images
3. **VM Bastion** - Jump host untuk akses ke cluster
4. **kubectl** - Terinstall di laptop/VM Bastion
5. **Docker** - Untuk build dan push images
6. **Helm** (optional) - Untuk deployment dengan Helm charts

---

## Arsitektur Deployment

```
┌─────────────────────────────────────────────────────────────────┐
│                    Nutanix Kubernetes Platform                   │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │                    Ingress Controller                    │    │
│  └─────────────────────────────────────────────────────────┘    │
│                              │                                   │
│         ┌────────────────────┼────────────────────┐             │
│         │                    │                    │             │
│  ┌──────▼──────┐     ┌──────▼──────┐     ┌──────▼──────┐       │
│  │   Laravel   │     │    MySQL    │     │    Redis    │       │
│  │   App Pod   │     │    Pod      │     │    Pod      │       │
│  │   (Replica) │     │  (Stateful) │     │  (Stateful) │       │
│  └──────┬──────┘     └──────┬──────┘     └────────────┘       │
│         │                   │                   │               │
│  ┌──────▼──────┐     ┌──────▼──────┐     ┌──────▼──────┐       │
│  │   PVC App   │     │   PVC MySQL │     │  PVC Redis  │       │
│  │  (Storage)  │     │  (Storage)  │     │  (Storage)  │       │
│  └─────────────┘     └─────────────┘     └─────────────┘       │
└─────────────────────────────────────────────────────────────────┘
                              │
                    ┌─────────▼─────────┐
                    │  Harbor Registry  │
                    │  (VM Separate)    │
                    └───────────────────┘
```

---

## Opsi Deployment Database

### Opsi A: Menggunakan NDB MySQL (Recommended untuk Production)

Jika Anda memiliki **Nutanix Database Service (NDB)**, Anda bisa menggunakan MySQL yang di-provision oleh NDB.

**Keuntungan:**
- Fully managed database service
- Automated backup dan point-in-time recovery
- High availability built-in
- Automated patching dan maintenance
- Dedicated resources untuk database

**Langkah-langkah:**

1. **Provision MySQL dari NDB Console**
   - Login ke NDB Console
   - Create Database → Pilih MySQL
   - Configure: CPU, Memory, Storage
   - Set credentials (root password, database name, user)
   - Deploy

2. **Dapatkan Connection Details**
   - Host/IP address dari MySQL instance --> 192.168.2.61
   - Port (default: 3306)
   - Database name -> sporteventbook
   - Username : root (db)/era (os)

3. **Update Kubernetes Secret**
   ```bash
   kubectl create secret generic app-secret \
       --from-literal=APP_KEY="base64:YourAppKey" \
       --from-literal=DB_HOST="192.168.2.61" \
       --from-literal=DB_PORT="3306" \
       --from-literal=DB_DATABASE="sporteventbook" \
       --from-literal=DB_USERNAME="root" \
       --from-literal=DB_PASSWORD="P@ssw0rd" \
       --from-literal=MIDTRANS_SERVER_KEY="Mid-server-QO31L-owEDPoRNuWdLBOyFFx" \
       --from-literal=MIDTRANS_CLIENT_KEY="Mid-client-duVgQAN4ELE6SarI" \
       -n sporteventbook
   ```

4. **Skip MySQL Deployment di K8s**
   - Tidak perlu apply `mysql-pv.yaml` dan `mysql-statefulset.yaml`
   - Langsung deploy aplikasi Laravel

---

### Opsi B: Deploy MySQL di Kubernetes

Gunakan opsi ini jika Anda tidak memiliki NDB atau ingin semua resource di dalam cluster.

**Catatan:** Opsi ini memerlukan management manual untuk backup, maintenance, dan high availability.

---

## Step-by-Step Deployment

### Step 1: Persiapan Environment

#### 1.1. SSH ke VM Bastion
```bash
# Dari laptop Anda
ssh username@bastion-ip-address
```

#### 1.2. Clone Repository ke Bastion
```bash
cd /home/username
git clone https://github.com/gipeypey/sporteventbook.git
cd sporteventbook
```

#### 1.3. Setup kubectl Context
```bash
# Download kubeconfig dari NKP
# Biasanya disediakan oleh admin NKP
export KUBECONFIG=/path/to/kubeconfig.yaml

# Verify connection
kubectl cluster-info
kubectl get nodes
```

---

### Step 2: Build dan Push Docker Image

#### 2.1. Buat Dockerfile (jika belum ada)

Buat file `Dockerfile` di root project:

```dockerfile
# Dockerfile
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application code
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Build assets
RUN npm install && npm run build

# Generate optimized autoloaders
RUN composer dump-autoload

# Create system user to run Composer and Artisan commands
RUN useradd -G www-data,root -u 1337 -d /home/laravel laravel
RUN mkdir -p /home/laravel/.composer && \
    chown -R laravel:laravel /home/laravel

# Create storage directories
RUN mkdir -p storage/app/public/assets/images/events && \
    mkdir -p storage/app/public/assets/images/event-prizes && \
    mkdir -p storage/logs && \
    mkdir -p bootstrap/cache && \
    chown -R laravel:laravel storage && \
    chmod -R 775 storage && \
    chmod -R 775 bootstrap/cache

# Copy custom php.ini
COPY .docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Expose port 9000
EXPOSE 9000

# Set user
USER laravel

# Run artisan optimize
RUN php artisan optimize

CMD ["php-fpm"]
```

#### 2.2. Buat Nginx Config untuk Laravel

Buat file `.docker/nginx/default.conf`:

```nginx
server {
    listen 80;
    server_name _;
    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass laravel:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~ /\.ht {
        deny all;
    }

    # Static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

#### 2.3. Build Docker Image

```bash
# Login ke Harbor Registry
docker login harbor.your-domain.com
# Username: admin (atau user Anda)
# Password: ******

# Build image
docker build -t harbor.your-domain.com/sporteventbook/app:latest .

# Push ke Harbor
docker push harbor.your-domain.com/sporteventbook/app:latest
```

---

### Step 3: Buat Namespace

```bash
kubectl create namespace sporteventbook
kubectl config set-context --current --namespace=sporteventbook
```

---

### Step 4: Deploy Database (MySQL)

#### 4.1. Buat Secret untuk MySQL Password

```bash
kubectl create secret generic mysql-secret \
    --from-literal=MYSQL_ROOT_PASSWORD='YourSecurePassword123!' \
    --from-literal=MYSQL_DATABASE='sporteventbook' \
    --from-literal=MYSQL_USER='sporteventbook' \
    --from-literal=MYSQL_PASSWORD='YourUserPassword123!' \
    -n sporteventbook
```

#### 4.2. Buat PersistentVolume (PV) dan PersistentVolumeClaim (PVC)

Buat file `k8s/mysql-pv.yaml`:

```yaml
apiVersion: v1
kind: PersistentVolume
metadata:
  name: mysql-pv
  namespace: sporteventbook
spec:
  capacity:
    storage: 10Gi
  accessModes:
    - ReadWriteOnce
  persistentVolumeReclaimPolicy: Retain
  storageClassName: nutanix-storage
  claimRef:
    namespace: sporteventbook
    name: mysql-pvc
---
apiVersion: v1
kind: PersistentVolumeClaim
metadata:
  name: mysql-pvc
  namespace: sporteventbook
spec:
  accessModes:
    - ReadWriteOnce
  resources:
    requests:
      storage: 10Gi
  storageClassName: nutanix-storage
```

Apply:
```bash
kubectl apply -f k8s/mysql-pv.yaml
```

#### 4.3. Deploy MySQL StatefulSet

Buat file `k8s/mysql-statefulset.yaml`:

```yaml
apiVersion: apps/v1
kind: StatefulSet
metadata:
  name: mysql
  namespace: sporteventbook
spec:
  serviceName: mysql
  replicas: 1
  selector:
    matchLabels:
      app: mysql
  template:
    metadata:
      labels:
        app: mysql
    spec:
      containers:
      - name: mysql
        image: mysql:8.0
        ports:
        - containerPort: 3306
          name: mysql
        env:
        - name: MYSQL_ROOT_PASSWORD
          valueFrom:
            secretKeyRef:
              name: mysql-secret
              key: MYSQL_ROOT_PASSWORD
        - name: MYSQL_DATABASE
          valueFrom:
            secretKeyRef:
              name: mysql-secret
              key: MYSQL_DATABASE
        - name: MYSQL_USER
          valueFrom:
            secretKeyRef:
              name: mysql-secret
              key: MYSQL_USER
        - name: MYSQL_PASSWORD
          valueFrom:
            secretKeyRef:
              name: mysql-secret
              key: MYSQL_PASSWORD
        volumeMounts:
        - name: mysql-storage
          mountPath: /var/lib/mysql
        resources:
          requests:
            memory: "512Mi"
            cpu: "500m"
          limits:
            memory: "1Gi"
            cpu: "1000m"
        livenessProbe:
          exec:
            command:
            - mysqladmin
            - ping
            - -h
            - localhost
          initialDelaySeconds: 30
          periodSeconds: 10
        readinessProbe:
          exec:
            command:
            - mysqladmin
            - ping
            - -h
            - localhost
          initialDelaySeconds: 10
          periodSeconds: 5
      volumes:
      - name: mysql-storage
        persistentVolumeClaim:
          claimName: mysql-pvc
---
apiVersion: v1
kind: Service
metadata:
  name: mysql
  namespace: sporteventbook
spec:
  ports:
  - port: 3306
    targetPort: 3306
  selector:
    app: mysql
  clusterIP: None
```

Apply:
```bash
kubectl apply -f k8s/mysql-statefulset.yaml
```

---

### Step 5: Deploy Redis (Optional, untuk cache/queue)

Buat file `k8s/redis-deployment.yaml`:

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: redis
  namespace: sporteventbook
spec:
  replicas: 1
  selector:
    matchLabels:
      app: redis
  template:
    metadata:
      labels:
        app: redis
    spec:
      containers:
      - name: redis
        image: redis:7-alpine
        ports:
        - containerPort: 6379
          name: redis
        resources:
          requests:
            memory: "128Mi"
            cpu: "100m"
          limits:
            memory: "256Mi"
            cpu: "200m"
        livenessProbe:
          tcpSocket:
            port: 6379
          initialDelaySeconds: 30
          periodSeconds: 10
        readinessProbe:
          tcpSocket:
            port: 6379
          initialDelaySeconds: 10
          periodSeconds: 5
---
apiVersion: v1
kind: Service
metadata:
  name: redis
  namespace: sporteventbook
spec:
  ports:
  - port: 6379
    targetPort: 6379
  selector:
    app: redis
  clusterIP: None
```

Apply:
```bash
kubectl apply -f k8s/redis-deployment.yaml
```

---

### Step 6: Deploy Laravel Application

#### 6.1. Buat ConfigMap untuk Environment Variables

Buat file `k8s/app-configmap.yaml`:

```yaml
apiVersion: v1
kind: ConfigMap
metadata:
  name: app-config
  namespace: sporteventbook
data:
  APP_NAME: "SportEventBook"
  APP_ENV: "production"
  APP_DEBUG: "false"
  APP_URL: "https://sporteventbook.your-domain.com"
  
  DB_CONNECTION: "mysql"
  DB_HOST: "mysql"
  DB_PORT: "3306"
  DB_DATABASE: "sporteventbook"
  DB_USERNAME: "sporteventbook"
  
  CACHE_DRIVER: "redis"
  QUEUE_CONNECTION: "redis"
  SESSION_DRIVER: "redis"
  
  REDIS_HOST: "redis"
  REDIS_PORT: "6379"
  
  LOG_CHANNEL: "stderr"
  LOG_LEVEL: "info"
```

#### 6.2. Buat Secret untuk Sensitive Data

Buat file `k8s/app-secret.yaml`:

```yaml
apiVersion: v1
kind: Secret
metadata:
  name: app-secret
  namespace: sporteventbook
type: Opaque
stringData:
  APP_KEY: "base64:YourGeneratedAppKey32Characters=="
  DB_PASSWORD: "YourUserPassword123!"
  MIDTRANS_SERVER_KEY: "SB-Mid-server-xxxxx"
  MIDTRANS_CLIENT_KEY: "SB-Mid-client-xxxxx"
```

Generate APP_KEY:
```bash
docker run --rm php:8.3 php -r "echo 'base64:' . base64_encode(random_bytes(32));"
```

Apply:
```bash
kubectl apply -f k8s/app-configmap.yaml
kubectl apply -f k8s/app-secret.yaml
```

#### 6.3. Buat Deployment untuk Laravel App

Buat file `k8s/app-deployment.yaml`:

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: laravel-app
  namespace: sporteventbook
  labels:
    app: laravel
spec:
  replicas: 3
  selector:
    matchLabels:
      app: laravel
  template:
    metadata:
      labels:
        app: laravel
    spec:
      containers:
      - name: laravel
        image: harbor.your-domain.com/sporteventbook/app:latest
        imagePullPolicy: Always
        ports:
        - containerPort: 9000
          name: fpm
        envFrom:
        - configMapRef:
            name: app-config
        - secretRef:
            name: app-secret
        env:
        - name: DB_PASSWORD
          valueFrom:
            secretKeyRef:
              name: app-secret
              key: DB_PASSWORD
        volumeMounts:
        - name: storage-volume
          mountPath: /var/www/html/storage
        - name: bootstrap-cache
          mountPath: /var/www/html/bootstrap/cache
        resources:
          requests:
            memory: "256Mi"
            cpu: "200m"
          limits:
            memory: "512Mi"
            cpu: "500m"
        livenessProbe:
          httpGet:
            path: /
            port: 80
            scheme: HTTP
          initialDelaySeconds: 30
          periodSeconds: 10
        readinessProbe:
          httpGet:
            path: /
            port: 80
            scheme: HTTP
          initialDelaySeconds: 10
          periodSeconds: 5
      volumes:
      - name: storage-volume
        persistentVolumeClaim:
          claimName: app-storage-pvc
      - name: bootstrap-cache
        emptyDir: {}
      imagePullSecrets:
      - name: harbor-secret
```

#### 6.4. Buat PVC untuk App Storage

Buat file `k8s/app-storage-pv.yaml`:

```yaml
apiVersion: v1
kind: PersistentVolume
metadata:
  name: app-storage-pv
  namespace: sporteventbook
spec:
  capacity:
    storage: 5Gi
  accessModes:
    - ReadWriteMany
  persistentVolumeReclaimPolicy: Retain
  storageClassName: nutanix-storage
  claimRef:
    namespace: sporteventbook
    name: app-storage-pvc
---
apiVersion: v1
kind: PersistentVolumeClaim
metadata:
  name: app-storage-pvc
  namespace: sporteventbook
spec:
  accessModes:
    - ReadWriteMany
  resources:
    requests:
      storage: 5Gi
  storageClassName: nutanix-storage
```

Apply:
```bash
kubectl apply -f k8s/app-storage-pv.yaml
kubectl apply -f k8s/app-deployment.yaml
```

---

### Step 7: Deploy Nginx Ingress Controller

#### 7.1. Buat Deployment untuk Nginx

Buat file `k8s/nginx-deployment.yaml`:

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: nginx
  namespace: sporteventbook
spec:
  replicas: 2
  selector:
    matchLabels:
      app: nginx
  template:
    metadata:
      labels:
        app: nginx
    spec:
      containers:
      - name: nginx
        image: nginx:alpine
        ports:
        - containerPort: 80
          name: http
        - containerPort: 443
          name: https
        volumeMounts:
        - name: nginx-config
          mountPath: /etc/nginx/conf.d/default.conf
          subPath: default.conf
        - name: app-public
          mountPath: /var/www/html/public
        resources:
          requests:
            memory: "64Mi"
            cpu: "50m"
          limits:
            memory: "128Mi"
            cpu: "100m"
        livenessProbe:
          httpGet:
            path: /
            port: 80
          initialDelaySeconds: 10
          periodSeconds: 10
        readinessProbe:
          httpGet:
            path: /
            port: 80
          initialDelaySeconds: 5
          periodSeconds: 5
      volumes:
      - name: nginx-config
        configMap:
          name: nginx-config
      - name: app-public
        persistentVolumeClaim:
          claimName: app-public-pvc
      imagePullSecrets:
      - name: harbor-secret
```

#### 7.2. Buat Service untuk Nginx (LoadBalancer)

Buat file `k8s/nginx-service.yaml`:

```yaml
apiVersion: v1
kind: Service
metadata:
  name: nginx-service
  namespace: sporteventbook
  annotations:
    metallb.universe.tf/loadBalancerIPs: "10.10.10.100"  # IP dari NKP
spec:
  type: LoadBalancer
  ports:
  - port: 80
    targetPort: 80
    name: http
  - port: 443
    targetPort: 443
    name: https
  selector:
    app: nginx
```

Apply:
```bash
kubectl apply -f k8s/nginx-deployment.yaml
kubectl apply -f k8s/nginx-service.yaml
```

---

### Step 8: Buat Harbor Secret untuk Image Pull

```bash
kubectl create secret docker-registry harbor-secret \
    --docker-server=harbor.your-domain.com \
    --docker-username=admin \
    --docker-password='YourHarborPassword' \
    --docker-email=your-email@domain.com \
    -n sporteventbook
```

---

### Step 9: Run Database Migrations

#### 9.1. Buat Migration Job

Buat file `k8s/migration-job.yaml`:

```yaml
apiVersion: batch/v1
kind: Job
metadata:
  name: migrate
  namespace: sporteventbook
spec:
  template:
    spec:
      containers:
      - name: migrate
        image: harbor.your-domain.com/sporteventbook/app:latest
        command:
        - php
        - artisan
        - migrate
        - --force
        envFrom:
        - configMapRef:
            name: app-config
        - secretRef:
            name: app-secret
        env:
        - name: DB_PASSWORD
          valueFrom:
            secretKeyRef:
              name: app-secret
              key: DB_PASSWORD
      restartPolicy: OnFailure
      imagePullSecrets:
      - name: harbor-secret
```

Apply:
```bash
kubectl apply -f k8s/migration-job.yaml
```

#### 9.2. Seed Database (Optional)

```bash
kubectl run seeder --image=harbor.your-domain.com/sporteventbook/app:latest \
    --rm -it --restart=Never -n sporteventbook \
    --env-from=configmap/app-config \
    --env-from=secret/app-secret \
    -- php artisan db:seed
```

---

### Step 10: Setup Storage Link

```bash
kubectl run storage-link --image=harbor.your-domain.com/sporteventbook/app:latest \
    --rm -it --restart=Never -n sporteventbook \
    --env-from=configmap/app-config \
    --env-from=secret/app-secret \
    -- php artisan storage:link
```

---

### Step 11: Deploy Queue Worker

Buat file `k8s/queue-worker-deployment.yaml`:

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: queue-worker
  namespace: sporteventbook
spec:
  replicas: 2
  selector:
    matchLabels:
      app: queue-worker
  template:
    metadata:
      labels:
        app: queue-worker
    spec:
      containers:
      - name: queue-worker
        image: harbor.your-domain.com/sporteventbook/app:latest
        command:
        - php
        - artisan
        - queue:work
        - --sleep=3
        - --tries=3
        - --max-time=3600
        envFrom:
        - configMapRef:
            name: app-config
        - secretRef:
            name: app-secret
        env:
        - name: DB_PASSWORD
          valueFrom:
            secretKeyRef:
              name: app-secret
              key: DB_PASSWORD
        resources:
          requests:
            memory: "256Mi"
            cpu: "100m"
          limits:
            memory: "512Mi"
            cpu: "300m"
      imagePullSecrets:
      - name: harbor-secret
```

Apply:
```bash
kubectl apply -f k8s/queue-worker-deployment.yaml
```

---

### Step 12: Deploy Scheduler (Cron)

Buat file `k8s/scheduler-cronjob.yaml`:

```yaml
apiVersion: batch/v1
kind: CronJob
metadata:
  name: laravel-scheduler
  namespace: sporteventbook
spec:
  schedule: "* * * * *"
  jobTemplate:
    spec:
      template:
        spec:
          containers:
          - name: scheduler
            image: harbor.your-domain.com/sporteventbook/app:latest
            command:
            - php
            - artisan
            - schedule:run
            envFrom:
            - configMapRef:
                name: app-config
            - secretRef:
                name: app-secret
            env:
            - name: DB_PASSWORD
              valueFrom:
                secretKeyRef:
                  name: app-secret
                  key: DB_PASSWORD
          restartPolicy: OnFailure
          imagePullSecrets:
          - name: harbor-secret
```

Apply:
```bash
kubectl apply -f k8s/scheduler-cronjob.yaml
```

---

### Step 13: Verifikasi Deployment

```bash
# Check semua pods
kubectl get pods -n sporteventbook

# Check services
kubectl get svc -n sporteventbook

# Check logs
kubectl logs -f deployment/laravel-app -n sporteventbook

# Check migration status
kubectl logs job/migrate -n sporteventbook

# Test akses aplikasi
curl http://<LOAD_BALANCER_IP>
```

---

## Troubleshooting

### Pod tidak running
```bash
# Describe pod untuk melihat error
kubectl describe pod <pod-name> -n sporteventbook

# Check logs
kubectl logs <pod-name> -n sporteventbook
```

### Database connection error
```bash
# Test koneksi ke MySQL
kubectl run mysql-test --image=mysql:8.0 -it --rm -n sporteventbook \
    --env="MYSQL_PWD=YourUserPassword123!" \
    -- mysql -h mysql -u sporteventbook -D sporteventbook -e "SELECT 1;"
```

### Storage permission issue
```bash
# Exec ke pod dan fix permission
kubectl exec -it deployment/laravel-app -n sporteventbook -- \
    chown -R laravel:laravel /var/www/html/storage
```

---

## Update Deployment

### Update Image
```bash
# Build dan push image baru
docker build -t harbor.your-domain.com/sporteventbook/app:v1.1.0 .
docker push harbor.your-domain.com/sporteventbook/app:v1.1.0

# Update deployment
kubectl set image deployment/laravel-app laravel=harbor.your-domain.com/sporteventbook/app:v1.1.0 -n sporteventbook

# Run migration jika ada
kubectl apply -f k8s/migration-job.yaml

# Monitor rollout
kubectl rollout status deployment/laravel-app -n sporteventbook
```

### Rollback
```bash
kubectl rollout undo deployment/laravel-app -n sporteventbook
```

---

## Monitoring

### Check resource usage
```bash
kubectl top pods -n sporteventbook
kubectl top nodes
```

### Check events
```bash
kubectl get events -n sporteventbook --sort-by='.lastTimestamp'
```

---

## Security Best Practices

1. **Gunakan Network Policies** untuk membatasi traffic antar pod
2. **Enable RBAC** untuk akses kubectl
3. **Scan images** dengan Trivy atau Clair sebelum deploy
4. **Gunakan HTTPS** dengan SSL certificate
5. **Rotate secrets** secara berkala
6. **Backup database** secara rutin

---

## Backup & Recovery

### Backup MySQL
```bash
kubectl run mysql-backup --image=mysql:8.0 --rm -it -n sporteventbook \
    --env="MYSQL_PWD=YourRootPassword" \
    -- mysqldump -h mysql -u root sporteventbook > backup.sql
```

### Restore MySQL
```bash
kubectl run mysql-restore --image=mysql:8.0 --rm -it -n sporteventbook \
    --env="MYSQL_PWD=YourRootPassword" \
    -- mysql -h mysql -u root sporteventbook < backup.sql
```

---

## Checklist Deployment

- [ ] SSH ke VM Bastion
- [ ] Clone repository
- [ ] Setup kubectl context
- [ ] Build Docker image
- [ ] Push ke Harbor Registry
- [ ] Create namespace
- [ ] Deploy MySQL
- [ ] Deploy Redis (optional)
- [ ] Create ConfigMap dan Secret
- [ ] Deploy Laravel App
- [ ] Deploy Nginx
- [ ] Run migrations
- [ ] Setup storage link
- [ ] Deploy queue worker
- [ ] Deploy scheduler
- [ ] Verifikasi semua pods running
- [ ] Test akses aplikasi
- [ ] Setup monitoring

---

## Contact & Support

Untuk pertanyaan atau issue terkait deployment, hubungi tim DevOps atau buat issue di repository.