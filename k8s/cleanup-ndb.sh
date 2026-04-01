#!/bin/bash

# SportEventBook Kubernetes Cleanup Script (NDB MySQL Version)
# This script cleans up failed pods and jobs from previous deployments

set -e

# Configuration
NAMESPACE="sporteventbook"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "========================================"
echo "  SportEventBook K8s Cleanup Script"
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

# Step 1: Suspend Scheduler CronJob
echo ""
print_status "Step 1: Suspending scheduler CronJob..."
kubectl patch cronjob laravel-scheduler -n $NAMESPACE -p '{"spec": {"suspend": true}}' --dry-run=client -o yaml | kubectl apply -f -
print_status "Scheduler CronJob suspended"

# Step 2: Delete Failed Migration Jobs
echo ""
print_status "Step 2: Cleaning up old migration jobs..."
kubectl delete job -n $NAMESPACE --all --wait=false 2>/dev/null || print_warning "No jobs to delete"
print_status "Migration jobs cleanup completed"

# Step 3: Delete Failed Scheduler Pods
echo ""
print_status "Step 3: Cleaning up failed scheduler pods..."
kubectl delete pod -n $NAMESPACE -l app=laravel-scheduler --force --grace-period=0 2>/dev/null || print_warning "No scheduler pods to delete"
print_status "Scheduler pods cleanup completed"

# Step 4: Delete Failed Queue Worker Pods (to trigger recreation)
echo ""
print_status "Step 4: Restarting queue worker deployment..."
kubectl rollout restart deployment/queue-worker -n $NAMESPACE 2>/dev/null || print_warning "Queue worker deployment not found"
print_status "Queue worker restart initiated"

# Step 5: Restart Laravel App Deployment
echo ""
print_status "Step 5: Restarting Laravel app deployment..."
kubectl rollout restart deployment/laravel-app -n $NAMESPACE 2>/dev/null || print_warning "Laravel app deployment not found"
print_status "Laravel app restart initiated"

# Step 6: Restart Nginx Deployment
echo ""
print_status "Step 6: Restarting Nginx deployment..."
kubectl rollout restart deployment/nginx -n $NAMESPACE 2>/dev/null || print_warning "Nginx deployment not found"
print_status "Nginx restart initiated"

# Step 7: Wait for Deployments
echo ""
print_status "Step 7: Waiting for deployments to stabilize..."
sleep 10

# Step 8: Show Status
echo ""
print_status "Step 8: Current deployment status..."
echo ""
echo "=== Pods Status ==="
kubectl get pods -n $NAMESPACE --sort-by='.status.startTime' | tail -20
echo ""
echo "=== Jobs Status ==="
kubectl get jobs -n $NAMESPACE 2>/dev/null || echo "No jobs found"
echo ""
echo "=== CronJobs Status ==="
kubectl get cronjobs -n $NAMESPACE
echo ""
echo "=== Services Status ==="
kubectl get svc -n $NAMESPACE
echo ""

print_status "Cleanup completed!"
echo ""
echo "Next steps:"
echo "  1. Check if image exists in Harbor: docker pull registry.bercalab.my.id/sporteventbook/app:latest"
echo "  2. Verify Harbor credentials: kubectl get secret harbor-secret -n $NAMESPACE -o yaml"
echo "  3. Check PVC status: kubectl get pvc -n $NAMESPACE"
echo "  4. Resume scheduler when ready: kubectl patch cronjob laravel-scheduler -n $NAMESPACE -p '{\"spec\": {\"suspend\": false}}'"
echo ""