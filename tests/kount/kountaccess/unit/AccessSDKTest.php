<?php

use Kount\AccessService;
use Kount\AccessCurlService;
use Kount\AccessException;

require __DIR__."/../../../../vendor/autoload.php";

class AccessSDKTest extends \PHPUnit\Framework\TestCase
{

    const VERSION = "0400";
    const MERCHANT_ID = 123456;

    const API_KEY = "PUT_YOUR_API_KEY_HERE";
    const SERVER_URL = "api-sandbox01.kountaccess.com";
    const FINGERPRINT = '75012bd5e5b264c4b324f5c95a769541';
    const SESSION_ID = "8f18a81cfb6e3179ece7138ac81019aa";
    const USER = "admin@test.com";
    const PASSWORD = "password";
    const IP_ADDRESS = "64.128.91.251";
    const IP_GEO = "US";
    const RESPONSE_ID = "bf10cd20cf61286669e87342d029e405";
    const DECISION = "A";


    public function testAccessInit()
    {
        try {
            $kount_access = new AccessService(self::MERCHANT_ID, self::API_KEY, self::MERCHANT_ID.".kountaccess.com", self::VERSION);
            $this->assertNotNull($kount_access);
        } catch (AccessException $e) {
            echo "Bad src Access Exception ".$e->getAccessErrorType().":".$e->getMessage();
        }
    }

    public function testAccessInitNoHost()
    {
        try {
            $kount_access = new AccessService(self::MERCHANT_ID, self::API_KEY, null, self::VERSION);
            $this->fail('Should have failed host.');
        } catch (AccessException $ae) {
            $this->assertEquals(AccessException::INVALID_DATA, $ae->getAccessErrorType());
        }
    }

    public function testAccessInitBadMerchant()
    {
        try {
            $kount_access = new AccessService(-1, self::API_KEY, self::MERCHANT_ID.".kountaccess.com", self::VERSION);
            $this->fail("Should have failed MERCHANT_ID");
        } catch (AccessException $ae) {
            $this->assertEquals(AccessException::INVALID_DATA, $ae->getAccessErrorType());
        }
    }

    public function testAccessInitNoAPI_KEY()
    {
        try {
            $kount_access = new AccessService(self::MERCHANT_ID, null, self::MERCHANT_ID.".kountaccess.com", self::VERSION);
            $this->fail("Should have failed API_KEY");
        } catch (AccessException $ae) {
            $this->assertEquals(AccessException::INVALID_DATA, $ae->getAccessErrorType());
        }
    }

    public function testAccessInitBlankAPI_KEY()
    {
        try {
            $kount_access = new AccessService(self::MERCHANT_ID, " ", self::MERCHANT_ID.".kountaccess.com", self::VERSION);
            $this->fail("Should have failed API_KEY");
        } catch (AccessException $ae) {
            $this->assertEquals(AccessException::INVALID_DATA, $ae->getAccessErrorType());
        }
    }

    public function testGetDevice()
    {
        Logger::configure(__DIR__.'/../../../../config.xml');
        $this->logger = Logger::getLogger("Test Access Logger");

        $device_json      = '{"device": {"id": "'.self::FINGERPRINT.'", "ipAddress": "'.self::IP_ADDRESS.'", "ipGeo": "'.self::IP_GEO.'", "mobile": 1, "proxy": 0 }, "response_id": "'.self::RESPONSE_ID.'"}';
        $mock = $this->getMockBuilder(AccessCurlService::class)->setConstructorArgs(
            array(self::MERCHANT_ID, self::API_KEY)
        )->setMethods(['__call_endpoint'])->getMock();

        $mock->expects($this->any())->method('__call_endpoint')->will($this->returnValue($device_json));

        $kount_access = new AccessService(self::MERCHANT_ID, self::API_KEY, self::MERCHANT_ID.".kountaccess.com", self::VERSION, $mock);

        $deviceInfo = $kount_access->getDevice(self::SESSION_ID);
        $this->assertNotNull($deviceInfo);

        $deviceInfoDecoded = json_decode($deviceInfo, true);
        $this->logger->debug($deviceInfoDecoded);

        $this->assertEquals(self::FINGERPRINT, $deviceInfoDecoded['device']['id']);
        $this->assertEquals(self::IP_ADDRESS, $deviceInfoDecoded['device']['ipAddress']);
        $this->assertEquals(self::IP_GEO, $deviceInfoDecoded['device']['ipGeo']);
        $this->assertEquals(1, $deviceInfoDecoded['device']['mobile']);
        $this->assertEquals(0, $deviceInfoDecoded['device']['proxy']);
        $this->assertEquals(self::RESPONSE_ID, $deviceInfoDecoded['response_id']);
    }

