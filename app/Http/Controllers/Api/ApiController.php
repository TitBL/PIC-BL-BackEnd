<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Models\Entity\UserAccessLog;
use App\Http\Controllers\APIResponse;

use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;

/**
 * The ApiController class handles the logic for all API.
 * @package App\Http\Controllers\Api 
 * @author Rafael Larrea <jrafael1108@gmail.com>
 * @OA\Info(
 *     title="Aplicativo Web para la Centralización de Documentos Electrónicos Tributarios",
 *     version="1.0.0",
 *      @OA\Contact(
 *          email="jrafael1108@gmail.com",
 *          name="Rafael Larrea"
 *      ),
 * ),
 */
class ApiController extends BaseController
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

        if (isset($log)) {
            $this->saveLogAuth($log, $response);
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
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    public function sendException($error, \Exception $exception, $log = null) 
    {
        $response = new APIResponse($error, false);
        $response->Exception = $exception;

        if (isset($log)) {
            $this->saveLogAuth($log, $response);
        }

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
    public function sendOk($message, $result = [], $code = 200, $log = null)
    {
        $response = new APIResponse($message, true);
        if (!empty($result)) {
            $response->Data = $result;
        }
        if ($code !== 200) {
            $response->Code = $code;
        }

        if (isset($log)) {
            $this->saveLogAuth($log, $response);
        }

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = $backtrace[1];
        $this->saveLog($response, $caller['function'], $caller['class']);

        return response()->json($response, $code)->getData();
    }

    /**
     * Send a success response for file  browser.
     *
     * @param string $file The success file.
     * @param string $content_type The value for content for show in browser. 
     * @param string $filename Name of file. The field is necessary when the user need to download it.
     * 
     * @return array The formatted JSON response data.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    public function sendOk_file($file, $content_type, $filename = null)
    {
        $response = new APIResponse("", true);
        $response->Code = 200;
        if (!empty($result)) {
            $response->Data = $file;
        }

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = $backtrace[1];
        $this->saveLog($response, $caller['function'], $caller['class']);

        if (isset($filename)) {
            return response($file,200)
                ->withHeaders([
                    'Content-Type' => $content_type,
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]);
        } else {
            return response($file,200)
                ->withHeaders([
                    'Content-Type' => $content_type,
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]);
        }
    }


    private function saveLogAuth($log, $response):void
    {
        UserAccessLog::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'user_id' => $log->get('idUser'),
            'browser' => $log->get('browser'),
            'ip' => $log->get('ip'),
            'token' => $log->get('token'),
            'expired_token' => $log->get('expire'),
            'request' => json_encode($log->get('request')),
            'response' => json_encode($response),
            'created_at' => Carbon::now()->format('Y-m-d\TH:i:s.v'),
            'updated_at' => Carbon::now()->format('Y-m-d\TH:i:s.v')
        ]);
    }

    private function saveLog($response, $caller_method, $caller_class):void
    {
        Log::channel('daily_api_log')->info('API Controller Auth:', [
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
