<?php
/**
 * Job
 *
 * @package  supervisor
 * @author   Tegan Snyder <tsnyder@tegdesign.com>
 */
namespace Crawler\Worker;

use Sinergi\Gearman\JobInterface;
use GearmanJob;


class CrawlJob implements JobInterface
{
    public function getName()
    {
        return 'CrawlJob';
    }

    public function execute(GearmanJob $job = null)
    {
    	$who = $job->workload();
    	sleep(1);
        echo "hello" . PHP_EOL;
        sleep(1);
        return true;
        
    	//$job->sendComplete(sprintf("%s: %s|%s\n", date('Y-m-d H:i:s'), $job->unique(), $job->workload()));

    }
}