<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;

// configure trả về builder
$builder = Application::configure(basePath: dirname(__DIR__));

$builder->withRouting(
    web: __DIR__ . '/../routes/web.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
);

$builder->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);

    $middleware->web(append: [
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    ]);

    $middleware->validateCsrfTokens(except: [
        'webhook',
        'buyer/checkout/webhook',
    ]);
});

// tạo Application thực sự
$app = $builder->create();

// bind exception handler vào app
$app->singleton(ExceptionHandler::class, Handler::class);

return $app;
