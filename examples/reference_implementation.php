<?php
/**
 * This is a SIMPLE reference implementation of how to use the three API calls
 * from the AccessCurlService.php class.
 * @copyright 2015 src, Inc. All Rights Reserved.
 */
//we are assuming that composer autoload is used, if not - you should require all the classes listed in src/ folder
require __DIR__."/../vendor/autoload.php";

//use the namespace
use Kount\AccessService;

/**
 * The KountAccessService can be set up once and then used multiple times
 * concurrently as needed. In this example we are creating it once and
 * passing it into all the example functions.  If you are just implementing 1
 * function, you can use this part and the function together. This just
 * makes the sample easier to manage as your credentials and server name are
 * in a single location.
 * @throws Kount\AccessException
 */

///////////////////////////////////////////////////////////////////////////
// Merchant information - replace with your own
///////////////////////////////////////////////////////////////////////////
$merchant_id = 0;
$api_key     = 'PUT_YOUR_API_KEY_HERE';

///////////////////////////////////////////////////////////////////////////
// Kount Access API server to use
///////////////////////////////////////////////////////////////////////////
$server = 'api-sandbox01.kountaccess.com';

///////////////////////////////////////////////////////////////////////////
// Sample Data Section (update with data used in your testing)
///////////////////////////////////////////////////////////////////////////
// Sample session ID (previously created by the server and passed to the
// data collector when it ran on the login page)
$session_id = '8f18a81cfb6e3179ece7138ac81019aa';

// Users credentials used to login for the test
$user     = 'admin';
$password = 'password';

///////////////////////////////////////////////////////////////////////////
// Now let's call each example and evaluate
///////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////
// If you are just looking for information about the device (like the
// device id, or pierced IP Address) then use the get_device function.
// This example shows how to get Device info and what it would look like as
// an associative array
///////////////////////////////////////////////////////////////////////////
try {
    echo "Example for calling Kount\AccessService->getDevice('$session_id')\n";
    $kountAccess = new AccessService($merchant_id, $api_key, $server);
    $response    = $kountAccess->getDevice($session_id);
    evaluateResponse($response);
} catch (\Exception $e) {
    //do something with the exception
    echo "Caught Exception:";
    var_dump($e->getMessage());
}


///////////////////////////////////////////////////////////////////////////
// If you make a bad request you will get an array abck with an ERROR_CODE
// and ERROR_MESSAGE in it. This could be cURL related (Networking
// issues?) or data releated (bad api key, invalid session_id, etc).
// This example shows what a bad request's response would look like as
// an associative array
///////////////////////////////////////////////////////////////////////////
try {
    echo "Example for calling Kount\AccessService->getDevice('BAD_SESSION_ID') with a bad session_id \n";
    $kountAccess = new AccessService($merchant_id, $api_key, $server);
    $response    = $kountAccess->getDevice('BAD_SESSION_ID');
    evaluateResponse($response);
} catch (\Exception $e) {
    //do something with the exception
    echo "Caught Exception:";
    var_dump($e->getMessage());
}


///////////////////////////////////////////////////////////////////////////
// This is an example of the type of response to expect when requesting
// Velocity information. The Device information is also included in this
// response. You can use this Velocity information in your own decisioning
// engine.
///////////////////////////////////////////////////////////////////////////
try {
    echo "Example calling Kount\AccessService->getVelocity('$session_id', '$user', '$password')\n";
    $kountAccess = new AccessService($merchant_id, $api_key, $server);
    $response    = $kountAccess->getVelocity($session_id, $user, $password);
    evaluateResponse($response);
} catch (\Exception $e) {
    //do something with the exception
    echo "Caught Exception:";
    var_dump($e->getMessage());
}


///////////////////////////////////////////////////////////////////////////
// If you want src Access to evaluate possible threats using our
// Thresholds Engine, you will want to call the get_decision endpoint.
// This example shows how to make the decision call and what it would look
// like as an associative array. This response includes Device information
// and Velocity data in addition to the Decision information. The decision
// value can be either "A" - Approve, or "D" - Decline.  In addition it will
// show the ruleEvents evaluated that forced a "D" (Decline) result. If you
// do not have any thresholds established it will always default to
// "A" (Approve). For more information on setting up thresholds, consult the
// User Guide.
///////////////////////////////////////////////////////////////////////////
try {
    echo "Example calling Kount\AccessService->getDecision('$session_id', '$user', '$password')\n";
    $kountAccess = new AccessService($merchant_id, $api_key, $server);
    $response    = $kountAccess->getDecision($session_id, $user, $password);
    evaluateResponse($response);
} catch (\Exception $e) {
    //do something with the exception
    echo "Caught Exception:";
    var_dump($e->getMessage());
}

