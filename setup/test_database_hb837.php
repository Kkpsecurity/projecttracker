<?php

// Test database connection and HB837 model functionality
echo "🔍 Testing Database Connection via Artisan\n";
echo "==========================================\n\n";

// Test database connection using artisan
echo "📊 Testing Database Connection:\n";
$dbTest = shell_exec('php artisan tinker --execute="echo \"Database: \" . config(\"database.default\") . PHP_EOL; echo \"Records: \" . App\\Models\\HB837::count() . PHP_EOL;"');
echo $dbTest;

echo "\n� Testing Model Configuration:\n";
$modelTest = shell_exec('php artisan tinker --execute="$model = new App\\Models\\HB837(); echo \"Fillable fields: \" . implode(\", \", $model->getFillable()) . PHP_EOL;"');
echo $modelTest;

echo "\n🧪 Testing Record Creation:\n";
$createTest = shell_exec('php artisan tinker --execute="
try {
    $test = App\\Models\\HB837::create([
        \"property_name\" => \"Test Property - \" . date(\"Y-m-d H:i:s\"),
        \"address\" => \"123 Test Street\",
        \"city\" => \"Test City\",
        \"state\" => \"TS\",
        \"zip\" => \"12345\",
        \"report_status\" => \"not-started\",
        \"contracting_status\" => \"quoted\"
    ]);
    echo \"✅ Test record created (ID: \" . $test->id . \")\" . PHP_EOL;
    $test->delete();
    echo \"✅ Test record deleted\" . PHP_EOL;
} catch (Exception $e) {
    echo \"❌ Error: \" . $e->getMessage() . PHP_EOL;
}
"');
echo $createTest;
