#!/bin/bash

# SportEventBook Kubernetes Deployment Script
# This script automates the deployment process to Nutanix Kubernetes Platform

set -e

# Configuration
NAMESPACE="sporteventbook"
HARBOR_URL="registry.bercalab.my.id"
HARBOR_USERNAME="admin"
IMAGE_TAG="latest"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "========================================"
echo "  SportEventBook K8s Deployment Script"
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

# Get the directory where the script is located
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# Go to project root (parent of k8s folder)
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

cd "$PROJECT_ROOT"
docker build -t ${HARBOR_URL}/sporteventbook/app:${IMAGE_TAG} .

print_status "Logging in to Harbor Registry..."
read -sp "Enter Harbor password: " HARBOR_PASSWORD
echo ""

# Try login with password-stdin (more secure)
# Note: If you get TLS certificate error, add registry to insecure-registries in /etc/docker/daemon.json
echo "${HARBOR_PASSWORD}" | docker login ${HARBOR_URL} -u ${HARBOR_USERNAME} --password-stdin 2>&1 || {
    print_warning "Docker login failed. This may be due to TLS certificate issues."
    print_warning "To fix this, add to /etc/docker/daemon.json:"
    echo '{"insecure-registries": ["registry.bercalab.my.id"]}'
    print_warning "Then run: sudo systemctl restart docker"
    exit 1
}

print_status "Pushing image to Harbor..."
docker push ${HARBOR_URL}/sporteventbook/app:${IMAGE_TAG}

# Step 3: Create Secrets
echo ""
print_status "Step 3: Creating Kubernetes secrets..."

# MySQL Secret
print_status "Creating MySQL secret..."
read -sp "Enter MySQL root password: " MYSQL_ROOT_PASSWORD
echo ""
read -sp "Enter MySQL user password: " MYSQL_PASSWORD
echo ""
read -sp "Enter APP_KEY (base64 encoded): " APP_KEY
echo ""
read -sp "Enter Midtrans Server Key: " MIDTRANS_SERVER_KEY
echo ""
read -sp "Enter Midtrans Client Key: " MIDTRANS_CLIENT_KEY
echo ""

kubectl create secret generic mysql-secret \
    --from-literal=MYSQL_ROOT_PASSWORD="${MYSQL_ROOT_PASSWORD}" \
    --from-literal=MYSQL_DATABASE="sporteventbook" \
    --from-literal=MYSQL_USER="sporteventbook" \
    --from-literal=MYSQL_PASSWORD="${MYSQL_PASSWORD}" \
    --namespace=$NAMESPACE \
    --dry-run=client -o yaml | kubectl apply -f -

# Generate APP_KEY if not provided
if [ -z "$APP_KEY" ]; then
    APP_KEY=$(docker run --rm php:8.3 php -r "echo 'base64:' . base64_encode(random_bytes(32));")
    print_status "Generated APP_KEY: $APP_KEY"
fi

# App Secret
kubectl create secret generic app-secret \
    --from-literal=APP_KEY="${APP_KEY}" \
    --from-literal=DB_PASSWORD="${MYSQL_PASSWORD}" \
    --from-literal=MIDTRANS_SERVER_KEY="${MIDTRANS_SERVER_KEY}" \
    --from-literal=MIDTRANS_CLIENT_KEY="${MIDTRANS_CLIENT_KEY}" \
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

# Step 4: Apply Persistent Volumes
echo ""
print_status "Step 4: Applying Persistent Volumes..."
kubectl apply -f mysql-pv.yaml -n $NAMESPACE

# Step 5: Deploy Infrastructure
echo ""
print_status "Step 5: Deploying infrastructure..."
kubectl apply -f mysql-statefulset.yaml -n $NAMESPACE
kubectl apply -f redis-deployment.yaml -n $NAMESPACE

print_status "Waiting for MySQL to be ready..."
sleep 30

# Step 6: Run Migrations
echo ""
print_status "Step 6: Running database migrations..."
kubectl apply -f migration-job.yaml -n $NAMESPACE

print_status "Waiting for migration to complete..."
kubectl wait --for=condition=complete job/migrate -n $NAMESPACE --timeout=300s

# Step 7: Deploy Application
echo ""
print_status "Step 7: Deploying Laravel application..."
kubectl apply -f app-configmap.yaml -n $NAMESPACE
kubectl apply -f app-deployment.yaml -n $NAMESPACE

# Step 8: Deploy Nginx
echo ""
print_status "Step 8: Deploying Nginx..."
kubectl apply -f nginx-deployment.yaml -n $NAMESPACE
kubectl apply -f nginx-service.yaml -n $NAMESPACE

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
LB_IP=$(kubectl get svc nginx-service -n $NAMESPACE -o jsonpath='{.status.loadBalancer.ingress[0].ip}')
if [ ! -z "$LB_IP" ]; then
    print_status "Application is accessible at: http://${LB_IP}"
fi

echo ""
print_status "Deployment completed successfully!"
echo ""
echo "Useful commands:"
echo "  kubectl get pods -n $NAMESPACE           # Check pod status"
echo "  kubectl get svc -n $NAMESPACE            # Check services"
echo "  kubectl logs -f deployment/laravel-app -n $NAMESPACE  # View app logs"
echo "  kubectl describe pod <pod-name> -n $NAMESPACE  # Debug pod issues"
echo ""