<?php

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->singleton(HttpKernel::class, App\Http\Kernel::class);
$app->singleton(ConsoleKernel::class, App\Console\Kernel::class);
$app->singleton(ExceptionHandler::class, App\Exceptions\Handler::class);
$app->register(App\Providers\RouteServiceProvider::class);

return $app;
