<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Collection;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response;
use App\Drivers\ValidateDriver;
use App\Http\Controllers\APIResponse;

class ExternalLoggingMiddleware
{
    /**
     * The list of required header fields for the session token validation.
     *
     * @var array
     */
    private $requiredFields = ['APIKEY'];

    /**
     * The list of exclude routes.
     *
     * @var array
     */
    private $excludedRoutes = [''];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Closure  $next
     * @return mixed
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->path(), $this->excludedRoutes)) {
            return $next($request);
        }

        ValidateDriver::ValidatedHeader($request, $this->requiredFields);
        return $next($request);
    }    
}
