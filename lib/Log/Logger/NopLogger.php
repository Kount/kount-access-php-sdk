<?php
/**
 * NopLogger.php file containing Kount_Access_NopLogger class.
 */

/**
 * Implementation of a No OPeration logger. Just silently discards logging.
 * @package Kount_Access_Log
 * @subpackage Logger
 * @author Kount <custserv@kount.com>
 * @version $Id$
 * @copyright 2012 Kount, Inc. All Rights Reserved.
 */
class Kount_Access_NopLogger implements Kount_Access_Logger {

  /**
   * Constructor for Nop logger.
   * @param string $name Logger name
   */
  public function __construct ($name) {
    //Just accept a logger name and do nothing with it.
  }

  /**
   * Discard a debug level message.
   * @param string $message Message to log
   * @param Exception $exception Exception to log
   * @return void
   */
  public function debug ($message, $exception = null) {
  }

  /**
   * Discard an info level message.
   * @param string $message Message to log
   * @param Exception $exception Exception to log
   * @return void
   */
  public function info ($message, $exception = null) {
  }

  /**
   * Discard a warn level message.
   * @param string $message Message to log
   * @param Exception $exception Exception to log
   * @return void
   */
  public function warn ($message, $exception = null) {
  }

  /**
   * Discard an error level message.
   * @param string $message Message to log
   * @param Exception $exception Exception to log
   * @return void
   */
  public function error ($message, $exception = null) {
  }

  /**
   * Discard a fatal level message.
   * @param string $message Message to log
   * @param Exception $exception Exception to log
   * @return void
   */
  public function fatal ($message, $exception = null) {
  }

} // end Kount_Access_NopLogger

