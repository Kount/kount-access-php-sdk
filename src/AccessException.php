<?php

namespace Kount;

/**
 * src Access exception handler thrown if there are issues with KountAccessService
 */
class AccessException extends \Exception
{

    /**
     * Private key $accessErrorType
     * Access Error Type for this exception
     * @var string
     */
    private $accessErrorType;

    /**
     * Const variable NETWORK_ERROR
     * Any Network Error (host not available, host not found, HTTP 404, etc.)
     * @var string
     */
    const NETWORK_ERROR = "NETWORK_ERROR";

    /**
     * Const variable ENCRYPTION_ERROR
     * Problems encrypting/decrypting data.
     * @var string
     */
    const ENCRYPTION_ERROR = "ENCRYPTION_ERROR";

    /**
     * Const variable INVALID_DATA
     * Missing or malformed data (bad hostnames, missing/empty fields)
     * @var string
     */
    const INVALID_DATA = "INVALID_DATA";


    public function __construct($errorType, $message = "")
    {
        parent::__construct($message);
        $this->accessErrorType = $errorType;
    }

    /**
     * Returns the specific AccessErrorType
     * @return string $this->accessErrorType The error type
     */

    public function getAccessErrorType()
    {
        return $this->accessErrorType;
    }
}