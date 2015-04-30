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

        $workload = $job->workload();
        $crawl_data = unserialize($workload);

        echo "crawl_id: " . $crawl_data['crawl_id'] . PHP_EOL;
        echo "domain_name: " . $crawl_data['domain_name'] . PHP_EOL;
        echo "term: " . $crawl_data['term'] . PHP_EOL;
        echo "batch_id: " . $crawl_data['batch_id'] . PHP_EOL;
        echo "search_url: " . $crawl_data['pattern']['search_url'] . PHP_EOL;
        echo "type: " . $crawl_data['pattern']['type'] . PHP_EOL;
        echo "max_page: " . $crawl_data['pattern']['max_page'] . PHP_EOL;
        echo "wrapper: " . $crawl_data['pattern']['wrapper'] . PHP_EOL;
        echo "item: " . $crawl_data['pattern']['item'] . PHP_EOL;
        echo "seller: " . $crawl_data['pattern']['seller'] . PHP_EOL;

        $max_count = 5;
        for ($x = 0; $x <= $max_count; $x++) {

            echo "Processing product: " . $x . PHP_EOL;
            $job->sendStatus($x, $max_count);
            sleep(1);

        }

    }
}