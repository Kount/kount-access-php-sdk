<?php

/**
 * Service API access class.
 * This class provides helper functions to utilize the Kount Access API
 * service.  In order to use this service you will be required to furnish:
 *   * Your Merchant ID
 *   * Your API Key
 *   * The API server you want to connect to
 *   * Information related to the queries you wish to make, including:
 *       * Session Id
 *       * username
 *       * password
 *
 * See the reference_implementation.php for sample usage.
 *
 * @version 2.1.0
 * @copyright 2015 Kount, Inc. All Rights Reserved.
 */
class Access_Service
{

  /**
   * Version number
   */
  private $__version;

  /**
   * Private Kount Access API server name
   */
  private $__server_name;

  /**
   * Variable instance for creating Access_CurlService
   */
  private $__curl_service;

  /**
   * A logger instance.
   * @var Access_Log_Factory_LoggerFactory
   */
  protected $logger;


  /**
   * Constructor
   *
   * @param int $merchant_id The Merchant's ID
   * @param string $api_key API key assigned to merchant
   * @param string $server_name The DNS name for the Kount Access API Server
   * @param string $version The version of the API to access (0200 is the default for this release of the SDK).
   * @param class Access_CurlService instance
   * @throws Access_Exception Thrown if any of the values are invalid.
   */
  public function __construct($merchant_id, $api_key, $server_name, $version = '0210', $__curl_service = null)
  {
    if (is_null($server_name) || !isset($server_name)) {
      throw new Access_Exception(Access_Exception::INVALID_DATA, " Missing host.");
    }

    if (is_null($merchant_id) || !isset($merchant_id)) {
      throw new Access_Exception(Access_Exception::INVALID_DATA, " Missing merchantId.");
    } else if($merchant_id < 99999 || $merchant_id > 1000000) {
      throw new Access_Exception(Access_Exception::INVALID_DATA, " Invalid merchantId.");
    }

    if (is_null($api_key) || trim($api_key) == '') {
      throw new Access_Exception(Access_Exception::INVALID_DATA, " Missing apiKey.");
    }

    if($__curl_service == null) {
      $this->__curl_service = new Access_CurlService($merchant_id, $api_key);
    } else {
      $this->__curl_service = $__curl_service;
    }

    $this->__server_name = $server_name;
    $this->__version = $version;

    $loggerFactory = Access_Log_Factory_LogFactory::getLogFactory();
    $this->logger = $loggerFactory->getLogger(__CLASS__);

    $this->logger->info("Access SDK using merchantId = " . $merchant_id . ", host = " . $server_name);
  } //end __construct

  /**
   * Gets the information for the Device based on the Session ID.
   *
   * @param string $session_id The Session ID used by the Device
   * @throws Access_Exception if session id is missing, null or wrong length
   * @return array from JSON object decoded with details about the Device.
   */
  public function get_device($session_id)
  {
    if(is_null($session_id) || empty($session_id) || sizeof($session_id) > 32) {
      throw new Access_Exception(Access_Exception::INVALID_DATA, "Invalid session id.");
    }

    $endpoint = "https://$this->__server_name/api/device?v=$this->__version&s=$session_id";
    $this->logger->debug("device endpoint: " . $endpoint);

    return $this->__curl_service->__call_endpoint($endpoint, "GET", null);
  } //end get_device


  /**
   * Gets the Velocity information for the Device based on the Session ID,
   * User ID and Password. These values are re-hashed in case the client
   * passed them in the clear prior to sending them off to the api server.
   *
   * @param string $session_id The Session ID used by the Device
   * @param string $user_id The user's User ID
   * @param string $password The user's Password
   * @throws Access_Exception thrown if session id, user id or password are invalid
   * @return array from JSON object decoded with details about the Device.
   */
  public function get_velocity($session_id, $user_id, $password)
  {
    if(is_null($session_id) || empty($session_id) || sizeof($session_id) > 32) {
      throw new Access_Exception(Access_Exception::INVALID_DATA, " Invalid session id.");
    }

    if(is_null($user_id) || empty($user_id)) {
      throw new Access_Exception(Access_Exception::INVALID_DATA, " Invalid user id.");
    }

    if(is_null($password) || empty($password)) {
      throw new Access_Exception(Access_Exception::INVALID_DATA, " Invalid password id.");
    }

    $endpoint = "https://$this->__server_name/api/velocity";
    $this->logger->debug("velocity endpoint: " . $endpoint);

    $u = hash('sha256', $user_id);
    $p = hash('sha256', $password);
    $a = hash('sha256', $user_id . ":" . $password);
    $data = array(
      "s" => $session_id,
      "v" => $this->__version,
      "uh" => $u,
      "ph" => $p,
      "ah" => $a
    );

    $this->logger->debug(
      "velocity request parameters : "
      . "user_id = "     . $u . ', '
      . "password = "    . $p . ', '
      . "credentials = " . $a . ', '
      . "session_id = "  . $session_id . ', '
      . "version = "     . $this->__version
    );
    return $this->__curl_service->__call_endpoint($endpoint, "POST", $data);
  } //end get_velocity

  /**
   * Gets the Decision, Device information, and Velocity information for
   * the Device based on the Session ID, User ID and passwords. These values
   * are re-hashed in case the client passed them in the clear prior to
   * sending them off to the api server.
   *
   * @param string $session_id The Session ID used by the Device
   * @param string $user_id The user's User ID
   * @param string $password The user's Password
   * @throws Access_Exception thrown if session id, user id or password are invalid
   * @return array from JSON object decoded with details about the Device.
   */
  public function get_decision($session_id, $user_id, $password)
  {
    if(is_null($session_id) || empty($session_id) || sizeof($session_id) > 32) {
      throw new Access_Exception(Access_Exception::INVALID_DATA, " Invalid session id.");
    }

    if(is_null($user_id) || empty($user_id)) {
      throw new Access_Exception(Access_Exception::INVALID_DATA, " Invalid user id.");
    }

    if(is_null($password) || empty($password)) {
      throw new Access_Exception(Access_Exception::INVALID_DATA, " Invalid password id.");
    }

    $endpoint = "https://$this->__server_name/api/decision";
    $this->logger->debug("decision endpoint: " . $endpoint);

    $u = hash('sha256', $user_id);
    $p = hash('sha256', $password);
    $a = hash('sha256', $user_id . ":" . $password);
    $data = array(
      "s" => $session_id,
      "v" => $this->__version,
      "uh" => $u,
      "ph" => $p,
      "ah" => $a
    );

    $this->logger->debug(
      "decision request parameters : "
      . "user_id = "      . $u . ', '
      . "password = "     . $p . ', '
      . "credentials = "  . $a . ', '
      . "session_id = "   . $session_id . ', '
      . "version = "      . $this->__version
    );
    return $this->__curl_service->__call_endpoint($endpoint, "POST", $data);
  } //end get_decision

} //end kount_access_api

