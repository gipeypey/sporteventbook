# SportEventBook Production Cleanup Script (PowerShell)
# Script untuk menghapus deployment dari Nutanix Kubernetes Platform

$ErrorActionPreference = "Stop"

# Configuration
$NAMESPACE = "sporteventbook"

# Parameters
[CmdletBinding()]
param(
    [switch]$Full,
    [switch]$NamespaceOnly,
    [switch]$DryRun,
    [switch]$Confirm
)

# Helper Functions
function Print-Status {
    param([string]$Message)
    Write-Host "[INFO] $Message" -ForegroundColor Green
}

function Print-Warning {
    param([string]$Message)
    Write-Host "[WARN] $Message" -ForegroundColor Yellow
}

function Print-Error {
    param([string]$Message)
    Write-Host "[ERROR] $Message" -ForegroundColor Red
}

function Print-Step {
    param([string]$Message)
    Write-Host "[STEP] $Message" -ForegroundColor Blue
}

function Print-Success {
    param([string]$Message)
    Write-Host "[SUCCESS] $Message" -ForegroundColor Cyan
}

function Print-Info {
    param([string]$Message)
    Write-Host "[INFO] $Message" -ForegroundColor White
}

function Confirm-Action {
    param([string]$Action)
    if ($Confirm) {
        $confirmation = Read-Host "Are you sure you want to $Action? (y/n)"
        if ($confirmation -ne 'y') {
            Print-Status "Operation cancelled by user"
            return $false
        }
    }
    return $true
}

function Invoke-KubectlDelete {
    param(
        [string]$ResourceType,
        [string]$ResourceName,
        [string]$Namespace,
        [switch]$IgnoreNotFound
    )
    
    $ignoreFlag = if ($IgnoreNotFound) { " --ignore-not-found" } else { "" }
    
    if ($DryRun) {
        Write-Host "  [DRY RUN] Would delete: $ResourceType/$ResourceName" -ForegroundColor Gray
        return
    }
    
    try {
        kubectl delete $ResourceType $ResourceName -n $Namespace --ignore-not-found 2>&1 | Out-Null
        Write-Host "  Deleted: $ResourceType/$ResourceName" -ForegroundColor DarkGray
    } catch {
        Print-Warning "Failed to delete $ResourceType/$ResourceName : $_"
    }
}

# Main Script
Write-Host ""
Write-Host "========================================"
Write-Host "  SportEventBook Cleanup Script"
Write-Host "  Namespace: $NAMESPACE"
Write-Host "========================================"
Write-Host ""

# Check prerequisites
Print-Status "Checking prerequisites..."

try {
    $null = Get-Command kubectl -ErrorAction Stop
    Print-Status "kubectl found"
} catch {
    Print-Error "kubectl is not installed. Please install kubectl first."
    exit 1
}

# Check cluster connection
Print-Status "Checking Kubernetes cluster connection..."
try {
    $currentContext = kubectl config current-context 2>&1
    Print-Status "Connected to cluster: $currentContext"
} catch {
    Print-Error "Cannot connect to Kubernetes cluster. Please check your kubeconfig."
    exit 1
}

# Check if namespace exists
Print-Status "Checking if namespace '$NAMESPACE' exists..."
$namespaceExists = kubectl get namespace $NAMESPACE --ignore-not-found 2>&1
if ([string]::IsNullOrWhiteSpace($namespaceExists)) {
    Print-Warning "Namespace '$NAMESPACE' does not exist. Nothing to cleanup."
    exit 0
}

# List resources that will be deleted
Write-Host ""
Print-Info "Resources in namespace '$NAMESPACE':"
kubectl get all -n $NAMESPACE 2>&1 | ForEach-Object { Write-Host "  $_" }

Write-Host ""
kubectl get configmap,secret,ingress,pvc -n $NAMESPACE 2>&1 | ForEach-Object { Write-Host "  $_" }

# Handle NamespaceOnly mode
if ($NamespaceOnly) {
    Write-Host ""
    Print-Step "NamespaceOnly mode: Deleting entire namespace..."
    
    if (-not (Confirm-Action "delete the entire namespace '$NAMESPACE'")) {
        exit 0
    }
    
    if ($DryRun) {
        Write-Host "  [DRY RUN] Would delete namespace: $NAMESPACE" -ForegroundColor Gray
    } else {
        try {
            kubectl delete namespace $NAMESPACE
            Print-Success "Namespace '$NAMESPACE' deleted successfully"
        } catch {
            Print-Error "Failed to delete namespace: $_"
            exit 1
        }
    }
    
    Write-Host ""
    Print-Success "Cleanup completed!"
    exit 0
}

