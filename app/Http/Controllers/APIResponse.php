<?php

namespace App\Http\Controllers;

/** 
 * * The APIResponse class provides properties for the responses of HTML methods.
 * @package App\Http\Controllers 
 * @author Rafael Larrea <jrafael1108@gmail.com> 
 * @OA\Schema( 
 *     schema="APIResponse",
 *     description=""
 * )
 */
class APIResponse
{
    public function __construct(string $message, bool $success = true)
    {
        $this->Success = $success;
        $this->Message = $message;
    }

    /**
     * @OA\Property(
     *   property="Success", 
     *   type="boolean"
     * ) 
     **/
    public bool $Success;

    /**
     * @OA\Property(
     *   property="Message", 
     *   type="string"
     * ) 
     **/
    public string $Message;

    /**
     * @OA\Property(
     *   property="Data", 
     *   type="object"
     * ) 
     **/
    public object $Data;

    /**
     * @OA\Property(
     *   property="Exception", 
     *   type="object"
     * ) 
     **/
    public \Exception $Exception;

    /**
     * @OA\Property(
     *   property="Code", 
     *   type="int"
     * ) 
     **/
    public int $Code;
}
