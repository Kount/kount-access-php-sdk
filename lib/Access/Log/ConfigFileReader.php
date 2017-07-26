<?php
/**
 * ConfigFileReader.php file containing Access_Log_ConfigFileReader class.
 */

if (!defined('KOUNT_SETTINGS_FILE')) {
  /*
   * Path to Kount Access configuration file.
   * @var string
   */
  define('KOUNT_SETTINGS_FILE', realpath(dirname(__FILE__) . '.' . DIRECTORY_SEPARATOR .  'logSettings.ini'));
}

/**
 * A class for reading Kount Access configuration files.
 *
 * @package Access_Log
 * @author Kount <custserv@kount.com>
 * @version $Id: Request.php 11177 2010-08-16 21:44:19Z bst $
 * @copyright 2012 Kount, Inc. All Rights Reserved.
 */
class Access_Log_ConfigFileReader {

  /**
   * An instance of this class.
   * @var Access_Log_ConfigFileReader
   */
  protected static $instance = null;

  /**
   * A map of the config settings.
   * @var $settings| Hash map of the settings in logSettings.ini
   */
  protected $settings;

  /**
   * Private constructor to prevent direct object instantiation.
   * @param string $path absolute path to custom settings file.
   * @throws Exception when reading a file fails.
   */
  private function __construct ($path = null) {
    if($path == null) {
      $file = KOUNT_SETTINGS_FILE;
      if (!is_readable($file)) {
        throw new Exception(
          "Unable to read configuration file '{$file}'. " .
          "Check that the file exists and is readable by the process " .
          "running this script.");
      }

      $this->settings = parse_ini_file($file, false);
    } else {
      $file = KOUNT_CUSTOM_SETTINGS_FILE;
      if (!is_readable($file)) {
        throw new Exception(
          "Unable to read configuration file '{$file}'. " .
          "Check that the file exists and is readable by the process " .
          "running this script.");
      }

      $this->settings = parse_ini_file($file, false);
    }
  }

  /**
   * Get an instance of this class.
   * @param string @path| Absolute path to custom settings file.
   * @return Access_Log_ConfigFileReader
   */
  public static function instance ($path = null) {
    if (null == self::$instance) {
      if($path == null) {
        self::$instance = new Access_Log_ConfigFileReader();
      } else {
        define('KOUNT_CUSTOM_SETTINGS_FILE', realpath($path));
        self::$instance = new Access_Log_ConfigFileReader($path);
      }
    }
    return self::$instance;
  }

  /**
   * Get static RIS settings from an ini file.
   * @return $this->settings| Hash map
   */
  public function getSettings () {
    return $this->settings;
  }

  /**
   * Get a named configuration setting.
   * @param string $name| Get a named configuration file setting
   * @return string
   * @throws Exception If the specified setting name does not exist.
   */
  public function getConfigSetting ($name) {
    $settings = $this->getSettings();
    if (array_key_exists($name, $settings)) {
      return $settings[$name];
    }
    throw new Exception("The configuration setting [{$name}] is not defined");
  }

} // end Access_Log_ConfigFileReader
