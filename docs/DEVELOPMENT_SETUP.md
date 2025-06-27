# Development Setup & CI/CD Guide

This document outlines the development workflow, code quality tools, and CI/CD pipeline for the Project Tracker application.

## Prerequisites

- PHP 8.1+
- Node.js 18+
- Composer
- Git

## Development Setup

### 1. Clone and Install Dependencies

```bash
git clone <repository-url>
cd projecttracker

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Build frontend assets
npm run dev
```

### 2. Initialize Git Hooks

```bash
# Install Husky (if not already done)
npm install

# Initialize Husky
npx husky install
```

## Code Quality Tools

### 1. PHP Code Quality

- **Laravel Pint**: PHP CS Fixer for Laravel
  ```bash
  # Check code style
  php ./vendor/bin/pint --test
  
  # Fix code style
  php ./vendor/bin/pint
  ```

- **PHPStan**: Static analysis tool
  ```bash
  # Install PHPStan
  composer require --dev phpstan/phpstan larastan/larastan
  
  # Run static analysis
  ./vendor/bin/phpstan analyse
  ```

### 2. JavaScript Code Quality

- **ESLint**: JavaScript linting
  ```bash
  # Check JavaScript code
  npm run lint
  
  # Fix JavaScript issues
  npm run lint:fix
  ```

- **Prettier**: Code formatting
  ```bash
  # Format code
  npm run format
  ```

### 3. Git Hooks (Husky)

Pre-commit hooks automatically run:
- ESLint on JavaScript files
- Prettier on JS/Vue/Blade files
- Laravel Pint on PHP files

Pre-push hooks run:
- Full test suite

## Available Scripts

### NPM Scripts
```bash
npm run dev          # Development build
npm run watch        # Watch for changes
npm run production   # Production build
npm run lint         # Run ESLint
npm run lint:fix     # Fix ESLint issues
npm run format       # Run Prettier
npm run test         # Run PHP tests
npm run security-check # Check for security vulnerabilities
```

### Composer Scripts
```bash
composer test        # Run PHPUnit tests
composer pint        # Run Laravel Pint
composer audit       # Security audit
```

## CI/CD Pipeline

### GitHub Actions Workflows

#### 1. Main CI/CD Pipeline (`.github/workflows/ci-cd.yml`)

**Triggered on**: Push/PR to `main` and `develop` branches

**Jobs**:
- **Test Job**:
  - Sets up PHP 8.1 and Node.js 18
  - Installs dependencies
  - Runs database migrations
  - Builds production assets
  - Runs ESLint and Laravel Pint
  - Executes PHPUnit tests with coverage
  - Uploads coverage to Codecov

- **Security Job**:
  - Runs Composer security audit
  - Checks for known vulnerabilities

- **Deploy Job** (production only):
  - Creates deployment artifact
  - Uploads build artifacts

#### 2. Code Quality Pipeline (`.github/workflows/code-quality.yml`)

**Triggered on**: Push/PR to `main` and `develop` branches

**Jobs**:
- **Code Quality**:
  - PHP syntax checking
  - Laravel Pint style checking
  - ESLint JavaScript checking
  - Prettier formatting check
  - PHPStan static analysis

- **Dependency Check**:
  - Checks for outdated packages
  - Runs security audits
  - Validates dependencies

## Configuration Files

### ESLint (`.eslintrc.js`)
- JavaScript/Vue linting rules
- jQuery and Bootstrap globals
- Consistent code style enforcement

### Prettier (`.prettierrc`)
- Code formatting rules
- PHP Blade template support
- Consistent formatting across file types

### PHPStan (`phpstan.neon`)
- Static analysis configuration
- Laravel-specific rules via Larastan
- Level 6 analysis (strict)

### Package.json
- Development dependencies
- Build scripts
- Lint-staged configuration
- Husky hooks configuration

## Workflow

### 1. Development Workflow

1. Create feature branch from `develop`
2. Make changes
3. Pre-commit hooks automatically run:
   - Code formatting
   - Linting
   - Style fixes
4. Commit changes
5. Pre-push hooks run tests
6. Push to GitHub
7. Create Pull Request
8. CI pipeline runs automatically
9. Review and merge

### 2. Release Workflow

1. Merge `develop` to `main`
2. CI pipeline runs full test suite
3. If tests pass, deployment artifacts are created
4. Manual deployment from artifacts

## Quality Gates

### Pre-commit
- ✅ ESLint passes
- ✅ Prettier formatting applied
- ✅ Laravel Pint style fixes applied

### Pre-push
- ✅ All tests pass

### CI Pipeline
- ✅ All tests pass with coverage
- ✅ Security audit passes
- ✅ Code quality checks pass
- ✅ Build completes successfully

## Security

### Automated Security Checks
- Composer security audit
- NPM audit for Node.js dependencies
- Dependency vulnerability scanning

### Manual Security Reviews
- Code review process
- Security-focused pull request reviews
- Regular dependency updates

## Monitoring and Reporting

### Code Coverage
- Automatic coverage reporting via Codecov
- Coverage thresholds enforced in CI

### Code Quality Metrics
- ESLint error/warning tracking
- PHPStan level compliance
- Code style consistency monitoring

## Troubleshooting

### Common Issues

1. **Husky hooks not running**:
   ```bash
   npx husky install
   chmod +x .husky/pre-commit
   chmod +x .husky/pre-push
   ```

2. **ESLint errors**:
   ```bash
   npm run lint:fix
   ```

3. **PHP style issues**:
   ```bash
   php ./vendor/bin/pint
   ```

4. **Test failures**:
   ```bash
   php artisan test --verbose
   ```

### Getting Help

- Check CI logs for specific error messages
- Run tools locally to debug issues
- Review this documentation for setup steps
- Contact the development team for assistance

## Contributing

1. Follow the established code style
2. Write tests for new features
3. Update documentation as needed
4. Ensure all quality gates pass
5. Submit pull requests for review
