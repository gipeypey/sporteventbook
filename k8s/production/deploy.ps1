# SportEventBook Production Deployment Script (PowerShell)
# Menggunakan NDB MySQL + NUS Object Storage + Traefik Ingress

$ErrorActionPreference = "Stop"

# Configuration
$NAMESPACE = "sporteventbook"
$HARBOR_URL = "registry.bercalab.my.id"
$HARBOR_USERNAME = "admin"
$IMAGE_TAG = "v1.0.0"
$DOMAIN = "sport.bercalab.my.id"

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
Write-Host "  SportEventBook Production Deployment"
Write-Host "  Domain: $DOMAIN"
Write-Host "  Database: NDB MySQL (192.168.2.61)"
Write-Host "  Storage: NUS Object Storage"
Write-Host "  Ingress: Traefik"
Write-Host "========================================"
Write-Host ""

# Check prerequisites
Print-Status "Checking prerequisites..."

$prerequisites = @("kubectl", "docker", "git")
$missingTools = @()

foreach ($cmd in $prerequisites) {
    try {
        $null = Get-Command $cmd -ErrorAction Stop
        Print-Status "$cmd found: $((Get-Command $cmd).Source)"
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

# Get version from argument
if ($args.Count -gt 0) {
    $IMAGE_TAG = $args[0]
}
Print-Status "Using image tag: $IMAGE_TAG"

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

# Step 2: Create Secrets
Write-Host ""
Print-Step "Step 2: Creating Kubernetes secrets..."

# Get Harbor password
$HARBOR_PASSWORD = Read-Host -AsSecureString "Enter Harbor password"
$HARBOR_PASSWORD_PLAIN = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto(
    [System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($HARBOR_PASSWORD)
)

# Get APP_KEY
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

# Get MySQL password
$DB_PASSWORD = Read-Host -AsSecureString "Enter NDB MySQL password"
$DB_PASSWORD_PLAIN = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto(
    [System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($DB_PASSWORD)
)

# Get AWS credentials
$AWS_ACCESS_KEY = Read-Host "Enter AWS Access Key ID"
$AWS_SECRET_KEY = Read-Host -AsSecureString "Enter AWS Secret Access Key"
$AWS_SECRET_KEY_PLAIN = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto(
    [System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($AWS_SECRET_KEY)
)

# Get Midtrans keys
$MIDTRANS_SERVER = Read-Host "Enter Midtrans Server Key"
$MIDTRANS_CLIENT = Read-Host "Enter Midtrans Client Key"

# Create app-secret
$secretYaml = @"
apiVersion: v1
kind: Secret
metadata:
  name: app-secret
  namespace: $NAMESPACE
type: Opaque
stringData:
  APP_KEY: "$APP_KEY"
  DB_HOST: "192.168.2.61"
  DB_PORT: "3306"
  DB_DATABASE: "sporteventbook"
  DB_USERNAME: "sporteventbook"
  DB_PASSWORD: "$DB_PASSWORD_PLAIN"
  AWS_ACCESS_KEY_ID: "$AWS_ACCESS_KEY"
  AWS_SECRET_ACCESS_KEY: "$AWS_SECRET_KEY_PLAIN"
  AWS_DEFAULT_REGION: "us-east-1"
  AWS_BUCKET: "sporteventbook-assets"
  MIDTRANS_SERVER_KEY: "$MIDTRANS_SERVER"
  MIDTRANS_CLIENT_KEY: "$MIDTRANS_CLIENT"
"@

$secretYaml | kubectl apply -f -
Print-Status "app-secret created"

# Create harbor-secret
try {
    kubectl create secret docker-registry harbor-secret `
        --docker-server=$HARBOR_URL `
        --docker-username=$HARBOR_USERNAME `
        --docker-password=$HARBOR_PASSWORD_PLAIN `
        --docker-email="admin@bercalab.my.id" `
        -n $NAMESPACE `
        --dry-run=client -o yaml | kubectl apply -f -
    Print-Status "harbor-secret created"
} catch {
    Print-Warning "Failed to create harbor-secret: $_"
}

# Clear sensitive data from memory
$HARBOR_PASSWORD_PLAIN = $null
$DB_PASSWORD_PLAIN = $null
$AWS_SECRET_KEY_PLAIN = $null
[System.GC]::Collect()

# Step 3: Deploy ConfigMap
Write-Host ""
Print-Step "Step 3: Deploying ConfigMap..."
try {
    kubectl apply -f "app-configmap.yaml" -n $NAMESPACE
    Print-Status "ConfigMap deployed"
} catch {
    Print-Error "Failed to deploy ConfigMap: $_"
    exit 1
}

# Step 4: Deploy Redis
Write-Host ""
Print-Step "Step 4: Deploying Redis..."
try {
    kubectl apply -f "../redis-deployment.yaml" -n $NAMESPACE
    Print-Status "Waiting for Redis to be ready (10 seconds)..."
    Start-Sleep -Seconds 10
    Print-Status "Redis deployed"
} catch {
    Print-Warning "Failed to deploy Redis: $_"
}

# Step 5: Build and Push Docker Image
Write-Host ""
Print-Step "Step 5: Building Docker image..."

try {
    # Login to Harbor
    Print-Status "Logging in to Harbor registry..."
    $loginResult = docker login $HARBOR_URL -u $HARBOR_USERNAME -p $HARBOR_PASSWORD_PLAIN 2>&1
    if ($LASTEXITCODE -ne 0) {
        Print-Error "Failed to login to Harbor"
        exit 1
    }
    Print-Status "Logged in to Harbor successfully"
    
    # Build image
    Print-Status "Building Docker image..."
    docker build -t "$HARBOR_URL/sporteventbook/app:$IMAGE_TAG" "../../"
    if ($LASTEXITCODE -ne 0) {
        Print-Error "Failed to build Docker image"
        exit 1
    }
    
    # Push image
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

# Step 6: Deploy Application
Write-Host ""
Print-Step "Step 6: Deploying Laravel application..."

try {
    $deploymentContent = Get-Content "app-deployment.yaml" -Raw
    $deploymentContent = $deploymentContent -replace "image: .*", "image: $HARBOR_URL/sporteventbook/app:$IMAGE_TAG"
    $deploymentContent | kubectl apply -f - -n $NAMESPACE
    Print-Status "Laravel application deployed"
} catch {
    Print-Error "Failed to deploy Laravel application: $_"
    exit 1
}

# Step 7: Deploy Nginx
Write-Host ""
Print-Step "Step 7: Deploying Nginx..."
try {
    kubectl apply -f "nginx-deployment.yaml" -n $NAMESPACE
    kubectl apply -f "nginx-service.yaml" -n $NAMESPACE
    Print-Status "Nginx deployed"
} catch {
    Print-Warning "Failed to deploy Nginx: $_"
}

# Step 8: Deploy Ingress
Write-Host ""
Print-Step "Step 8: Deploying Ingress (Traefik)..."
try {
    kubectl apply -f "ingress.yaml" -n $NAMESPACE
    Print-Status "Ingress deployed for $DOMAIN"
} catch {
    Print-Warning "Failed to deploy Ingress: $_"
}

# Step 9: Run Migrations
Write-Host ""
Print-Step "Step 9: Running database migrations..."

try {
    $migrationContent = Get-Content "../migration-job.yaml" -Raw
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

# Step 10: Deploy Queue Worker
Write-Host ""
Print-Step "Step 10: Deploying queue worker..."

try {
    $queueContent = Get-Content "../queue-worker-deployment.yaml" -Raw
    $queueContent = $queueContent -replace "image: .*", "image: $HARBOR_URL/sporteventbook/app:$IMAGE_TAG"
    $queueContent | kubectl apply -f - -n $NAMESPACE
    Print-Status "Queue worker deployed"
} catch {
    Print-Warning "Failed to deploy queue worker: $_"
}

# Step 11: Deploy Scheduler
Write-Host ""
Print-Step "Step 11: Deploying scheduler..."

try {
    $schedulerContent = Get-Content "../scheduler-cronjob.yaml" -Raw
    $schedulerContent = $schedulerContent -replace "image: .*", "image: $HARBOR_URL/sporteventbook/app:$IMAGE_TAG"
    $schedulerContent | kubectl apply -f - -n $NAMESPACE
    Print-Status "Scheduler deployed"
} catch {
    Print-Warning "Failed to deploy scheduler: $_"
}

# Step 12: Verify Deployment
Write-Host ""
Print-Step "Step 12: Verifying deployment..."
Start-Sleep -Seconds 30

Write-Host ""
Print-Status "Pod Status:"
kubectl get pods -n $NAMESPACE

Write-Host ""
Print-Status "Service Status:"
kubectl get svc -n $NAMESPACE

Write-Host ""
Print-Status "Ingress Status:"
kubectl get ingress -n $NAMESPACE

# Summary
Write-Host ""
Write-Host "========================================"
Write-Host "  Deployment Summary"
Write-Host "========================================"
Write-Host "  Domain:        https://$DOMAIN"
Write-Host "  Database:      NDB MySQL (192.168.2.61)"
Write-Host "  Storage:       NUS Object Storage"
Write-Host "  Ingress:       Traefik"
Write-Host "  Image:         $HARBOR_URL/sporteventbook/app:$IMAGE_TAG"
Write-Host "  Namespace:     $NAMESPACE"
Write-Host "========================================"
Write-Host ""
Print-Success "Deployment completed!"
Write-Host ""
Write-Host "Useful commands:"
Write-Host "  kubectl get pods -n $NAMESPACE"
Write-Host "  kubectl logs -f deployment/laravel-app -n $NAMESPACE"
Write-Host "  kubectl logs -f deployment/nginx -n $NAMESPACE"
Write-Host "  kubectl describe ingress sporteventbook-ingress -n $NAMESPACE"
Write-Host ""
Write-Host "To rollback:"
Write-Host "  kubectl rollout undo deployment/laravel-app -n $NAMESPACE"
Write-Host ""