<?php

require __DIR__ . "/../../../../lib/kount_access_service.php";

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

  const deviseJSON   = "{" . "    \"device\": {" . "        \"id\": \"" . self::fingerprint . "\", "
  . "        \"ipAddress\": \"" . self::ipAddress . "\", " . "        \"ipGeo\": \"" . self::ipGeo . "\", "
  . "        \"mobile\": 1, " . "        \"proxy\": 0" . "    }," . "    \"response_id\": \"" . self::responseId
  . "\"" . "}";

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
                           . "        \"ipAddress\": \"" . self::ipAddress . "\", " . "   l     \"ipGeo\": \"" . self::ipGeo . "\", "
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
}