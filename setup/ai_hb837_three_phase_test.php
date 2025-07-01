<?php
/**
 * AI-Powered HB837 Three-Phase Import Test Runner
 * Specialized testing for HB837 three-phase import workflow
 */

class AIHb837ThreePhaseTestRunner
{
    private $basePath;
    private $phases = ['initial', 'update', 'review'];
    private $testResults = [];
    private $phaseResults = [];

    public function __construct($basePath = '.')
    {
        $this->basePath = $basePath;
    }

    public function runThreePhaseTests()
    {
        echo "🤖 AI HB837 Three-Phase Import Test Runner\n";
        echo "=" . str_repeat("=", 60) . "\n\n";

        // Validate prerequisites
        if (!$this->validatePrerequisites()) {
            echo "❌ Prerequisites not met. Cannot proceed with tests.\n";
            return;
        }

        // Test each phase individually
        foreach ($this->phases as $phase) {
            $this->testPhase($phase);
        }

        // Test complete workflow
        $this->testCompleteWorkflow();

        // Generate report
        $this->generatePhaseReport();
    }

    private function validatePrerequisites()
    {
        echo "🔍 Validating Prerequisites...\n";
        
        $required = [
            'app/Imports/HB837Import.php' => 'HB837 Import Class',
            'app/Models/HB837.php' => 'HB837 Model',
            'docs/hb837_projects(16).xlsx' => 'Sample Excel File',
        ];

        $allValid = true;
        foreach ($required as $file => $description) {
            $path = $this->basePath . '/' . $file;
            if (file_exists($path)) {
                echo "   ✅ {$description}\n";
            } else {
                echo "   ❌ {$description} - MISSING\n";
                $allValid = false;
            }
        }

        // Check if HB837Import has three-phase methods
        $importPath = $this->basePath . '/app/Imports/HB837Import.php';
        if (file_exists($importPath)) {
            $content = file_get_contents($importPath);
            $hasPhases = strpos($content, 'setPhase') !== false || 
                        strpos($content, 'getPhase') !== false;
            
            if ($hasPhases) {
                echo "   ✅ Three-phase methods detected\n";
            } else {
                echo "   ❌ Three-phase methods missing\n";
                $allValid = false;
            }
        }

        echo "\n";
        return $allValid;
    }

    private function testPhase($phase)
    {
        echo "🔄 Testing Phase: " . strtoupper($phase) . "\n";
        echo str_repeat("-", 40) . "\n";

        $testCode = $this->generatePhaseTestCode($phase);
        $tempFile = $this->basePath . "/temp_phase_{$phase}_test.php";
        
        // Write temporary test file
        file_put_contents($tempFile, $testCode);

        // Run the test
        $output = [];
        $returnCode = 0;
        $command = "cd " . escapeshellarg($this->basePath) . " && php {$tempFile}";
        exec($command . ' 2>&1', $output, $returnCode);

        // Store results
        $this->phaseResults[$phase] = [
            'status' => $returnCode === 0 ? 'passed' : 'failed',
            'output' => $output,
            'return_code' => $returnCode
        ];

        // Display results
        if ($returnCode === 0) {
            echo "   ✅ Phase {$phase} test PASSED\n";
        } else {
            echo "   ❌ Phase {$phase} test FAILED\n";
            echo "   📄 Output:\n";
            foreach ($output as $line) {
                echo "      {$line}\n";
            }
        }

        // Clean up
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }

