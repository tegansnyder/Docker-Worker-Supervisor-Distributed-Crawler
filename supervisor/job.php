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

use Sinergi\Gearman\JobInterface;
use GearmanJob;

class Job implements JobInterface
{
    public function getName()
    {
        return 'JobExample';
    }

    public function execute(GearmanJob $job)
    {
        // Do something
    }
}