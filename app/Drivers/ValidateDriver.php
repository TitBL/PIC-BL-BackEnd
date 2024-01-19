<?php

namespace App\Drivers;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

use App\Http\Controllers\APIResponse;

/**
 * Class ValidateDriver
 * Static class  
 * @package App\Drivers
 * @author Rafael Larrea <jrafael1108@gmail.com>
 */
class ValidateDriver
{
    /**
     * Validate the session token.
     *
     * @param string $sessionId
     * @param string $token
     * @return false|int
     */
    public static function ValidatedHeader(Request $request, array $requiredFields):void
    {
        foreach ($requiredFields as $field) {
            if (!$request->header($field)) {
                $response = new APIResponse(ERROR_AUtH, false);
                $response->Data = new Collection(['error' => "Missing required header field: $field"]);
                throw new HttpResponseException(response()->json($response, 401));
            }
        }
    }
}
