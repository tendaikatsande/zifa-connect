<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait LogsAuthorizationDenials
{
    /**
     * Log resource-level authorization denial
     */
    protected function logResourceDenial(
        Request $request,
        string $resource,
        int|string $resourceId,
        string $action
    ): void {
        $user = $request->user();

        Log::channel('security')->warning('Resource access denied', [
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'resource' => $resource,
            'resource_id' => $resourceId,
            'action' => $action,
            'reason' => 'ownership_check_failed',
            'route' => $request->route()?->getName() ?? $request->path(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
