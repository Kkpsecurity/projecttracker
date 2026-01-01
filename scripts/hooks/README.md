# Git Hooks

This directory contains Git automation scripts and hooks.

## Scripts

- **`pre-commit-hook.ps1`** - PowerShell pre-commit hook for Windows
- **`pre-commit-hook.sh`** - Bash pre-commit hook for Linux/Mac

## Purpose

These hooks provide:
- Automated code quality checks before commits
- Syntax validation
- Test execution before commit
- Code formatting verification

## Installation

### Windows (PowerShell)
```powershell
# Copy to Git hooks directory
Copy-Item scripts/hooks/pre-commit-hook.ps1 .git/hooks/pre-commit
```

### Linux/Mac (Bash)
```bash
# Copy to Git hooks directory
cp scripts/hooks/pre-commit-hook.sh .git/hooks/pre-commit
chmod +x .git/hooks/pre-commit
```

## Usage

These hooks run automatically when you commit changes to the repository. They help ensure code quality and prevent broken code from being committed.