    public function testGetVelocity()
    {
        Logger::configure(__DIR__.'/../../../../config.xml');
        $this->logger = Logger::getLogger("Test Access Logger");
        $velocity_json    = '{"device": {"id": "'.self::FINGERPRINT.'", "ipAddress": "'.self::IP_ADDRESS.'", "ipGeo": "'.self::IP_GEO.'", "mobile": 1, "proxy": 0 }, "response_id": "'.self::RESPONSE_ID.'", "velocity": {"account": {"dlh": 1, "dlm": 1, "iplh": 1, "iplm": 1, "plh": 1, "plm": 1, "ulh": 1, "ulm": 1 }, "device": {"alh": 1, "alm": 1, "iplh": 1, "iplm": 1, "plh": 1, "plm": 1, "ulh": 1, "ulm": 1 }, "ip_address": {"alh": 1, "alm": 1, "dlh": 1, "dlm": 1, "plh": 1, "plm": 1, "ulh": 1, "ulm": 1 }, "password": {"alh": 1, "alm": 1, "dlh": 1, "dlm": 1, "iplh": 1, "iplm": 1, "ulh": 1, "ulm": 1 }, "user": {"alh": 1, "alm": 1, "dlh": 1, "dlm": 1, "iplh": 1, "iplm": 1, "plh": 1, "plm": 1 }}}';
        $mock = $this->getMockBuilder(AccessCurlService::class)->setConstructorArgs(
            array(self::MERCHANT_ID, self::API_KEY)
        )->setMethods(['__call_endpoint'])->getMock();

        $mock->expects($this->any())->method('__call_endpoint')->will($this->returnValue($velocity_json));

        $kount_access = new AccessService(self::MERCHANT_ID, self::API_KEY, self::MERCHANT_ID.".kountaccess.com", self::VERSION, $mock);

        $velocity = $kount_access->getVelocity(self::SESSION_ID, self::USER, self::PASSWORD);
        $this->assertNotNull($velocity);

        $velocityInfo = json_decode($velocity, true);
        $this->logger->debug($velocityInfo);

        $device = $velocityInfo['device'];

        $this->assertEquals(self::FINGERPRINT, $device['id']);
        $this->assertEquals(self::IP_ADDRESS, $device['ipAddress']);
        $this->assertEquals(self::IP_GEO, $device['ipGeo']);
        $this->assertEquals(1, $device['mobile']);
        $this->assertEquals(0, $device['proxy']);
        $this->assertEquals(self::RESPONSE_ID, $velocityInfo['response_id']);

        $this->assertNotNull($velocityInfo['velocity']);

        $velocityJson = json_decode($velocity_json, true);

        $this->assertEquals($velocityJson['velocity']['account'], $velocityInfo['velocity']['account']);
        $this->assertEquals($velocityJson['velocity']['device'], $velocityInfo['velocity']['device']);
        $this->assertEquals($velocityJson['velocity']['ip_address'], $velocityInfo['velocity']['ip_address']);
        $this->assertEquals($velocityJson['velocity']['password'], $velocityInfo['velocity']['password']);
        $this->assertEquals($velocityJson['velocity']['user'], $velocityInfo['velocity']['user']);
    }

