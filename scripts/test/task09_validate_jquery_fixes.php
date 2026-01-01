<?php
/**
 * Task 09: Validate jQuery fixes in analytics view
 * 
 * This script validates that all jQuery calls are properly wrapped
 * in the safeJQuery function to prevent "$ is not defined" errors.
 */

$viewFile = __DIR__ . '/../../resources/views/admin/analytics/index.blade.php';

if (!file_exists($viewFile)) {
    echo "ERROR: Analytics view file not found at: $viewFile\n";
    exit(1);
}

$content = file_get_contents($viewFile);

echo "=== Task 09: Validating jQuery Fixes ===\n\n";

// Check for unsafe jQuery usage patterns
$unsafePatterns = [
    '/^\s*\$\(/m' => 'Direct $ usage',
    '/^\s*jQuery\(/m' => 'Direct jQuery usage',
    '/\$\([\'"]/' => '$ with string selector',
    '/jQuery\([\'"]/' => 'jQuery with string selector'
];

$foundUnsafe = false;
$lineNumber = 0;
$lines = explode("\n", $content);

echo "1. Checking for unsafe jQuery patterns...\n";

foreach ($lines as $index => $line) {
    $lineNumber = $index + 1;
    
    // Skip lines that are comments or inside strings
    if (preg_match('/^\s*\/\//', $line) || preg_match('/^\s*\*/', $line)) {
        continue;
    }
    
    // Check for direct $ usage that's not properly wrapped
    if (preg_match('/^\s*\$\(/', $line)) {
        // Look for safeJQuery wrapper within 20 lines before this line
        $contextStart = max(0, $index - 20);
        $contextEnd = min(count($lines) - 1, $index);
        
        $hasSafeWrapper = false;
        $openBraces = 0;
        
        // Check if we're inside a safeJQuery block
        for ($i = $contextStart; $i <= $contextEnd; $i++) {
            $contextLine = $lines[$i];
            
            // Count braces to determine scope
            $openBraces += substr_count($contextLine, '{');
            $openBraces -= substr_count($contextLine, '}');
            
            if (strpos($contextLine, 'safeJQuery(function($)') !== false) {
                $hasSafeWrapper = true;
                $openBraces = 1; // Reset brace count from safeJQuery start
            }
        }
        
        // If we found a safeJQuery wrapper and we're still inside its scope (openBraces > 0)
        if (!($hasSafeWrapper && $openBraces > 0)) {
            echo "   ❌ UNSAFE: Line $lineNumber: " . trim($line) . "\n";
            $foundUnsafe = true;
        }
    }
}

if (!$foundUnsafe) {
    echo "   ✅ No unsafe jQuery patterns found\n";
}

echo "\n2. Checking for proper safeJQuery wrapper usage...\n";

// Count safeJQuery wrappers
$safeWrapperCount = preg_match_all('/safeJQuery\(function\(\$\)/', $content);
echo "   Found $safeWrapperCount safeJQuery wrapper(s)\n";

// Check for global $ override
if (strpos($content, 'window.$ = function()') !== false) {
    echo "   ✅ Global $ override found\n";
} else {
    echo "   ❌ Global $ override missing\n";
}

// Check for jQuery loading mechanism
if (strpos($content, 'loadJQueryIfNeeded') !== false || strpos($content, 'document.createElement(\'script\')') !== false) {
    echo "   ✅ jQuery loading mechanism found\n";
} else {
    echo "   ❌ jQuery loading mechanism missing\n";
}

echo "\n3. Checking for specific problematic lines mentioned in error...\n";

// Check lines around 1180, 1562, 1982
$problematicLines = [1180, 1562, 1982];
foreach ($problematicLines as $lineNum) {
    if (isset($lines[$lineNum - 1])) {
        $line = trim($lines[$lineNum - 1]);
        echo "   Line $lineNum: $line\n";
        
        // Check if this line contains jQuery and is properly wrapped
        if (strpos($line, '$') !== false || strpos($line, 'jQuery') !== false) {
            // Look for safeJQuery wrapper
            $contextStart = max(0, $lineNum - 15);
            $contextEnd = min(count($lines) - 1, $lineNum + 5);
            $context = array_slice($lines, $contextStart, $contextEnd - $contextStart + 1);
            
            $hasSafeWrapper = false;
            foreach ($context as $contextLine) {
                if (strpos($contextLine, 'safeJQuery(function($)') !== false) {
                    $hasSafeWrapper = true;
                    break;
                }
            }
            
            if ($hasSafeWrapper) {
                echo "     ✅ Properly wrapped in safeJQuery\n";
            } else {
                echo "     ❌ NOT wrapped in safeJQuery\n";
            }
        } else {
            echo "     ✅ No jQuery usage on this line\n";
        }
    } else {
        echo "   Line $lineNum: (line does not exist)\n";
    }
}

echo "\n4. Summary of jQuery safety measures...\n";

$measures = [
    'safeJQuery function defined' => strpos($content, 'window.safeJQuery = function') !== false,
    'Global $ override defined' => strpos($content, 'window.$ = function()') !== false,
    'jQuery loading fallback' => strpos($content, 'document.createElement(\'script\')') !== false,
    'AdminLTE compatibility' => strpos($content, 'AdminLTE') !== false || strpos($content, 'adminlte') !== false,
    'Error handling in place' => strpos($content, 'catch') !== false || strpos($content, 'try') !== false,
];

foreach ($measures as $measure => $implemented) {
    $status = $implemented ? '✅' : '❌';
    echo "   $status $measure\n";
}

echo "\n=== Validation Complete ===\n";

if (!$foundUnsafe) {
    echo "✅ All jQuery usage appears to be safe from '$ is not defined' errors\n";
    exit(0);
} else {
    echo "❌ Some unsafe jQuery usage patterns were found\n";
    exit(1);
}
