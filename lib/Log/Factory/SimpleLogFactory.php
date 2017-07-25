<?php
require __DIR__  . './LoggerFactory.php';
require __DIR__  . '/../Logger/SimpleLogger.php';

/**
 * SimpleLogFactory.php file containing Kount_Access_SimpleLogFactory class.
 */

/**
 * A factory class that creates Kount_Access_SimpleLogger objects
 *
 * @package Kount_Access_Log
 * @subpackage Factory
 * @author Kount <custserv@kount.com>
 * @copyright 2012 Kount, Inc. All Rights Reserved.
 */
class Kount_Access_SimpleLogFactory implements
  Kount_Access_LoggerFactory {

  /**
   * Get a Simple Logger.
   * @param string $name Logger name
   * @return Kount_Access_SimpleLogger
   */
  public static function getLogger ($name) {
    return new Kount_Access_SimpleLogger($name);
  }

}
