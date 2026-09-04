<?php

namespace App\Http\Controllers;

use App\Services\HealthCheckService;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function __invoke(HealthCheckService $health): JsonResponse
    {
        $payload = $health->check();

        $status = match ($payload['status']) {
            'down' => 503,
            'degraded' => 200,
            default => 200,
        };

        return response()->json($payload, $status);
    }
}
