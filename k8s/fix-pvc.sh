#!/bin/bash

# SportEventBook PVC Fix Script
# This script fixes the PVC issue by deleting old PVC and applying new one

set -e

# Configuration
NAMESPACE="sporteventbook"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "========================================"
echo "  SportEventBook PVC Fix Script"
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

# Step 1: Scale down deployments that use PVC
echo ""
print_status "Step 1: Scaling down deployments that use PVC..."
kubectl scale deployment laravel-app --replicas=0 -n $NAMESPACE
kubectl scale deployment queue-worker --replicas=0 -n $NAMESPACE
print_status "Deployments scaled down"

# Step 2: Delete old PVC
echo ""
print_status "Step 2: Deleting old PVC..."
kubectl delete pvc app-storage-pvc -n $NAMESPACE --ignore-not-found=true
print_status "Old PVC deleted"

# Step 3: Apply new PVC with correct storage class
echo ""
print_status "Step 3: Applying new PVC with nutanix-volume storage class..."
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
kubectl apply -f "${SCRIPT_DIR}/app-storage-pvc.yaml" -n $NAMESPACE
print_status "New PVC applied"

# Step 4: Wait for PVC to bind
echo ""
print_status "Step 4: Waiting for PVC to bind..."
kubectl wait --for=jsonpath='{.status.phase}'=Bound pvc/app-storage-pvc -n $NAMESPACE --timeout=120s || {
    print_warning "PVC did not bind within 120 seconds"
    kubectl get pvc -n $NAMESPACE
}

# Step 5: Force delete terminating pods
echo ""
print_status "Step 5: Force deleting terminating pods..."

# Get all terminating pods and force delete them
for pod in $(kubectl get pods -n $NAMESPACE --field-selector=status.phase=Terminating --no-headers -o custom-columns=":metadata.name" 2>/dev/null); do
    print_status "Force deleting pod: $pod"
    kubectl delete pod $pod -n $NAMESPACE --force --grace-period=0 2>/dev/null || true
done

print_status "Terminating pods cleanup completed"

# Step 6: Scale up deployments
echo ""
print_status "Step 6: Scaling up deployments..."
kubectl scale deployment laravel-app --replicas=2 -n $NAMESPACE
kubectl scale deployment queue-worker --replicas=1 -n $NAMESPACE
print_status "Deployments scaled up"

# Step 7: Wait and show status
echo ""
print_status "Step 7: Waiting for pods to stabilize..."
sleep 30

echo ""
echo "=== Current Status ==="
echo ""
echo "PVC Status:"
kubectl get pvc -n $NAMESPACE
echo ""
echo "Pods Status:"
kubectl get pods -n $NAMESPACE --sort-by='.status.startTime' | tail -15
echo ""
echo "=== Next Steps ==="
echo "1. Wait for laravel-app pods to be Running"
echo "2. Check nginx pods - they should recover once laravel-app is ready"
echo "3. Resume scheduler: kubectl patch cronjob laravel-scheduler -n $NAMESPACE -p '{\"spec\": {\"suspend\": false}}'"
echo ""