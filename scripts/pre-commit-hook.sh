#!/bin/bash

# Git Pre-commit Hook for ProjectTracker Fresh
# This script runs essential tests before allowing a commit

echo "🔍 Running Pre-commit Tests..."
echo "================================"

# Run application health tests
echo "Running Application Health Tests..."
php artisan test tests/Feature/ApplicationHealthTest.php --stop-on-failure
HEALTH_EXIT_CODE=$?

if [ $HEALTH_EXIT_CODE -ne 0 ]; then
    echo "❌ Application Health Tests failed!"
    echo "Please fix the failing tests before committing."
    exit 1
fi

# Run inspection calendar tests
echo "Running Inspection Calendar Tests..."
php artisan test tests/Feature/Admin/HB837/InspectionCalendarTest.php --stop-on-failure
CALENDAR_EXIT_CODE=$?

if [ $CALENDAR_EXIT_CODE -ne 0 ]; then
    echo "❌ Inspection Calendar Tests failed!"
    echo "Please fix the failing tests before committing."
    exit 1
fi

# Run a quick syntax check
echo "Running PHP Syntax Check..."
find . -name "*.php" -not -path "./vendor/*" -not -path "./storage/*" -not -path "./bootstrap/cache/*" | xargs -I {} php -l {} > /dev/null
SYNTAX_EXIT_CODE=$?

if [ $SYNTAX_EXIT_CODE -ne 0 ]; then
    echo "❌ PHP Syntax errors found!"
    echo "Please fix syntax errors before committing."
    exit 1
fi

echo "✅ All pre-commit tests passed!"
echo "Commit is allowed to proceed."
exit 0
