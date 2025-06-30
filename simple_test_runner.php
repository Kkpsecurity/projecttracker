<?php

/**
 * Simple HB837 Test Runner for ProjectTracker Fresh (Rolive)
 * 
 * Quick and simple test runner for HB837 module in the fresh Laravel installation
 */

function runHB837Tests() {
    echo "=== HB837 Test Runner (ProjectTracker Fresh) ===\n";
    echo "Started: " . date('Y-m-d H:i:s') . "\n\n";
    
    $tests = [
        'All Tests' => 'php artisan test',
        'HB837 Unit Tests' => 'php artisan test --filter=HB837',
        'Import Tests' => 'php artisan test --filter=Import',
        'Feature Tests' => 'php artisan test tests/Feature/',
        'Unit Tests' => 'php artisan test tests/Unit/'
    ];
    
    $results = [];
    
    foreach ($tests as $name => $command) {
        echo "Running {$name}...\n";
        echo str_repeat('-', 40) . "\n";
        
        $startTime = microtime(true);
        
        // Execute command and capture output
        $output = [];
        $returnCode = 0;
        exec($command . ' 2>&1', $output, $returnCode);
        
        $duration = microtime(true) - $startTime;
        $success = $returnCode === 0;
        
        // Display output
        foreach ($output as $line) {
            echo $line . "\n";
        }
        
        // Show result
        $status = $success ? '✅ PASSED' : '❌ FAILED';
        echo "\n{$status} - Duration: " . number_format($duration, 2) . "s\n\n";
        
        $results[$name] = ['success' => $success, 'duration' => $duration];
    }
    
    // Summary
    echo str_repeat('=', 50) . "\n";
    echo "SUMMARY\n";
    echo str_repeat('=', 50) . "\n";
    
    $totalTests = count($results);
    $passedTests = 0;
    
    foreach ($results as $name => $result) {
        $status = $result['success'] ? '✅' : '❌';
        echo "{$status} {$name} (" . number_format($result['duration'], 2) . "s)\n";
        if ($result['success']) $passedTests++;
    }
    
    echo "\nTotal: {$passedTests}/{$totalTests} tests passed\n";
    
    if ($passedTests === $totalTests) {
        echo "🎉 All tests passed!\n";
        return true;
    } else {
        echo "⚠️  Some tests failed\n";
        return false;
    }
}

// Run the tests
$success = runHB837Tests();
exit($success ? 0 : 1);
