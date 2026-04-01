#!/bin/bash

# SportEventBook Final Fix Script
# This script fixes the PVC StorageType issue

set -e

# Configuration
NAMESPACE="sporteventbook"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "========================================"
echo "  SportEventBook FINAL FIX"
echo "  (PVC StorageType Fix)"
echo "========================================"
echo ""

print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check kubectl
if ! command -v kubectl &> /dev/null; then
    print_error "kubectl is not installed."
    exit 1
fi

print_status "Connected to cluster: $(kubectl config current-context)"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Step 1: Delete old PVC
echo ""
print_status "Step 1: Deleting old PVC (this may take a moment)..."
kubectl delete pvc app-storage-pvc -n $NAMESPACE --ignore-not-found=true --force --grace-period=0 2>/dev/null || print_warning "PVC not found"
sleep 3
print_status "Old PVC deleted"

# Step 2: Delete laravel-app pod to release PVC reference
echo ""
print_status "Step 2: Deleting laravel-app pods..."
kubectl delete pod -n $NAMESPACE -l app=laravel --force --grace-period=0 2>/dev/null || true
sleep 2
print_status "Laravel pods deleted"

# Step 3: Apply new PVC with StorageType annotation
echo ""
print_status "Step 3: Applying new PVC with StorageType annotation..."
kubectl apply -f "${SCRIPT_DIR}/app-storage-pvc.yaml" -n $NAMESPACE
print_status "New PVC applied"

# Step 4: Wait for PVC to bind
echo ""
print_status "Step 4: Waiting for PVC to bind (max 120 seconds)..."
for i in {1..24}; do
    PVC_STATUS=$(kubectl get pvc app-storage-pvc -n $NAMESPACE -o jsonpath='{.status.phase}' 2>/dev/null || echo "Pending")
    if [ "$PVC_STATUS" == "Bound" ]; then
        print_status "PVC is now Bound!"
        kubectl get pvc -n $NAMESPACE
        break
    fi
    echo "  Waiting... ($i/24)"
    sleep 5
done

# Check final PVC status
echo ""
print_status "Final PVC Status:"
kubectl get pvc -n $NAMESPACE
kubectl describe pvc app-storage-pvc -n $NAMESPACE | grep -A 5 "Events" || true

# Step 5: Restart laravel-app deployment
echo ""
print_status "Step 5: Restarting laravel-app deployment..."
kubectl rollout restart deployment/laravel-app -n $NAMESPACE 2>/dev/null || {
    print_warning "Deployment not found, applying from manifest..."
    kubectl apply -f "${SCRIPT_DIR}/app-deployment.yaml" -n $NAMESPACE
}
print_status "Laravel app restart initiated"

# Step 6: Wait and show status
echo ""
print_status "Step 6: Waiting for pods to stabilize..."
sleep 15

echo ""
echo "========================================"
echo "  CURRENT STATUS"
echo "========================================"
echo ""
echo "=== PVC ==="
kubectl get pvc -n $NAMESPACE
echo ""
echo "=== Pods ==="
kubectl get pods -n $NAMESPACE
echo ""
echo "=== Deployments ==="
kubectl get deployments -n $NAMESPACE
echo ""

# Check for any errors
echo "========================================"
echo "  DIAGNOSTICS"
echo "========================================"
echo ""

# Check if any pods are still pending
PENDING_PODS=$(kubectl get pods -n $NAMESPACE --field-selector=status.phase=Pending --no-headers 2>/dev/null | wc -l)
if [ "$PENDING_PODS" -gt 0 ]; then
    print_error "Found $PENDING_PODS pending pods"
    echo ""
    echo "To debug, run:"
    echo "  kubectl describe pod <pod-name> -n $NAMESPACE | grep -A 10 Events"
    echo ""
fi

# Check for CrashLoopBackOff
CRASH_PODS=$(kubectl get pods -n $NAMESPACE --no-headers 2>/dev/null | grep -c "CrashLoopBackOff" || echo "0")
if [ "$CRASH_PODS" -gt 0 ]; then
    print_error "Found $CRASH_PODS pods in CrashLoopBackOff"
    echo ""
    echo "To debug, run:"
    echo "  kubectl logs <pod-name> -n $NAMESPACE --previous"
    echo ""
fi

print_status "Fix script completed!"
echo ""