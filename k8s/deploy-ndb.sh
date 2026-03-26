#!/bin/bash

# SportEventBook Kubernetes Deployment Script (NDB MySQL Version)
# This script deploys to Nutanix Kubernetes Platform using MySQL from NDB

set -e

# Configuration
NAMESPACE="sporteventbook"
HARBOR_URL="harbor.your-domain.com"
HARBOR_USERNAME="admin"
IMAGE_TAG="latest"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "========================================"
echo "  SportEventBook K8s Deployment (NDB)"
echo "========================================"
echo ""

# Function to print status
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check prerequisites
print_status "Checking prerequisites..."

# Check kubectl
if ! command -v kubectl &> /dev/null; then
    print_error "kubectl is not installed. Please install kubectl first."
    exit 1
fi
print_status "kubectl found: $(kubectl version --client --short 2>/dev/null | tail -1)"

# Check Docker
if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed. Please install Docker first."
    exit 1
fi
print_status "Docker found: $(docker --version)"

# Check connection to cluster
print_status "Checking Kubernetes cluster connection..."
if ! kubectl cluster-info &> /dev/null; then
    print_error "Cannot connect to Kubernetes cluster. Please check your kubeconfig."
    exit 1
fi
print_status "Connected to cluster: $(kubectl config current-context)"

# Step 1: Create Namespace
echo ""
print_status "Step 1: Creating namespace..."
kubectl create namespace $NAMESPACE --dry-run=client -o yaml | kubectl apply -f -
print_status "Namespace '$NAMESPACE' created/verified"

# Step 2: Build and Push Docker Image
echo ""
print_status "Step 2: Building Docker image..."
docker build -t ${HARBOR_URL}/sporteventbook/app:${IMAGE_TAG} .

print_status "Logging in to Harbor Registry..."
read -sp "Enter Harbor password: " HARBOR_PASSWORD
echo ""
docker login ${HARBOR_URL} -u ${HARBOR_USERNAME} -p "${HARBOR_PASSWORD}"

print_status "Pushing image to Harbor..."
docker push ${HARBOR_URL}/sporteventbook/app:${IMAGE_TAG}

# Step 3: Get NDB MySQL Connection Details
echo ""
print_status "Step 3: Configure NDB MySQL Connection..."
echo ""
echo "Please enter your NDB MySQL connection details:"
echo "(You can find these in NDB Console → Database → Your MySQL Instance)"
echo ""
read -p "NDB MySQL Host/IP: " NDB_HOST
read -p "NDB MySQL Port (default: 3306): " NDB_PORT
NDB_PORT=${NDB_PORT:-3306}
read -p "NDB MySQL Database Name: " NDB_DATABASE
read -p "NDB MySQL Username: " NDB_USERNAME
read -sp "NDB MySQL Password: " NDB_PASSWORD
echo ""

# Generate APP_KEY if not provided
print_status "Generating APP_KEY..."
APP_KEY=$(docker run --rm php:8.3 php -r "echo 'base64:' . base64_encode(random_bytes(32));")

# Step 4: Create Secrets
echo ""
print_status "Step 4: Creating Kubernetes secrets..."

# App Secret with NDB MySQL credentials
kubectl create secret generic app-secret \
    --from-literal=APP_KEY="${APP_KEY}" \
    --from-literal=DB_HOST="${NDB_HOST}" \
    --from-literal=DB_PORT="${NDB_PORT}" \
    --from-literal=DB_DATABASE="${NDB_DATABASE}" \
    --from-literal=DB_USERNAME="${NDB_USERNAME}" \
    --from-literal=DB_PASSWORD="${NDB_PASSWORD}" \
    --from-literal=MIDTRANS_SERVER_KEY="SB-Mid-server-xxxxx" \
    --from-literal=MIDTRANS_CLIENT_KEY="SB-Mid-client-xxxxx" \
    --namespace=$NAMESPACE \
    --dry-run=client -o yaml | kubectl apply -f -

# Harbor Secret
print_status "Creating Harbor registry secret..."
kubectl create secret docker-registry harbor-secret \
    --docker-server=${HARBOR_URL} \
    --docker-username=${HARBOR_USERNAME} \
    --docker-password="${HARBOR_PASSWORD}" \
    --namespace=$NAMESPACE \
    --dry-run=client -o yaml | kubectl apply -f -

