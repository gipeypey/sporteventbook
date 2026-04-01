#!/bin/bash

# SportEventBook Emergency Fix Script
# This script performs emergency cleanup and fix

set -e

# Configuration
NAMESPACE="sporteventbook"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "========================================"
echo "  SportEventBook EMERGENCY FIX"
echo "========================================"
echo ""

print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Step 1: Delete CronJob completely
echo ""
print_status "Step 1: Deleting scheduler CronJob completely..."
kubectl delete cronjob laravel-scheduler -n $NAMESPACE --ignore-not-found=true
print_status "CronJob deleted"

# Step 2: Delete ALL scheduler pods immediately
echo ""
print_status "Step 2: Force deleting ALL scheduler pods..."
kubectl delete pod -n $NAMESPACE -l app=laravel-scheduler --force --grace-period=0 2>/dev/null || print_status "No scheduler pods found"
print_status "Scheduler pods deleted"

# Step 3: Delete PVC
echo ""
print_status "Step 3: Deleting PVC..."
kubectl delete pvc app-storage-pvc -n $NAMESPACE --force --grace-period=0 2>/dev/null || true
sleep 2
print_status "PVC deleted"

# Step 4: Delete ALL pods
echo ""
print_status "Step 4: Force deleting ALL pods..."
kubectl delete pod -n $NAMESPACE --all --force --grace-period=0 2>/dev/null || true
sleep 3
print_status "All pods deleted"

# Step 5: Delete deployments
echo ""
print_status "Step 5: Deleting deployments..."
kubectl delete deployment -n $NAMESPACE --all 2>/dev/null || true
sleep 2
print_status "Deployments deleted"

# Step 6: Apply PVC first (without any deployment running)
echo ""
print_status "Step 6: Applying PVC..."
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
kubectl apply -f "${SCRIPT_DIR}/app-storage-pvc.yaml" -n $NAMESPACE
print_status "PVC applied"

# Step 7: Wait for PVC to bind (with no pods competing)
echo ""
print_status "Step 7: Waiting for PVC to bind (max 60 seconds)..."
for i in {1..12}; do
    PVC_STATUS=$(kubectl get pvc app-storage-pvc -n $NAMESPACE -o jsonpath='{.status.phase}' 2>/dev/null || echo "Pending")
    if [ "$PVC_STATUS" == "Bound" ]; then
        print_status "PVC is now Bound!"
        kubectl get pvc -n $NAMESPACE
        break
    fi
    echo "  Waiting... ($i/12)"
    sleep 5
done

# Step 8: Apply deployments one by one
echo ""
print_status "Step 8: Applying laravel-service..."
kubectl apply -f "${SCRIPT_DIR}/laravel-service.yaml" -n $NAMESPACE

print_status "Step 9: Applying app-deployment..."
kubectl apply -f "${SCRIPT_DIR}/app-deployment.yaml" -n $NAMESPACE

print_status "Step 10: Applying redis..."
kubectl apply -f "${SCRIPT_DIR}/redis-deployment.yaml" -n $NAMESPACE

print_status "Step 11: Applying nginx..."
kubectl apply -f "${SCRIPT_DIR}/nginx-deployment.yaml" -n $NAMESPACE
kubectl apply -f "${SCRIPT_DIR}/nginx-service.yaml" -n $NAMESPACE

print_status "Step 12: Applying queue-worker..."
kubectl apply -f "${SCRIPT_DIR}/queue-worker-deployment.yaml" -n $NAMESPACE

# Step 13: Wait and show status
echo ""
print_status "Step 13: Waiting for pods to start..."
sleep 20

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

print_status "Emergency fix completed!"
echo ""
echo "If PVC is still Pending, run:"
echo "  kubectl describe pvc app-storage-pvc -n sporteventbook"
echo ""