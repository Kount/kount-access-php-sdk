<?php

/**
 * LogFactory.php file containing Access_Log_Factory_LogFactory class.
 */

/**
 * A factory class for creating Access_Log_Factory_LogFactory objects.
 *
 * @package Access_Log
 * @subpackage Factory
 * @author Kount <custserv@kount.com>
 * @copyright 2012 Kount, Inc. All Rights Reserved.
 */
class Access_Log_Factory_LogFactory {

  /**
   * NOP logger configuration setting name.
   * @var string
   */
  const NOP_LOGGER = 'NOP';

  /**
   * Simple logger configuration setting name.
   * @var string
   */
  const SIMPLE_LOGGER = 'SIMPLE';

  /**
   * Logger factory.
   * @var Access_Log_Factory_LogFactory
   */
  protected static $loggerFactory = null;

  /**
   * Get the logger factory to be used.
   * @throws Exception "Unknown logger defined in setting file" when $logger doesn't mach
   * any of the configuration setting names.
   * @return Access_Log_Factory_LoggerFactory
   */
  public static function getLogFactory () {

    if (self::$loggerFactory == null) {
      $configReader = Access_Log_ConfigFileReader::instance();
      $logger = $configReader->getConfigSetting('LOGGER');


      if ($logger == self::NOP_LOGGER) {
        self::$loggerFactory = new Access_Log_Factory_NopLogFactory();
      } else if ($logger == self::SIMPLE_LOGGER) {
        self::$loggerFactory = new Access_Log_Factory_SimpleLogFactory();
      } else {
        throw new Exception("Unknown logger [{$logger}] defined in setting " .
          "file [" . Access_Log_ConfigFileReader::SETTINGS_FILE . "]");
      }
    }

    return self::$loggerFactory;
  }
} // end Access_Log_Factory_LogFactory