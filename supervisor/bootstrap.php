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

use Sinergi\Gearman\BootstrapInterface;
use Sinergi\Gearman\Application;

class Bootstrap implements BootstrapInterface
{
    public function run(Application $application)
    {
        $application->add(new Job());
    }
}