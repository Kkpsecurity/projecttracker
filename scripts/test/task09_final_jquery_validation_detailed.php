<?php
/**
 * Final detailed jQuery validation for analytics view
 * Checks for specific unsafe jQuery patterns and provides exact fixes
 */

$file = __DIR__ . '/../../resources/views/admin/analytics/index.blade.php';

if (!file_exists($file)) {
    echo "❌ File not found: $file\n";
    exit(1);
}

$content = file_get_contents($file);
$lines = explode("\n", $content);

echo "=== DETAILED JQUERY VALIDATION FOR ANALYTICS VIEW ===\n\n";

$issues = [];
$totalLines = count($lines);

// Look for unsafe jQuery patterns
$unsafePatterns = [
    'direct_jquery' => '/^\s*\$\(/',
    'jquery_get' => '/^\s*\$\.get\(/',
    'jquery_post' => '/^\s*\$\.post\(/',
    'jquery_ajax' => '/^\s*\$\.ajax\(/',
    'document_ready' => '/^\s*\$\(document\)\.ready\(/',
    'window_load' => '/^\s*\$\(window\)\.load\(/'
];

$safeContexts = [];
$inSafeContext = false;
$safeContextDepth = 0;

// First pass: identify safe contexts (safeJQuery wrappers)
for ($i = 0; $i < $totalLines; $i++) {
    $line = $lines[$i];
    $lineNum = $i + 1;
    
    if (preg_match('/safeJQuery\s*\(\s*function\s*\(\s*\$\s*\)\s*\{/', $line)) {
        $inSafeContext = true;
        $safeContextDepth = substr_count($line, '{') - substr_count($line, '}');
        $safeContexts[$lineNum] = ['start' => $lineNum, 'depth' => $safeContextDepth];
        continue;
    }
    
    if ($inSafeContext) {
        $safeContextDepth += substr_count($line, '{') - substr_count($line, '}');
        if ($safeContextDepth <= 0) {
            $inSafeContext = false;
            $safeContextDepth = 0;
            // Mark end of safe context
            foreach ($safeContexts as $key => &$context) {
                if (!isset($context['end'])) {
                    $context['end'] = $lineNum;
                    break;
                }
            }
        }
    }
}

// Second pass: check for unsafe jQuery usage
for ($i = 0; $i < $totalLines; $i++) {
    $line = $lines[$i];
    $lineNum = $i + 1;
    
    // Skip comments and Blade directives
    if (preg_match('/^\s*\/\/|^\s*\/\*|^\s*\*|^\s*@/', $line)) {
        continue;
    }
    
    // Check if line is in a safe context
    $isInSafeContext = false;
    foreach ($safeContexts as $context) {
        if (isset($context['start']) && isset($context['end']) && 
            $lineNum >= $context['start'] && $lineNum <= $context['end']) {
            $isInSafeContext = true;
            break;
        }
    }
    
    // Check for unsafe patterns
    foreach ($unsafePatterns as $patternName => $pattern) {
        if (preg_match($pattern, $line)) {
            if (!$isInSafeContext) {
                $issues[] = [
                    'line' => $lineNum,
                    'type' => $patternName,
                    'content' => trim($line),
                    'severity' => 'HIGH'
                ];
            }
        }
    }
}

// Check if jQuery is properly loaded
$jqueryLoadCheck = preg_match('/script.*jquery/i', $content);
$safeJqueryDefined = preg_match('/function\s+safeJQuery|window\.safeJQuery\s*=/', $content);
$globalOverride = preg_match('/window\.\$\s*=\s*function/', $content);

echo "📋 JQUERY LOADING ANALYSIS:\n";
echo "✅ jQuery loading detected: " . ($jqueryLoadCheck ? "YES" : "NO") . "\n";
echo "✅ safeJQuery function defined: " . ($safeJqueryDefined ? "YES" : "NO") . "\n";
echo "✅ Global $ override: " . ($globalOverride ? "YES" : "NO") . "\n\n";

echo "🔍 SAFE CONTEXT ANALYSIS:\n";
foreach ($safeContexts as $context) {
    if (isset($context['start']) && isset($context['end'])) {
        echo "✅ Safe context: lines {$context['start']}-{$context['end']}\n";
    }
}
echo "\n";

if (empty($issues)) {
    echo "✅ NO UNSAFE JQUERY USAGE FOUND!\n\n";
    echo "🎉 All jQuery calls appear to be properly wrapped in safeJQuery functions.\n";
    echo "🎉 The browser errors might be from cached content or external scripts.\n\n";
    
    echo "🔧 TROUBLESHOOTING SUGGESTIONS:\n";
    echo "1. Clear browser cache and hard refresh (Ctrl+Shift+R)\n";
    echo "2. Check browser Developer Tools for the exact error source\n";
    echo "3. Verify that AdminLTE scripts are loading properly\n";
    echo "4. Check if external CDN scripts are accessible\n\n";
    
} else {
    echo "❌ FOUND " . count($issues) . " UNSAFE JQUERY USAGE(S):\n\n";
    
    foreach ($issues as $issue) {
        echo "🚨 Line {$issue['line']} ({$issue['severity']}): {$issue['type']}\n";
        echo "   Code: {$issue['content']}\n";
        echo "   Fix: Wrap in safeJQuery(function($) { ... });\n\n";
    }
}

echo "📊 SUMMARY:\n";
echo "Total lines checked: $totalLines\n";
echo "Safe contexts found: " . count($safeContexts) . "\n";
echo "Issues found: " . count($issues) . "\n";
echo "Status: " . (empty($issues) ? "✅ SAFE" : "❌ NEEDS FIXING") . "\n";

if (empty($issues)) {
    exit(0);
} else {
    exit(1);
}
?>