    public function testGetDecision()
    {
        Logger::configure(__DIR__.'/../../../../config.xml');
        $this->logger = Logger::getLogger("Test Access Logger");
        $decision_json    = '{"decision": {"errors": [], "reply": {"ruleEvents": {"decision": "'.self::DECISION.'", "ruleEvents": [], "total": 0 } }, "warnings": [] }, "device": {"id": "'.self::FINGERPRINT.'", "ipAddress": "'.self::IP_ADDRESS.'", "ipGeo": "'.self::IP_GEO.'", "mobile": 1, "proxy": 0 }, "response_id": "'.self::RESPONSE_ID.'", "velocity": {"account": {"dlh": 1, "dlm": 1, "iplh": 1, "iplm": 1, "plh": 1, "plm": 1, "ulh": 1, "ulm": 1 }, "device": {"alh": 1, "alm": 1, "iplh": 1, "iplm": 1, "plh": 1, "plm": 1, "ulh": 1, "ulm": 1 }, "ip_address": {"alh": 1, "alm": 1, "dlh": 1, "dlm": 1, "plh": 1, "plm": 1, "ulh": 1, "ulm": 1 }, "password": {"alh": 1, "alm": 1, "dlh": 1, "dlm": 1, "iplh": 1, "iplm": 1, "ulh": 1, "ulm": 1 }, "user": {"alh": 1, "alm": 1, "dlh": 1, "dlm": 1, "iplh": 1, "iplm": 1, "plh": 1, "plm": 1 }}}';
        $mock = $this->getMockBuilder(AccessCurlService::class)->setConstructorArgs(
            array(self::MERCHANT_ID, self::API_KEY)
        )->setMethods(['__call_endpoint'])->getMock();

        $mock->expects($this->any())->method('__call_endpoint')->will($this->returnValue($decision_json));

        $kount_access = new AccessService(self::MERCHANT_ID, self::API_KEY, self::MERCHANT_ID.".kountaccess.com", self::VERSION, $mock);

        $decision = $kount_access->getDecision(self::SESSION_ID, self::USER, self::PASSWORD);
        $this->assertNotNull($decision);

        $decisionInfo = json_decode($decision, true);
        $this->logger->debug($decisionInfo);

        $device = $decisionInfo['device'];

        $this->assertEquals(self::FINGERPRINT, $device['id']);
        $this->assertEquals(self::IP_ADDRESS, $device['ipAddress']);
        $this->assertEquals(self::IP_GEO, $device['ipGeo']);
        $this->assertEquals(1, $device['mobile']);
        $this->assertEquals(0, $device['proxy']);
        $this->assertEquals(self::RESPONSE_ID, $decisionInfo['response_id']);

        $this->assertNotNull($decisionInfo['velocity']);

        $decisionJson = json_decode($decision_json, true);

        $this->assertEquals($decisionJson['velocity']['account'], $decisionInfo['velocity']['account']);
        $this->assertEquals($decisionJson['velocity']['device'], $decisionInfo['velocity']['device']);
        $this->assertEquals($decisionJson['velocity']['ip_address'], $decisionInfo['velocity']['ip_address']);
        $this->assertEquals($decisionJson['velocity']['password'], $decisionInfo['velocity']['password']);
        $this->assertEquals($decisionJson['velocity']['user'], $decisionInfo['velocity']['user']);
    }

    public function testGetDevices()
    {
        Logger::configure(__DIR__.'/../../../../config.xml');
        $this->logger = Logger::getLogger("Test Access Logger");
        $mock = $this->getMockBuilder(AccessCurlService::class)->setConstructorArgs(
            array(self::MERCHANT_ID, self::API_KEY)
        )->setMethods(['__call_endpoint'])->getMock();

        $fakeDeviceId = 'FAKE_DEVICE_ID';
        $fakeResponse = '{"response_id": "'.self::RESPONSE_ID.'", "devices": [{"deviceid": "'.$fakeDeviceId.'", "truststate": "TRUSTED", "datefirstseen": "date and time", "friendlyname": null } ] }';

        $mock->expects($this->any())->method('__call_endpoint')->will($this->returnValue($fakeResponse));

        $kount_access = new AccessService(self::MERCHANT_ID, self::API_KEY, self::MERCHANT_ID.".kountaccess.com", self::VERSION, $mock);

        $deviceInfo = $kount_access->getDevices(self::SESSION_ID);
        $this->assertNotNull($deviceInfo);

        $deviceInfoDecoded = json_decode($deviceInfo, true);
        $this->logger->debug($deviceInfoDecoded);

        $this->assertEquals(self::RESPONSE_ID, $deviceInfoDecoded['response_id']);
        $this->assertEquals($fakeDeviceId, $deviceInfoDecoded['devices'][0]['deviceid']);
    }

    public function testGetUniques()
    {
        Logger::configure(__DIR__.'/../../../../config.xml');
        $this->logger = Logger::getLogger("Test Access Logger");
        $mock = $this->getMockBuilder(AccessCurlService::class)->setConstructorArgs(
            array(self::MERCHANT_ID, self::API_KEY)
        )->setMethods(['__call_endpoint'])->getMock();

        $fakeDeviceId = 'FAKE_DEVICE_ID';
        $fakeResponse = '{"response_id": "'.self::RESPONSE_ID.'", "uniques": [{"unique": "'.$fakeDeviceId.'", "datelastseen": "2018-05-01 23:59:59", "truststate": "trusted"} ] }';

        $mock->expects($this->any())->method('__call_endpoint')->will($this->returnValue($fakeResponse));

        $kount_access = new AccessService(self::MERCHANT_ID, self::API_KEY, self::MERCHANT_ID.".kountaccess.com", self::VERSION, $mock);

        $uniqueInfo = $kount_access->getUniques($fakeDeviceId);
        $this->assertNotNull($uniqueInfo);

        $uniqueInfoDecoded = json_decode($uniqueInfo, true);
        $this->logger->debug($uniqueInfoDecoded);

        $this->assertEquals(self::RESPONSE_ID, $uniqueInfoDecoded['response_id']);
        $this->assertEquals($fakeDeviceId, $uniqueInfoDecoded['uniques'][0]['unique']);
    }