///////////////////////////////////////////////////////////////////////////
//If you need to get devices based on unique identifiers
///////////////////////////////////////////////////////////////////////////
try {
    echo "Example for calling Kount\AccessService->getDevices('$user')\n";
    // Create an instance of the service
    $kountAccess = new AccessService($merchant_id, $api_key, $server, '0400');
    // Call desired method
    $response = $kountAccess->getDevices($user);
    // Do something with the response
    evaluateResponse($response);
} catch (\Exception $e) {
    //do something with the exception
    echo "Caught Exception:";
    var_dump($e->getMessage());
}



///////////////////////////////////////////////////////////////////////////
//If you need to get Unique identifiers by device
///////////////////////////////////////////////////////////////////////////
try {
    echo "Example for calling Kount\AccessService->getUniques('$session_id')\n";
    // Create an instance of the service
    $kountAccess = new AccessService($merchant_id, $api_key, $server, '0400');
    // Call desired method
    $response = $kountAccess->getUniques('DEVICE_ID');
    // Do something with the response
    evaluateResponse($response);
} catch (\Exception $e) {
    //do something with the exception
    echo "Caught Exception:";
    var_dump($e->getMessage());
}

///////////////////////////////////////////////////////////////////////////
//getInfo() for a device
//this method can have several different answers based on the methods called beforehand
// at least 1 method of the listed below is required before making the getInfo() call
// withDeviceInfo() - returns information about the device
// withVelocity() - returns information about velocity
// withDecision() - returns information about decision
// withTrystedDeviceInfo() - returns information about device trust state
// withBehavioSec() - returns information about behavio sec
///////////////////////////////////////////////////////////////////////////
try {
    echo "Example for calling Kount\AccessService->getInfo('$session_id')\n";
    // Create an instance of the service
    $kountAccess = new AccessService($merchant_id, $api_key, $server, '0400');
    // Call desired method
    $response = $kountAccess->withDeviceInfo()->withVelocity()->withDecision()->withTrustedDeviceInfo()->withBehavioSec()->getInfo($session_id, $user, $user, $password);
} catch (\Exception $e) {
    //do something with the exception
    echo "Caught Exception:";
    var_dump($e->getMessage());
}

///////////////////////////////////////////////////////////////////////////
// Set a trusted state status for a device based on the session
///////////////////////////////////////////////////////////////////////////
try {
    echo "Example for calling Kount\AccessService->deviceTrustBySession('$session_id')\n";
    // Create an instance of the service
    $kountAccess = new AccessService($merchant_id, $api_key, $server, '0400');
    // Call desired method
    $response = $kountAccess->deviceTrustBySession($session_id, $user, 'trusted');
} catch (\Exception $e) {
    //do something with the exception
    echo "Caught Exception:";
    var_dump($e->getMessage());
}

//////////////////////////////////////////////////////////////////////////
// Set a trusted state status for a device based on the session
///////////////////////////////////////////////////////////////////////////
try {
    echo "Example for calling Kount\AccessService->deviceTrustByDevice('$session_id')\n";
    // Create an instance of the service
    $kountAccess = new AccessService($merchant_id, $api_key, $server, '0400');
    // Call desired method
    $response = $kountAccess->deviceTrustByDevice('DEVICE_ID', $user, 'trusted');
} catch (\Exception $e) {
    //do something with the exception
    echo "Caught Exception:";
    var_dump($e->getMessage());
}

//////////////////////////////////////////////////////////////////////////
// use behavioSec Data
///////////////////////////////////////////////////////////////////////////
try {
    echo "Example for calling Kount\AccessService->behaviosecData('$session_id')\n";
    // Create an instance of the service
    $kountAccess = new AccessService($merchant_id, $api_key, $server, '0400');
    // Call desired method
    $response = $kountAccess->behaviosecData('DEVICE_ID', $user, 'trusted', "api.behavio.kaptcha.com/sandbox/");
} catch (\Exception $e) {
    //do something with the exception
    echo "Caught Exception:";
    var_dump($e->getMessage());
}


/**
 * Simple evaluator that either prints the errors, or the associative array
 * result
 */
function evaluateResponse($response)
{
    echo "//////////////START RESPONSE//////////////////////\n";
    // Check for an error
    if ($response['ERROR_CODE']) {
        // Handle the Error. The two keys in the error array are ERROR_CODE and
        // ERROR_MESSAGE
        echo "Error code [".$response['ERROR_CODE']."] returned with message [".$response['ERROR_MESSAGE']."]\n";
    } else {
        // do something with the response
        echo "Got a response:";
        var_dump($response);
    }
    echo "///////////////END RESPONSE///////////////////////\n";
}
