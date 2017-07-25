<?php

require __DIR__ . '/../ConfigFileReader.php';
require __DIR__ . './SimpleLogFactory.php';

/**
 * LogFactory.php file containing Kount_Access_Log_Factory_LogFactory class.
 */

/**
 * A factory class for creating Kount_Access_Log_Factory_LogFactory objects.
 *
 * @package Kount_Access_Log
 * @subpackage Factory
 * @author Kount <custserv@kount.com>
 * @copyright 2012 Kount, Inc. All Rights Reserved.
 */
class Kount_Access_LogFactory {

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
   * @var Kount_Access_LogFactory
   */
  protected static $loggerFactory = null;

  /**
   * Get the logger factory to be used.
   * @throws Exception "Unknown logger defined in setting file" when $logger doesn't mach
   * any of the configuration setting names.
   * @return Kount_Access_LoggerFactory
   */
  public static function getLogFactory () {

    if (self::$loggerFactory == null) {
      $configReader = Kount_ConfigFileReader::instance();
      $logger = $configReader->getConfigSetting('LOGGER');


      if ($logger == self::NOP_LOGGER) {
        self::$loggerFactory = new Kount_Access_NopLoggerFactory();
      } else if ($logger == self::SIMPLE_LOGGER) {
        self::$loggerFactory = new Kount_Access_SimpleLogFactory();
      } else {
        throw new Exception("Unknown logger [{$logger}] defined in setting " .
          "file [" . Kount_ConfigFileReader::SETTINGS_FILE . "]");
      }
    }

    return self::$loggerFactory;
  }
}