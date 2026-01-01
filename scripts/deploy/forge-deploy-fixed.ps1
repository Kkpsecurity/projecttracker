# Laravel Forge Deployment Script for HB837 Training Server
# Author: Fixed version for Task 08
# Date: July 15, 2025
# PowerShell-compatible syntax with proper error handling

param(
    [Parameter(Mandatory=$false)]
    [string]$ForgeServer = "projecttracker.hb837training.com",
    
    [Parameter(Mandatory=$false)]
    [string]$ProjectPath = "/home/projecttracker/projecttracker.hb837training.com",
    
    [Parameter(Mandatory=$false)]
    [string]$ForgeUser = "projecttracker",
    
    [Parameter(Mandatory=$false)]
    [string]$SSHKey = "",
    
    [switch]$UpdateDatabase,
    [switch]$RunMigrations,
    [switch]$RunSeeders,
    [switch]$Force,
    [switch]$DryRun,
    [switch]$BackupOnly
)

# HB837 Training Server Configuration
# Server ID: 783941
# Site ID: 2763978
# Public IP: 54.158.31.216
# Private IP: 10.0.1.86
# Region: Virginia (Ubuntu 22.04)

Write-Host "Laravel Forge Deployment Script - HB837 Training Server" -ForegroundColor Green
Write-Host "=======================================================" -ForegroundColor Green

# Error handling setup
$ErrorActionPreference = "Stop"

# Functions
function Write-Log {
    param(
        [string]$Message,
        [string]$Level = "INFO"
    )
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $color = switch ($Level) {
        "ERROR" { "Red" }
        "WARNING" { "Yellow" }
        "SUCCESS" { "Green" }
        default { "White" }
    }
    Write-Host "[$timestamp] [$Level] $Message" -ForegroundColor $color
}

function Test-SSHConnection {
    param(
        [string]$Server,
        [string]$User,
        [string]$KeyPath = ""
    )
    
    Write-Log "Testing SSH connection to $User@$Server" "INFO"
    
    try {
        $sshArgs = @()
        if ($KeyPath -and (Test-Path $KeyPath)) {
            $sshArgs += @("-i", $KeyPath)
        }
        $sshArgs += @("-o", "ConnectTimeout=10", "-o", "StrictHostKeyChecking=no")
        $sshArgs += "$User@$Server"
        $sshArgs += "echo 'SSH connection successful'"
        
        $result = & ssh @sshArgs 2>&1
        
        if ($LASTEXITCODE -eq 0) {
            Write-Log "SSH connection successful" "SUCCESS"
            return $true
        } else {
            Write-Log "SSH connection failed: $result" "ERROR"
            return $false
        }
    } catch {
        Write-Log "SSH connection error: $($_.Exception.Message)" "ERROR"
        return $false
    }
}

function Invoke-RemoteCommand {
    param(
        [string]$Server,
        [string]$User,
        [string]$Command,
        [string]$KeyPath = "",
        [switch]$DryRun
    )
    
    if ($DryRun) {
        Write-Log "DRY RUN: Would execute: $Command" "INFO"
        return $true
    }
    
    try {
        $sshArgs = @()
        if ($KeyPath -and (Test-Path $KeyPath)) {
            $sshArgs += @("-i", $KeyPath)
        }
        $sshArgs += @("-o", "StrictHostKeyChecking=no")
        $sshArgs += "$User@$Server"
        $sshArgs += $Command
        
        Write-Log "Executing: $Command" "INFO"
        $result = & ssh @sshArgs 2>&1
        
        if ($LASTEXITCODE -eq 0) {
            Write-Log "Command executed successfully" "SUCCESS"
            if ($result) {
                Write-Host $result
            }
            return $true
        } else {
            Write-Log "Command failed: $result" "ERROR"
            return $false
        }
    } catch {
        Write-Log "Command execution error: $($_.Exception.Message)" "ERROR"
        return $false
    }
}

function Create-DatabaseBackup {
    param(
        [string]$Server,
        [string]$User,
        [string]$ProjectPath,
        [string]$KeyPath = "",
        [switch]$DryRun
    )
    
    $timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
    $backupName = "projecttracker_backup_$timestamp.sql"
    $backupPath = "$ProjectPath/storage/backups/$backupName"
    
    Write-Log "Creating database backup: $backupName" "INFO"
    
    # Create backup directory
    $createDirCmd = "mkdir -p $ProjectPath/storage/backups"
    if (-not (Invoke-RemoteCommand -Server $Server -User $User -Command $createDirCmd -KeyPath $KeyPath -DryRun:$DryRun)) {
        return $false
    }
    
    # Create database backup
    $backupCmd = "cd $ProjectPath; php artisan db:backup --filename=$backupName"
    if (-not (Invoke-RemoteCommand -Server $Server -User $User -Command $backupCmd -KeyPath $KeyPath -DryRun:$DryRun)) {
        Write-Log "Database backup failed, trying alternative method" "WARNING"
        
        # Alternative backup using sqlite3 (if using SQLite)
        $altBackupCmd = "cd $ProjectPath; cp database/database.sqlite storage/backups/database_backup_$timestamp.sqlite"
        if (-not (Invoke-RemoteCommand -Server $Server -User $User -Command $altBackupCmd -KeyPath $KeyPath -DryRun:$DryRun)) {
            Write-Log "Alternative backup also failed" "ERROR"
            return $false
        }
    }
    
    Write-Log "Database backup created successfully" "SUCCESS"
    return $true
}

