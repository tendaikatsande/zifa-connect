<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (!$user) {
            $this->logDenial($request, null, $permissions, 'unauthenticated');
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Super admin bypasses all permission checks
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return $next($request);
            }
        }

        $this->logDenial($request, $user, $permissions, 'insufficient_permissions');

        return response()->json([
            'message' => 'You do not have permission to perform this action',
            'required_permissions' => $permissions,
        ], 403);
    }

    /**
     * Log permission denial for audit trail
     */
    private function logDenial(Request $request, $user, array $permissions, string $reason): void
    {
        Log::channel('security')->warning('Permission denied', [
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'reason' => $reason,
            'required_permissions' => $permissions,
            'route' => $request->route()?->getName() ?? $request->path(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
