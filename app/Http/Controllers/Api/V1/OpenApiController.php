<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class OpenApiController extends Controller
{
    public function ui(): View
    {
        return view('api.docs', [
            'specUrl' => route('api.v1.openapi'),
        ]);
    }

    public function spec(): Response
    {
        $path = base_path('openapi/openapi.yaml');

        abort_unless(File::exists($path), 404, 'Especificação OpenAPI não encontrada.');

        return response(File::get($path), 200, [
            'Content-Type' => 'application/yaml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=300',
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'name' => config('learnproof.name'),
                'version' => 'v1',
                'documentation' => route('api.v1.docs'),
                'openapi' => route('api.v1.openapi'),
                'endpoints' => [
                    'POST /api/v1/login',
                    'GET /api/v1/me',
                    'POST /api/v1/logout',
                    'GET /api/v1/courses',
                    'GET /api/v1/courses/{slug}',
                    'GET /api/v1/courses/{slug}/ai/history',
                    'POST /api/v1/courses/{slug}/ai/chat',
                    'GET /api/v1/staff/ping',
                ],
            ],
            'meta' => [
                'api_version' => 'v1',
            ],
        ]);
    }
}
