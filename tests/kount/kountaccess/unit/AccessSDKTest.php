<?php

require __DIR__ . "/../../../../lib/kount_access_service.php";
require __DIR__ . "/../../../../vendor/autoload.php";

class AccessSDKTest extends PHPUnit_Framework_TestCase {

  const version     = "0210";
  const merchantId  = 999999;
  const host        = self::merchantId . ".kountaccess.com";
  const accessUrl   = "https://" . self::host . "/access";
  const apiKey      = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiI5OTk2NjYiLCJhdWQiOiJLb3VudC4xIiwiaWF0IjoxNDk0NTM0Nzk5LCJzY3AiOnsia2EiOm51bGwsImtjIjpudWxsLCJhcGkiOmZhbHNlLCJyaXMiOnRydWV9fQ.eMmumYFpIF-d1up_mfxA5_VXBI41NSrNVe9CyhBUGck";
  const serverUrl   = "api-sandbox02.kountaccess.com";
  const fingerprint = '75012bd5e5b264c4b324f5c95a769541';
  const sessionId   = "8f18a81cfb6e3179ece7138ac81019aa";
  const sessionUrl  = "https://" . self::host . "/api/session=" . self::sessionId;
  const user        = "admin@test.com";
  const password    = "password";
  const ipAddress   = "64.128.91.251";
  const ipGeo       = "US";
  const responseId  = "bf10cd20cf61286669e87342d029e405";
  const decision    = "A";

  const deviceJSON   = "{" . "    \"device\": {" . "        \"id\": \"" . self::fingerprint . "\", "
  . "        \"ipAddress\": \"" . self::ipAddress . "\", " . "        \"ipGeo\": \"" . self::ipGeo . "\", "
  . "        \"mobile\": 1, " . "        \"proxy\": 0" . "    }," . "    \"response_id\": \"" . self::responseId
  . "\"" . "}";

  private $logger;

  public function __construct()
  {
    Logger::configure(__DIR__ . '/../../../../config.xml');
    $this->logger = Logger::getLogger("Test Access Logger");
  }

  const velocityJSON = "{" . "    \"device\": {" . "        \"id\": \"" . self::fingerprint . "\", "
                           . "        \"ipAddress\": \"" . self::ipAddress . "\", " . "        \"ipGeo\": \"" . self::ipGeo . "\", "
                           . "        \"mobile\": 1, " . "        \"proxy\": 0" . "    }, " . "    \"response_id\": \"" . self::responseId
                           . "\", " . "    \"velocity\": {" . "        \"account\": {" . "            \"dlh\": 1, "
                           . "            \"dlm\": 1, " . "            \"iplh\": 1, " . "            \"iplm\": 1, "
                           . "            \"plh\": 1, " . "            \"plm\": 1, " . "            \"ulh\": 1, "
                           . "            \"ulm\": 1" . "        }, " . "        \"device\": {" . "            \"alh\": 1, "
                           . "            \"alm\": 1, " . "            \"iplh\": 1, " . "            \"iplm\": 1, "
                           . "            \"plh\": 1, " . "            \"plm\": 1, " . "            \"ulh\": 1, "
                           . "            \"ulm\": 1" . "        }, " . "        \"ip_address\": {" . "            \"alh\": 1, "
                           . "            \"alm\": 1, " . "            \"dlh\": 1, " . "            \"dlm\": 1, "
                           . "            \"plh\": 1, " . "            \"plm\": 1, " . "            \"ulh\": 1, "
                           . "            \"ulm\": 1" . "        }, " . "        \"password\": {" . "           \"alh\": 1, "
                           . "           \"alm\": 1, " . "           \"dlh\": 1, " . "           \"dlm\": 1, "
                           . "            \"iplh\": 1, " . "            \"iplm\": 1, " . "            \"ulh\": 1, "
                           . "            \"ulm\": 1" . "        }, " . "        \"user\": {" . "            \"alh\": 1, "
                           . "            \"alm\": 1, " . "            \"dlh\": 1, " . "            \"dlm\": 1, "
                           . "            \"iplh\": 1, " . "            \"iplm\": 1, " . "            \"plh\": 1, "
                           . "            \"plm\": 1" . "        }" . "    }" . "}";


