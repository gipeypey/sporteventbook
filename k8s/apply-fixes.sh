#!/bin/bash

# SportEventBook Apply Fixes Script
# This script applies all fixes and restarts deployments

set -e

# Configuration
NAMESPACE="sporteventbook"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "========================================"
echo "  SportEventBook Apply Fixes Script"
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

# Check kubectl
if ! command -v kubectl &> /dev/null; then
    print_error "kubectl is not installed. Please install kubectl first."
    exit 1
fi

print_status "Connected to cluster: $(kubectl config current-context)"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Step 1: Suspend scheduler to prevent new jobs
echo ""
print_status "Step 1: Suspending scheduler CronJob..."
kubectl patch cronjob laravel-scheduler -n $NAMESPACE -p '{"spec": {"suspend": true}}' --dry-run=client -o yaml | kubectl apply -f - 2>/dev/null || true
print_status "Scheduler CronJob suspended"

# Step 2: Delete old PVC
echo ""
print_status "Step 2: Deleting old PVC..."
kubectl delete pvc app-storage-pvc -n $NAMESPACE --ignore-not-found=true --force --grace-period=0 2>/dev/null || print_warning "PVC not found or already deleted"
sleep 2
print_status "Old PVC deleted"

# Step 3: Apply new PVC
echo ""
print_status "Step 3: Applying new PVC..."
kubectl apply -f "${SCRIPT_DIR}/app-storage-pvc.yaml" -n $NAMESPACE
print_status "New PVC applied"

# Step 4: Delete old deployments
echo ""
print_status "Step 4: Deleting old deployments..."
kubectl delete deployment laravel-app -n $NAMESPACE --ignore-not-found=true 2>/dev/null || true
kubectl delete deployment queue-worker -n $NAMESPACE --ignore-not-found=true 2>/dev/null || true
kubectl delete deployment nginx -n $NAMESPACE --ignore-not-found=true 2>/dev/null || true
print_status "Old deployments deleted"

# Step 5: Force delete terminating pods
echo ""
print_status "Step 5: Force deleting terminating pods..."
kubectl delete pod -n $NAMESPACE --all --force --grace-period=0 2>/dev/null || print_warning "No pods to delete"
sleep 5
print_status "Terminating pods cleanup completed"

# Step 6: Apply new deployments
echo ""
print_status "Step 6: Applying new deployments..."
kubectl apply -f "${SCRIPT_DIR}/laravel-service.yaml" -n $NAMESPACE
kubectl apply -f "${SCRIPT_DIR}/app-deployment.yaml" -n $NAMESPACE
kubectl apply -f "${SCRIPT_DIR}/nginx-deployment.yaml" -n $NAMESPACE
kubectl apply -f "${SCRIPT_DIR}/nginx-service.yaml" -n $NAMESPACE
kubectl apply -f "${SCRIPT_DIR}/queue-worker-deployment.yaml" -n $NAMESPACE
kubectl apply -f "${SCRIPT_DIR}/redis-deployment.yaml" -n $NAMESPACE
print_status "Deployments applied"

# Step 7: Wait for PVC to bind
echo ""
print_status "Step 7: Waiting for PVC to bind..."
for i in {1..30}; do
    PVC_STATUS=$(kubectl get pvc app-storage-pvc -n $NAMESPACE -o jsonpath='{.status.phase}' 2>/dev/null || echo "Pending")
    if [ "$PVC_STATUS" == "Bound" ]; then
        print_status "PVC is now Bound!"
        break
    fi
    echo "  Waiting for PVC to bind... ($i/30)"
    sleep 5
done

kubectl get pvc -n $NAMESPACE

# Step 8: Show status
echo ""
print_status "Step 8: Current deployment status..."
sleep 10
echo ""
echo "=== Pods Status ==="
kubectl get pods -n $NAMESPACE
echo ""
echo "=== PVC Status ==="
kubectl get pvc -n $NAMESPACE
echo ""
echo "=== Services Status ==="
kubectl get svc -n $NAMESPACE
echo ""

print_status "Fixes applied successfully!"
echo ""
echo "========================================"
echo "  Next Steps"
echo "========================================"
echo "1. Wait for pods to be Running (check with: kubectl get pods -n $NAMESPACE)"
echo "2. Check logs: kubectl logs deployment/laravel-app -n $NAMESPACE"
echo "3. Run migration: kubectl exec -it deployment/laravel-app -n $NAMESPACE -- php artisan migrate --force"
echo "4. Resume scheduler when ready: kubectl patch cronjob laravel-scheduler -n $NAMESPACE -p '{\"spec\": {\"suspend\": false}}'"
echo ""