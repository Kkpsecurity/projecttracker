<?php
/**
 * AI-Powered HB837 Test Runner
 * Automated testing and diagnostics for HB837 three-phase import functionality
 */

class AITestRunner
{
    private $basePath;
    private $testResults = [];
    private $issues = [];
    private $recommendations = [];

    public function __construct($basePath = '.')
    {
        $this->basePath = $basePath;
    }

    public function runDiagnostics()
    {
        echo "🤖 AI Test Runner: HB837 Three-Phase Import Diagnostics\n";
        echo "=" . str_repeat("=", 60) . "\n\n";

        // Check project structure and required files
        $this->checkProjectStructure();
        
        // Check database configuration
        $this->checkDatabaseConfig();
        
        // Check HB837 models and imports
        $this->checkHB837Components();
        
        // Check test files
        $this->checkTestFiles();
        
        // Check demo/sample files
        $this->checkDemoFiles();
        
        // Run actual tests if possible
        $this->runTests();
        
        // Generate report
        $this->generateReport();
    }

    private function checkProjectStructure()
    {
        echo "📁 Checking Project Structure...\n";
        
        $requiredDirs = [
            'app/Http/Controllers/Admin/HB837',
            'app/Imports',
            'app/Models',
            'tests/Unit',
            'tests/Feature',
            'database/migrations',
            'resources/views/admin/hb837'
        ];

        foreach ($requiredDirs as $dir) {
            $path = $this->basePath . '/' . $dir;
            if (is_dir($path)) {
                echo "   ✅ {$dir}\n";
            } else {
                echo "   ❌ {$dir} - MISSING\n";
                $this->issues[] = "Missing directory: {$dir}";
            }
        }
        echo "\n";
    }

    private function checkDatabaseConfig()
    {
        echo "🗄️  Checking Database Configuration...\n";
        
        $envFile = $this->basePath . '/.env';
        $envTestingFile = $this->basePath . '/.env.testing';
        
        if (file_exists($envFile)) {
            echo "   ✅ .env file exists\n";
            $this->checkEnvDatabase($envFile, 'production');
        } else {
            echo "   ❌ .env file missing\n";
            $this->issues[] = "Missing .env file";
        }
        
        if (file_exists($envTestingFile)) {
            echo "   ✅ .env.testing file exists\n";
            $this->checkEnvDatabase($envTestingFile, 'testing');
        } else {
            echo "   ❌ .env.testing file missing\n";
            $this->issues[] = "Missing .env.testing file";
        }
        echo "\n";
    }

    private function checkEnvDatabase($file, $type)
    {
        $content = file_get_contents($file);
        
        if (strpos($content, 'DB_CONNECTION=') !== false) {
            preg_match('/DB_CONNECTION=(.*)/', $content, $matches);
            $connection = trim($matches[1] ?? 'unknown');
            echo "   📊 {$type} DB: {$connection}\n";
            
            if ($connection === 'sqlite' && $type === 'testing') {
                echo "   ✅ SQLite configured for testing\n";
            } elseif ($connection === 'pgsql' || $connection === 'mysql') {
                echo "   ✅ {$connection} configured for {$type}\n";
            } else {
                echo "   ⚠️  Unusual database connection: {$connection}\n";
            }
        } else {
            echo "   ❌ No DB_CONNECTION found in {$file}\n";
            $this->issues[] = "No database connection configured in {$file}";
        }
    }

    private function checkHB837Components()
    {
        echo "🔧 Checking HB837 Components...\n";
        
        $components = [
            'app/Models/HB837.php' => 'HB837 Model',
            'app/Imports/HB837Import.php' => 'HB837 Import Class',
            'app/Http/Controllers/Admin/HB837/HB837Controller.php' => 'HB837 Controller',
            'app/Exports/HB837Export.php' => 'HB837 Export Class (optional)'
        ];

        foreach ($components as $file => $description) {
            $path = $this->basePath . '/' . $file;
            if (file_exists($path)) {
                echo "   ✅ {$description}\n";
                $this->analyzeComponent($path, $description);
            } else {
                echo "   ❌ {$description} - MISSING\n";
                $this->issues[] = "Missing component: {$description}";
            }
        }
        echo "\n";
    }

    private function analyzeComponent($path, $description)
    {
        $content = file_get_contents($path);
        
        // Check for three-phase functionality
        if (strpos($description, 'Import') !== false) {
            $hasPhases = strpos($content, 'setPhase') !== false || 
                        strpos($content, 'getPhase') !== false ||
                        strpos($content, 'phase') !== false;
            
            if ($hasPhases) {
                echo "      🔄 Three-phase functionality detected\n";
            } else {
                echo "      ⚠️  No three-phase functionality detected\n";
                $this->issues[] = "Three-phase functionality missing in {$description}";
            }
        }

        // Check for basic structure
        if (strpos($content, 'class ') !== false) {
            echo "      ✅ Valid PHP class structure\n";
        } else {
            echo "      ❌ Invalid PHP class structure\n";
            $this->issues[] = "Invalid class structure in {$description}";
        }
    }

