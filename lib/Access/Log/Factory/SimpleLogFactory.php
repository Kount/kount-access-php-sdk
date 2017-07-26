<?php
//require __DIR__  . './LoggerFactory.php';
//require __DIR__  . '/../Logger/SimpleLogger.php';

/**
 * SimpleLogFactory.php file containing Access_Log_Factory_SimpleLogFactory class.
 */

/**
 * A factory class that creates Access_Log_Factory_SimpleLogFactory objects
 *
 * @package Access_Log
 * @subpackage Factory
 * @author Kount <custserv@kount.com>
 * @copyright 2012 Kount, Inc. All Rights Reserved.
 */
class Access_Log_Factory_SimpleLogFactory implements
  Access_Log_Factory_LoggerFactory {

  /**
   * Get a Simple Logger.
   * @param string $name Logger name
   * @return Access_Log_Logger_SimpleLogger
   */
  public static function getLogger ($name) {
    return new Access_Log_Logger_SimpleLogger($name);
  }

}