# Full cleanup mode
if ($Full) {
    Write-Host ""
    Print-Step "Full cleanup mode: Deleting all resources..."
    
    if (-not (Confirm-Action "delete all resources in namespace '$NAMESPACE'")) {
        exit 0
    }
    
    # Step 1: Delete Deployments
    Write-Host ""
    Print-Step "Step 1: Deleting Deployments..."
    Invoke-KubectlDelete "deployment" "laravel-app" $NAMESPACE
    Invoke-KubectlDelete "deployment" "nginx" $NAMESPACE
    Invoke-KubectlDelete "deployment" "queue-worker" $NAMESPACE
    Invoke-KubectlDelete "deployment" "redis" $NAMESPACE
    
    # Step 2: Delete StatefulSets
    Write-Host ""
    Print-Step "Step 2: Deleting StatefulSets..."
    Invoke-KubectlDelete "statefulset" "mysql" $NAMESPACE
    
    # Step 3: Delete DaemonSets
    Write-Host ""
    Print-Step "Step 3: Deleting DaemonSets..."
    Invoke-KubectlDelete "daemonset" "" $NAMESPACE -IgnoreNotFound
    
    # Step 4: Delete Services
    Write-Host ""
    Print-Step "Step 4: Deleting Services..."
    Invoke-KubectlDelete "service" "nginx-service" $NAMESPACE
    Invoke-KubectlDelete "service" "redis" $NAMESPACE
    Invoke-KubectlDelete "service" "mysql" $NAMESPACE
    
    # Step 5: Delete Ingress
    Write-Host ""
    Print-Step "Step 5: Deleting Ingress..."
    Invoke-KubectlDelete "ingress" "sporteventbook-ingress" $NAMESPACE
    
    # Step 6: Delete Jobs
    Write-Host ""
    Print-Step "Step 6: Deleting Jobs..."
    Invoke-KubectlDelete "job" "migrate" $NAMESPACE -IgnoreNotFound
    
    # Step 7: Delete CronJobs
    Write-Host ""
    Print-Step "Step 7: Deleting CronJobs..."
    Invoke-KubectlDelete "cronjob" "scheduler" $NAMESPACE -IgnoreNotFound
    
    # Step 8: Delete ConfigMaps
    Write-Host ""
    Print-Step "Step 8: Deleting ConfigMaps..."
    Invoke-KubectlDelete "configmap" "app-configmap" $NAMESPACE -IgnoreNotFound
    
    # Step 9: Delete Secrets
    Write-Host ""
    Print-Step "Step 9: Deleting Secrets..."
    Invoke-KubectlDelete "secret" "app-secret" $NAMESPACE -IgnoreNotFound
    Invoke-KubectlDelete "secret" "harbor-secret" $NAMESPACE -IgnoreNotFound
    
    # Step 10: Delete PVCs (Persistent Volume Claims)
    Write-Host ""
    Print-Step "Step 10: Deleting Persistent Volume Claims..."
    $pvcs = kubectl get pvc -n $NAMESPACE -o jsonpath='{.items[*].metadata.name}' 2>&1
    if ($pvcs) {
        foreach ($pvc in $pvcs.Split(' ')) {
            if (-not [string]::IsNullOrWhiteSpace($pvc)) {
                Invoke-KubectlDelete "pvc" $pvc $NAMESPACE
            }
        }
    }
    
    # Step 11: Delete Namespace (optional)
    Write-Host ""
    $deleteNamespace = Read-Host "Do you want to delete the namespace '$NAMESPACE' as well? (y/n)"
    if ($deleteNamespace -eq 'y') {
        Print-Step "Deleting namespace..."
        if ($DryRun) {
            Write-Host "  [DRY RUN] Would delete namespace: $NAMESPACE" -ForegroundColor Gray
        } else {
            try {
                kubectl delete namespace $NAMESPACE
                Print-Success "Namespace '$NAMESPACE' deleted"
            } catch {
                Print-Warning "Failed to delete namespace: $_"
            }
        }
    }
    
    Write-Host ""
    Print-Success "Full cleanup completed!"
    exit 0
}

