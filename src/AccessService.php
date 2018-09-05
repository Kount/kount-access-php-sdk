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

    /**
     * The i value total - based on requested data
     */
    private $info_value = 0;

    /**
     * @var int device request index
     */
    private $device_info_value = 1;

    /**
     * @var int velocity request index
     */
    private $velocity_info_value = 2;

    /**
     * @var int decisionrequest index
     */
    private $decision_info_value = 4;

    /**
     * @var int tdi request index
     */
    private $trusted_device_info_value = 8;

    /**
     * @var int behaviosec request index
     */
    private $behavio_sec_info_value = 16;


    /**
     * @var int
     */
    private $merchant_id;

    /**
     * @var array - definition of not required values for total info sums for the getInfo request
     * all those params are required at some point based on the requested response
     */
    private $not_required_for_info_values = [
        'uniq' => [1, 2, 3, 4, 5, 6, 7, 8], //uniq = unique
        'uh'   => [1, 8, 9, 16, 17, 24, 25], //uh = username hash
        'ph'   => [1, 8, 9, 16, 17, 24, 25], //ph = password hash
        'ah'   => [1, 8, 9, 16, 17, 24, 25], //ah = account hash
    ];

    /**
     * permitted string states for deviceTrustBySession
     * @var array
     */
    private $trusted_states = [
        "trusted",
        "not_trusted",
        "banned"
    ];

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

    /**
     * retrieves devices based on unique identifier passed by the user
     * @param $unique
     * @return mixed
     */
    public function getDevices($unique)
    {

        $data     = array(
            "uniq" => $unique,
            "v"    => $this->version,
        );
        $endpoint = "https://$this->server_name/api/getdevices?".http_build_query($data);
        $this->logger->debug("getdevices endpoint: ".$endpoint);

        return $this->curl_service->__call_endpoint($endpoint, "GET");
    }

    /**
     * retrieves uniques for a device id passed by the user
     * @param $device_id
     * @return mixed
     */
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

    /**
     * set a trust state for a device based on session
     * @param $session_id
     * @param $unique
     * @param $state
     * @param $timing
     * @return mixed
     * @throws AccessException
     */
    public function deviceTrustBySession($session_id, $unique, $state, $timing)
    {
        $this->checkState($state);

        if(!is_array(json_decode($timing, true)))
        {
            throw new AccessException(
                AccessException::INVALID_DATA,
                ' Param $timing should be a valid json.'
            );
        }

        $data     = array(
            "v"      => $this->version,
            "s"      => $session_id,
            "uniq"   => $unique,
            "ts"     => $state,
            "m"      => $this->merchant_id,
            "timing" => $timing,
        );

        $endpoint = "https://".$this->server_name."/api/devicetrustbysession";
        $this->logger->debug("data endpoint: ".$endpoint);

        return $this->curl_service->__call_endpoint($endpoint, "POST", $data);
    }

    /**
     * set a trust state for a device based on device_id
     * @param $session_id
     * @param $unique
     * @param $state
     * @return mixed
     */
    public function deviceTrustByDevice($device_id, $unique, $state)
    {
        $this->checkState($state);

        $data     = array(
            "v"      => $this->version,
            "d"      => $device_id,
            "uniq"   => $unique,
            "ts"     => $state,
            "m"      => $this->merchant_id,
            "timing" => time(),
        );

        $endpoint = "https://".$this->server_name."/api/devicetrustbydevice";
        $this->logger->debug("data endpoint: ".$endpoint);

        return $this->curl_service->__call_endpoint($endpoint, "POST", $data);
    }

    /**
     * @param $session_id
     * @param null $unique
     * @param null $user_id
     * @param null $password
     * @return mixed
     * @throws AccessException
     */
    public function getInfo($session_id, $unique = null, $user_id = null, $password = null)
    {
        $this->verifySession($session_id);
        $this->checkRequiredInfo($unique, $user_id, $password);

        $endpoint = "https://$this->server_name/api/info";
        $this->logger->debug("info endpoint: ".$endpoint);

        $u = hash('sha256', $user_id);
        $p = hash('sha256', $password);
        $a = hash('sha256', $user_id.":".$password);

        $data = $this->buildInfoRequestParams($session_id, $unique, $user_id, $password);

        $this->logger->debug(
            "info request parameters : "."user_id = ".$u.', '."password = ".$p.', '."credentials = ".$a.', '."session_id = ".$session_id.', '."version = ".$this->version
        );

        return $this->curl_service->__call_endpoint($endpoint, "POST", $data);
    } //end getInfo

    /**
     * @param $session_id
     * @param null $unique
     * @param null $user_id
     * @param null $password
     * @return array
     */
    private function buildInfoRequestParams($session_id, $unique = null, $user_id = null, $password = null)
    {
        $data = array(
            "s" => $session_id,
            "v" => $this->version,
            "i" => $this->info_value,
        );

        if (!in_array($this->info_value, $this->not_required_for_info_values['uniq'])) {
            $data['uniq'] = $unique;
        }

        if (!in_array($this->info_value, $this->not_required_for_info_values['uh'])) {
            $u = hash('sha256', $user_id);
            $p = hash('sha256', $password);
            $a = hash('sha256', $user_id.":".$password);

            $data['uh'] = $u;
            $data['ph'] = $p;
            $data['ah'] = $a;
        }

        return $data;
    }

    /**
     * checks the required params for the getInfo method based on the $info_value
     * @param null $unique
     * @param null $user_id
     * @param null $password
     * @throws AccessException
     */
    public function checkRequiredInfo($unique = null, $user_id = null, $password = null)
    {
        if ($this->info_value == 0) {
            throw new AccessException(
                AccessException::INVALID_DATA, ' A response type should be requested before calling getInfo().'
            );
        }

        if (is_null($unique) && !in_array($this->info_value, $this->not_required_for_info_values['uniq'])) {
            throw new AccessException(
                AccessException::INVALID_DATA,
                ' Param $unique is required for getInfo() when requesting this type of response.'
            );
        }

        if ((is_null($user_id) || is_null($password)) && !in_array(
                $this->info_value,
                $this->not_required_for_info_values['uh']
            )) {
            throw new AccessException(
                AccessException::INVALID_DATA,
                ' Param $user_id аnd $password is required for getInfo() when requesting this type of response.'
            );
        }
    }

    /**
     * Setter for the device info in the response of getInfo
     * @return $this
     */
    public function withDeviceInfo()
    {
        $this->info_value        += $this->device_info_value;
        $this->device_info_value = 0;

        return $this;
    }

    /**
     * Setter for the velocity info in the response of getInfo
     * @return $this
     */
    public function withVelocity()
    {
        $this->info_value          += $this->velocity_info_value;
        $this->velocity_info_value = 0;

        return $this;
    }

    /**
     * Setter for the decision info in the response of getInfo
     * @return $this
     */
    public function withDecision()
    {
        $this->info_value          += $this->decision_info_value;
        $this->decision_info_value = 0;

        return $this;
    }

    /**
     * Setter for the decision info in the response of getInfo
     * @return $this
     */
    public function withTrustedDeviceInfo()
    {
        $this->info_value                += $this->trusted_device_info_value;
        $this->trusted_device_info_value = 0;

        return $this;
    }

    /**
     * Setter for the behavio sec info in the response of get_info
     * @return $this
     */
    public function withBehavioSec()
    {
        $this->info_value             += $this->behavio_sec_info_value;
        $this->behavio_sec_info_value = 0;

        return $this;
    }

    /**
     * retrieves behaviosec data from
     * @param $session_id
     * @param $unique
     * @param $timing
     * @param $behavioServer
     * @return mixed
     * @throws AccessException
     */
    public function behaviosecData($session_id, $unique, $timing, $behavioServer)
    {
        $behavioServer = rtrim(preg_replace('{^(http:\/\/|https:\/\/)}', '', $behavioServer), '/');

        if(!preg_match('{api\.behavio\.kaptcha\.com}', $behavioServer))
        {
            throw new AccessException(
                AccessException::INVALID_DATA, 'This method should be used with a different server: api.behavio.kaptcha.com'
            );
        }

        if(!is_array(json_decode($timing, true)))
        {
            throw new AccessException(
                AccessException::INVALID_DATA, 'The timing parameter should be a valid json field'
            );
        }

        $data     = array(
            "m"    => $this->merchant_id,
            "s"    => $session_id,
            "timing" => $timing,
            "uniq" => $unique,
        );
        $endpoint = "https://".$behavioServer."/behavio/data";
        $this->logger->debug("behavioSec endpoint: ".$endpoint);

        return $this->curl_service->__call_endpoint($endpoint, "POST", $data);
    }

    private function checkState($state)
    {
        if (!in_array($state, $this->trusted_states)) {
            throw new AccessException(
                AccessException::INVALID_DATA, 'The posted state must be one of: not_trusted, trusted, banned'
            );
        }
    }

} //end kount_access_api

