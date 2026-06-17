<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $uptime = shell_exec('uptime 2>/dev/null') ?: (PHP_OS_FAMILY === 'Windows' ? trim(shell_exec('systeminfo | find "System Boot Time"') ?: 'Windows') : 'Unknown');

    return view('api-index', [
        'uptime' => trim($uptime),
        'timestamp' => now()->toIso8601String(),
    ]);
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'env' => config('app.env'),
        'version' => config('app.version', '1.0.0'),
        'time' => now()->toIso8601String(),
        'php' => PHP_VERSION,
    ]);
});

Route::get('/phpinfo', function () {
    if (config('app.env') !== 'local') {
        abort(403);
    }

    return response(phpinfo());
});
