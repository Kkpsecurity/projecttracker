#!/bin/bash
#
# Comprehensive Test Suite Runner for Laravel Application
# Usage: ./run-tests.sh [type] [options]
#

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${2}${1}${NC}"
}

# Function to print section headers
print_header() {
    echo -e "\n${BLUE}================================================${NC}"
    echo -e "${BLUE}🧪 $1${NC}"
    echo -e "${BLUE}================================================${NC}\n"
}

# Function to handle test results
check_result() {
    if [ $? -eq 0 ]; then
        print_status "✅ $1 passed!" $GREEN
        return 0
    else
        print_status "❌ $1 failed!" $RED
        return 1
    fi
}

# Default settings
RUN_QUICK=false
RUN_COVERAGE=false
RUN_PARALLEL=false
STOP_ON_FAILURE=false
VERBOSE=false

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        quick|q)
            RUN_QUICK=true
            shift
            ;;
        coverage|c)
            RUN_COVERAGE=true
            shift
            ;;
        parallel|p)
            RUN_PARALLEL=true
            shift
            ;;
        stop|s)
            STOP_ON_FAILURE=true
            shift
            ;;
        verbose|v)
            VERBOSE=true
            shift
            ;;
        help|h)
            echo "Usage: $0 [quick|coverage|parallel|stop|verbose|help]"
            echo ""
            echo "Options:"
            echo "  quick     - Run only critical tests"
            echo "  coverage  - Generate code coverage report"
            echo "  parallel  - Run tests in parallel"
            echo "  stop      - Stop on first failure"
            echo "  verbose   - Verbose output"
            echo "  help      - Show this help"
            exit 0
            ;;
        *)
            echo "Unknown option: $1"
            echo "Use 'help' for usage information"
            exit 1
            ;;
    esac
done

# Check if we're in a Laravel project
if [ ! -f "artisan" ]; then
    print_status "❌ Error: Not in a Laravel project directory" $RED
    exit 1
fi

print_header "LARAVEL APPLICATION TEST SUITE"

# Build test command options
TEST_OPTIONS=""
if [ "$STOP_ON_FAILURE" = true ]; then
    TEST_OPTIONS="$TEST_OPTIONS --stop-on-failure"
fi

if [ "$VERBOSE" = true ]; then
    TEST_OPTIONS="$TEST_OPTIONS --verbose"
fi

if [ "$RUN_PARALLEL" = true ]; then
    TEST_OPTIONS="$TEST_OPTIONS --parallel"
fi

if [ "$RUN_COVERAGE" = true ]; then
    TEST_OPTIONS="$TEST_OPTIONS --coverage-html=storage/app/coverage --coverage-clover=storage/app/coverage.xml"
fi

# Initialize counters
TOTAL_TESTS=0
PASSED_TESTS=0
FAILED_TESTS=0

print_header "PRE-TEST ENVIRONMENT CHECKS"

print_status "🔍 Checking PHP version..." $CYAN
php --version | head -1

print_status "🔍 Checking Laravel version..." $CYAN
php artisan --version

print_status "🔍 Clearing application caches..." $CYAN
php artisan config:clear > /dev/null 2>&1
php artisan route:clear > /dev/null 2>&1
php artisan view:clear > /dev/null 2>&1
check_result "Cache clearing"

print_status "🔍 Testing database connection..." $CYAN
php artisan migrate:status > /dev/null 2>&1
check_result "Database connection"

print_status "🔍 Checking critical routes..." $CYAN
php artisan route:list --name=admin.dashboard > /dev/null 2>&1 && \
php artisan route:list --name=inspection-calendar > /dev/null 2>&1
check_result "Route availability"

if [ "$RUN_QUICK" = true ]; then
    print_header "QUICK TEST SUITE (CRITICAL TESTS ONLY)"

    print_status "🧪 Running Application Health Tests..." $YELLOW
    php artisan test tests/Feature/ApplicationHealthTest.php $TEST_OPTIONS
    if check_result "Application Health Tests"; then
        ((PASSED_TESTS++))
    else
        ((FAILED_TESTS++))
    fi
    ((TOTAL_TESTS++))

    print_status "📅 Running Inspection Calendar Tests..." $YELLOW
    php artisan test tests/Feature/Admin/HB837/InspectionCalendarTest.php $TEST_OPTIONS
    if check_result "Inspection Calendar Tests"; then
        ((PASSED_TESTS++))
    else
        ((FAILED_TESTS++))
    fi
    ((TOTAL_TESTS++))

