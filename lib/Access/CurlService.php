<?php

/**
 * Service curl access class.
 * This class provides a helper function to utilize the Kount Access API
 * service.  In order to use this service you will be required to furnish:
 *   * Your Merchant ID
 *   * Your API Key
 *
 * @version 2.1.0
 * @copyright 2015 Kount, Inc. All Rights Reserved.
 */
class Access_CurlService
{

  /**
   * Options for curl call
   */
  private $__encoded_credentials;

  /**
   * Possible error code.
   * @var string
   */
  private $errorCode;

  /**
   * Possible error code.
   * @var string
   */
  private $errorMsg;

  /**
   * Access_CurlService constructor.
   *
   * Initializes __encoded_credentials variable used in curl call.
   *
   * @param $merchant_id
   * @param $api_key
   */

  /**
   * A logger instance.
   * @var Access_Log_Logger_Logger
   */
  protected $logger;

  public function __construct($merchant_id, $api_key) {
    $loggerFactory = Access_Log_Factory_LogFactory::getLogFactory();
    $this->logger = $loggerFactory->getLogger(__CLASS__);

    $this->__encoded_credentials = base64_encode($merchant_id . ":" . $api_key);
    $this->logger->debug("encoded credentials: " . $this->__encoded_credentials);
  }

  /**
   * Call a service endpoint.
   *
   * @param string $endpoint URL to endpoint
   * @param string $method Either POST or GET
   * @param array $params POST parameters
   * @throws Access_Exception::NETWORK_ERROR if there is a problem with the curl call
   * @return array JSON Response decoded or error array with cURL's
   *               ERROR_CODE and ERROR_MESSAGE values
   */
  public function __call_endpoint($endpoint, $method = null, $params = null)
  {
    $options = array(
      CURLOPT_FAILONERROR => false,
      CURLOPT_USERAGENT =>
        'Mozilla/5.0 (compatible; Service_Client/$Revision: 22622 $;)',
      CURLOPT_COOKIESESSION => true,
      CURLOPT_HTTPHEADER => array(
        "Accept: text/json",
        "Authorization: Basic $this->__encoded_credentials",
      ),
      CURLOPT_CONNECTTIMEOUT => 3, // 3 seconds
      CURLOPT_TIMEOUT => 5, // 5 seconds
      CURLOPT_HEADER => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_URL => $endpoint,
    );
    if ("POST" == $method) {
      $options[CURLOPT_POST] = true;
      $options[CURLOPT_POSTFIELDS] = $params;
    } else {
      $options[CURLOPT_CUSTOMREQUEST] = $method;
    }

    // Create curl resource
    $ch = curl_init();
    //Set Curl options
    curl_setopt_array($ch, $options);
    // Execute the request
    $raw_resp = curl_exec($ch);

    //Parse the response
    $err_code = curl_errno($ch);
    $resp_body = "";

    if (CURLE_OK == $err_code) {
      // parse the raw response
      $info = curl_getinfo($ch);
      $resp_code = (int)$info['http_code'];
      $hdr_size = $info['header_size'];
      $msg = mb_substr($raw_resp, $hdr_size,
        mb_strlen($raw_resp, 'latin1'), 'latin1');
      if (200 != $resp_code) {
        $resp_body = array(
          $this->errorCode => $resp_code,
          $this->errorMsg => $msg,
        );
        throw new Access_Exception(Access_Exception::NETWORK_ERROR, "Bad Response(" . $resp_code . ") " . $msg);
      } else {
        $resp_body = json_decode($msg, true);
      }
    } else {
      $resp_body = array(
        $this->errorCode => $err_code,
        $this->errorMsg => curl_error($ch),
      );
      throw new Access_Exception(Access_Exception::NETWORK_ERROR, "Bad Response(" . $err_code . ") " . curl_error($ch));
    }

    curl_close($ch);
    return $resp_body;
  } //end call_endpoint

} // end Access_CurlService