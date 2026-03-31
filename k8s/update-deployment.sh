#!/bin/bash

# SportEventBook Kubernetes Update Deployment Script
# Script untuk update deployment dengan code terbaru

set -e

# Configuration
NAMESPACE="sporteventbook"
HARBOR_URL="harbor.your-domain.com"
HARBOR_USERNAME="admin"
GIT_BRANCH="main"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Get version from argument
VERSION=${1:-""}

echo ""
echo "========================================"
echo "  SportEventBook Update Deployment"
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

print_step() {
    echo -e "${BLUE}[STEP]${NC} $1"
}

# Show usage
if [ "$1" == "-h" ] || [ "$1" == "--help" ]; then
    echo "Usage: ./update-deployment.sh [VERSION]"
    echo ""
    echo "Examples:"
    echo "  ./update-deployment.sh v1.1.0    # Update dengan version tag spesifik"
    echo "  ./update-deployment.sh           # Auto-generate version dari git commit"
    echo "  ./update-deployment.sh hotfix    # Update dengan nama hotfix"
    exit 0
fi

# Check prerequisites
print_status "Checking prerequisites..."

if ! command -v kubectl &> /dev/null; then
    print_error "kubectl is not installed."
    exit 1
fi

if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed."
    exit 1
fi

if ! command -v git &> /dev/null; then
    print_error "Git is not installed."
    exit 1
fi

print_status "Prerequisites OK"

# Step 1: Pull latest code
echo ""
print_step "Step 1: Pulling latest code from Git..."
git fetch origin $GIT_BRANCH
git checkout $GIT_BRANCH
git pull origin $GIT_BRANCH
print_status "Code updated to latest commit: $(git rev-parse --short HEAD)"

# Step 2: Generate version tag if not provided
if [ -z "$VERSION" ]; then
    # Auto-generate version from git
    COMMIT_COUNT=$(git rev-list --count HEAD)
    SHORT_HASH=$(git rev-parse --short HEAD)
    VERSION="v1.0.${COMMIT_COUNT}-${SHORT_HASH}"
    print_status "Auto-generated version: ${VERSION}"
else
    print_status "Using provided version: ${VERSION}"
fi

# Step 3: Get Harbor password
echo ""
print_status "Harbor Registry Login..."
read -sp "Enter Harbor password: " HARBOR_PASSWORD
echo ""

docker login ${HARBOR_URL} -u ${HARBOR_USERNAME} -p "${HARBOR_PASSWORD}" 2>/dev/null || {
    print_error "Failed to login to Harbor. Please check credentials."
    exit 1
}

# Step 4: Build Docker image
echo ""
print_step "Step 2: Building Docker image..."
print_status "Image: ${HARBOR_URL}/sporteventbook/app:${VERSION}"

docker build -t ${HARBOR_URL}/sporteventbook/app:${VERSION} .

if [ $? -ne 0 ]; then
    print_error "Failed to build Docker image"
    exit 1
fi

print_status "Docker image built successfully"

# Step 5: Push to Harbor
echo ""
print_step "Step 3: Pushing image to Harbor..."
docker push ${HARBOR_URL}/sporteventbook/app:${VERSION}

if [ $? -ne 0 ]; then
    print_error "Failed to push image to Harbor"
    exit 1
fi

print_status "Image pushed to Harbor"

# Step 6: Update Kubernetes deployment
echo ""
print_step "Step 4: Updating Kubernetes deployment..."

kubectl set image deployment/laravel-app \
    laravel=${HARBOR_URL}/sporteventbook/app:${VERSION} \
    -n ${NAMESPACE}

print_status "Deployment image updated"

# Step 7: Monitor rollout
echo ""
print_step "Step 5: Monitoring rollout status..."
print_status "This may take a few minutes..."

kubectl rollout status deployment/laravel-app -n ${NAMESPACE} --timeout=300s

if [ $? -ne 0 ]; then
    print_error "Rollout failed or timed out"
    echo ""
    print_warning "Check pods status:"
    kubectl get pods -n ${NAMESPACE}
    echo ""
    print_warning "Check deployment status:"
    kubectl describe deployment laravel-app -n ${NAMESPACE}
    exit 1
fi

print_status "Rollout completed successfully"

# Step 8: Verify deployment
echo ""
print_step "Step 6: Verifying deployment..."

echo ""
echo "Pod Status:"
kubectl get pods -n ${NAMESPACE} -l app=laravel

echo ""
echo "Deployment Info:"
kubectl get deployment laravel-app -n ${NAMESPACE} -o wide

echo ""
echo "Image Version:"
kubectl get deployment laravel-app -n ${NAMESPACE} \
    -o jsonpath='{.spec.template.spec.containers[0].image}'
echo ""

# Step 9: Check if migration is needed
echo ""
print_status "Checking for new migrations..."

MIGRATION_NEEDED=false
read -p "Run database migrations? (y/n): " RUN_MIGRATION
if [ "$RUN_MIGRATION" == "y" ] || [ "$RUN_MIGRATION" == "Y" ]; then
    MIGRATION_NEEDED=true
fi

if [ "$MIGRATION_NEEDED" == true ]; then
    echo ""
    print_step "Running database migrations..."
    
    # Update and run migration job
    sed "s|image: .*|image: ${HARBOR_URL}/sporteventbook/app:${VERSION}|" migration-job.yaml | \
        kubectl apply -f - -n ${NAMESPACE}
    
    print_status "Waiting for migration to complete..."
    kubectl wait --for=condition=complete job/migrate -n ${NAMESPACE} --timeout=300s || \
        print_warning "Migration may still be running. Check with: kubectl get jobs -n ${NAMESPACE}"
    
    print_status "Migration job submitted"
fi

# Summary
echo ""
echo "========================================"
echo "  Update Summary"
echo "========================================"
echo "  Version:     ${VERSION}"
echo "  Git Commit:  $(git rev-parse --short HEAD)"
echo "  Git Branch:  $(git branch --show-current)"
echo "  Namespace:   ${NAMESPACE}"
echo "  Image:       ${HARBOR_URL}/sporteventbook/app:${VERSION}"
echo "========================================"
echo ""
print_status "Update completed successfully!"
echo ""
echo "Useful commands:"
echo "  kubectl get pods -n ${NAMESPACE}                    # Check pods"
echo "  kubectl logs -f deployment/laravel-app -n ${NAMESPACE}  # View logs"
echo "  kubectl rollout history deployment/laravel-app -n ${NAMESPACE}  # History"
echo ""
echo "To rollback if needed:"
echo "  kubectl rollout undo deployment/laravel-app -n ${NAMESPACE}"
echo ""