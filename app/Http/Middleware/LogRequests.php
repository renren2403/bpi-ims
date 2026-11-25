<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        // Skip health check and internal dev assets
        if ($request->is('up') || $request->is('_ignition/*') || $request->is('livewire/update')) {
            return $response;
        }

        $durationMs = round((microtime(true) - $startTime) * 1000, 2);
        $method = $request->method();
        $uri = $request->getRequestUri();
        $status = $response->getStatusCode();

        $user = Auth::user();
        $userInfo = $user ? "{$user->name} [ID: {$user->id}]" : 'Guest';
        $ip = $request->ip();

        // Spring Boot style request log format
        $message = sprintf(
            'HTTP %s %s [%d] in %sms (User: %s | IP: %s)',
            $method,
            $uri,
            $status,
            $durationMs,
            $userInfo,
            $ip
        );

        // Include sanitized payload for state-changing operations
        $context = [];
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $payload = $request->except(['password', 'password_confirmation', '_token', '_method']);
            if (!empty($payload)) {
                $context['payload'] = $payload;
            }
        }

        if ($status >= 500) {
            Log::error($message, $context);
        } elseif ($status >= 400) {
            Log::warning($message, $context);
        } else {
            Log::info($message, $context);
        }

        return $response;
    }
}
