<?php

namespace Kount;

/**
 * Service curl access class.
 * This class provides a helper function to utilize the src Access API
 * service.  In order to use this service you will be required to furnish:
 *   * Your Merchant ID
 *   * Your API Key
 * @version 2.1.0
 * @copyright 2015 src, Inc. All Rights Reserved.
 */
class AccessCurlService
{

    /**
     * Options for curl call
     */
    private $encoded_credentials;

    /**
     * Possible error code.
     * @var string
     */
    private $error_code;

    /**
     * Possible error code.
     * @var string
     */
    private $error_msg;

    /**
     * AccessCurlService constructor.
     * Initializes __encoded_credentials variable used in curl call.
     * @param $merchant_id
     * @param $api_key
     */
    public function __construct($merchant_id, $api_key)
    {
        $this->encoded_credentials = base64_encode($merchant_id.":".$api_key);
    }

    /**
     * Call a service endpoint.
     * @param string $endpoint URL to endpoint
     * @param string $method Either POST or GET
     * @param array $params POST parameters
     * @throws AccessException::NETWORK_ERROR if there is a problem with the curl call
     * @return array JSON Response decoded or error array with cURL's
     *               ERROR_CODE and ERROR_MESSAGE values
     */
    public function __call_endpoint($endpoint, $method = null, $params = null)
    {
        $options = array(
            CURLOPT_FAILONERROR    => false,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; Service_Client/$Revision: 22622 $;)',
            CURLOPT_COOKIESESSION  => true,
            CURLOPT_HTTPHEADER     => array(
                "Accept: text/json",
                "Authorization: Basic $this->encoded_credentials",
            ),
            CURLOPT_CONNECTTIMEOUT => 3, // 3 seconds
            CURLOPT_TIMEOUT        => 5, // 5 seconds
            CURLOPT_HEADER         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL            => $endpoint,
        );
        if ("POST" == $method) {
            $options[CURLOPT_POST]       = true;
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
        $err_code  = curl_errno($ch);
        $resp_body = "";

        if (CURLE_OK == $err_code) {
            // parse the raw response
            $info      = curl_getinfo($ch);
            $resp_code = (int)$info['http_code'];
            $hdr_size  = $info['header_size'];
            $msg       = mb_substr(
                $raw_resp,
                $hdr_size,
                mb_strlen($raw_resp, 'latin1'),
                'latin1'
            );
            if (200 != $resp_code) {
                $resp_body = array(
                    $this->error_code => $resp_code,
                    $this->error_msg  => $msg,
                );
                throw new AccessException(AccessException::NETWORK_ERROR, "Bad Response(".$resp_code.") ".$msg);
            } else {
                $resp_body = json_decode($msg, true);
            }
        } else {
            $resp_body = array(
                $this->error_code => $err_code,
                $this->error_msg  => curl_error($ch),
            );
            throw new AccessException(AccessException::NETWORK_ERROR, "Bad Response(".$err_code.") ".curl_error($ch));
        }

        curl_close($ch);

        return $resp_body;
    } //end call_endpoint

}