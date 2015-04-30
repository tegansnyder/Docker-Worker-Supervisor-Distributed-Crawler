<?php
/**
 * Bootstrap
 *
 * @package  supervisor
 * @author   Tegan Snyder <tsnyder@tegdesign.com>
 */

set_time_limit(0);
error_reporting(E_ALL | E_STRICT);
error_reporting(error_reporting() & ~E_NOTICE);

require_once('vendor/autoload.php');

use Sinergi\Gearman\Dispatcher;
use Sinergi\Gearman\Config;

$gearman_server = getenv('JOBSERVER_PORT_4730_TCP_ADDR');

$config = (new Config())->addServer($gearman_server, 4730);

$dispatcher = new Dispatcher($config);
$result = $dispatcher->background('CrawlJob', ['data' => 'value']);