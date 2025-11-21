<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'queue' => $this->checkQueue(),
        ];

        $healthy = !in_array('fail', array_column($checks, 'status'));

        return response()->json([
            'status' => $healthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toISOString(),
            'checks' => $checks,
        ], $healthy ? 200 : 503);
    }

    public function detailed(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'queue' => $this->checkQueue(),
            'external_services' => $this->checkExternalServices(),
        ];

        $healthy = !in_array('fail', array_column($checks, 'status'));

        return response()->json([
            'status' => $healthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toISOString(),
            'version' => config('app.version', '1.0.0'),
            'environment' => config('app.env'),
            'checks' => $checks,
            'metrics' => $this->getMetrics(),
        ], $healthy ? 200 : 503);
    }

    private function checkDatabase(): array
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $latency = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => 'ok',
                'latency_ms' => $latency,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'fail',
                'error' => 'Database connection failed',
            ];
        }
    }

    private function checkCache(): array
    {
        try {
            $start = microtime(true);
            $key = 'health_check_' . uniqid();
            Cache::put($key, true, 10);
            $result = Cache::get($key);
            Cache::forget($key);
            $latency = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => $result ? 'ok' : 'fail',
                'latency_ms' => $latency,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'fail',
                'error' => 'Cache operation failed',
            ];
        }
    }

    private function checkStorage(): array
    {
        try {
            $start = microtime(true);
            $disk = Storage::disk('local');
            $path = 'health_check_' . uniqid() . '.txt';
            $disk->put($path, 'test');
            $exists = $disk->exists($path);
            $disk->delete($path);
            $latency = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => $exists ? 'ok' : 'fail',
                'latency_ms' => $latency,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'fail',
                'error' => 'Storage operation failed',
            ];
        }
    }

    private function checkQueue(): array
    {
        try {
            $size = Queue::size();

            return [
                'status' => 'ok',
                'queue_size' => $size,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'fail',
                'error' => 'Queue check failed',
            ];
        }
    }

    private function checkExternalServices(): array
    {
        return [
            'pesepay' => $this->checkPesePay(),
        ];
    }

    private function checkPesePay(): array
    {
        try {
            $start = microtime(true);
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->get(config('pesepay.base_url', 'https://api.pesepay.com') . '/health');
            $latency = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => $response->successful() ? 'ok' : 'degraded',
                'latency_ms' => $latency,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'degraded',
                'error' => 'PesePay unreachable',
            ];
        }
    }

    private function getMetrics(): array
    {
        return [
            'pending_jobs' => DB::table('jobs')->count(),
            'failed_jobs' => DB::table('failed_jobs')->count(),
            'active_users_24h' => DB::table('users')
                ->where('last_login_at', '>=', now()->subDay())
                ->count(),
            'pending_registrations' => DB::table('registrations')
                ->where('status', 'pending_review')
                ->count(),
            'pending_transfers' => DB::table('transfers')
                ->whereIn('status', ['pending_from_club', 'pending_zifa_review'])
                ->count(),
        ];
    }
}
