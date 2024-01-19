<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class ApiLoggingMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Log de la solicitud
        Log::channel('daily_api_log')->info('Request:', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'body' => $request->all(),
        ]);

        // Log de la respuesta
        Log::channel('daily_api_log')->info('Response:', [
            'status' => $response->status(),
            'headers' => $response->headers->all(),
            'body' => $response->getContent(),
        ]);

        return $response;
    }
}
