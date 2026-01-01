<?php
/**
 * Create a cache-busting version of the analytics view
 * This will help verify if jQuery errors are resolved by adding a unique timestamp
 */

$analyticsFile = __DIR__ . '/../../resources/views/admin/analytics/index.blade.php';

if (!file_exists($analyticsFile)) {
    echo "❌ Analytics file not found: $analyticsFile\n";
    exit(1);
}

$content = file_get_contents($analyticsFile);

// Add cache busting comment at the top of script section
$cacheBuster = "/* Cache Buster: " . date('Y-m-d H:i:s') . " - jQuery fixes applied */\n";

// Find the script section and add cache buster
if (preg_match('/<script[^>]*>/', $content, $matches, PREG_OFFSET_CAPTURE)) {
    $scriptStart = $matches[0][1] + strlen($matches[0][0]);
    $beforeScript = substr($content, 0, $scriptStart);
    $afterScript = substr($content, $scriptStart);
    
    $newContent = $beforeScript . "\n" . $cacheBuster . $afterScript;
    
    if (file_put_contents($analyticsFile, $newContent)) {
        echo "✅ Cache buster added to analytics view\n";
        echo "📅 Timestamp: " . date('Y-m-d H:i:s') . "\n";
        echo "🔄 This should force browser to reload the script section\n\n";
        
        echo "🔧 To verify fixes:\n";
        echo "1. Clear browser cache (Ctrl+Shift+Delete)\n";
        echo "2. Hard refresh the analytics page (Ctrl+Shift+R)\n";
        echo "3. Check browser console for any remaining jQuery errors\n";
        echo "4. If errors persist, check line numbers - they should be different now\n\n";
        
        exit(0);
    } else {
        echo "❌ Failed to write cache buster to file\n";
        exit(1);
    }
} else {
    echo "❌ Could not find script section in analytics file\n";
    exit(1);
}
?>
