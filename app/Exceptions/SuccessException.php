<?php

namespace App\Exceptions;

use Exception;

/**
 * Class SuccessException
 *
 * This exception is thrown to indicate successful execution with additional result information.
 * It extends the base Exception class and includes a result object that provides additional details about the success.
 *
 * @package App\Exceptions
 * @author Rafael Larrea <jrafael1108@gmail.com>
 */
class SuccessException extends Exception
{
    /**
     * @var object The result object containing additional details about the success.
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    protected object $result;

    /**
     * Constructor for SuccessException.
     *
     * @param string $message The exception message.
     * @param array $resultObject The result object providing additional details (default: []).
     * @param int $code The exception code (default: 200).
     * @param Exception|null $previous The previous exception, if any (default: null).
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    public function __construct($message = SUCESS, $resultObject = [], $code = 200, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        if (!empty($resultObject)) {
            $this->result = $resultObject;
        }
    }

    /**
     * Get the result object containing additional details about the success.
     *
     * @return object
     * @author Rafael Larrea <jrafael1108@gmail.com>
     */
    final public function getResul()
    {
        return $this->result;
    }
}
