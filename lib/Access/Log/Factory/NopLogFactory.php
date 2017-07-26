<?php

//require __DIR__ . '/../Logger/NopLogger.php';

/**
 * NopLogFactory.php file containing Access_Log_Factory_NopLogFactory class.
 */

/**
 * A factory class that creates Access_Log_Factory_NopLogFactory objects.
 *
 * @package Access_Log
 * @subpackage Factory
 * @author Kount <custserv@kount.com>
 * @copyright 2012 Kount, Inc. All Rights Reserved.
 */
class Access_Log_Factory_NopLogFactory implements Access_Log_Factory_LoggerFactory {

  /**
   * Get a Nop Logger.
   * @param string $name Logger name
   * @return Access_Log_Logger_NopLogger
   */
  public static function getLogger ($name) {
    return new Access_Log_Logger_NopLogger($name);
  }

} // end Kount_Log_Factory_NopLoggerFactory