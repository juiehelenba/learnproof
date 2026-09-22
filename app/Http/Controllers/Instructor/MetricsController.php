<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Services\HealthCheckService;
use App\Services\MetricsService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MetricsController extends Controller
{
    public function __invoke(
        Request $request,
        MetricsService $metrics,
        HealthCheckService $health,
    ): View {
        $days = $metrics->normalizeDays((int) $request->integer('days', 7));

        return view('instructor.metrics.index', [
            'metrics' => $metrics->dashboard($days),
            'health' => $health->check(),
            'days' => $days,
        ]);
    }

    public function export(Request $request, MetricsService $metrics): StreamedResponse
    {
        $days = $metrics->normalizeDays((int) $request->integer('days', 7));
        $csv = $metrics->toCsv($days);
        $filename = sprintf('learnproof-metricas-%dd-%s.csv', $days, now()->format('Y-m-d'));

        return response()->streamDownload(
            function () use ($csv) {
                echo $csv;
            },
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }
}
