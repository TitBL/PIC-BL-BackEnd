<?php

namespace App\Http\Controllers\External;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller as BaseController;

use App\Http\Controllers\APIResponse;

/**
 * The ExternalController class handles the logic for all external API.
 * @package App\Http\Controllers\ExternalApi
 * @author Rafael Larrea <jrafael1108@gmail.com>
 */
class ExternalController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Send an error response.
     *
     * @param string $error The main error message.
     * @param array $errorMessages Additional error messages (optional).
     *
     * @throws HttpResponseException
     * 
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    public function sendError($error, $errorMessages = [], $log = null)
    {
        $response = new APIResponse($error, false);

        if (!empty($errorMessages)) {
            $response->Data = $errorMessages;
        }

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = $backtrace[1];
        $this->saveLog($response, $caller['function'], $caller['class']);

        throw new HttpResponseException(response()->json($response, 400));
    }

    /**
     * Send an exception response.
     *
     * @param string $error The error message.
     * @param \Exception $exception The exception object.
     *
     * @throws HttpResponseException
     * 
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    public function sendException($error, \Exception $exception, $log = null)
    {
        $response = new APIResponse($error, false);
        $response->Exception = $exception;

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = $backtrace[1];
        $this->saveLog($response, $caller['function'], $caller['class']);

        throw new HttpResponseException(response()->json($response, 404));
    }

    /**
     * Send a success response.
     *
     * @param string $message The success message.
     * @param array $result The result data (optional).
     * @param int $code The HTTP status code (default is 200).
     *
     * @return array The formatted JSON response data.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    public function sendOk($message, $result = [], $code = 200): array
    {
        $response = new APIResponse($message, true);
        if (!empty($result)) {
            $response->Data = $result;
        }
        if ($code !== 200) {
            $response->Code = $code;
        }

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = $backtrace[1];
        $this->saveLog($response, $caller['function'], $caller['class']);

        return response()->json($response, $code)->getData();
    }

    private function saveLog($response, $caller_method, $caller_class):void
    {
        Log::channel('daily_api_log')->info('External API Controller Auth:', [
            'caller_method' => $caller_method,
            'caller_class' => $caller_class,
            'Success' => isset($response->Success) ? $response->Success : null,
            'Message' => isset($response->Message) ? $response->Message : null,
            'Data' => isset($response->Data) ? $response->Data : null,
            'Exception' => isset($response->Exception) ? $response->Exception : null,
            'Code' => isset($response->Code) ? $response->Code : null,
        ]);
    }
}