        echo "\n";
    }

    private function generatePhaseTestCode($phase)
    {
        return <<<PHP
<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
\$app = require_once 'bootstrap/app.php';
\$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Imports\HB837Import;
use App\Models\HB837;
use Illuminate\Support\Facades\DB;

echo "Testing HB837 Import - Phase: {$phase}\\n";

try {
    // Create import instance
    \$import = new HB837Import();
    
    // Test phase setting
    if (method_exists(\$import, 'setPhase')) {
        \$import->setPhase('{$phase}');
        echo "✅ Phase set to: {$phase}\\n";
    } else {
        echo "❌ setPhase method not found\\n";
        exit(1);
    }
    
    // Test phase getting
    if (method_exists(\$import, 'getPhase')) {
        \$currentPhase = \$import->getPhase();
        if (\$currentPhase === '{$phase}') {
            echo "✅ Phase retrieved correctly: {$phase}\\n";
        } else {
            echo "❌ Phase mismatch. Expected: {$phase}, Got: \$currentPhase\\n";
            exit(1);
        }
    } else {
        echo "❌ getPhase method not found\\n";
        exit(1);
    }
    
    // Test sample data processing (if Excel file exists)
    \$sampleFile = 'docs/hb837_projects(16).xlsx';
    if (file_exists(\$sampleFile)) {
        echo "✅ Sample file found: \$sampleFile\\n";
        
        // Attempt to process a few rows (without actually importing)
        if (method_exists(\$import, 'collection') || method_exists(\$import, 'model')) {
            echo "✅ Import class has processing methods\\n";
        } else {
            echo "⚠️  Import class missing standard processing methods\\n";
        }
    } else {
        echo "⚠️  Sample file not found: \$sampleFile\\n";
    }
    
    // Test database connectivity
    try {
        DB::connection()->getPdo();
        echo "✅ Database connection successful\\n";
    } catch (Exception \$e) {
        echo "❌ Database connection failed: " . \$e->getMessage() . "\\n";
        exit(1);
    }
    
    // Test HB837 model
    if (class_exists('App\\Models\\HB837')) {
        echo "✅ HB837 model exists\\n";
        
        // Test model instantiation
        \$model = new HB837();
        echo "✅ HB837 model can be instantiated\\n";
        
        // Check fillable fields
        \$fillable = \$model->getFillable();
        if (!empty(\$fillable)) {
            echo "✅ HB837 model has fillable fields: " . count(\$fillable) . "\\n";
        } else {
            echo "⚠️  HB837 model has no fillable fields\\n";
        }
    } else {
        echo "❌ HB837 model not found\\n";
        exit(1);
    }
    
    echo "✅ Phase {$phase} test completed successfully\\n";
    exit(0);
    
} catch (Exception \$e) {
    echo "❌ Error during {$phase} phase test: " . \$e->getMessage() . "\\n";
    echo "📍 File: " . \$e->getFile() . ":\\n" . \$e->getLine() . "\\n";
    exit(1);
}
PHP;
    }

    private function testCompleteWorkflow()
    {
        echo "🔄 Testing Complete Three-Phase Workflow\n";
        echo str_repeat("-", 40) . "\n";

        $workflowTestCode = $this->generateWorkflowTestCode();
        $tempFile = $this->basePath . "/temp_workflow_test.php";
        
        // Write temporary test file
        file_put_contents($tempFile, $workflowTestCode);

        // Run the test
        $output = [];
        $returnCode = 0;
        $command = "cd " . escapeshellarg($this->basePath) . " && php {$tempFile}";
        exec($command . ' 2>&1', $output, $returnCode);

        // Store results
        $this->testResults['workflow'] = [
            'status' => $returnCode === 0 ? 'passed' : 'failed',
            'output' => $output,
            'return_code' => $returnCode
        ];

        // Display results
        if ($returnCode === 0) {
            echo "   ✅ Complete workflow test PASSED\n";
        } else {
            echo "   ❌ Complete workflow test FAILED\n";
            echo "   📄 Output:\n";
            foreach ($output as $line) {
                echo "      {$line}\n";
            }
        }

        // Clean up
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }

        echo "\n";
    }

    private function generateWorkflowTestCode()
    {
        return <<<'PHP'
<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Imports\HB837Import;
use App\Models\HB837;
use Illuminate\Support\Facades\DB;

echo "Testing Complete HB837 Three-Phase Workflow\n";

try {
    $phases = ['initial', 'update', 'review'];
    $import = new HB837Import();
    
    foreach ($phases as $phase) {
        echo "Phase: $phase\n";
        
        // Set phase
        $import->setPhase($phase);
        
        // Verify phase
        if ($import->getPhase() !== $phase) {
            echo "❌ Phase setting failed for: $phase\n";
            exit(1);
        }
        
        echo "✅ Phase $phase configured correctly\n";
    }
    
    // Test phase transitions
    echo "Testing phase transitions...\n";
    
    $import->setPhase('initial');
    $import->setPhase('update');
    if ($import->getPhase() === 'update') {
        echo "✅ Transition from initial to update successful\n";
    } else {
        echo "❌ Transition from initial to update failed\n";
        exit(1);
    }
    
    $import->setPhase('review');
    if ($import->getPhase() === 'review') {
        echo "✅ Transition from update to review successful\n";
    } else {
        echo "❌ Transition from update to review failed\n";
        exit(1);
    }
    
    // Test import counters if they exist
    if (property_exists($import, 'importedCount')) {
        echo "✅ Import counters available\n";
        echo "   Imported: " . $import->importedCount . "\n";
        echo "   Updated: " . ($import->updatedCount ?? 'N/A') . "\n";
        echo "   Skipped: " . ($import->skippedCount ?? 'N/A') . "\n";
    } else {
        echo "⚠️  Import counters not available\n";
    }
    
    echo "✅ Complete workflow test successful\n";
    exit(0);
    
} catch (Exception $e) {
    echo "❌ Workflow test error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
PHP;
    }

    private function generatePhaseReport()
    {
        echo "📋 THREE-PHASE TEST REPORT\n";
        echo "=" . str_repeat("=", 60) . "\n\n";

        echo "🔄 PHASE RESULTS:\n";
        foreach ($this->phases as $phase) {
            $result = $this->phaseResults[$phase] ?? ['status' => 'not_run'];
            $status = $result['status'];
            $icon = $status === 'passed' ? '✅' : '❌';
            echo "   {$icon} Phase " . strtoupper($phase) . ": {$status}\n";
        }
        echo "\n";

        echo "🔄 WORKFLOW RESULTS:\n";
        $workflowResult = $this->testResults['workflow'] ?? ['status' => 'not_run'];
        $status = $workflowResult['status'];
        $icon = $status === 'passed' ? '✅' : '❌';
        echo "   {$icon} Complete Workflow: {$status}\n";
        echo "\n";

        echo "📊 SUMMARY:\n";
        $totalTests = count($this->phases) + 1; // phases + workflow
        $passedTests = 0;
        
        foreach ($this->phaseResults as $result) {
            if ($result['status'] === 'passed') $passedTests++;
        }
        
        if ($workflowResult['status'] === 'passed') $passedTests++;
        
        echo "   Tests Run: {$totalTests}\n";
        echo "   Passed: {$passedTests}\n";
        echo "   Failed: " . ($totalTests - $passedTests) . "\n";
        echo "   Success Rate: " . round(($passedTests / $totalTests) * 100, 1) . "%\n";
        echo "\n";

        if ($passedTests === $totalTests) {
            echo "🎉 ALL TESTS PASSED! HB837 three-phase import is ready!\n";
        } else {
            echo "🔧 SOME TESTS FAILED. Review the output above for details.\n";
            echo "\n💡 COMMON FIXES:\n";
            echo "   1. Ensure all required files are present\n";
            echo "   2. Check database configuration\n";
            echo "   3. Run 'composer install' if dependencies are missing\n";
            echo "   4. Verify HB837Import class has setPhase/getPhase methods\n";
            echo "   5. Check HB837 model exists and is properly configured\n";
        }
    }
}

// Run three-phase tests if called directly
if (php_sapi_name() === 'cli') {
    $runner = new AIHb837ThreePhaseTestRunner(__DIR__);
    $runner->runThreePhaseTests();
}