else
    print_header "FULL TEST SUITE"

    print_status "🧪 Running Application Health Tests..." $YELLOW
    php artisan test tests/Feature/ApplicationHealthTest.php $TEST_OPTIONS
    if check_result "Application Health Tests"; then
        ((PASSED_TESTS++))
    else
        ((FAILED_TESTS++))
    fi
    ((TOTAL_TESTS++))

    print_status "📅 Running Inspection Calendar Tests..." $YELLOW
    php artisan test tests/Feature/Admin/HB837/InspectionCalendarTest.php $TEST_OPTIONS
    if check_result "Inspection Calendar Tests"; then
        ((PASSED_TESTS++))
    else
        ((FAILED_TESTS++))
    fi
    ((TOTAL_TESTS++))

    print_status "🏗️ Running HB837 Controller Tests..." $YELLOW
    if [ -f "tests/Feature/HB837ControllerTest.php" ]; then
        php artisan test tests/Feature/HB837ControllerTest.php $TEST_OPTIONS
        if check_result "HB837 Controller Tests"; then
            ((PASSED_TESTS++))
        else
            ((FAILED_TESTS++))
        fi
        ((TOTAL_TESTS++))
    else
        print_status "⚠️ HB837 Controller Tests not found, skipping..." $YELLOW
    fi

    print_status "📊 Running Import/Export Tests..." $YELLOW
    if [ -f "tests/Feature/HB837ImportExportTest.php" ]; then
        php artisan test tests/Feature/HB837ImportExportTest.php $TEST_OPTIONS
        if check_result "Import/Export Tests"; then
            ((PASSED_TESTS++))
        else
            ((FAILED_TESTS++))
        fi
        ((TOTAL_TESTS++))
    else
        print_status "⚠️ Import/Export Tests not found, skipping..." $YELLOW
    fi

    print_status "🔄 Running Three Phase Import Tests..." $YELLOW
    if [ -f "tests/Feature/HB837ThreePhaseImportTest.php" ]; then
        php artisan test tests/Feature/HB837ThreePhaseImportTest.php $TEST_OPTIONS
        if check_result "Three Phase Import Tests"; then
            ((PASSED_TESTS++))
        else
            ((FAILED_TESTS++))
        fi
        ((TOTAL_TESTS++))
    else
        print_status "⚠️ Three Phase Import Tests not found, skipping..." $YELLOW
    fi

    print_status "🔧 Running Unit Tests..." $YELLOW
    if [ -d "tests/Unit" ] && [ "$(ls -A tests/Unit)" ]; then
        php artisan test tests/Unit $TEST_OPTIONS
        if check_result "Unit Tests"; then
            ((PASSED_TESTS++))
        else
            ((FAILED_TESTS++))
        fi
        ((TOTAL_TESTS++))
    else
        print_status "⚠️ No Unit Tests found, skipping..." $YELLOW
    fi

    print_status "🌟 Running Feature Tests..." $YELLOW
    if [ -f "tests/Feature/ExampleTest.php" ]; then
        php artisan test tests/Feature/ExampleTest.php $TEST_OPTIONS
        if check_result "Example Feature Tests"; then
            ((PASSED_TESTS++))
        else
            ((FAILED_TESTS++))
        fi
        ((TOTAL_TESTS++))
    else
        print_status "⚠️ Example Feature Tests not found, skipping..." $YELLOW
    fi
fi

print_header "POST-TEST ENVIRONMENT CHECKS"

print_status "🔍 Checking application state after tests..." $CYAN
php artisan route:list --name=admin.dashboard > /dev/null 2>&1
check_result "Routes still accessible"

print_status "🔍 Testing database integrity..." $CYAN
php artisan migrate:status > /dev/null 2>&1
check_result "Database integrity"

print_status "📊 Collecting application statistics..." $CYAN
PROJECT_COUNT=$(php artisan tinker --execute="echo \App\Models\HB837::count();" 2>/dev/null | tail -1)
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | tail -1)
SCHEDULED_COUNT=$(php artisan tinker --execute="echo \App\Models\HB837::whereNotNull('scheduled_date_of_inspection')->count();" 2>/dev/null | tail -1)

if [ "$PROJECT_COUNT" ] && [ "$USER_COUNT" ] && [ "$SCHEDULED_COUNT" ]; then
    print_status "📈 Application Stats: $USER_COUNT users, $PROJECT_COUNT projects, $SCHEDULED_COUNT scheduled inspections" $GREEN
fi

print_header "TEST RESULTS SUMMARY"

if [ $FAILED_TESTS -eq 0 ]; then
    print_status "🎉 ALL TESTS PASSED! ($PASSED_TESTS/$TOTAL_TESTS)" $GREEN
    print_status "✨ Application is ready for deployment!" $GREEN
    EXIT_CODE=0
else
    print_status "❌ SOME TESTS FAILED! ($PASSED_TESTS/$TOTAL_TESTS passed, $FAILED_TESTS failed)" $RED
    print_status "🔧 Please fix the failing tests before proceeding." $YELLOW
    EXIT_CODE=1
fi

if [ "$RUN_COVERAGE" = true ]; then
    print_status "📊 Code coverage report generated in storage/app/coverage/" $CYAN
fi

print_status "⏱️ Test suite completed at $(date)" $BLUE

# Additional tips
print_header "HELPFUL COMMANDS"
print_status "💡 Run specific test: php artisan test tests/Feature/[TestName].php" $CYAN
print_status "💡 Run with coverage: php artisan test --coverage" $CYAN
print_status "💡 Run in parallel: php artisan test --parallel" $CYAN
print_status "💡 Watch for changes: php artisan test --watch" $CYAN

exit $EXIT_CODE
