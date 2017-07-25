<?php
/**
 * Logger.php file containing Kount_Access_Logger interface.
 */

/**
 * Logger interface containing the debug level messages.
 *
 * @package Kount_Access_Log
 * @subpackage Logger
 * @author Kount <custserv@kount.com>
 * @version $Id$
 * @copyright 2012 Kount, Inc. All Rights Reserved.
 */
interface Kount_Access_Logger {

  /**
   * Log a debug level message.
   * @param string $message Message to log
   * @param Exception $exception Exception to log
   * @return void
   */
  public function debug ($message, $exception = null);

  /**
   * Log an info level message.
   * @param string $message Message to log
   * @param Exception $exception Exception to log
   * @return void
   */
  public function info ($message, $exception = null);

  /**
   * Log a warn level message.
   * @param string $message Message to log
   * @param Exception $exception Exception to log
   * @return void
   */
  public function warn ($message, $exception = null);

  /**
   * Log an error level message.
   * @param string $message Message to log
   * @param Exception $exception Exception to log
   * @return void
   */
  public function error ($message, $exception = null);

  /**
   * Log a fatal level message.
   * @param string $message Message to log
   * @param Exception $exception Exception to log
   * @return void
   */
  public function fatal ($message, $exception = null);

} // end Kount_Access_Logger