function Deploy-Application {
    param(
        [string]$Server,
        [string]$User,
        [string]$ProjectPath,
        [string]$KeyPath = "",
        [switch]$UpdateDatabase,
        [switch]$RunMigrations,
        [switch]$RunSeeders,
        [switch]$DryRun
    )
    
    Write-Log "Starting application deployment" "INFO"
    
    # Pull latest code
    $gitPullCmd = "cd $ProjectPath; git pull origin main"
    if (-not (Invoke-RemoteCommand -Server $Server -User $User -Command $gitPullCmd -KeyPath $KeyPath -DryRun:$DryRun)) {
        Write-Log "Git pull failed" "ERROR"
        return $false
    }
    
    # Install/update dependencies
    $composerCmd = "cd $ProjectPath; composer install --no-dev --optimize-autoloader"
    if (-not (Invoke-RemoteCommand -Server $Server -User $User -Command $composerCmd -KeyPath $KeyPath -DryRun:$DryRun)) {
        Write-Log "Composer install failed" "ERROR"
        return $false
    }
    
    # Run database migrations if requested
    if ($RunMigrations) {
        $migrateCmd = "cd $ProjectPath; php artisan migrate --force"
        if (-not (Invoke-RemoteCommand -Server $Server -User $User -Command $migrateCmd -KeyPath $KeyPath -DryRun:$DryRun)) {
            Write-Log "Database migration failed" "ERROR"
            return $false
        }
    }
    
    # Run seeders if requested
    if ($RunSeeders) {
        $seedCmd = "cd $ProjectPath; php artisan db:seed --force"
        if (-not (Invoke-RemoteCommand -Server $Server -User $User -Command $seedCmd -KeyPath $KeyPath -DryRun:$DryRun)) {
            Write-Log "Database seeding failed" "WARNING"
            # Don't fail deployment for seeder issues
        }
    }
    
    # Clear caches and optimize
    $optimizeCommands = @(
        "php artisan config:clear",
        "php artisan route:clear",
        "php artisan view:clear",
        "php artisan cache:clear",
        "php artisan config:cache",
        "php artisan route:cache",
        "php artisan view:cache"
    )
    
    foreach ($cmd in $optimizeCommands) {
        $fullCmd = "cd $ProjectPath; $cmd"
        if (-not (Invoke-RemoteCommand -Server $Server -User $User -Command $fullCmd -KeyPath $KeyPath -DryRun:$DryRun)) {
            Write-Log "Optimization command failed: $cmd" "WARNING"
            # Continue with deployment even if some optimization fails
        }
    }
    
    Write-Log "Application deployment completed successfully" "SUCCESS"
    return $true
}

# Main deployment logic
try {
    # Load configuration
    $configPath = "$PSScriptRoot\deploy-config.json"
    if (Test-Path $configPath) {
        $config = Get-Content $configPath | ConvertFrom-Json
        Write-Log "Configuration loaded from deploy-config.json" "SUCCESS"
        
        # Override parameters with config values if not provided
        if (-not $PSBoundParameters.ContainsKey('ForgeServer') -and $config.ForgeServer) {
            $ForgeServer = $config.ForgeServer
        }
        if (-not $PSBoundParameters.ContainsKey('ProjectPath') -and $config.ProjectPath) {
            $ProjectPath = $config.ProjectPath
        }
        if (-not $PSBoundParameters.ContainsKey('ForgeUser') -and $config.ForgeUser) {
            $ForgeUser = $config.ForgeUser
        }
        if (-not $SSHKey -and $config.SSHKey) {
            $SSHKey = $config.SSHKey
        }
    } else {
        Write-Log "Configuration file not found, using parameters and defaults" "WARNING"
    }
    
    # Display deployment information
    Write-Host ""
    Write-Log "Deployment Information:" "INFO"
    Write-Host "   Server: $ForgeServer"
    Write-Host "   User: $ForgeUser"
    Write-Host "   Path: $ProjectPath"
    Write-Host "   Update Database: $UpdateDatabase"
    Write-Host "   Run Migrations: $RunMigrations"
    Write-Host "   Run Seeders: $RunSeeders"
    Write-Host "   Dry Run: $DryRun"
    Write-Host ""
    
    # Test SSH connection
    if (-not (Test-SSHConnection -Server $ForgeServer -User $ForgeUser -KeyPath $SSHKey)) {
        Write-Log "Cannot connect to server. Deployment aborted." "ERROR"
        exit 1
    }
    
    # Create backup if not in dry run mode
    if (-not $DryRun -and -not $BackupOnly) {
        if (-not (Create-DatabaseBackup -Server $ForgeServer -User $ForgeUser -ProjectPath $ProjectPath -KeyPath $SSHKey -DryRun:$DryRun)) {
            if (-not $Force) {
                Write-Log "Backup creation failed. Use -Force to continue anyway." "ERROR"
                exit 1
            } else {
                Write-Log "Backup failed but continuing due to -Force flag" "WARNING"
            }
        }
    }
    
    # Exit if backup only
    if ($BackupOnly) {
        Write-Log "Backup completed. Exiting as requested." "SUCCESS"
        exit 0
    }
    
    # Deploy application
    if (-not (Deploy-Application -Server $ForgeServer -User $ForgeUser -ProjectPath $ProjectPath -KeyPath $SSHKey -UpdateDatabase:$UpdateDatabase -RunMigrations:$RunMigrations -RunSeeders:$RunSeeders -DryRun:$DryRun)) {
        Write-Log "Deployment failed!" "ERROR"
        exit 1
    }
    
    Write-Log "Deployment completed successfully!" "SUCCESS"
    
} catch {
    Write-Log "Deployment failed with error: $($_.Exception.Message)" "ERROR"
    Write-Log "Stack trace: $($_.ScriptStackTrace)" "ERROR"
    exit 1
}
