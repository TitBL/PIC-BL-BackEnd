<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

use App\Http\Controllers\APIResponse;
use App\Drivers\ValidateDriver;
use App\Models\Entity\UserSession;

use Carbon\Carbon;

/**
 * Class SessionTokenMiddleware
 *
 * The SessionTokenMiddleware class is responsible for validating session tokens in the API requests.
 *
 * @package App\Http\Middleware
 */
class SessionTokenMiddleware
{
    /**
     * The list of required header fields for the session token validation.
     *
     * @var array
     */
    private $requiredFields = ['Authorization', 'SessionId'];

    /**
     * The list of exclude routes.
     *
     * @var array
     */
    private $excludedRoutes = ['api/login', 'api/reset-password'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->path(), $this->excludedRoutes)) {
            return $next($request);
        }

        ValidateDriver::ValidatedHeader($request, $this->requiredFields);
        $token = $request->header('Authorization');
        $sessionId = $request->header('SessionId');

        $userId = $this->isValidSessionToken($sessionId, $token);

        if ($userId !== false) {
            $request->merge(['IdUsuario' => $userId]);
            return $next($request);
        }

        $response = new APIResponse(ERROR_AUtH, false);
        $response->Data = ['error' => 'Unauthorized'];
        throw new HttpResponseException(response()->json($response, 401));
    }

    /**
     * Validate the required header fields.
     *
     * @param  \Illuminate\Http\Request  $request 
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    private function isValidSessionToken($sessionId, $token)
    {
        // Quita la palabra "Bearer" del token, si está presente
        $cleanToken = str_replace('Bearer ', '', $token);
        $user = UserSession::Select('user_id', 'expired')
            ->where('state', '=', true)
            ->where('id', '=', $sessionId)
            ->where('token', '=', $cleanToken)
            ->first();

        if ($user && Carbon::now()->isBefore($user->expired)) {
            return $user->user_id;
        }

        return false;
    }
}