# Step 5: Deploy Redis (Optional, untuk cache/queue)
echo ""
print_status "Step 5: Deploying Redis..."
kubectl apply -f redis-deployment.yaml -n $NAMESPACE

# Step 6: Deploy Application
echo ""
print_status "Step 6: Deploying Laravel application..."

# Create ConfigMap with NDB MySQL host
cat <<EOF | kubectl apply -f -
apiVersion: v1
kind: ConfigMap
metadata:
  name: app-config
  namespace: $NAMESPACE
data:
  APP_NAME: "SportEventBook"
  APP_ENV: "production"
  APP_DEBUG: "false"
  APP_URL: "https://sporteventbook.your-domain.com"
  
  DB_CONNECTION: "mysql"
  DB_HOST: "${NDB_HOST}"
  DB_PORT: "${NDB_PORT}"
  DB_DATABASE: "${NDB_DATABASE}"
  DB_USERNAME: "${NDB_USERNAME}"
  
  CACHE_DRIVER: "redis"
  QUEUE_CONNECTION: "redis"
  SESSION_DRIVER: "redis"
  
  REDIS_HOST: "redis"
  REDIS_PORT: "6379"
  
  LOG_CHANNEL: "stderr"
  LOG_LEVEL: "info"
EOF

kubectl apply -f app-deployment.yaml -n $NAMESPACE

# Step 7: Deploy Nginx
echo ""
print_status "Step 7: Deploying Nginx..."
kubectl apply -f nginx-deployment.yaml -n $NAMESPACE
kubectl apply -f nginx-service.yaml -n $NAMESPACE

# Step 8: Run Migrations
echo ""
print_status "Step 8: Running database migrations..."
kubectl apply -f migration-job.yaml -n $NAMESPACE

print_status "Waiting for migration to complete..."
kubectl wait --for=condition=complete job/migrate -n $NAMESPACE --timeout=300s || print_warning "Migration may still be running. Check with: kubectl get jobs -n $NAMESPACE"

# Step 9: Deploy Queue Worker
echo ""
print_status "Step 9: Deploying queue worker..."
kubectl apply -f queue-worker-deployment.yaml -n $NAMESPACE

# Step 10: Deploy Scheduler
echo ""
print_status "Step 10: Deploying scheduler..."
kubectl apply -f scheduler-cronjob.yaml -n $NAMESPACE

# Step 11: Verify Deployment
echo ""
print_status "Step 11: Verifying deployment..."
echo ""
echo "Pods status:"
kubectl get pods -n $NAMESPACE
echo ""
echo "Services:"
kubectl get svc -n $NAMESPACE
echo ""

# Get LoadBalancer IP
LB_IP=$(kubectl get svc nginx-service -n $NAMESPACE -o jsonpath='{.status.loadBalancer.ingress[0].ip}' 2>/dev/null || echo "pending")
if [ ! -z "$LB_IP" ] && [ "$LB_IP" != "pending" ]; then
    print_status "Application is accessible at: http://${LB_IP}"
else
    print_status "LoadBalancer IP is pending. Check with: kubectl get svc -n $NAMESPACE"
fi

echo ""
print_status "Deployment completed successfully!"
echo ""
echo "========================================"
echo "  Deployment Summary"
echo "========================================"
echo "  Database: NDB MySQL (${NDB_HOST}:${NDB_PORT})"
echo "  Namespace: ${NAMESPACE}"
echo "  Image: ${HARBOR_URL}/sporteventbook/app:${IMAGE_TAG}"
echo "========================================"
echo ""
echo "Useful commands:"
echo "  kubectl get pods -n $NAMESPACE           # Check pod status"
echo "  kubectl get svc -n $NAMESPACE            # Check services"
echo "  kubectl logs -f deployment/laravel-app -n $NAMESPACE  # View app logs"
echo "  kubectl describe pod <pod-name> -n $NAMESPACE  # Debug pod issues"
echo "  kubectl logs job/migrate -n $NAMESPACE   # Check migration logs"
echo ""
echo "To test database connection:"
echo "  kubectl run db-test --image=mysql:8.0 --rm -it -n $NAMESPACE \\"
echo "    --env=\"MYSQL_PWD=${NDB_PASSWORD}\" \\"
echo "    -- mysql -h ${NDB_HOST} -u ${NDB_USERNAME} -D ${NDB_DATABASE} -e \"SELECT 1;\""
echo ""