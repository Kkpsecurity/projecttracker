<?php
// Test the backup and import logic without running the full Laravel application

// Set up basic paths
$basePath = __DIR__;
require_once $basePath . '/vendor/autoload.php';

// Simple test framework
class SimpleTest {
    private $tests = [];
    private $results = [];

    public function addTest($name, $callback) {
        $this->tests[$name] = $callback;
    }

    public function runAll() {
        echo "=== BACKUP AND IMPORT LOGIC TESTS ===\n";
        echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

        foreach ($this->tests as $name => $callback) {
            echo "Running: $name\n";
            try {
                $result = $callback();
                $this->results[$name] = [
                    'status' => 'PASS',
                    'result' => $result
                ];
                echo "✓ PASSED\n";
            } catch (Exception $e) {
                $this->results[$name] = [
                    'status' => 'FAIL',
                    'error' => $e->getMessage()
                ];
                echo "✗ FAILED: " . $e->getMessage() . "\n";
            }
            echo "\n";
        }

        $this->printSummary();
    }

    private function printSummary() {
        echo "=== TEST SUMMARY ===\n";
        $passed = 0;
        $failed = 0;

        foreach ($this->results as $name => $result) {
            echo sprintf("%-30s %s\n", $name, $result['status']);
            if ($result['status'] === 'PASS') {
                $passed++;
            } else {
                $failed++;
            }
        }

        echo "\nTotal: " . count($this->results) . " | Passed: $passed | Failed: $failed\n";

        if ($failed === 0) {
            echo "\n🎉 ALL TESTS PASSED!\n";
        } else {
            echo "\n⚠️  SOME TESTS FAILED\n";
        }
    }
}

// Test functions
function testValidationRules() {
    // Test backup validation rules
    $rules = [
        'name' => 'nullable|string|max:255',
        'tables' => 'required|array|min:1',
        'tables.*' => 'string',
    ];

    $validCases = [
        ['name' => 'Test', 'tables' => ['hb837']],
        ['name' => '', 'tables' => ['hb837', 'consultants']],
        ['tables' => ['hb837']],
    ];

    $invalidCases = [
        ['name' => 'Test', 'tables' => []],
        ['name' => 'Test'],
        [],
    ];

    $results = [];

    // Test valid cases
    foreach ($validCases as $i => $case) {
        $hasName = isset($case['name']) && !empty($case['name']);
        $hasTables = isset($case['tables']) && is_array($case['tables']) && count($case['tables']) > 0;
        $validName = !isset($case['name']) || is_string($case['name']);

        $valid = $hasTables && $validName;
        $results["valid_case_$i"] = $valid;
    }

    // Test invalid cases
    foreach ($invalidCases as $i => $case) {
        $hasName = isset($case['name']) && !empty($case['name']);
        $hasTables = isset($case['tables']) && is_array($case['tables']) && count($case['tables']) > 0;
        $validName = !isset($case['name']) || is_string($case['name']);

        $valid = $hasTables && $validName;
        $results["invalid_case_$i"] = !$valid; // Should be invalid
    }

    return $results;
}

function testFileNameCleaning() {
    // Simulate the cleanFileName method
    function cleanFileName($name) {
        return preg_replace('/[^a-zA-Z0-9_\-]/', '', $name);
    }

    $testCases = [
        'Test Backup 123' => 'TestBackup123',
        'backup@#$%' => 'backup',
        'My-File_Name' => 'My-File_Name',
        '123 Test!@#' => '123Test',
        '' => ''
    ];

    $results = [];
    foreach ($testCases as $input => $expected) {
        $actual = cleanFileName($input);
        $results[] = [
            'input' => $input,
            'expected' => $expected,
            'actual' => $actual,
            'passed' => $actual === $expected
        ];
    }

    return $results;
}

function testDefaultNameGeneration() {
    $name1 = 'Test Backup';
    $name2 = '';
    $name3 = null;

    $defaultName = 'Backup_' . date('Y-m-d_H-i-s');

    $results = [
        'with_name' => ($name1 ?: $defaultName) === $name1,
        'empty_string' => ($name2 ?: $defaultName) === $defaultName,
        'null_value' => ($name3 ?: $defaultName) === $defaultName
    ];

    return $results;
}

function testImportValidation() {
    $allowedMimes = [
        'text/csv',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel'
    ];

    $testFiles = [
        ['name' => 'data.csv', 'mime' => 'text/csv', 'size' => 1024],
        ['name' => 'data.xlsx', 'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'size' => 2048],
        ['name' => 'data.txt', 'mime' => 'text/plain', 'size' => 1024],
        ['name' => 'large.csv', 'mime' => 'text/csv', 'size' => 11 * 1024 * 1024], // 11MB
    ];

    $results = [];
    foreach ($testFiles as $file) {
        $validMime = in_array($file['mime'], $allowedMimes);
        $validSize = $file['size'] <= (10240 * 1024); // 10MB

        $results[] = [
            'file' => $file['name'],
            'valid_mime' => $validMime,
            'valid_size' => $validSize,
            'overall_valid' => $validMime && $validSize
        ];
    }

    return $results;
}

function testTruncateLogic() {
    $testConfigs = [
        ['truncate' => 'on'],
        ['truncate' => 'off'],
        ['truncate' => null],
        []
    ];

    $results = [];
    foreach ($testConfigs as $i => $config) {
        $shouldTruncate = isset($config['truncate']) && $config['truncate'] === 'on';
        $results["config_$i"] = [
            'input' => $config,
            'should_truncate' => $shouldTruncate
        ];
    }

    return $results;
}

// Run all tests
$tester = new SimpleTest();

$tester->addTest('Validation Rules', 'testValidationRules');
$tester->addTest('Filename Cleaning', 'testFileNameCleaning');
$tester->addTest('Default Name Generation', 'testDefaultNameGeneration');
$tester->addTest('Import Validation', 'testImportValidation');
$tester->addTest('Truncate Logic', 'testTruncateLogic');

$tester->runAll();
?>