  const decisionJSON = "{" . "   \"decision\": {" . "       \"errors\": []," . "       \"reply\": {"
                           . "           \"ruleEvents\": {" . "               \"decision\": \"" . self::decision . "\","
                           . "               \"ruleEvents\": []," . "               \"total\": 0" . "           }" . "       },"
                           . "       \"warnings\": []" . "   }," . "    \"device\": {" . "        \"id\": \"" . self::fingerprint . "\", "
                           . "        \"ipAddress\": \"" . self::ipAddress . "\", " . "       \"ipGeo\": \"" . self::ipGeo . "\", "
                           . "        \"mobile\": 1, " . "        \"proxy\": 0" . "    }, " . "    \"response_id\": \"" . self::responseId
                           . "\", " . "    \"velocity\": {" . "        \"account\": {" . "            \"dlh\": 1, "
                           . "            \"dlm\": 1, " . "            \"iplh\": 1, " . "            \"iplm\": 1, "
                           . "            \"plh\": 1, " . "            \"plm\": 1, " . "            \"ulh\": 1, "
                           . "            \"ulm\": 1" . "        }, " . "        \"device\": {" . "            \"alh\": 1, "
                           . "            \"alm\": 1, " . "            \"iplh\": 1, " . "            \"iplm\": 1, "
                           . "            \"plh\": 1, " . "            \"plm\": 1, " . "            \"ulh\": 1, "
                           . "            \"ulm\": 1" . "        }, " . "        \"ip_address\": {" . "            \"alh\": 1, "
                           . "            \"alm\": 1, " . "            \"dlh\": 1, " . "            \"dlm\": 1, "
                           . "            \"plh\": 1, " . "            \"plm\": 1, " . "            \"ulh\": 1, "
                           . "            \"ulm\": 1" . "        }, " . "        \"password\": {" . "           \"alh\": 1, "
                           . "           \"alm\": 1, " . "           \"dlh\": 1, " . "           \"dlm\": 1, "
                           . "            \"iplh\": 1, " . "            \"iplm\": 1, " . "            \"ulh\": 1, "
                           . "            \"ulm\": 1" . "        }, " . "        \"user\": {" . "            \"alh\": 1, "
                           . "            \"alm\": 1, " . "            \"dlh\": 1, " . "            \"dlm\": 1, "
                           . "            \"iplh\": 1, " . "            \"iplm\": 1, " . "            \"plh\": 1, "
                           . "            \"plm\": 1" . "        }" . "    }" . "}";
  

  public function testAccessInit () {
    try {
      $kount_access = new Kount_Access_Service(self::merchantId, self::apiKey, self::host, self::version);
      $this->assertNotNull($kount_access);
    } catch (Kount_Access_Exception $e) {
      echo "Bad Kount Access Exception " . $e->getAccessErrorType() . ":" . $e->getMessage();
    }
  }

  public function testAccessInitNoHost() {
    try {
      $kount_access = new Kount_Access_Service(self::merchantId, self::apiKey, null, self::version);
      $this->fail('Should have failed host.');
    } catch (Kount_Access_Exception $ae) {
      $this->assertEquals(Kount_Access_Exception::INVALID_DATA, $ae->getAccessErrorType());
    }
  }

  public function testAccessInitBadMerchant() {
    try {
      $kount_access = new Kount_Access_Service(-1, self::apiKey, self::host, self::version);
      $this->fail("Should have failed merchantId");
    } catch (Kount_Access_Exception $ae) {
      $this->assertEquals(Kount_Access_Exception::INVALID_DATA, $ae->getAccessErrorType());
    }
  }

  public function testAccessInitNoApiKey() {
    try {
      $kount_access = new Kount_Access_Service(self::merchantId, null, self::host, self::version);
      $this->fail("Should have failed apiKey");
    } catch (Kount_Access_Exception $ae) {
      $this->assertEquals(Kount_Access_Exception::INVALID_DATA, $ae->getAccessErrorType());
    }
  }

  public function testAccessInitBlankApiKey() {
    try {
      $kount_access = new Kount_Access_Service(self::merchantId, " ", self::host, self::version);
      $this->fail("Should have failed apiKey");
    } catch (Kount_Access_Exception $ae) {
      $this->assertEquals(Kount_Access_Exception::INVALID_DATA, $ae->getAccessErrorType());
    }
  }

