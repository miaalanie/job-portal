<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

final class MlRequestLogger
{
    public static function log(string $requestType, array $payload): void
    {
        $payload['request_type'] = $requestType;
        $payload['logged_at'] = now()->toIso8601String();

        Log::channel('rekomendasi_cf')->info('ML request metric', $payload);
    }
}