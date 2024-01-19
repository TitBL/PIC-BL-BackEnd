<?php

namespace App\Http\Controllers\Api;

use App\Models\Entity\User;
use App\Models\Entity\UserSession;
use Illuminate\Http\Request;

use Illuminate\Validation\ValidationException;

use Illuminate\Support\Collection;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

/** 
 * @OA\Controller(
 *   tags={"Autorización"},
 *   path="/api/autorizacion",
 *   summary="Controlador de autorizaciones para api",
 *   description="Este controlador gestiona las autorizaciones de la API",
 * ),
 */
class AuthController extends ApiController
{
    protected $UserModel;
    public function  __construct(User $user)
    {
        $this->UserModel = $user;
        $this->middleware('auth:api')->except('login');
    }

    /**
     * @OA\Post(
     *   tags={"Autorización"},
     *   path="/api/login",
     *   summary="Verifica si el usuario tiene acceso al sistema retornando un token de acceso",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent( 
     *              @OA\Property(property="dni", type="string", nullable=true),
     *              @OA\Property(property="name", type="string", nullable=true),
     *              @OA\Property(property="email", type="string", nullable=true),
     *              @OA\Property(property="pwd", type="string"),
     *              ) 
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/APIResponse")
     *     ),
     *     @OA\Response(
     *         response=404, 
     *         description="Not Found",
     *         @OA\JsonContent(ref="#/components/schemas/APIResponse")
     *     ),
     *     @OA\Response(
     *         response=400, 
     *         description="Bad Request",
     *         @OA\JsonContent(ref="#/components/schemas/APIResponse")
     *     ),
     *  )
     */
    public function login(Request $request)
    {
        try {

            $loginField = $request->has('dni') ? 'dni' : ($request->has('name') ? 'name' : 'email');
            $credentials = [
                $loginField => $request->get($loginField),
                PWD_FIELD => $request->get('pwd'),
            ];


            $token = JWTAuth::attempt($credentials);

            if (!$token) {
                throw  ValidationException::withMessages(['error' => ERROR_LOGIN]);
            }

            $ttl = config('jwt.ttl');
            $expirationDateTime = Carbon::now()->addMinutes($ttl)->format('Y-m-d\TH:i:s.v');

            $sessionId = $this->setSession(JWTAuth::user()->id, $token, $expirationDateTime);

            $success = new Collection();
            $success->put('token', $token);
            $success->put('token_type', 'Bearer');
            $success->put('token_expires',  $expirationDateTime);
            $success->put('session',  $sessionId);

            $log = $this->getLog($request, JWTAuth::user()->id, $token, $expirationDateTime);
            return $this->sendOk(SUCESS, $success, 200, $log);
        } catch (ValidationException $e) {
            $log = $this->getLog($request);
            return $this->sendError(ERROR_VALIDATION, $e->validator->errors(), $log);
        } catch (\Exception $e) {
            $log = $this->getLog($request);
            return $this->sendException($e->getMessage(), $e, $log);
        }
    }

    // $success->put('entity', $this->UserModel->castLoginEntity($user));

    /**
     * @OA\Post(
     *   tags={"Autorización"},
     *   path="/api/refresh",
     *   summary="Actualiza el token de acceso utilizando un token de actualización válido.",
     *   operationId="refresh",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent( 
     *              @OA\Property(property="refresh_token", type="string", description="El token de actualización"), 
     *              ) 
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/APIResponse")
     *     ),
     *     @OA\Response(
     *         response=404, 
     *         description="Not Found",
     *         @OA\JsonContent(ref="#/components/schemas/APIResponse")
     *     ),
     *     @OA\Response(
     *         response=400, 
     *         description="Bad Request",
     *         @OA\JsonContent(ref="#/components/schemas/APIResponse")
     *     ),
     *   security={
     *      {"Token": {}},
     *      {"Session": {}}
     *      }
     * )
     */
    public function refresh(Request $request)
    {
        try {
            $token = JWTAuth::parseToken()->refresh();

            $log = $this->getLog($request, JWTAuth::user()->id, $token);
            return $this->sendOk(SUCESS, new Collection(['token' => $token]), 200, $log);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            // The token has already expired, but you can still check if the refresh token is present
            $refreshToken = $request->input('refresh_token');

            if (!$refreshToken) {
                // No refresh token present, the client needs to authenticate again
                $log = $this->getLog($request);
                return $this->sendError(ERROR_TOKEN, 'Refresh token not present. Authenticate again.', 401, $log);
            }

            $oldToken = JWTAuth::getToken();
            $payload = JWTAuth::getPayload($oldToken)->toArray();

            // Get the old token and check if it expired in the last 3 minutes
            $expirationTime = $payload['exp'] - time();
            $allowableExpiration = 3 * 60;

            if ($expirationTime <= $allowableExpiration) {
                // Renew the token
                $newToken = JWTAuth::fromUser(JWTAuth::toUser($oldToken));
                $log = $this->getLog($request, JWTAuth::user()->id, $token);
                return $this->sendOk(SUCESS, new Collection(['token' => $newToken]), 200, $log);
            }

            // The refresh token is present, but it has also expired
            $log = $this->getLog($request);
            return $this->sendError(ERROR_TOKEN, 'Both tokens have expired. Authenticate again.', 401, $log);
        }
    }

    private function getLog(Request $request, $iduser = null, $token = null, $expire = null)
    {
        $log = new Collection();
        $log->put('idUser', $iduser);
        $log->put('browser', $request->header('User-Agent'));
        $log->put('request', $request->all());
        $log->put('ip', $request->ip());
        $log->put('token', $token);
        $log->put('expire', $expire);
        return $log;
    }

    private function setSession($idUser, $token, $expire)
    {
        UserSession::where('user_id', $idUser)
            ->update([
                'state' => false,
                'updated_at' => Carbon::now()->format('Y-m-d\TH:i:s.v')
            ]);

        $sessionID = \Illuminate\Support\Str::uuid()->toString();
        UserSession::create([
            'id' => $sessionID,
            'user_id' => $idUser,
            'token' => $token,
            'expired' => $expire,
            'created_at' => Carbon::now()->format('Y-m-d\TH:i:s.v'),
            'updated_at' => Carbon::now()->format('Y-m-d\TH:i:s.v')
        ]);

        return $sessionID;
    }
}
