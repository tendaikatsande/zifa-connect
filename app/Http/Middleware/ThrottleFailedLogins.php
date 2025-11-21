<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleFailedLogins
{
    /**
     * Maximum login attempts before lockout
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Lockout duration in minutes
     */
    private const LOCKOUT_MINUTES = 15;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->throttleKey($request);

        if ($this->isLockedOut($key)) {
            $remainingSeconds = RateLimiter::availableIn($key);
            $minutes = ceil($remainingSeconds / 60);

            return response()->json([
                'message' => "Too many login attempts. Please try again in {$minutes} minutes.",
                'retry_after' => $remainingSeconds,
            ], 429);
        }

        $response = $next($request);

        // If login failed (401 or specific error), increment attempts
        if ($response->getStatusCode() === 401 || $this->isFailedLogin($response)) {
            $this->incrementAttempts($key);
        } else {
            // Successful login - clear attempts
            $this->clearAttempts($key);
        }

        return $response;
    }

    /**
     * Generate unique key for rate limiting
     */
    private function throttleKey(Request $request): string
    {
        $identifier = $request->input('email', $request->ip());

        return 'login_attempts:' . sha1($identifier . '|' . $request->ip());
    }

    /**
     * Check if the user is locked out
     */
    private function isLockedOut(string $key): bool
    {
        return RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS);
    }

    /**
     * Increment failed login attempts
     */
    private function incrementAttempts(string $key): void
    {
        RateLimiter::hit($key, self::LOCKOUT_MINUTES * 60);

        $attempts = RateLimiter::attempts($key);

        // Log if approaching lockout
        if ($attempts >= self::MAX_ATTEMPTS - 1) {
            \Illuminate\Support\Facades\Log::warning('Account approaching lockout', [
                'key' => $key,
                'attempts' => $attempts,
            ]);
        }
    }

    /**
     * Clear login attempts after successful login
     */
    private function clearAttempts(string $key): void
    {
        RateLimiter::clear($key);
    }

    /**
     * Check if response indicates failed login
     */
    private function isFailedLogin(Response $response): bool
    {
        if ($response->getStatusCode() !== 422) {
            return false;
        }

        $content = json_decode($response->getContent(), true);

        return isset($content['errors']['email']) || isset($content['message'])
            && str_contains(strtolower($content['message'] ?? ''), 'credentials');
    }
}