    private function checkTestFiles()
    {
        echo "🧪 Checking Test Files...\n";
        
        $testFiles = [
            'tests/Unit/HB837ImportTest.php' => 'Unit Tests',
            'tests/Feature/HB837ImportExportTest.php' => 'Feature Tests',
            'tests/Feature/HB837ThreePhaseImportTest.php' => 'Three-Phase Tests',
            'tests/Feature/HB837ControllerTest.php' => 'Controller Tests'
        ];

        foreach ($testFiles as $file => $description) {
            $path = $this->basePath . '/' . $file;
            if (file_exists($path)) {
                echo "   ✅ {$description}\n";
                $this->analyzeTestFile($path, $description);
            } else {
                echo "   ❌ {$description} - MISSING\n";
                $this->issues[] = "Missing test file: {$description}";
            }
        }
        echo "\n";
    }

    private function analyzeTestFile($path, $description)
    {
        $content = file_get_contents($path);
        
        // Count test methods
        preg_match_all('/public function test_/', $content, $matches);
        $testCount = count($matches[0]);
        echo "      📊 {$testCount} test methods found\n";
        
        // Check for common issues
        if (strpos($content, '$this->actingAs') !== false) {
            echo "      🔐 Authentication tests present\n";
        }
        
        if (strpos($content, 'RefreshDatabase') !== false) {
            echo "      🗄️  Database refresh enabled\n";
        }
    }

    private function checkDemoFiles()
    {
        echo "📄 Checking Demo/Sample Files...\n";
        
        $demoFiles = [
            'docs/hb837_projects(16).xlsx' => 'Sample Excel File',
            'setup/agent_sample_upload.csv' => 'Sample CSV File',
            'agent_sample_upload.csv' => 'Alternative Sample CSV'
        ];

        foreach ($demoFiles as $file => $description) {
            $path = $this->basePath . '/' . $file;
            if (file_exists($path)) {
                echo "   ✅ {$description}\n";
                $size = filesize($path);
                echo "      📏 Size: " . number_format($size) . " bytes\n";
            } else {
                echo "   ❌ {$description} - MISSING\n";
                $this->issues[] = "Missing demo file: {$description}";
            }
        }
        echo "\n";
    }

    private function runTests()
    {
        echo "🏃 Running Tests...\n";
        
        if (!$this->canRunTests()) {
            echo "   ⚠️  Cannot run tests due to missing dependencies or configuration\n";
            return;
        }

        $testCommands = [
            'php artisan test tests/Unit/HB837ImportTest.php',
            'php artisan test tests/Feature/HB837ImportExportTest.php'
        ];

        foreach ($testCommands as $command) {
            echo "   🔄 Running: {$command}\n";
            
            $output = [];
            $returnCode = 0;
            exec($command . ' 2>&1', $output, $returnCode);
            
            if ($returnCode === 0) {
                echo "      ✅ Tests passed\n";
            } else {
                echo "      ❌ Tests failed\n";
                $this->testResults[$command] = [
                    'status' => 'failed',
                    'output' => $output
                ];
            }
        }
        echo "\n";
    }

    private function canRunTests()
    {
        // Check if artisan exists
        if (!file_exists($this->basePath . '/artisan')) {
            $this->issues[] = "Laravel artisan command not found";
            return false;
        }

        // Check if vendor directory exists
        if (!is_dir($this->basePath . '/vendor')) {
            $this->issues[] = "Vendor directory missing - run composer install";
            return false;
        }

        return true;
    }

    private function generateReport()
    {
        echo "📋 DIAGNOSTIC REPORT\n";
        echo "=" . str_repeat("=", 60) . "\n\n";

        echo "🔍 ISSUES FOUND (" . count($this->issues) . "):\n";
        if (empty($this->issues)) {
            echo "   ✅ No issues detected!\n";
        } else {
            foreach ($this->issues as $issue) {
                echo "   ❌ {$issue}\n";
            }
        }
        echo "\n";

        echo "💡 RECOMMENDATIONS:\n";
        $this->generateRecommendations();
        foreach ($this->recommendations as $recommendation) {
            echo "   💡 {$recommendation}\n";
        }
        echo "\n";

        echo "🎯 NEXT STEPS:\n";
        $this->generateNextSteps();
    }

    private function generateRecommendations()
    {
        if (in_array("Missing .env.testing file", $this->issues)) {
            $this->recommendations[] = "Create .env.testing with SQLite configuration for faster tests";
        }

        if (in_array("Missing demo file: Sample Excel File", $this->issues)) {
            $this->recommendations[] = "Copy docs/hb837_projects(16).xlsx from original project";
        }

        if (in_array("Missing demo file: Sample CSV File", $this->issues)) {
            $this->recommendations[] = "Copy setup/agent_sample_upload.csv from original project";
        }

        foreach ($this->issues as $issue) {
            if (strpos($issue, 'Missing test file:') !== false) {
                $this->recommendations[] = "Copy missing test files from original project";
                break;
            }
        }

        if (in_array("Vendor directory missing - run composer install", $this->issues)) {
            $this->recommendations[] = "Run 'composer install' to install dependencies";
        }
    }

    private function generateNextSteps()
    {
        echo "   1. 🔧 Fix all missing files and directories\n";
        echo "   2. 🗄️  Configure database for testing (.env.testing)\n";
        echo "   3. 📄 Copy required demo files\n";
        echo "   4. 🧪 Run tests individually to identify specific failures\n";
        echo "   5. 🐛 Debug and fix failing tests\n";
        echo "   6. 🚀 Validate three-phase import workflow\n";
        echo "   7. 🌐 Test UI integration\n";
    }
}

// Run diagnostics if called directly
if (php_sapi_name() === 'cli') {
    $runner = new AITestRunner(__DIR__);
    $runner->runDiagnostics();
}
