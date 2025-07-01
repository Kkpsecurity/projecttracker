<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

echo "🧪 Smart Import Column Mapping Test\n";
echo "====================================\n\n";

// Test file path
$testFile = 'setup/TEST SHEET 02 - Executed & Contacts.xlsx';

if (!file_exists($testFile)) {
    echo "❌ Test file not found: {$testFile}\n";
    exit(1);
}

echo "✅ Test file found: {$testFile}\n";

try {
    // Load spreadsheet
    $spreadsheet = IOFactory::load($testFile);
    $worksheet = $spreadsheet->getActiveSheet();
    $data = $worksheet->toArray();

    if (empty($data)) {
        throw new Exception('No data found in file');
    }

    // Extract headers
    $headers = array_shift($data);
    $headers = array_map('trim', $headers);

    echo "📊 File Analysis:\n";
    echo "   - Total rows: " . count($data) . "\n";
    echo "   - Columns: " . count($headers) . "\n\n";

    echo "📋 Headers found:\n";
    foreach ($headers as $i => $header) {
        echo "   " . ($i + 1) . ". {$header}\n";
    }
    echo "\n";

    // Test improved column mapping
    $fieldMappings = [
        'property_name' => ['property name', 'property', 'building name', 'complex name', 'site name'],
        'address' => ['address', 'street', 'location', 'street address'],
        'city' => ['city', 'town'],
        'county' => ['county', 'parish'],
        'state' => ['state', 'province', 'st'],
        'zip' => ['zip', 'zipcode', 'postal', 'postal code'],
        'phone' => ['phone', 'telephone', 'tel', 'contact phone', 'phone number'],
        'management_company' => ['management', 'company', 'mgmt', 'management company', 'mgmt company'],
        'owner_name' => ['owner', 'property owner', 'landlord', 'owner name'],
        'property_type' => ['type', 'property type', 'building type'],
        'units' => ['units', 'unit count', 'number of units', '# units', 'total units'],
        'securitygauge_crime_risk' => ['crime risk', 'risk', 'security risk', 'crime', 'risk level', 'securitygauge crime risk'],
        'macro_client' => ['macro client', 'client', 'parent company'],
        'macro_contact' => ['macro contact', 'primary contact', 'main contact'],
        'macro_email' => ['macro email', 'primary email', 'main email'],
        'property_manager_name' => ['property manager', 'pm', 'manager name', 'property manager name'],
        'property_manager_email' => ['pm email', 'manager email', 'property manager email'],
        'regional_manager_name' => ['regional manager', 'rm', 'regional', 'regional manager name'],
        'regional_manager_email' => ['rm email', 'regional email', 'regional manager email'],
        'report_status' => ['status', 'report status', 'progress'],
        'contracting_status' => ['contract status', 'contract', 'phase', 'contracting status'],
        'scheduled_date_of_inspection' => ['inspection date', 'scheduled', 'date', 'scheduled date', 'scheduled date of inspection'],
        'quoted_price' => ['price', 'quote', 'quoted price', 'amount'],
        'sub_fees_estimated_expenses' => ['sub fees', 'estimated expenses', 'sub fees estimated expenses', 'expenses', 'additional fees'],
        'financial_notes' => ['financial notes', 'finance notes', 'financial', 'billing notes'],
        'consultant_notes' => ['consultant notes', 'notes', 'agent notes', 'inspector notes'],
        'general_notes' => ['general notes', 'comments', 'remarks', 'additional notes'],
        'assigned_consultant' => ['assigned consultant', 'consultant', 'assigned to', 'inspector', 'agent']
    ];

    function calculateSimilarity($str1, $str2)
    {
        $len1 = strlen($str1);
        $len2 = strlen($str2);

        if ($len1 == 0) return $len2 == 0 ? 1 : 0;
        if ($len2 == 0) return 0;

        // Check for exact match first
        if ($str1 === $str2) {
            return 1.0;
        }

        // Check for substring matches
        if (strpos($str1, $str2) !== false || strpos($str2, $str1) !== false) {
            $minLen = min($len1, $len2);
            $maxLen = max($len1, $len2);
            return 0.8 + (0.2 * ($maxLen > 0 ? $minLen / $maxLen : 0));
        }

        // Calculate Levenshtein distance with better threshold
        $levenshtein = levenshtein($str1, $str2);
        $maxLen = max($len1, $len2);
        $similarity = 1 - ($levenshtein / $maxLen);

        // Check for word overlap
        $words1 = explode(' ', $str1);
        $words2 = explode(' ', $str2);
        $overlap = count(array_intersect($words1, $words2));
        $totalWords = count(array_unique(array_merge($words1, $words2)));

        if ($overlap > 0 && $similarity > 0.3) {
            $wordBoost = ($overlap / $totalWords) * 0.3;
            $similarity += $wordBoost;
        }

        return min(1, max(0, $similarity));
    }

    echo "🎯 Column Mapping Results:\n";
    echo "==========================\n";

    $mappings = [];
    $usedFields = [];
    $lowConfidenceCount = 0;

    foreach ($headers as $header) {
        $bestMatch = null;
        $bestScore = 0;
        $headerLower = strtolower(trim($header));

        foreach ($fieldMappings as $field => $patterns) {
            // Skip field if already used with high confidence
            if (isset($usedFields[$field]) && $usedFields[$field] > 0.8) {
                continue;
            }

            foreach ($patterns as $pattern) {
                $score = calculateSimilarity($headerLower, strtolower($pattern));

                // Increase minimum threshold to 0.6 for better accuracy
                if ($score >= 0.6 && $score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $field;
                }
            }
        }

        $confidence = round($bestScore * 100);
        $status = $confidence >= 80 ? '✅' : ($confidence >= 60 ? '⚠️' : '❌');

        if ($confidence < 60) {
            $lowConfidenceCount++;
        }

        echo "{$status} {$header} → " . ($bestMatch ?: 'unmapped') . " ({$confidence}% confidence)\n";

        $mappings[] = [
            'source_column' => $header,
            'target_field' => $bestMatch ?: 'unmapped',
            'confidence' => $bestScore
        ];

        // Mark field as used if mapped with reasonable confidence
        if ($bestMatch && $bestScore > 0.7) {
            $usedFields[$bestMatch] = $bestScore;
        }
    }

    echo "\n📈 Summary:\n";
    echo "   - Total columns: " . count($headers) . "\n";
    echo "   - Mapped columns: " . count(array_filter($mappings, fn($m) => $m['target_field'] !== 'unmapped')) . "\n";
    echo "   - Low confidence mappings: {$lowConfidenceCount}\n";

    if ($lowConfidenceCount > 0) {
        echo "\n⚠️ Low Confidence Mappings (require manual review):\n";
        foreach ($mappings as $mapping) {
            if ($mapping['confidence'] < 0.6) {
                $confidence = round($mapping['confidence'] * 100);
                echo "   - '{$mapping['source_column']}' → '{$mapping['target_field']}' ({$confidence}%)\n";
            }
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
