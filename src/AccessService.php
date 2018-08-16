<?php

namespace Kount;

/**
 * Service API access class.
 * This class provides helper functions to utilize the src Access API
 * service.  In order to use this service you will be required to furnish:
 *   * Your Merchant ID
 *   * Your API Key
 *   * The API server you want to connect to
 *   * Information related to the queries you wish to make, including:
 *       * Session Id
 *       * username
 *       * password
 * See the reference_implementation.php for sample usage.
 * @version 2.1.0
 * @copyright 2015 src, Inc. All Rights Reserved.
 */
class AccessService
{

    /**
     * Version number
     */
    private $version = '0400';

    /**
     * Private src Access API server_name
     */
    private $server_name;

    /**
     * Variable instance for creating AccessCurlService
     */
    private $curl_service;

    /**
     * A log4php logger instance.
     */
    private $logger;

    private $merchant_id;


    /**
     * Constructor
     * @param int $merchant_id The Merchant's ID
     * @param string $api_key API key assigned to merchant
     * @param string $server_name The DNS name for the src Access API Server
     * @param string $version The version of the API to access (0200 is the default for this release of the SDK).
     * @param class AccessCurlService instance
     * @throws AccessException Thrown if any of the values are invalid.
     */
    public function __construct($merchant_id, $api_key, $server_name, $version = '0210', $curl_service = null)
    {
        \Logger::configure(__DIR__.'/../config.xml');
        $this->logger = \Logger::getLogger('src Access Logger');

        if (is_null($server_name) || !isset($server_name)) {
            throw new AccessException(AccessException::INVALID_DATA, " Missing host.");
        }

        if (is_null($merchant_id) || !isset($merchant_id)) {
            throw new AccessException(AccessException::INVALID_DATA, " Missing merchantId.");
        } else if ($merchant_id < 99999 || $merchant_id > 1000000) {
            throw new AccessException(AccessException::INVALID_DATA, " Invalid merchantId.");
        }

        if (is_null($api_key) || trim($api_key) == '') {
            throw new AccessException(AccessException::INVALID_DATA, " Missing apiKey.");
        }

        if ($curl_service == null) {
            $this->curl_service = new AccessCurlService($merchant_id, $api_key);
        } else {
            $this->curl_service = $curl_service;
        }

        if (!is_null($version)) {
            $this->version = $version;
        }

        $this->server_name = $server_name;


        $this->merchant_id = $merchant_id;

        $this->logger->info("Access SDK using merchantId = ".$merchant_id.", host = ".$server_name);
    } //end __construct

    /**
     * Gets the information for the Device based on the Session ID.
     * @param string $session_id The Session ID used by the Device
     * @throws AccessException if session id is missing, null or wrong length
     * @return array from JSON object decoded with details about the Device.
     */
    public function getDevice($session_id)
    {
        $this->verifySession($session_id);

        $endpoint = "https://$this->server_name/api/device?v=$this->version&s=$session_id";
        $this->logger->debug("device endpoint: ".$endpoint);

        return $this->curl_service->__call_endpoint($endpoint, "GET", null);
    } //end get_device


    /**
     * Gets the Velocity information for the Device based on the Session ID,
     * User ID and Password. These values are re-hashed in case the client
     * passed them in the clear prior to sending them off to the api server.
     * @param string $session_id The Session ID used by the Device
     * @param string $user_id The user's User ID
     * @param string $password The user's Password
     * @throws AccessException thrown if session id, user id or password are invalid
     * @return array from JSON object decoded with details about the Device.
     */
    public function getVelocity($session_id, $user_id, $password)
    {
        $this->verifySession($session_id);
        $this->verifyUserCredentials($user_id, $password);

        $endpoint = "https://$this->server_name/api/velocity";
        $this->logger->debug("velocity endpoint: ".$endpoint);

        $u    = hash('sha256', $user_id);
        $p    = hash('sha256', $password);
        $a    = hash('sha256', $user_id.":".$password);
        $data = array(
            "s"  => $session_id,
            "v"  => $this->version,
            "uh" => $u,
            "ph" => $p,
            "ah" => $a,
        );

        $this->logger->debug(
            "velocity request parameters : "."user_id = ".$u.', '."password = ".$p.', '."credentials = ".$a.', '."session_id = ".$session_id.', '."version = ".$this->version
        );

        return $this->curl_service->__call_endpoint($endpoint, "POST", $data);
    } //end get_velocity

    /**
     * Gets the Decision, Device information, and Velocity information for
     * the Device based on the Session ID, User ID and passwords. These values
     * are re-hashed in case the client passed them in the clear prior to
     * sending them off to the api server.
     * @param string $session_id The Session ID used by the Device
     * @param string $user_id The user's User ID
     * @param string $password The user's Password
     * @throws AccessException thrown if session id, user id or password are invalid
     * @return array from JSON object decoded with details about the Device.
     */
    public function getDecision($session_id, $user_id, $password)
    {
        $this->verifySession($session_id);
        $this->verifyUserCredentials($user_id, $password);

        $endpoint = "https://$this->server_name/api/decision";
        $this->logger->debug("decision endpoint: ".$endpoint);

        $u    = hash('sha256', $user_id);
        $p    = hash('sha256', $password);
        $a    = hash('sha256', $user_id.":".$password);
        $data = array(
            "s"  => $session_id,
            "v"  => $this->version,
            "uh" => $u,
            "ph" => $p,
            "ah" => $a,
        );

        $this->logger->debug(
            "decision request parameters : "."user_id = ".$u.', '."password = ".$p.', '."credentials = ".$a.', '."session_id = ".$session_id.', '."version = ".$this->version
        );

        return $this->curl_service->__call_endpoint($endpoint, "POST", $data);
    } //end get_decision

    /**
     * Function that validates the session id being passed.
     * @param string $session_id | The session ID used by the Device
     * @throws AccessException thrown if session is invalid
     */
    public function verifySession($session_id)
    {
        if (is_null($session_id) || strlen(utf8_decode($session_id)) != 32) {
            throw new AccessException(
                AccessException::INVALID_DATA,
                " Invalid session".$session_id." id. Must be 32 characters in length"
            );
        }
    }

    /**
     * Function that validates the user id  and password being passed.
     * @param string $user_id | The user's User ID
     * @param string $password | The user's Password
     * @throws AccessException thrown if user id or password are invalid
     */
    public function verifyUserCredentials($user_id, $password)
    {
        if (is_null($user_id) || empty($user_id)) {
            throw new AccessException(AccessException::INVALID_DATA, " Invalid user id.");
        }

        if (is_null($password) || empty($password)) {
            throw new AccessException(AccessException::INVALID_DATA, " Invalid password id.");
        }
    }


    public function getUniques($device_id)
    {

        $data     = array(
            "d" => $device_id,
            "v" => $this->version,
        );
        $endpoint = "https://$this->server_name/api/getuniques?".http_build_query($data);
        $this->logger->debug("getuniques endpoint: ".$endpoint);

        return $this->curl_service->__call_endpoint($endpoint, "GET");
    }

} //end kount_access_api

