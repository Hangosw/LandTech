<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $slug = \App\Models\Property::first()->slug ?? null;
    $request = \Illuminate\Http\Request::create('/thue/' . $slug);
    $app->make('router')->get('/thue/{slug}', ['uses' => 'App\Http\Controllers\PageController@rentDetail'])->name('rent.detail');
    
    $controller = new \App\Http\Controllers\PageController();
    $response = $controller->rentDetail($slug, $request);
    file_put_contents('output.html', $response->render());
    echo "Saved to output.html\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