  public function testGetDevice() {
    $mock = $this->getMockBuilder(Kount_access_curl_service::class)
                 ->setConstructorArgs(array(self::merchantId, self::apiKey))
                 ->setMethods(['__call_endpoint'])
                 ->getMock();

    $mock->expects($this->any())
         ->method('__call_endpoint')
         ->will($this->returnValue(self::deviceJSON));

    $kount_access = new Kount_Access_Service(self::merchantId, self::apiKey, self::host, self::version, $mock);

    $deviceInfo = $kount_access->get_device(self::sessionId);
    $this->assertNotNull($deviceInfo);

    $deviceInfoDecoded = json_decode($deviceInfo, true);
    $this->logger->debug($deviceInfoDecoded);

    $this->assertEquals(self::fingerprint, $deviceInfoDecoded['device']['id']);
    $this->assertEquals(self::ipAddress, $deviceInfoDecoded['device']['ipAddress']);
    $this->assertEquals(self::ipGeo, $deviceInfoDecoded['device']['ipGeo']);
    $this->assertEquals(1, $deviceInfoDecoded['device']['mobile']);
    $this->assertEquals(0, $deviceInfoDecoded['device']['proxy']);
    $this->assertEquals(self::responseId, $deviceInfoDecoded['response_id']);
  }

  public function testGetVelocity() {
    $mock = $this->getMockBuilder(Kount_access_curl_service::class)
                 ->setConstructorArgs(array(self::merchantId, self::apiKey))
                 ->setMethods(['__call_endpoint'])
                 ->getMock();

    $mock->expects($this->any())
         ->method('__call_endpoint')
         ->will($this->returnValue(self::velocityJSON));

    $kount_access = new Kount_Access_Service(self::merchantId, self::apiKey, self::host, self::version, $mock);

    $velocity = $kount_access->get_velocity(self::sessionId, self::user, self::password);
    $this->assertNotNull($velocity);

    $velocityInfo = json_decode($velocity, true);
    $this->logger->debug($velocityInfo);

    $device = $velocityInfo['device'];

    $this->assertEquals(self::fingerprint, $device['id']);
    $this->assertEquals(self::ipAddress, $device['ipAddress']);
    $this->assertEquals(self::ipGeo, $device['ipGeo']);
    $this->assertEquals(1, $device['mobile']);
    $this->assertEquals(0, $device['proxy']);
    $this->assertEquals(self::responseId, $velocityInfo['response_id']);

    $this->assertNotNull($velocityInfo['velocity']);

    $velocityJson = json_decode(self::velocityJSON, true);

    $this->assertEquals($velocityJson['velocity']['account'], $velocityInfo['velocity']['account']);
    $this->assertEquals($velocityJson['velocity']['device'], $velocityInfo['velocity']['device']);
    $this->assertEquals($velocityJson['velocity']['ip_address'], $velocityInfo['velocity']['ip_address']);
    $this->assertEquals($velocityJson['velocity']['password'], $velocityInfo['velocity']['password']);
    $this->assertEquals($velocityJson['velocity']['user'], $velocityInfo['velocity']['user']);
  }

  public function testGetDecision() {
    $mock = $this->getMockBuilder(Kount_access_curl_service::class)
                 ->setConstructorArgs(array(self::merchantId, self::apiKey))
                 ->setMethods(['__call_endpoint'])
                 ->getMock();

    $mock->expects($this->any())
         ->method('__call_endpoint')
         ->will($this->returnValue(self::decisionJSON));

    $kount_access = new Kount_Access_Service(self::merchantId, self::apiKey, self::host, self::version, $mock);

    $decision = $kount_access->get_decision(self::sessionId, self::user, self::password);
    $this->assertNotNull($decision);

    $decisionInfo = json_decode($decision, true);
    $this->logger->debug($decisionInfo);

    $device = $decisionInfo['device'];

    $this->assertEquals(self::fingerprint, $device['id']);
    $this->assertEquals(self::ipAddress, $device['ipAddress']);
    $this->assertEquals(self::ipGeo, $device['ipGeo']);
    $this->assertEquals(1, $device['mobile']);
    $this->assertEquals(0, $device['proxy']);
    $this->assertEquals(self::responseId, $decisionInfo['response_id']);

    $this->assertNotNull($decisionInfo['velocity']);

    $decisionJson = json_decode(self::decisionJSON, true);

    $this->assertEquals($decisionJson['velocity']['account'], $decisionInfo['velocity']['account']);
    $this->assertEquals($decisionJson['velocity']['device'], $decisionInfo['velocity']['device']);
    $this->assertEquals($decisionJson['velocity']['ip_address'], $decisionInfo['velocity']['ip_address']);
    $this->assertEquals($decisionJson['velocity']['password'], $decisionInfo['velocity']['password']);
    $this->assertEquals($decisionJson['velocity']['user'], $decisionInfo['velocity']['user']);
  }
}