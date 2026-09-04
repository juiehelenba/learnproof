<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class HealthCheckService
{
    /**
     * @return array{status: string, checks: array<string, array<string, mixed>>, checked_at: string}
     */
    public function check(): array
    {
        $checks = [
            'database' => $this->database(),
            'cache' => $this->cache(),
            'queue' => $this->queue(),
            'ai' => $this->ai(),
            'blockchain' => $this->blockchain(),
        ];

        $statuses = array_column($checks, 'status');
        $status = in_array('down', $statuses, true)
            ? 'down'
            : (in_array('degraded', $statuses, true) ? 'degraded' : 'ok');

        return [
            'status' => $status,
            'app' => config('app.name'),
            'environment' => config('app.env'),
            'checked_at' => now()->toIso8601String(),
            'checks' => $checks,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function database(): array
    {
        try {
            DB::connection()->getPdo();
            DB::select('select 1');

            return ['status' => 'ok', 'driver' => config('database.default')];
        } catch (Throwable $e) {
            return ['status' => 'down', 'error' => 'connection_failed'];
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function cache(): array
    {
        try {
            $key = 'learnproof:health:'.Str::random(8);
            Cache::put($key, '1', 10);
            $ok = Cache::pull($key) === '1';

            return [
                'status' => $ok ? 'ok' : 'degraded',
                'driver' => config('cache.default'),
            ];
        } catch (Throwable) {
            return ['status' => 'degraded', 'driver' => config('cache.default')];
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function queue(): array
    {
        $pending = null;
        $failed = null;

        try {
            if (Schema::hasTable('jobs')) {
                $pending = DB::table('jobs')->count();
            }
            if (Schema::hasTable('failed_jobs')) {
                $failed = DB::table('failed_jobs')->count();
            }
        } catch (Throwable) {
            return [
                'status' => 'degraded',
                'connection' => config('queue.default'),
            ];
        }

        $status = ($failed ?? 0) > 0 ? 'degraded' : 'ok';

        return [
            'status' => $status,
            'connection' => config('queue.default'),
            'pending_jobs' => $pending,
            'failed_jobs' => $failed,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function ai(): array
    {
        $enabled = (bool) config('learnproof.ai.enabled');
        $hasKey = filled(config('learnproof.ai.api_key'));

        $status = match (true) {
            ! $enabled => 'ok',
            $enabled && $hasKey => 'ok',
            default => 'degraded',
        };

        return [
            'status' => $status,
            'enabled' => $enabled,
            'provider' => config('learnproof.ai.provider'),
            'model' => config('learnproof.ai.model'),
            'api_key_configured' => $hasKey,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function blockchain(): array
    {
        $enabled = (bool) config('learnproof.blockchain.enabled');
        $mode = (string) config('learnproof.blockchain.mode');
        $configured = filled(config('learnproof.blockchain.rpc_url'))
            && filled(config('learnproof.blockchain.contract_address'))
            && filled(config('learnproof.blockchain.wallet_private_key'));

        $status = match (true) {
            ! $enabled => 'ok',
            $mode === 'mock' => 'ok',
            $mode === 'evm' && $configured => 'ok',
            $mode === 'evm' && ! $configured => 'degraded',
            default => 'degraded',
        };

        return [
            'status' => $status,
            'enabled' => $enabled,
            'mode' => $mode,
            'network' => config('learnproof.blockchain.network'),
            'configured' => $mode !== 'evm' || $configured,
        ];
    }
}
