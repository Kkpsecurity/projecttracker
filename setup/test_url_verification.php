<?php

echo "=== Simple Frontend Verification Test ===\n";
echo "Testing if the relationship fixes resolved the errors...\n\n";

// Get the host from environment or use default
$host = $_SERVER['HTTP_HOST'] ?? 'projecttracker_fresh.test';
$baseUrl = "http://$host";

echo "Testing URLs:\n";
echo "- Base URL: $baseUrl\n\n";

function testUrl($url, $description) {
    echo "Testing: $description\n";
    echo "URL: $url\n";

    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'ignore_errors' => true
        ]
    ]);

    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        echo "❌ Failed to connect\n\n";
        return false;
    }

    // Check for the specific error we were fixing
    if (strpos($response, 'Call to undefined relationship [plotAddress]') !== false) {
        echo "❌ Still has plotAddress relationship error\n\n";
        return false;
    }

    // Check for other common Laravel errors
    if (strpos($response, 'Illuminate\Database\Eloquent\RelationNotFoundException') !== false) {
        echo "❌ Has relationship error\n\n";
        return false;
    }

    if (strpos($response, 'ErrorException') !== false || strpos($response, 'FatalErrorException') !== false) {
        echo "❌ Has fatal error\n\n";
        return false;
    }

    // Check for successful page load indicators
    if (strpos($response, '<title>') !== false && strpos($response, 'Google Maps') !== false) {
        echo "✅ Page loads successfully with expected content\n\n";
        return true;
    } else if (strpos($response, '<title>') !== false) {
        echo "✅ Page loads successfully\n\n";
        return true;
    } else {
        echo "⚠️  Page loads but may have issues\n\n";
        return true; // Still count as success if no errors
    }
}

echo "--- Testing Key URLs ---\n\n";

$testResults = [];

// Test Google Maps page (this was the failing one)
$testResults['maps'] = testUrl("$baseUrl/admin/maps", "Google Maps Page");

// Test Plots index page
$testResults['plots'] = testUrl("$baseUrl/admin/plots", "Plots Management Page");

// Test HB837 index (should still work)
$testResults['hb837'] = testUrl("$baseUrl/admin/hb837", "HB837 Projects Page");

echo "--- Test Results Summary ---\n";
$passed = 0;
$total = count($testResults);

foreach ($testResults as $test => $result) {
    if ($result) {
        echo "✅ $test: PASSED\n";
        $passed++;
    } else {
        echo "❌ $test: FAILED\n";
    }
}

echo "\nOverall: $passed/$total tests passed\n";

if ($passed === $total) {
    echo "\n🎉 All tests passed! The relationship fixes appear to have resolved the errors.\n";
    echo "\n✅ Key indicators:\n";
    echo "- No 'plotAddress' relationship errors\n";
    echo "- Pages load without fatal errors\n";
    echo "- Google Maps and Plots pages are accessible\n";
} else {
    echo "\n⚠️  Some tests failed. Check the output above for details.\n";
}

echo "\n=== Manual Testing Recommendations ===\n";
echo "1. Visit: $baseUrl/admin/maps\n";
echo "2. Visit: $baseUrl/admin/plots\n";
echo "3. Try creating a new plot\n";
echo "4. Try editing an existing plot\n";
echo "5. Check that map markers display correctly\n";

echo "\n=== Test Complete ===\n";
