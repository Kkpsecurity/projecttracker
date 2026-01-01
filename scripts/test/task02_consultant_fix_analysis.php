<?php
/**
 * Task 02 Consultant Bug Fix
 * Fixes the consultant assignment logic in HB837Import.php
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "==========================================\n";
echo "  CONSULTANT ASSIGNMENT BUG FIX ANALYSIS \n";
echo "==========================================\n\n";

echo "IDENTIFIED ISSUE:\n";
echo "The processConsultantId() method in HB837Import.php has flawed logic:\n\n";

echo "CURRENT FLAWED LOGIC:\n";
echo "1. processConsultantId() always creates new consultants using updateOrCreate()\n";
echo "2. resolveConsultant() tries to find existing but only searches first_name\n";
echo "3. Import uses processConsultantId() which bypasses the existing consultant lookup\n\n";

echo "RECOMMENDED FIX:\n";
echo "Modify the processConsultantId() method to:\n";
echo "1. First try to find existing consultant by full name match\n";
echo "2. Then try case-insensitive partial matching\n";
echo "3. Only create new consultant if no matches found\n\n";

echo "PROPOSED SOLUTION CODE:\n";
echo "=======================\n";

$proposedCode = <<<'PHP'
private function processConsultantId(?string $name = null): ?int
{
    // Bail on empty or non-alphabetic values (e.g. a date)
    if (empty($name) || ! preg_match('/[A-Za-z]/', $name)) {
        return null;
    }

    $name = trim($name);
    
    // Step 1: Try exact full name match (case insensitive)
    $consultant = Consultant::whereRaw("LOWER(CONCAT(first_name, ' ', last_name)) = LOWER(?)", [$name])->first();
    if ($consultant) {
        return $consultant->id;
    }
    
    // Step 2: Try partial match on concatenated name
    $consultant = Consultant::whereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE LOWER(?)", ["%{$name}%"])->first();
    if ($consultant) {
        return $consultant->id;
    }
    
    // Step 3: Try first name only match
    $nameParts = explode(' ', $name);
    $firstName = $nameParts[0];
    $consultant = Consultant::where('first_name', 'ILIKE', "%{$firstName}%")->first();
    if ($consultant) {
        return $consultant->id;
    }
    
    // Step 4: Try last name only match (if available)
    if (count($nameParts) > 1) {
        $lastName = end($nameParts);
        $consultant = Consultant::where('last_name', 'ILIKE', "%{$lastName}%")->first();
        if ($consultant) {
            return $consultant->id;
        }
    }
    
    // Step 5: Only create new consultant if absolutely no match found
    // AND log this for review
    Log::warning('Creating new consultant - no existing match found', [
        'name' => $name,
        'existing_consultants' => Consultant::pluck('first_name', 'last_name')->toArray()
    ]);
    
    // Split into first/last (if no last, mirror first)
    [$first, $last] = array_pad(explode(' ', $name, 2), 2, $firstName);

    // Build a predictable email
    $email = Str::slug("{$first}.{$last}").'@example.com';

    // Find or create by first & last name; update email if missing
    $consultant = Consultant::updateOrCreate(
        ['first_name' => $first, 'last_name' => $last],
        ['email' => $email]
    );

    return $consultant->id;
}
PHP;

echo $proposedCode . "\n\n";

echo "TESTING THE FIX:\n";
echo "================\n";

// Test the improved logic
$testNames = ['Craig Gundry', 'craig gundry', 'Craig', 'Gundry', 'Michael Torres', 'Jennifer'];

foreach ($testNames as $testName) {
    echo "Testing: '{$testName}'\n";
    
    // Simulate the improved logic
    $name = trim($testName);
    
    // Step 1: Exact match
    $exact = Illuminate\Support\Facades\DB::table('consultants')
        ->whereRaw("LOWER(CONCAT(first_name, ' ', last_name)) = LOWER(?)", [$name])
        ->first();
    
    if ($exact) {
        echo "✅ Found exact match: {$exact->first_name} {$exact->last_name} (ID: {$exact->id})\n";
        continue;
    }
    
    // Step 2: Partial match
    $partial = Illuminate\Support\Facades\DB::table('consultants')
        ->whereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE LOWER(?)", ["%{$name}%"])
        ->first();
    
    if ($partial) {
        echo "✅ Found partial match: {$partial->first_name} {$partial->last_name} (ID: {$partial->id})\n";
        continue;
    }
    
    // Step 3: First name match
    $nameParts = explode(' ', $name);
    $firstName = $nameParts[0];
    $firstNameMatch = Illuminate\Support\Facades\DB::table('consultants')
        ->whereRaw("LOWER(first_name) LIKE LOWER(?)", ["%{$firstName}%"])
        ->first();
    
    if ($firstNameMatch) {
        echo "✅ Found first name match: {$firstNameMatch->first_name} {$firstNameMatch->last_name} (ID: {$firstNameMatch->id})\n";
        continue;
    }
    
    // Step 4: Last name match (if available)
    if (count($nameParts) > 1) {
        $lastName = end($nameParts);
        $lastNameMatch = Illuminate\Support\Facades\DB::table('consultants')
            ->whereRaw("LOWER(last_name) LIKE LOWER(?)", ["%{$lastName}%"])
            ->first();
        
        if ($lastNameMatch) {
            echo "✅ Found last name match: {$lastNameMatch->first_name} {$lastNameMatch->last_name} (ID: {$lastNameMatch->id})\n";
            continue;
        }
    }
    
    echo "❌ No match found - would create new consultant\n";
    echo "\n";
}

echo "\nIMPLEMENTATION STEPS:\n";
echo "1. Backup current HB837Import.php\n";
echo "2. Replace processConsultantId() method with improved version\n";
echo "3. Test with known consultant names\n";
echo "4. Run import with TEST SHEET to validate\n";
echo "5. Remove any duplicate consultants created during testing\n";

echo "\nNEXT ACTION: Apply this fix to app/Imports/HB837Import.php\n";
echo "==========================================\n";
