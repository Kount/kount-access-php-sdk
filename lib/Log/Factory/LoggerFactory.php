<?php
/**
 * LoggerFactory.php file containing Kount_Access_LoggerFactory interface.
 */

/**
 * Interface for factory classes that create Kount_Access_Logger objects.
 *
 * @package Kount_Access_Log
 * @subpackage Factory
 * @author Kount <custserv@kount.com>
 * @copyright 2012 Kount, Inc. All Rights Reserved.
 */
interface Kount_Access_LoggerFactory {

  /**
   * Get a named logger.
   *
   * @param string $name Name of logger
   * @return Kount_Access_LoggerFactory
   */
  public static function getLogger ($name);

} // end Kount_Access_LoggerFactory
