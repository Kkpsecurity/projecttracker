# Laravel Forge Deployment Scripts

This directory contains automated deployment scripts for deploying the Laravel application to the HB837 Training server on Laravel Forge.

## 🚀 Quick Start

### PowerShell (Windows)
```powershell
# Basic deployment
.\scripts\deploy\forge-deploy.ps1

# Deployment with database updates
.\scripts\deploy\forge-deploy.ps1 -UpdateDatabase -RunMigrations

# Dry run to see what would happen
.\scripts\deploy\forge-deploy.ps1 -DryRun
```

### Bash (Linux/Mac)
```bash
# Basic deployment
./scripts/deploy/forge-deploy.sh

# Deployment with database updates
./scripts/deploy/forge-deploy.sh --update-db --migrations

# Dry run to see what would happen
./scripts/deploy/forge-deploy.sh --dry-run
```

## 📁 Files Overview

| File | Purpose |
|------|---------|
| `deploy-config.json` | Configuration settings for deployment |
| `forge-deploy.ps1` | PowerShell deployment script |
| `forge-deploy.sh` | Bash deployment script |
| `rollback.ps1` | PowerShell rollback script |
| `README.md` | This documentation file |

## 🏗️ HB837 Training Server Details

- **Domain**: projecttracker.hb837training.com
- **Server ID**: 783941
- **Site ID**: 2763978
- **SSH User**: projecttracker
- **Public IP**: 54.158.31.216
- **Private IP**: 10.0.1.86
- **Project Path**: `/home/projecttracker/projecttracker.hb837training.com`

## 🔧 Configuration

The deployment is configured through `deploy-config.json`:

```json
{
  "forge": {
    "server_id": "783941",
    "site_id": "2763978",
    "server_ip": "54.158.31.216",
    "server_name": "projecttracker.hb837training.com",
    "ssh_user": "projecttracker",
    "project_path": "/home/projecttracker/projecttracker.hb837training.com"
  },
  "deployment": {
    "backup_before_deploy": true,
    "selective_folders": ["app", "config", "database", "resources", "routes"],
    "exclude_folders": [".git", "tests", "docs", "node_modules"],
    "laravel_optimizations": [
      "composer install --no-dev --optimize-autoloader",
      "php artisan config:cache",
      "php artisan route:cache",
      "php artisan optimize"
    ]
  }
}
```

## 🎯 Deployment Process

The deployment scripts follow this sequence:

1. **🔍 SSH Connection Test** - Verify connection to the server
2. **💾 Backup Creation** - Create timestamped backup of current files
3. **🗑️ Folder Removal** - Remove existing application folders
4. **📁 Folder Copy** - Copy new application folders from development
5. **⚡ Laravel Optimization** - Run Laravel optimization commands
6. **🗄️ Database Operations** - Optional database migrations and seeders
7. **✅ Final Verification** - Verify deployment success

## 📝 PowerShell Script Options

```powershell
.\forge-deploy.ps1 [OPTIONS]

Options:
  -ForgeServer <string>     # Server hostname (default: projecttracker.hb837training.com)
  -ProjectPath <string>     # Project path on server
  -ForgeUser <string>       # SSH user (default: projecttracker)
  -UpdateDatabase           # Update database
  -RunMigrations            # Run database migrations
  -RunSeeders              # Run database seeders
  -Force                   # Skip confirmation prompts
  -DryRun                  # Show what would be done without executing
  -BackupOnly              # Create backup only, skip deployment
```

## 🐧 Bash Script Options

```bash
./forge-deploy.sh [OPTIONS]

Options:
  --server <hostname>       # Forge server hostname
  --path <path>            # Project path on server
  --user <username>        # SSH user
  --update-db              # Update database
  --migrations             # Run database migrations
  --seeders                # Run database seeders
  --force                  # Skip confirmation prompts
  --dry-run                # Show what would be done without executing
  --backup-only            # Create backup only, skip deployment
  --help                   # Show help message
```

## 🔄 Rollback

If a deployment fails or causes issues, you can rollback using the backup:

```powershell
# PowerShell rollback
.\scripts\deploy\rollback.ps1 -BackupPath "/home/projecttracker/backups/deployment_2025-07-15_14-30-25"

# Find available backups on server
ssh projecttracker@projecttracker.hb837training.com "ls -la /home/projecttracker/backups/"
```

## 📋 Prerequisites

### SSH Access Setup
1. Ensure you have SSH access to the HB837 server
2. Add your SSH key to the server:
   ```bash
   ssh-copy-id projecttracker@projecttracker.hb837training.com
   ```

### Local Development Environment
1. Ensure you're running the script from the project root directory
2. Local folders must exist: `app`, `config`, `database`, `resources`, `routes`
3. Composer and PHP should be available on the server

## 🛡️ Safety Features

- **Automatic Backups**: Every deployment creates a timestamped backup
- **Confirmation Prompts**: Safety confirmations before destructive operations
- **Dry Run Mode**: See what would happen without making changes
- **Rollback Capability**: Quick restore from backup if needed
- **SSH Connection Verification**: Test connection before deployment

## 📊 Deployment Examples

### Standard Deployment
```powershell
# Create backup and deploy application files
.\scripts\deploy\forge-deploy.ps1
```

### Full Deployment with Database Updates
```powershell
# Deploy with database migrations
.\scripts\deploy\forge-deploy.ps1 -UpdateDatabase -RunMigrations
```

### Safe Testing
```powershell
# See what would happen without doing it
.\scripts\deploy\forge-deploy.ps1 -DryRun

# Create backup only for safety
.\scripts\deploy\forge-deploy.ps1 -BackupOnly
```

### Emergency Rollback
```powershell
# List available backups
ssh projecttracker@projecttracker.hb837training.com "ls -la /home/projecttracker/backups/"

# Rollback to specific backup
.\scripts\deploy\rollback.ps1 -BackupPath "/home/projecttracker/backups/deployment_2025-07-15_14-30-25"
```

## 🔍 Troubleshooting

### SSH Connection Issues
```bash
# Test SSH connection manually
ssh projecttracker@projecttracker.hb837training.com

# Check SSH key
ssh-add -l

# Add SSH key if needed
ssh-add ~/.ssh/id_rsa
```

### Permission Issues
```bash
# Check file permissions on server
ssh projecttracker@projecttracker.hb837training.com "ls -la /home/projecttracker/projecttracker.hb837training.com/"

# Fix permissions if needed
ssh projecttracker@projecttracker.hb837training.com "chmod -R 755 /home/projecttracker/projecttracker.hb837training.com/"
```

### Laravel Issues
```bash
# Check Laravel status on server
ssh projecttracker@projecttracker.hb837training.com "cd /home/projecttracker/projecttracker.hb837training.com && php artisan --version"

# Clear all caches
ssh projecttracker@projecttracker.hb837training.com "cd /home/projecttracker/projecttracker.hb837training.com && php artisan optimize:clear"
```

## 📞 Support

- **Server Details**: HB837 Training Server (ID: 783941)
- **Site Details**: Site ID: 2763978
- **Documentation**: `/docs/tasks/task_07.md`
- **Backup Location**: `/home/projecttracker/backups/`

## 🚨 Important Notes

1. **Always test first**: Use `--dry-run` to see what would happen
2. **Backup verification**: Verify backups are created before proceeding
3. **Monitor after deployment**: Check the application and logs after deployment
4. **Database safety**: Database operations are optional and require explicit flags
5. **Environment files**: The `.env` file is preserved during deployment

## 🔮 Future Enhancements

- Automated testing integration
- Slack/email notifications
- Database backup verification
- Health check endpoints
- Blue-green deployment support
