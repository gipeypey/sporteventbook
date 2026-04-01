# SportEventBook Kubernetes Deployment Script (PowerShell)
# Deploy ke Nutanix Kubernetes Platform dengan MySQL StatefulSet (Self-Hosted)

$ErrorActionPreference = "Stop"

# Configuration
$NAMESPACE = "sporteventbook"
$HARBOR_URL = "registry.bercalab.my.id"
$HARBOR_USERNAME = "admin"
$IMAGE_TAG = "latest"

# Helper Functions
function Print-Status {
    param([string]$Message)
    Write-Host "[INFO] $Message" -ForegroundColor Green
}

function Print-Warning {
    param([string]$Message)
    Write-Host "[WARN] $Message" -ForegroundColor Yellow
}

function Print-Error {
    param([string]$Message)
    Write-Host "[ERROR] $Message" -ForegroundColor Red
}

function Print-Step {
    param([string]$Message)
    Write-Host "[STEP] $Message" -ForegroundColor Blue
}

function Print-Success {
    param([string]$Message)
    Write-Host "[SUCCESS] $Message" -ForegroundColor Cyan
}

# Main Script
Write-Host ""
Write-Host "========================================"
Write-Host "  SportEventBook K8s Deployment"
Write-Host "  Database: MySQL StatefulSet (Self-Hosted)"
Write-Host "  Cache: Redis"
Write-Host "========================================"
Write-Host ""

# Check prerequisites
Print-Status "Checking prerequisites..."

$prerequisites = @("kubectl", "docker")
$missingTools = @()

foreach ($cmd in $prerequisites) {
    try {
        $null = Get-Command $cmd -ErrorAction Stop
        Print-Status "$cmd found"
    } catch {
        $missingTools += $cmd
    }
}

if ($missingTools.Count -gt 0) {
    Print-Error "Missing tools: $($missingTools -join ', ')"
    Print-Error "Please install the missing tools and try again."
    exit 1
}

Print-Status "Prerequisites OK"

# Check Kubernetes cluster connection
Print-Status "Checking Kubernetes cluster connection..."
try {
    $clusterInfo = kubectl cluster-info 2>&1 | Out-String
    $currentContext = kubectl config current-context 2>&1
    Print-Status "Connected to cluster: $currentContext"
} catch {
    Print-Error "Cannot connect to Kubernetes cluster. Please check your kubeconfig."
    exit 1
}

# Step 1: Create Namespace
Write-Host ""
Print-Step "Step 1: Creating namespace..."
try {
    kubectl create namespace $NAMESPACE --dry-run=client -o yaml | kubectl apply -f -
    Print-Status "Namespace '$NAMESPACE' created/verified"
} catch {
    Print-Error "Failed to create namespace: $_"
    exit 1
}

# Step 2: Build and Push Docker Image
Write-Host ""
Print-Step "Step 2: Building Docker image..."

