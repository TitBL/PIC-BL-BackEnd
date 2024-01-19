<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Controllers\APIResponse;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {

        $response = new APIResponse("Unauthenticated", false); 
        throw new HttpResponseException(response()->json($response, 401));
        // return $request->expectsJson() ? null : route('login');
    }
}
