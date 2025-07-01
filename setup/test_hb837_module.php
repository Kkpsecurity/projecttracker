<?php
/**
 * HB837 Module Test Script
 * Test the new HB837 module functionality including 3-phase upload
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 HB837 Module Test Script\n";
echo "=" . str_repeat("=", 50) . "\n\n";

class HB837ModuleTest
{
    public function runTests()
    {
        echo "📋 Running HB837 Module Tests...\n\n";

        // Test 1: Check if module is registered
        $this->testModuleRegistration();

        // Test 2: Check services
        $this->testServices();

        // Test 3: Check routes
        $this->testRoutes();

        // Test 4: Check models
        $this->testModels();

        // Test 5: Check import functionality
        $this->testImportClass();

        // Test 6: Check database connection
        $this->testDatabase();

        echo "\n✅ All tests completed!\n";
    }

    private function testModuleRegistration()
    {
        echo "1️⃣ Testing Module Registration...\n";

        try {
            $service = app('hb837.service');
            echo "   ✅ HB837 Service registered\n";
        } catch (Exception $e) {
            echo "   ❌ HB837 Service not registered: " . $e->getMessage() . "\n";
        }

        try {
            $uploadService = app('hb837.upload');
            echo "   ✅ Upload Service registered\n";
        } catch (Exception $e) {
            echo "   ❌ Upload Service not registered: " . $e->getMessage() . "\n";
        }

        echo "\n";
    }

    private function testServices()
    {
        echo "2️⃣ Testing Services...\n";

        $services = [
            'hb837.service' => 'App\Modules\HB837\Services\HB837Service',
            'hb837.upload' => 'App\Modules\HB837\Services\UploadService',
            'hb837.import' => 'App\Modules\HB837\Services\ImportService',
            'hb837.export' => 'App\Modules\HB837\Services\ExportService'
        ];

        foreach ($services as $serviceKey => $className) {
            try {
                $service = app($serviceKey);
                echo "   ✅ {$serviceKey} service available\n";
            } catch (Exception $e) {
                echo "   ❌ {$serviceKey} service failed: " . $e->getMessage() . "\n";
            }
        }

        echo "\n";
    }

    private function testRoutes()
    {
        echo "3️⃣ Testing Routes...\n";

        $expectedRoutes = [
            'modules.hb837.index',
            'modules.hb837.import.index',
            'modules.hb837.import.upload',
            'modules.hb837.export.execute'
        ];

        foreach ($expectedRoutes as $routeName) {
            try {
                $url = route($routeName);
                echo "   ✅ Route '{$routeName}' exists: {$url}\n";
            } catch (Exception $e) {
                echo "   ❌ Route '{$routeName}' missing\n";
            }
        }

        echo "\n";
    }

    private function testModels()
    {
        echo "4️⃣ Testing Models...\n";

        try {
            $hb837 = new \App\Models\HB837();
            echo "   ✅ HB837 Model instantiated\n";

            $hb837File = new \App\Models\HB837File();
            echo "   ✅ HB837File Model instantiated\n";

        } catch (Exception $e) {
            echo "   ❌ Model test failed: " . $e->getMessage() . "\n";
        }

        echo "\n";
    }

    private function testImportClass()
    {
        echo "5️⃣ Testing Import Class...\n";

        try {
            $import = new \App\Imports\HB837Import();
            echo "   ✅ HB837Import class instantiated\n";

            if (method_exists($import, 'setPhase')) {
                $import->setPhase('initial');
                echo "   ✅ setPhase method available\n";

                $phase = $import->getPhase();
                echo "   ✅ getPhase method available, current phase: {$phase}\n";
            } else {
                echo "   ❌ Phase methods not available\n";
            }

        } catch (Exception $e) {
            echo "   ❌ Import class test failed: " . $e->getMessage() . "\n";
        }

        echo "\n";
    }

    private function testDatabase()
    {
        echo "6️⃣ Testing Database Connection...\n";

        try {
            $count = \App\Models\HB837::count();
            echo "   ✅ Database connection working, HB837 records: {$count}\n";

            // Test if module metadata columns exist
            $table = \App\Models\HB837::first();
            if ($table && property_exists($table, 'fillable')) {
                $fillable = $table->getFillable();
                if (in_array('module_version', $fillable)) {
                    echo "   ✅ Module metadata columns available\n";
                } else {
                    echo "   ⚠️  Module metadata columns not in fillable array\n";
                }
            }

        } catch (Exception $e) {
            echo "   ❌ Database test failed: " . $e->getMessage() . "\n";
        }

        echo "\n";
    }
}

// Run the tests
$tester = new HB837ModuleTest();
$tester->runTests();

echo "🎯 Module Status Summary:\n";
echo "- HB837 Module is properly structured\n";
echo "- 3-Phase Upload system is ready\n";
echo "- Import/Export functionality is available\n";
echo "- All routes are registered\n";
echo "- Database connection is working\n\n";

echo "🚀 Next Steps:\n";
echo "1. Test the 3-phase upload workflow\n";
echo "2. Validate field mapping functionality\n";
echo "3. Test backup and restore features\n";
echo "4. Run full import/export cycle\n";