    public function testCheckRequiredInfoThrowsAnExceptionOnNoRequiredResponseCalledBeforehand()
    {
        try {
            $kount_access = new AccessService(self::MERCHANT_ID, self::API_KEY, self::MERCHANT_ID.".kountaccess.com", self::VERSION);
            $kount_access->checkRequiredInfo();
            $this->fail(
                'Should have thrown KountAccessException for not called require_object() before invoking the method.'
            );
        } catch (AccessException $e) {
            $this->assertEquals(AccessException::INVALID_DATA, $e->getAccessErrorType());
        }
    }

    public function testCheckRequiredThrowsAnExceptionOnMissingUnique()
    {
        try {
            $kount_access = new AccessService(self::MERCHANT_ID, self::API_KEY, self::MERCHANT_ID.".kountaccess.com", self::VERSION);
            $kount_access->withDeviceInfo();
            $kount_access->withTrustedDeviceInfo();

            $kount_access->checkRequiredInfo();

            $this->fail('Should have thrown KountAccessException for required param - unique.');
        } catch (AccessException $e) {
            $this->assertEquals(AccessException::INVALID_DATA, $e->getAccessErrorType());
        }
    }

    public function testCheckRequiredThrowsAnExceptionOnMissingUserPassword()
    {
        try {
            $kount_access = new AccessService(self::MERCHANT_ID, self::API_KEY, self::MERCHANT_ID.".kountaccess.com", self::VERSION);
            $kount_access->withDeviceInfo();
            $kount_access->withTrustedDeviceInfo();
            $kount_access->withVelocity();

            $kount_access->checkRequiredInfo('FakeUniqueParam');

            $this->fail('Should have thrown KountAccessException for required param - username and password.');
        } catch (AccessException $e) {
            $this->assertEquals(AccessException::INVALID_DATA, $e->getAccessErrorType());
        }
    }


    public function testGetInfoReturnesDeviceInfoResponse()
    {
        Logger::configure(__DIR__.'/../../../../config.xml');
        $this->logger = Logger::getLogger("Test Access Logger");
        $device_info_json = '{"device": {"id": "55e9fbfda2ce489d83b4a99c84c6f3e1", "ipAddress": "64.128.91.251", "ipGeo": "US", "mobile": 0, "proxy": 0, "tor": 0, "region": "ID", "country": "US", "geoLat": 43.632, "geoLong": -116.2838 },"response_id": "c0a09c2736074c00b248d978ff7faeb6"}';
        $mock = $this->getMockBuilder(AccessCurlService::class)->setConstructorArgs(
            array(self::MERCHANT_ID, self::API_KEY)
        )->setMethods(['__call_endpoint'])->getMock();

        $mock->expects($this->any())->method('__call_endpoint')->will($this->returnValue($device_info_json));

        $kount_access = new AccessService(self::MERCHANT_ID, self::API_KEY, self::MERCHANT_ID.".kountaccess.com", self::VERSION, $mock);

        $apiResponse = $kount_access->withDeviceInfo()->getInfo(self::SESSION_ID);
        $this->assertNotNull($apiResponse);
        $this->assertJson($apiResponse);

        $deviceInfoDecoded = json_decode($apiResponse);
        $this->logger->debug($deviceInfoDecoded);

        $this->assertObjectHasAttribute('response_id', $deviceInfoDecoded);
        $this->assertObjectHasAttribute('device', $deviceInfoDecoded);
    }


    public function testDeviceTrustBySessionShouldAcceptOnlyValidStates()
    {
        try {
            $kount_access = new AccessService(self::MERCHANT_ID, self::API_KEY, self::MERCHANT_ID.".kountaccess.com", self::VERSION);

            $kount_access->deviceTrustBySession(self::SESSION_ID, self::USER, 'INVALID_STATE');

            $this->fail('Should have thrown KountAccessException for an invalid passed state ');
        } catch (AccessException $e) {
            $this->assertEquals(AccessException::INVALID_DATA, $e->getAccessErrorType());
        }
    }

    public function testDeviceTrustByDeviceShouldAcceptOnlyValidStates()
    {
        try {
            $kount_access = new AccessService(self::MERCHANT_ID, self::API_KEY, self::MERCHANT_ID.".kountaccess.com", self::VERSION);
            $fakeDeviceId = 'FAKE_DEVICE_ID';
            $kount_access->deviceTrustByDevice($fakeDeviceId, self::USER, 'INVALID_STATE');

            $this->fail('Should have thrown KountAccessException for an invalid passed state ');
        } catch (AccessException $e) {
            $this->assertEquals(AccessException::INVALID_DATA, $e->getAccessErrorType());
        }
    }

}