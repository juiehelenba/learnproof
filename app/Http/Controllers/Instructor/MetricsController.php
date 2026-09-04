<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Services\HealthCheckService;
use App\Services\MetricsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MetricsController extends Controller
{
    public function __invoke(
        Request $request,
        MetricsService $metrics,
        HealthCheckService $health,
    ): View {
        $days = (int) $request->integer('days', 7);
        $days = in_array($days, [1, 7, 30], true) ? $days : 7;

        return view('instructor.metrics.index', [
            'metrics' => $metrics->dashboard($days),
            'health' => $health->check(),
            'days' => $days,
        ]);
    }
}
