<?php
/**
 * Task 02 Consultant Investigation Script
 * Analyzes consultant assignment issues during import updates
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "==========================================\n";
echo "  CONSULTANT ASSIGNMENT INVESTIGATION    \n";
echo "==========================================\n\n";

// 1. Consultant Table Analysis
echo "=== 1. CONSULTANT TABLE ANALYSIS ===\n";
try {
    // Check if consultants table exists and get structure
    if (Illuminate\Support\Facades\Schema::hasTable('consultants')) {
        $consultants = Illuminate\Support\Facades\DB::table('consultants')->get();
        echo "✅ Consultants table found with " . $consultants->count() . " records\n\n";
        
        if ($consultants->count() > 0) {
            echo "Available consultants:\n";
            foreach ($consultants as $index => $consultant) {
                $id = $consultant->id ?? 'N/A';
                $name = $consultant->name ?? $consultant->full_name ?? $consultant->first_name . ' ' . $consultant->last_name ?? 'Unknown';
                $email = $consultant->email ?? 'N/A';
                echo "ID {$id}: {$name}";
                if ($email !== 'N/A') echo " ({$email})";
                echo "\n";
                
                // Show all available fields for first consultant
                if ($index === 0) {
                    echo "  Available fields: " . implode(', ', array_keys((array)$consultant)) . "\n";
                }
            }
        }
    } else {
        echo "❌ Consultants table not found\n";
    }
} catch (Exception $e) {
    echo "❌ Error accessing consultants table: " . $e->getMessage() . "\n";
}

// 2. HB837 Consultant Field Analysis
echo "\n=== 2. HB837 CONSULTANT FIELD ANALYSIS ===\n";
try {
    $columns = Illuminate\Support\Facades\Schema::getColumnListing('hb837');
    $consultantFields = array_filter($columns, function($col) {
        return strpos(strtolower($col), 'consultant') !== false || 
               strpos(strtolower($col), 'assigned') !== false;
    });
    
    echo "Consultant-related fields in HB837:\n";
    foreach ($consultantFields as $field) {
        echo "├─ {$field}\n";
    }
    
    if (empty($consultantFields)) {
        echo "⚠️  No obvious consultant fields found. Checking common patterns...\n";
        $possibleFields = ['consultant_id', 'assigned_consultant_id', 'consultant', 'assigned_to', 'project_manager_id'];
        foreach ($possibleFields as $field) {
            if (in_array($field, $columns)) {
                echo "├─ {$field} (found)\n";
                $consultantFields[] = $field;
            }
        }
    }
} catch (Exception $e) {
    echo "❌ Error checking HB837 schema: " . $e->getMessage() . "\n";
}

// 3. Current Assignment Status
echo "\n=== 3. CURRENT ASSIGNMENT STATUS ===\n";
if (!empty($consultantFields)) {
    foreach ($consultantFields as $field) {
        try {
            $assigned = App\Models\HB837::whereNotNull($field)
                ->where($field, '!=', '')
                ->where($field, '!=', '0')
                ->count();
            $total = App\Models\HB837::count();
            $percentage = $total > 0 ? round(($assigned / $total) * 100, 1) : 0;
            
            echo "Field '{$field}':\n";
            echo "├─ Records with assignment: {$assigned}/{$total} ({$percentage}%)\n";
            
            // Show sample values
            $samples = App\Models\HB837::whereNotNull($field)
                ->where($field, '!=', '')
                ->where($field, '!=', '0')
                ->select('id', 'property_name', $field)
                ->take(3)->get();
            
            if ($samples->count() > 0) {
                echo "├─ Sample assignments:\n";
                foreach ($samples as $sample) {
                    echo "│  ├─ ID {$sample->id}: {$sample->property_name} → {$sample->$field}\n";
                }
            }
            echo "\n";
        } catch (Exception $e) {
            echo "❌ Error checking field '{$field}': " . $e->getMessage() . "\n";
        }
    }
} else {
    echo "No consultant fields identified for analysis\n";
}

// 4. Import Field Mapping Check
echo "=== 4. IMPORT FIELD MAPPING CHECK ===\n";
$configFile = 'config/hb837_field_mapping.php';
if (file_exists($configFile)) {
    $config = require $configFile;
    echo "Checking field mapping configuration for consultant fields...\n";
    
    $consultantMappings = array_filter($config, function($dbField, $csvField) {
        $dbFieldStr = is_array($dbField) ? implode(',', $dbField) : (string)$dbField;
        $csvFieldStr = (string)$csvField;
        return strpos(strtolower($dbFieldStr), 'consultant') !== false ||
               strpos(strtolower($csvFieldStr), 'consultant') !== false ||
               strpos(strtolower($dbFieldStr), 'assigned') !== false ||
               strpos(strtolower($csvFieldStr), 'assigned') !== false;
    }, ARRAY_FILTER_USE_BOTH);
    
    if (!empty($consultantMappings)) {
        echo "✅ Consultant mappings found:\n";
        foreach ($consultantMappings as $csvField => $dbField) {
            echo "├─ CSV '{$csvField}' → DB '{$dbField}'\n";
        }
    } else {
        echo "⚠️  No consultant mappings found in configuration\n";
        echo "Available mappings:\n";
        foreach ($config as $csvField => $dbField) {
            echo "├─ '{$csvField}' → '{$dbField}'\n";
        }
    }
} else {
    echo "❌ Field mapping configuration not found\n";
}

// 5. Name Matching Analysis
echo "\n=== 5. NAME MATCHING ANALYSIS ===\n";
if (isset($consultants) && $consultants->count() > 0) {
    echo "Testing name variations for consultant lookup:\n\n";
    
    foreach ($consultants as $consultant) {
        $fullName = $consultant->name ?? $consultant->full_name ?? 
                   ($consultant->first_name . ' ' . $consultant->last_name) ?? 'Unknown';
        
        echo "Consultant: {$fullName}\n";
        
        // Generate possible name variations
        $nameParts = explode(' ', $fullName);
        $variations = [
            $fullName,                                    // Full name
            $nameParts[0] ?? '',                         // First name only
            end($nameParts),                             // Last name only
            strtolower($fullName),                       // Lowercase
            ucwords(strtolower($fullName)),              // Title case
        ];
        
        // Remove duplicates and empty values
        $variations = array_unique(array_filter($variations));
        
        echo "├─ Possible variations to test:\n";
        foreach ($variations as $variation) {
            echo "│  ├─ '{$variation}'\n";
        }
        
        // Test case-insensitive matching
        echo "├─ Case-insensitive matches:\n";
        foreach ($variations as $variation) {
            $matches = Illuminate\Support\Facades\DB::table('consultants')
                ->whereRaw("LOWER(name) LIKE LOWER(?)", ["%{$variation}%"])
                ->orWhereRaw("LOWER(full_name) LIKE LOWER(?)", ["%{$variation}%"])
                ->count();
            echo "│  ├─ '{$variation}': {$matches} matches\n";
        }
        echo "\n";
    }
}

// 6. Common Issues Detection
echo "=== 6. COMMON ISSUES DETECTION ===\n";

// Check for records that might have consultant names as text instead of IDs
if (!empty($consultantFields)) {
    foreach ($consultantFields as $field) {
        try {
            $textValues = App\Models\HB837::whereNotNull($field)
                ->where($field, '!=', '')
                ->whereRaw("NOT {$field} ~ '^[0-9]+$'")  // PostgreSQL regex for non-numeric
                ->select('id', 'property_name', $field)
                ->take(5)->get();
            
            if ($textValues->count() > 0) {
                echo "⚠️  Non-numeric values found in '{$field}' (should be consultant IDs):\n";
                foreach ($textValues as $record) {
                    echo "├─ ID {$record->id}: '{$record->$field}'\n";
                }
                echo "\n";
            }
        } catch (Exception $e) {
            // Handle different database engines
            try {
                $textValues = App\Models\HB837::whereNotNull($field)
                    ->where($field, '!=', '')
                    ->whereRaw("{$field} NOT REGEXP '^[0-9]+$'")  // MySQL regex
                    ->select('id', 'property_name', $field)
                    ->take(5)->get();
                
                if ($textValues->count() > 0) {
                    echo "⚠️  Non-numeric values found in '{$field}' (should be consultant IDs):\n";
                    foreach ($textValues as $record) {
                        echo "├─ ID {$record->id}: '{$record->$field}'\n";
                    }
                    echo "\n";
                }
            } catch (Exception $e2) {
                echo "Note: Could not check for non-numeric values in '{$field}'\n";
            }
        }
    }
}

// 7. Recommendations
echo "=== 7. RECOMMENDATIONS ===\n";
echo "To fix consultant assignment issues:\n";
echo "├─ 1. Verify consultant names in import files match database exactly\n";
echo "├─ 2. Implement fuzzy name matching (LIKE '%name%', case-insensitive)\n";
echo "├─ 3. Add logging for failed consultant lookups during import\n";
echo "├─ 4. Consider name normalization (trim, case-insensitive)\n";
echo "├─ 5. Add fallback to partial name matching (first name or last name)\n";
echo "└─ 6. Create consultant name alias/mapping table for common variations\n\n";

echo "NEXT STEPS:\n";
echo "1. Check import files for consultant name format\n";
echo "2. Review import code for consultant lookup logic\n";
echo "3. Test with known consultant names from test sheets\n";
echo "==========================================\n";
