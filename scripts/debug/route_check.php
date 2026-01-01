<?php

require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$targetRoutes = [
    'admin.users.index',
    'admin.hb837.inspection-calendar.index',
    'admin.hb837.inspection-calendar.events',
];

foreach ($targetRoutes as $routeName) {
    echo $routeName . ': ' . (Illuminate\Support\Facades\Route::has($routeName) ? 'yes' : 'no') . PHP_EOL;
}

// Print a small sample of admin.* routes for debugging.
$adminRouteNames = [];
foreach (Illuminate\Support\Facades\Route::getRoutes() as $route) {
    $name = $route->getName();
    if (is_string($name) && str_starts_with($name, 'admin.')) {
        $adminRouteNames[] = $name;
    }
}

sort($adminRouteNames);

echo 'admin.* route count: ' . count($adminRouteNames) . PHP_EOL;
echo 'first 25 admin.* routes:' . PHP_EOL;
foreach (array_slice($adminRouteNames, 0, 25) as $name) {
    echo ' - ' . $name . PHP_EOL;
}
