<?php

/**
 * An example logger client.
 *
 * @package Kount_Access_Log
 * @author Kount <custserv@kount.com>
 * @version $Id$
 * @copyright 2012 Kount, Inc. All Rights Reserved.
 */

require __DIR__ . './Factory/LogFactory.php';

$loggerFactory = Kount_Access_LogFactory::getLogFactory();
$logger = $loggerFactory->getLogger('ExampleClient');

//Example log messages
$logger->debug("Hello world");
$logger->info("Hello world");
$logger->warn("Hello world");
$logger->error("Hello world");
$logger->fatal("Hello world",
  new Exception("Detail message for the exception"));
