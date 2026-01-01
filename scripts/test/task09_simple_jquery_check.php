<?php
/**
 * Task 09: Simple jQuery error check
 * 
 * This script identifies the actual problematic jQuery lines
 * by parsing the file and checking wrapper scopes more accurately.
 */

$viewFile = __DIR__ . '/../../resources/views/admin/analytics/index.blade.php';

if (!file_exists($viewFile)) {
    echo "ERROR: Analytics view file not found at: $viewFile\n";
    exit(1);
}

$content = file_get_contents($viewFile);
$lines = explode("\n", $content);

echo "=== Task 09: jQuery Error Analysis ===\n\n";

// Detailed analysis of actual structure
$inSafeJQueryBlock = false;
$braceDepth = 0;
$safeJQueryDepth = 0;
$problematicLines = [];

for ($i = 0; $i < count($lines); $i++) {
    $line = $lines[$i];
    $lineNumber = $i + 1;
    
    // Track safeJQuery blocks
    if (strpos($line, 'safeJQuery(function($)') !== false) {
        $inSafeJQueryBlock = true;
        $safeJQueryDepth = substr_count($line, '{') - substr_count($line, '}');
        continue;
    }
    
    // Track brace depth if in safeJQuery block
    if ($inSafeJQueryBlock) {
        $safeJQueryDepth += substr_count($line, '{') - substr_count($line, '}');
        
        // If we've closed all braces, we're out of the safeJQuery block
        if ($safeJQueryDepth <= 0) {
            $inSafeJQueryBlock = false;
        }
    }
    
    // Check for direct jQuery usage
    if (preg_match('/^\s*\$\(/', $line)) {
        if (!$inSafeJQueryBlock) {
            $problematicLines[] = [
                'line' => $lineNumber,
                'content' => trim($line),
                'context' => array_slice($lines, max(0, $i-2), 5)
            ];
        }
    }
}

echo "Found " . count($problematicLines) . " potentially problematic jQuery lines:\n\n";

foreach ($problematicLines as $problem) {
    echo "Line {$problem['line']}: {$problem['content']}\n";
    echo "Context:\n";
    foreach ($problem['context'] as $contextLine) {
        echo "  " . trim($contextLine) . "\n";
    }
    echo "\n";
}

// Check if the lines mentioned in the error (1180, 1562, 1982) actually contain jQuery
$errorLines = [1180, 1562, 1982];
echo "Checking specific error lines mentioned:\n";
foreach ($errorLines as $lineNum) {
    if (isset($lines[$lineNum - 1])) {
        $content = trim($lines[$lineNum - 1]);
        echo "Line $lineNum: $content\n";
        if (strpos($content, '$') !== false || strpos($content, 'jQuery') !== false) {
            echo "  ⚠️  Contains jQuery/$ usage\n";
        } else {
            echo "  ✅ No jQuery usage\n";
        }
    } else {
        echo "Line $lineNum: (does not exist)\n";
    }
}

echo "\n=== Summary ===\n";
if (count($problematicLines) == 0) {
    echo "✅ All jQuery usage appears to be properly wrapped\n";
    echo "✅ The errors might be from outdated line numbers or browser cache\n";
} else {
    echo "❌ Found " . count($problematicLines) . " unwrapped jQuery calls\n";
    echo "⚠️  These need to be wrapped in safeJQuery()\n";
}
