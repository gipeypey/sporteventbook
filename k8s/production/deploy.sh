#!/bin/bash

# SportEventBook Production Deployment Script
# Menggunakan NDB MySQL + NUS Object Storage + Traefik Ingress

set -e

# Configuration
NAMESPACE="sporteventbook"
HARBOR_URL="registry.bercalab.my.id"
HARBOR_USERNAME="admin"
IMAGE_TAG="v1.0.0"
DOMAIN="sport.bercalab.my.id"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_status() { echo -e "${GREEN}[INFO]${NC} $1"; }
print_warning() { echo -e "${YELLOW}[WARN]${NC} $1"; }
print_error() { echo -e "${RED}[ERROR]${NC} $1"; }
print_step() { echo -e "${BLUE}[STEP]${NC} $1"; }

echo ""
echo "========================================"
echo "  SportEventBook Production Deployment"
echo "  Domain: ${DOMAIN}"
echo "  Database: NDB MySQL (192.168.2.61)"
echo "  Storage: NUS Object Storage"
echo "  Ingress: Traefik"
echo "========================================"
echo ""

# Check prerequisites
print_status "Checking prerequisites..."
for cmd in kubectl docker git; do
    if ! command -v $cmd &> /dev/null; then
        print_error "$cmd is not installed."
        exit 1
    fi
done
print_status "Prerequisites OK"

# Get version
if [ -n "$1" ]; then
    IMAGE_TAG=$1
fi
print_status "Using image tag: ${IMAGE_TAG}"

# Step 1: Create Namespace
echo ""
print_step "Step 1: Creating namespace..."
kubectl create namespace $NAMESPACE --dry-run=client -o yaml | kubectl apply -f -
print_status "Namespace '${NAMESPACE}' ready"

# Step 2: Create Secrets
echo ""
print_step "Step 2: Creating Kubernetes secrets..."

# Get values from user
read -sp "Enter Harbor password: " HARBOR_PASSWORD
echo ""

read -sp "Enter APP_KEY (or press enter to generate): " APP_KEY
echo ""
if [ -z "$APP_KEY" ]; then
    APP_KEY=$(docker run --rm php:8.3 php -r "echo 'base64:' . base64_encode(random_bytes(32));")
    print_status "Generated APP_KEY"
fi

read -sp "Enter NDB MySQL password: " DB_PASSWORD
echo ""

read -sp "Enter AWS Access Key ID: " AWS_ACCESS_KEY
echo ""

read -sp "Enter AWS Secret Access Key: " AWS_SECRET_KEY
echo ""

read -sp "Enter Midtrans Server Key: " MIDTRANS_SERVER
echo ""

read -sp "Enter Midtrans Client Key: " MIDTRANS_CLIENT
echo ""

# Create app-secret
cat <<EOF | kubectl apply -f -
apiVersion: v1
kind: Secret
metadata:
  name: app-secret
  namespace: ${NAMESPACE}
type: Opaque
stringData:
  APP_KEY: "${APP_KEY}"
  DB_HOST: "192.168.2.61"
  DB_PORT: "3306"
  DB_DATABASE: "sporteventbook"
  DB_USERNAME: "sporteventbook"
  DB_PASSWORD: "${DB_PASSWORD}"
  AWS_ACCESS_KEY_ID: "${AWS_ACCESS_KEY}"
  AWS_SECRET_ACCESS_KEY: "${AWS_SECRET_KEY}"
  AWS_DEFAULT_REGION: "us-east-1"
  AWS_BUCKET: "sporteventbook-assets"
  MIDTRANS_SERVER_KEY: "${MIDTRANS_SERVER}"
  MIDTRANS_CLIENT_KEY: "${MIDTRANS_CLIENT}"
EOF

# Create harbor-secret
kubectl create secret docker-registry harbor-secret \
    --docker-server=${HARBOR_URL} \
    --docker-username=${HARBOR_USERNAME} \
    --docker-password="${HARBOR_PASSWORD}" \
    --docker-email=admin@bercalab.my.id \
    -n ${NAMESPACE} \
    --dry-run=client -o yaml | kubectl apply -f -

print_status "Secrets created"

# Step 3: Deploy ConfigMap
echo ""
print_step "Step 3: Deploying ConfigMap..."
kubectl apply -f app-configmap.yaml -n ${NAMESPACE}
print_status "ConfigMap deployed"

