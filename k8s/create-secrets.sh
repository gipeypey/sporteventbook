#!/bin/bash

# SportEventBook - Create Kubernetes Secrets
# Run this after initial deployment to create secrets

set -e

NAMESPACE="sporteventbook"

echo "========================================"
echo "  Create Kubernetes Secrets"
echo "========================================"
echo ""

# Check if namespace exists
if ! kubectl get namespace $NAMESPACE &> /dev/null; then
    echo "Namespace $NAMESPACE does not exist. Please deploy first!"
    exit 1
fi

# Generate APP_KEY if not exists
APP_KEY=$(php artisan key:generate --show 2>/dev/null || echo "base64:$(openssl rand -base64 32)")

echo "Generating APP_KEY..."
echo "APP_KEY: $APP_KEY"
echo ""

# Prompt for DB password
read -p "Enter DB_PASSWORD: " -s DB_PASSWORD
echo ""

# Prompt for Midtrans keys (optional)
read -p "Enter MIDTRANS_SERVER_KEY (press enter to skip): " -s MIDTRANS_SERVER_KEY
echo ""
read -p "Enter MIDTRANS_CLIENT_KEY (press enter to skip): " -s MIDTRANS_CLIENT_KEY
echo ""

# Create secret using kubectl create
echo ""
echo "Creating app-secret..."

kubectl create secret generic app-secret -n $NAMESPACE \
    --from-literal=APP_KEY="$APP_KEY" \
    --from-literal=DB_PASSWORD="$DB_PASSWORD" \
    --from-literal=MIDTRANS_SERVER_KEY="$MIDTRANS_SERVER_KEY" \
    --from-literal=MIDTRANS_CLIENT_KEY="$MIDTRANS_CLIENT_KEY" \
    --dry-run=client -o yaml | kubectl apply -f -

echo ""
echo "Secret created successfully!"
echo ""

# Verify
echo "Verifying secret..."
kubectl get secret app-secret -n $NAMESPACE

echo ""
echo "========================================"
echo "  Next Steps"
echo "========================================"
echo "1. Restart pods to pick up new secrets:"
echo "   kubectl rollout restart deployment -n $NAMESPACE"
echo ""
echo "2. Check pods are running:"
echo "   kubectl get pods -n $NAMESPACE"
echo ""