# Interactive mode (default)
Write-Host ""
Write-Host "========================================"
Write-Host "  Interactive Cleanup Mode"
Write-Host "========================================"
Write-Host ""
Write-Host "Available options:"
Write-Host "  1. Delete specific resource"
Write-Host "  2. Delete all deployments"
Write-Host "  3. Delete all services"
Write-Host "  4. Delete all pods"
Write-Host "  5. Delete all configmaps"
Write-Host "  6. Delete all secrets"
Write-Host "  7. Delete all ingress"
Write-Host "  8. Delete all jobs/cronjobs"
Write-Host "  9. Delete all PVCs"
Write-Host "  10. Delete namespace (WARNING: deletes everything)"
Write-Host "  11. Full cleanup (delete all resources)"
Write-Host "  0. Exit"
Write-Host ""

$choice = Read-Host "Select option (0-11)"

switch ($choice) {
    "1" {
        Write-Host ""
        Write-Host "Resource types: deployment, service, pod, configmap, secret, ingress, job, cronjob, pvc, statefulset"
        $resourceType = Read-Host "Enter resource type"
        $resourceName = Read-Host "Enter resource name (or '*' for all)"
        
        if (-not (Confirm-Action "delete $resourceType/$resourceName")) {
            exit 0
        }
        
        if ($resourceName -eq '*') {
            $resources = kubectl get $resourceType -n $NAMESPACE -o jsonpath='{.items[*].metadata.name}' 2>&1
            foreach ($resource in $resources.Split(' ')) {
                if (-not [string]::IsNullOrWhiteSpace($resource)) {
                    Invoke-KubectlDelete $resourceType $resource $NAMESPACE
                }
            }
        } else {
            Invoke-KubectlDelete $resourceType $resourceName $NAMESPACE
        }
        Print-Success "Resource(s) deleted"
    }
    
    "2" {
        if (-not (Confirm-Action "delete all deployments")) {
            exit 0
        }
        Print-Step "Deleting all deployments..."
        kubectl delete deployment --all -n $NAMESPACE
        Print-Success "All deployments deleted"
    }
    
    "3" {
        if (-not (Confirm-Action "delete all services")) {
            exit 0
        }
        Print-Step "Deleting all services..."
        kubectl delete service --all -n $NAMESPACE
        Print-Success "All services deleted"
    }
    
    "4" {
        if (-not (Confirm-Action "delete all pods")) {
            exit 0
        }
        Print-Step "Deleting all pods..."
        kubectl delete pod --all -n $NAMESPACE
        Print-Success "All pods deleted"
    }
    
    "5" {
        if (-not (Confirm-Action "delete all configmaps")) {
            exit 0
        }
        Print-Step "Deleting all configmaps..."
        kubectl delete configmap --all -n $NAMESPACE
        Print-Success "All configmaps deleted"
    }
    
    "6" {
        if (-not (Confirm-Action "delete all secrets")) {
            exit 0
        }
        Print-Step "Deleting all secrets..."
        kubectl delete secret --all -n $NAMESPACE
        Print-Success "All secrets deleted"
    }
    
    "7" {
        if (-not (Confirm-Action "delete all ingress")) {
            exit 0
        }
        Print-Step "Deleting all ingress..."
        kubectl delete ingress --all -n $NAMESPACE
        Print-Success "All ingress deleted"
    }
    
    "8" {
        if (-not (Confirm-Action "delete all jobs and cronjobs")) {
            exit 0
        }
        Print-Step "Deleting all jobs and cronjobs..."
        kubectl delete job --all -n $NAMESPACE --ignore-not-found
        kubectl delete cronjob --all -n $NAMESPACE --ignore-not-found
        Print-Success "All jobs and cronjobs deleted"
    }
    
    "9" {
        if (-not (Confirm-Action "delete all PVCs")) {
            exit 0
        }
        Print-Step "Deleting all PVCs..."
        kubectl delete pvc --all -n $NAMESPACE --ignore-not-found
        Print-Success "All PVCs deleted"
    }
    
    "10" {
        if (-not (Confirm-Action "delete the entire namespace '$NAMESPACE'")) {
            exit 0
        }
        Print-Step "Deleting namespace '$NAMESPACE'..."
        kubectl delete namespace $NAMESPACE
        Print-Success "Namespace deleted"
    }
    
    "11" {
        Print-Info "Running full cleanup..."
        & $PSCommandPath -Full -Confirm:$Confirm -DryRun:$DryRun
    }
    
    "0" {
        Print-Status "Cleanup cancelled"
        exit 0
    }
    
    default {
        Print-Error "Invalid option"
        exit 1
    }
}

Write-Host ""
Print-Status "Current resources in namespace:"
kubectl get all -n $NAMESPACE 2>&1 | ForEach-Object { Write-Host "  $_" }
Write-Host ""
Print-Success "Cleanup operation completed!"