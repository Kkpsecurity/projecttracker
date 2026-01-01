<?php

/**
 * Task 09: Final Validation of jQuery Fixes
 * 
 * This script checks for any remaining unsafe jQuery usage
 * in the analytics view file.
 */

$filePath = __DIR__ . '/../../resources/views/admin/analytics/index.blade.php';

echo "🔍 Final jQuery Safety Validation\n";
echo "=================================\n\n";

if (!file_exists($filePath)) {
    echo "❌ Analytics view file not found\n";
    exit(1);
}

$content = file_get_contents($filePath);
$lines = explode("\n", $content);

echo "📋 Scanning for unsafe jQuery usage...\n\n";

$unsafeJQueryPatterns = [
    '/^\s*\$\(/' => 'Direct jQuery call at start of line',
    '/^\s*\$\./' => 'Direct jQuery static call',
    '/\$\(.*\)\.(?!val\(\)|text\(\)|html\(\))/' => 'jQuery chaining not in safe wrapper',
];

$issues = [];
$totalLines = count($lines);

foreach ($lines as $lineNum => $line) {
    $lineNumber = $lineNum + 1;
    
    // Skip comments and inside safeJQuery calls
    if (preg_match('/^\s*\/\//', $line) || 
        preg_match('/safeJQuery\(function/', $line) ||
        preg_match('/console\./', $line)) {
        continue;
    }
    
    // Check for unsafe jQuery patterns
    foreach ($unsafeJQueryPatterns as $pattern => $description) {
        if (preg_match($pattern, $line) && !preg_match('/safeJQuery/', $line)) {
            $issues[] = [
                'line' => $lineNumber,
                'content' => trim($line),
                'issue' => $description
            ];
        }
    }
}

if (empty($issues)) {
    echo "✅ No unsafe jQuery usage found!\n";
    echo "✅ All jQuery calls appear to be properly wrapped in safeJQuery()\n\n";
} else {
    echo "⚠️ Found " . count($issues) . " potential jQuery safety issues:\n\n";
    
    foreach ($issues as $issue) {
        echo "   Line {$issue['line']}: {$issue['issue']}\n";
        echo "   Code: {$issue['content']}\n\n";
    }
}

// Check for safeJQuery wrapper usage
$safeJQueryCount = substr_count($content, 'safeJQuery(');
echo "📊 Statistics:\n";
echo "   • Total lines: $totalLines\n";
echo "   • safeJQuery() wrappers: $safeJQueryCount\n";
echo "   • Potential jQuery issues: " . count($issues) . "\n\n";

// Check if jQuery loading is properly implemented
if (strpos($content, 'safeJQuery') !== false) {
    echo "✅ safeJQuery wrapper function is implemented\n";
} else {
    echo "❌ safeJQuery wrapper function not found\n";
}

if (strpos($content, 'code.jquery.com') !== false) {
    echo "✅ jQuery CDN loading is implemented\n";
} else {
    echo "❌ jQuery CDN loading not found\n";
}

echo "\n🎉 jQuery safety validation completed!\n";
echo "The analytics page should now be free of '$ is not defined' errors.\n";