# Step 4: Deploy Redis
echo ""
print_step "Step 4: Deploying Redis..."
kubectl apply -f ../redis-deployment.yaml -n ${NAMESPACE}
print_status "Waiting for Redis to be ready..."
sleep 10
print_status "Redis deployed"

# Step 5: Build and Push Docker Image
echo ""
print_step "Step 5: Building Docker image..."

docker login ${HARBOR_URL} -u ${HARBOR_USERNAME} -p "${HARBOR_PASSWORD}" 2>/dev/null || {
    print_error "Failed to login to Harbor"
    exit 1
}

docker build -t ${HARBOR_URL}/sporteventbook/app:${IMAGE_TAG} ../../
docker push ${HARBOR_URL}/sporteventbook/app:${IMAGE_TAG}

print_status "Image built and pushed: ${IMAGE_TAG}"

# Step 6: Deploy Application
echo ""
print_step "Step 6: Deploying Laravel application..."

# Update image in deployment
sed "s|image: .*|image: ${HARBOR_URL}/sporteventbook/app:${IMAGE_TAG}|" app-deployment.yaml | \
    kubectl apply -f - -n ${NAMESPACE}

print_status "Laravel application deployed"

# Step 7: Deploy Nginx
echo ""
print_step "Step 7: Deploying Nginx..."
kubectl apply -f nginx-deployment.yaml -n ${NAMESPACE}
kubectl apply -f nginx-service.yaml -n ${NAMESPACE}
print_status "Nginx deployed"

# Step 8: Deploy Ingress
echo ""
print_step "Step 8: Deploying Ingress (Traefik)..."
kubectl apply -f ingress.yaml -n ${NAMESPACE}
print_status "Ingress deployed for ${DOMAIN}"

# Step 9: Run Migrations
echo ""
print_step "Step 9: Running database migrations..."

# Update and apply migration job
sed "s|image: .*|image: ${HARBOR_URL}/sporteventbook/app:${IMAGE_TAG}|" ../migration-job.yaml | \
    kubectl apply -f - -n ${NAMESPACE}

print_status "Waiting for migration to complete..."
kubectl wait --for=condition=complete job/migrate -n ${NAMESPACE} --timeout=300s || \
    print_warning "Migration may still be running"

print_status "Migration job submitted"

# Step 10: Deploy Queue Worker
echo ""
print_step "Step 10: Deploying queue worker..."

# Update queue worker image
sed "s|image: .*|image: ${HARBOR_URL}/sporteventbook/app:${IMAGE_TAG}|" ../queue-worker-deployment.yaml | \
    kubectl apply -f - -n ${NAMESPACE}

print_status "Queue worker deployed"

# Step 11: Deploy Scheduler
echo ""
print_step "Step 11: Deploying scheduler..."

# Update scheduler image
sed "s|image: .*|image: ${HARBOR_URL}/sporteventbook/app:${IMAGE_TAG}|" ../scheduler-cronjob.yaml | \
    kubectl apply -f - -n ${NAMESPACE}

print_status "Scheduler deployed"

# Step 12: Verify Deployment
echo ""
print_step "Step 12: Verifying deployment..."
sleep 30

echo ""
print_status "Pod Status:"
kubectl get pods -n ${NAMESPACE}

echo ""
print_status "Service Status:"
kubectl get svc -n ${NAMESPACE}

echo ""
print_status "Ingress Status:"
kubectl get ingress -n ${NAMESPACE}

# Summary
echo ""
echo "========================================"
echo "  Deployment Summary"
echo "========================================"
echo "  Domain:        https://${DOMAIN}"
echo "  Database:      NDB MySQL (192.168.2.61)"
echo "  Storage:       NUS Object Storage"
echo "  Ingress:       Traefik"
echo "  Image:         ${HARBOR_URL}/sporteventbook/app:${IMAGE_TAG}"
echo "  Namespace:     ${NAMESPACE}"
echo "========================================"
echo ""
print_status "Deployment completed!"
echo ""
echo "Useful commands:"
echo "  kubectl get pods -n ${NAMESPACE}"
echo "  kubectl logs -f deployment/laravel-app -n ${NAMESPACE}"
echo "  kubectl logs -f deployment/nginx -n ${NAMESPACE}"
echo "  kubectl describe ingress sporteventbook-ingress -n ${NAMESPACE}"
echo ""
echo "To rollback:"
echo "  kubectl rollout undo deployment/laravel-app -n ${NAMESPACE}"
echo ""