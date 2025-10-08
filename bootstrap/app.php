<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
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

$builder->withSchedule(function (Schedule $schedule) {
    // chạy mỗi tiếng, xoá đơn hàng unpaid quá 6 tiếng
    $schedule->command('orders:clear-unpaid')->hourly();
});

// tạo Application thực sự
$app = $builder->create();

// bind exception handler vào app
$app->singleton(ExceptionHandler::class, Handler::class);

return $app;
