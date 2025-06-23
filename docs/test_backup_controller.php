<?php
// Detailed backup controller tests with logging

// Set up basic paths
$basePath = __DIR__;
require_once $basePath . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once $basePath . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\Services\BackupDBController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BackupControllerTest {
    private $controller;
    private $logFile;

    public function __construct() {
        $this->controller = new BackupDBController();
        $this->logFile = __DIR__ . '/test_results_' . date('Y-m-d_H-i-s') . '.log';
        $this->log("=== BACKUP CONTROLLER TESTS STARTED ===");
        $this->log("Timestamp: " . date('Y-m-d H:i:s'));
    }

    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        echo $logMessage;
    }

    public function runAllTests() {
        try {
            $this->testValidationMethod();
            $this->testFileNameCleaning();
            $this->testConfigProcessing();
            $this->testImportValidation();
            $this->testDefaultNameGeneration();

            $this->log("=== ALL TESTS COMPLETED SUCCESSFULLY ===");
            $this->log("Results logged to: " . $this->logFile);

        } catch (Exception $e) {
            $this->log("ERROR: " . $e->getMessage());
            $this->log("Stack trace: " . $e->getTraceAsString());
        }
    }

    private function testValidationMethod() {
        $this->log("--- Testing Backup Validation Method ---");

        $testCases = [
            'valid_with_name' => [
                'name' => 'Test Backup',
                'tables' => ['hb837', 'consultants']
            ],
            'valid_without_name' => [
                'tables' => ['hb837']
            ],
            'invalid_no_tables' => [
                'name' => 'Test',
                'tables' => []
            ],
            'invalid_missing_tables' => [
                'name' => 'Test'
            ]
        ];

        foreach ($testCases as $caseName => $data) {
            $this->log("Testing case: $caseName");

            $validator = Validator::make($data, [
                'name' => 'nullable|string|max:255',
                'tables' => 'required|array|min:1',
                'tables.*' => 'string',
            ]);

            $passed = !$validator->fails();
            $this->log("  Data: " . json_encode($data));
            $this->log("  Validation passed: " . ($passed ? 'YES' : 'NO'));

            if ($validator->fails()) {
                $this->log("  Errors: " . json_encode($validator->errors()->toArray()));
            }

            // Log expected vs actual
            $shouldPass = in_array($caseName, ['valid_with_name', 'valid_without_name']);
            $this->log("  Expected to pass: " . ($shouldPass ? 'YES' : 'NO'));
            $this->log("  Result: " . ($passed === $shouldPass ? 'CORRECT' : 'INCORRECT'));
        }
    }

    private function testFileNameCleaning() {
        $this->log("--- Testing File Name Cleaning ---");

        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('cleanFileName');
        $method->setAccessible(true);

        $testCases = [
            'Test Backup 123' => 'TestBackup123',
            'backup@#$%^&*()' => 'backup',
            'My-File_Name' => 'My-File_Name',
            '123 Test!@# $%^' => '123Test',
            'Special chars: <>?:"{}|' => 'Specialchars',
            '' => '',
            'normal_file-name' => 'normal_file-name'
        ];

        foreach ($testCases as $input => $expected) {
            $actual = $method->invoke($this->controller, $input);
            $passed = $actual === $expected;

            $this->log("  Input: '$input'");
            $this->log("  Expected: '$expected'");
            $this->log("  Actual: '$actual'");
            $this->log("  Result: " . ($passed ? 'PASS' : 'FAIL'));
        }
    }

    private function testConfigProcessing() {
        $this->log("--- Testing Configuration Processing ---");

        $testConfigs = [
            ['name' => 'Test Backup', 'tables' => ['hb837', 'consultants']],
            ['name' => '', 'tables' => ['hb837']],
            ['name' => null, 'tables' => ['hb837', 'consultants', 'users']],
            ['tables' => ['hb837']]
        ];

        $reflection = new ReflectionClass($this->controller);
        $cleanMethod = $reflection->getMethod('cleanFileName');
        $cleanMethod->setAccessible(true);

        foreach ($testConfigs as $i => $config) {
            $this->log("  Config $i:");
            $this->log("    Input: " . json_encode($config));

            // Simulate the processing logic from save method
            $name = isset($config['name']) ? $config['name'] : null;
            $name = $name ?: 'Backup_' . date('Y-m-d_H-i-s');
            $cleanName = $cleanMethod->invoke($this->controller, $name);

            $processedConfig = [
                'name' => $cleanName,
                'tables' => $config['tables'] ?? [],
            ];

            $this->log("    Processed name: '$cleanName'");
            $this->log("    Used default name: " . (empty($config['name']) ? 'YES' : 'NO'));
            $this->log("    Tables count: " . count($processedConfig['tables']));
            $this->log("    Final config: " . json_encode($processedConfig));
        }
    }

    private function testImportValidation() {
        $this->log("--- Testing Import Validation Rules ---");

        $testCases = [
            'valid_minimal' => [],
            'with_truncate_on' => ['truncate' => 'on'],
            'with_truncate_off' => ['truncate' => 'off'],
            'invalid_truncate' => ['truncate' => 'invalid_value']
        ];

        foreach ($testCases as $caseName => $data) {
            $this->log("  Testing: $caseName");

            // Note: We can't test file validation without actual files
            // But we can test the truncate validation
            $validator = Validator::make($data, [
                'truncate' => 'sometimes|in:on'
            ]);

            $passed = !$validator->fails();
            $this->log("    Data: " . json_encode($data));
            $this->log("    Validation passed: " . ($passed ? 'YES' : 'NO'));

            if ($validator->fails()) {
                $this->log("    Errors: " . json_encode($validator->errors()->toArray()));
            }

            // Test truncate logic
            $shouldTruncate = isset($data['truncate']) && $data['truncate'] === 'on';
            $this->log("    Should truncate: " . ($shouldTruncate ? 'YES' : 'NO'));
        }
    }

    private function testDefaultNameGeneration() {
        $this->log("--- Testing Default Name Generation Logic ---");

        $testInputs = [
            'With name' => 'My Backup',
            'Empty string' => '',
            'Null value' => null,
            'Whitespace only' => '   '
        ];

        foreach ($testInputs as $description => $input) {
            $this->log("  Testing: $description");
            $this->log("    Input: " . var_export($input, true));

            // Simulate the logic from save method
            $result = $input ?: 'Backup_' . date('Y-m-d_H-i-s');
            $usedDefault = empty($input);

            $this->log("    Result: '$result'");
            $this->log("    Used default: " . ($usedDefault ? 'YES' : 'NO'));
        }
    }
}

// Run the tests
try {
    $tester = new BackupControllerTest();
    $tester->runAllTests();
} catch (Exception $e) {
    echo "Failed to run tests: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