$HARBOR_PASSWORD = Read-Host -AsSecureString "Enter Harbor password"
$HARBOR_PASSWORD_PLAIN = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto(
    [System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($HARBOR_PASSWORD)
)

try {
    Print-Status "Logging in to Harbor Registry..."
    docker login $HARBOR_URL -u $HARBOR_USERNAME -p $HARBOR_PASSWORD_PLAIN 2>&1 | Out-Null
    if ($LASTEXITCODE -ne 0) {
        Print-Error "Failed to login to Harbor"
        exit 1
    }
    
    Print-Status "Building Docker image..."
    docker build -t "$HARBOR_URL/sporteventbook/app:$IMAGE_TAG" "../"
    if ($LASTEXITCODE -ne 0) {
        Print-Error "Failed to build Docker image"
        exit 1
    }
    
    Print-Status "Pushing image to Harbor..."
    docker push "$HARBOR_URL/sporteventbook/app:$IMAGE_TAG"
    if ($LASTEXITCODE -ne 0) {
        Print-Error "Failed to push Docker image"
        exit 1
    }
    
    Print-Status "Image built and pushed: $IMAGE_TAG"
} catch {
    Print-Error "Docker operation failed: $_"
    exit 1
}

# Clear sensitive data
$HARBOR_PASSWORD_PLAIN = $null
[System.GC]::Collect()

# Step 3: Create Secrets
Write-Host ""
Print-Step "Step 3: Creating Kubernetes secrets..."

# Get MySQL credentials
$MYSQL_ROOT_PASSWORD = Read-Host -AsSecureString "Enter MySQL root password"
$MYSQL_ROOT_PASSWORD_PLAIN = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto(
    [System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($MYSQL_ROOT_PASSWORD)
)

$MYSQL_PASSWORD = Read-Host -AsSecureString "Enter MySQL user password"
$MYSQL_PASSWORD_PLAIN = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto(
    [System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($MYSQL_PASSWORD)
)

$APP_KEY_INPUT = Read-Host "Enter APP_KEY (or press enter to generate)"
if ([string]::IsNullOrWhiteSpace($APP_KEY_INPUT)) {
    try {
        $APP_KEY = docker run --rm php:8.3 php -r "echo 'base64:' . base64_encode(random_bytes(32));" 2>&1
        Print-Status "Generated APP_KEY: $APP_KEY"
    } catch {
        Print-Warning "Failed to generate APP_KEY using Docker. Please provide manually."
        $APP_KEY = Read-Host "Enter APP_KEY manually"
    }
} else {
    $APP_KEY = $APP_KEY_INPUT
}

$MIDTRANS_SERVER_KEY = Read-Host "Enter Midtrans Server Key"
$MIDTRANS_CLIENT_KEY = Read-Host "Enter Midtrans Client Key"

# Create MySQL secret
$mysqlSecretYaml = @"
apiVersion: v1
kind: Secret
metadata:
  name: mysql-secret
  namespace: $NAMESPACE
type: Opaque
stringData:
  MYSQL_ROOT_PASSWORD: "$MYSQL_ROOT_PASSWORD_PLAIN"
  MYSQL_DATABASE: "sporteventbook"
  MYSQL_USER: "sporteventbook"
  MYSQL_PASSWORD: "$MYSQL_PASSWORD_PLAIN"
"@

$mysqlSecretYaml | kubectl apply -f -
Print-Status "MySQL secret created"

# Create App secret
$appSecretYaml = @"
apiVersion: v1
kind: Secret
metadata:
  name: app-secret
  namespace: $NAMESPACE
type: Opaque
stringData:
  APP_KEY: "$APP_KEY"
  DB_PASSWORD: "$MYSQL_PASSWORD_PLAIN"
  MIDTRANS_SERVER_KEY: "$MIDTRANS_SERVER_KEY"
  MIDTRANS_CLIENT_KEY: "$MIDTRANS_CLIENT_KEY"
"@

$appSecretYaml | kubectl apply -f -
Print-Status "App secret created"

# Create Harbor secret
$harborSecretYaml = @"
apiVersion: v1
kind: Secret
metadata:
  name: harbor-secret
  namespace: $NAMESPACE
type: kubernetes.io/dockerconfigjson
stringData:
  .dockerconfigjson: '{"auths":{"$HARBOR_URL":{"username":"$HARBOR_USERNAME","password":"$HARBOR_PASSWORD_PLAIN","auth":"$(ConvertTo-Base64 "$HARBOR_USERNAME:$HARBOR_PASSWORD_PLAIN")"}}}'
"@

try {
    kubectl create secret docker-registry harbor-secret `
        --docker-server=$HARBOR_URL `
        --docker-username=$HARBOR_USERNAME `
        --docker-password=$HARBOR_PASSWORD_PLAIN `
        --namespace=$NAMESPACE `
        --dry-run=client -o yaml | kubectl apply -f -
    Print-Status "Harbor secret created"
} catch {
    Print-Warning "Failed to create harbor-secret: $_"
}

# Clear sensitive data
$MYSQL_ROOT_PASSWORD_PLAIN = $null
$MYSQL_PASSWORD_PLAIN = $null
$HARBOR_PASSWORD_PLAIN = $null
[System.GC]::Collect()

# Step 4: Apply Persistent Volumes
Write-Host ""
Print-Step "Step 4: Applying Persistent Volumes..."
try {
    kubectl apply -f "mysql-pv.yaml" -n $NAMESPACE
    Print-Status "Persistent Volumes applied"
} catch {
    Print-Warning "Failed to apply PV: $_"
}

# Step 5: Deploy Infrastructure
Write-Host ""
Print-Step "Step 5: Deploying infrastructure..."
try {
    kubectl apply -f "mysql-statefulset.yaml" -n $NAMESPACE
    kubectl apply -f "redis-deployment.yaml" -n $NAMESPACE
    Print-Status "MySQL StatefulSet and Redis Deployment created"
} catch {
    Print-Error "Failed to deploy infrastructure: $_"
    exit 1
}

Print-Status "Waiting for MySQL to be ready (30 seconds)..."
Start-Sleep -Seconds 30

# Check MySQL pod status
Print-Status "Checking MySQL pod status..."
kubectl get pods -n $NAMESPACE -l app=mysql 2>&1 | ForEach-Object { Write-Host "  $_" }

# Step 6: Run Migrations
Write-Host ""
Print-Step "Step 6: Running database migrations..."

try {
    $migrationContent = Get-Content "migration-job.yaml" -Raw
    $migrationContent = $migrationContent -replace "image: .*", "image: $HARBOR_URL/sporteventbook/app:$IMAGE_TAG"
    $migrationContent | kubectl apply -f - -n $NAMESPACE
    
    Print-Status "Waiting for migration to complete (timeout: 300s)..."
    kubectl wait --for=condition=complete job/migrate -n $NAMESPACE --timeout=300s 2>&1 | Out-Null
    if ($LASTEXITCODE -ne 0) {
        Print-Warning "Migration may still be running"
    } else {
        Print-Status "Migration completed successfully"
    }
} catch {
    Print-Warning "Migration job failed: $_"
}

# Step 7: Deploy Application
Write-Host ""
Print-Step "Step 7: Deploying Laravel application..."

try {
    $deploymentContent = Get-Content "app-deployment.yaml" -Raw
    $deploymentContent = $deploymentContent -replace "image: .*", "image: $HARBOR_URL/sporteventbook/app:$IMAGE_TAG"
    $deploymentContent | kubectl apply -f - -n $NAMESPACE
    
    kubectl apply -f "app-configmap.yaml" -n $NAMESPACE
    Print-Status "Laravel application deployed"
} catch {
    Print-Error "Failed to deploy Laravel application: $_"
    exit 1
}

# Step 8: Deploy Nginx
Write-Host ""
Print-Step "Step 8: Deploying Nginx..."
try {
    kubectl apply -f "nginx-deployment.yaml" -n $NAMESPACE
    kubectl apply -f "nginx-service.yaml" -n $NAMESPACE
    Print-Status "Nginx deployed"
} catch {
    Print-Warning "Failed to deploy Nginx: $_"
}

# Step 9: Deploy Queue Worker
Write-Host ""
Print-Step "Step 9: Deploying queue worker..."
try {
    $queueContent = Get-Content "queue-worker-deployment.yaml" -Raw
    $queueContent = $queueContent -replace "image: .*", "image: $HARBOR_URL/sporteventbook/app:$IMAGE_TAG"
    $queueContent | kubectl apply -f - -n $NAMESPACE
    Print-Status "Queue worker deployed"
} catch {
    Print-Warning "Failed to deploy queue worker: $_"
}

# Step 10: Deploy Scheduler
Write-Host ""
Print-Step "Step 10: Deploying scheduler..."
try {
    $schedulerContent = Get-Content "scheduler-cronjob.yaml" -Raw
    $schedulerContent = $schedulerContent -replace "image: .*", "image: $HARBOR_URL/sporteventbook/app:$IMAGE_TAG"
    $schedulerContent | kubectl apply -f - -n $NAMESPACE
    Print-Status "Scheduler deployed"
} catch {
    Print-Warning "Failed to deploy scheduler: $_"
}

# Step 11: Verify Deployment
Write-Host ""
Print-Step "Step 11: Verifying deployment..."
Start-Sleep -Seconds 10

Write-Host ""
Print-Status "Pods status:"
kubectl get pods -n $NAMESPACE

Write-Host ""
Print-Status "Services:"
kubectl get svc -n $NAMESPACE

# Get LoadBalancer IP
try {
    $LB_IP = kubectl get svc nginx-service -n $NAMESPACE -o jsonpath='{.status.loadBalancer.ingress[0].ip}' 2>&1
    if (-not [string]::IsNullOrWhiteSpace($LB_IP)) {
        Write-Host ""
        Print-Success "Application is accessible at: http://$LB_IP"
    }
} catch {
    Print-Warning "Could not get LoadBalancer IP"
}

# Summary
Write-Host ""
Write-Host "========================================"
Write-Host "  Deployment Summary"
Write-Host "========================================"
Write-Host "  Database:      MySQL StatefulSet (Self-Hosted)"
Write-Host "  Cache:         Redis"
Write-Host "  Image:         $HARBOR_URL/sporteventbook/app:$IMAGE_TAG"
Write-Host "  Namespace:     $NAMESPACE"
Write-Host "========================================"
Write-Host ""
Print-Success "Deployment completed!"
Write-Host ""
Write-Host "Useful commands:"
Write-Host "  kubectl get pods -n $NAMESPACE"
Write-Host "  kubectl get svc -n $NAMESPACE"
Write-Host "  kubectl logs -f deployment/laravel-app -n $NAMESPACE"
Write-Host "  kubectl logs -f statefulset/mysql -n $NAMESPACE"
Write-Host "  kubectl describe pod <pod-name> -n $NAMESPACE"
Write-Host ""
Write-Host "To rollback:"
Write-Host "  kubectl rollout undo deployment/laravel-app -n $NAMESPACE"
Write-Host ""

# Helper function for Base64 encoding
function ConvertTo-Base64 {
    param([string]$Value)
    $bytes = [System.Text.Encoding]::UTF8.GetBytes($Value)
    return [Convert]::ToBase64String($bytes)
}