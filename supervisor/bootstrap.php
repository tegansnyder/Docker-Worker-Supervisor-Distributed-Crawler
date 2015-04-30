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

// Establish Gearman connection
$gearman_server = getenv('JOBSERVER_PORT_4730_TCP_ADDR');
$config = (new Config())->addServer($gearman_server, 4730);

// intialize Gearman job dispatcher
$dispatcher = new Dispatcher($config);

// Establish MySQL connection
$db_server = getenv('DATABASE_PORT_3306_TCP_ADDR');
$db_name = 'crawl_operations';

ORM::configure(array(
    'connection_string'  => 'mysql:host='.$db_server.';dbname=' . $db_name,
    'username' 			 => 'root',
    'password' 			 => '',
    'return_result_sets' => true,
    'driver_options' 	 => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'),
));

// find all the current crawl jobs
$crawl_jobs = ORM::for_table('crawl_jobs')
	->table_alias('cj')
	->select('cj.*')
	->where('cj.status', 'ready')
	->find_many();

foreach ($results as $row) {

	$crawl_id = $row->id;
	$domain_name = $row->domain_name;

	$pattern = array();
	$pattern = unserialize($row->pattern);
	$term = $row->term;
	$batch_id = $row->batch_id;

	// send job to workers
	$job_handle = $dispatcher->background('CrawlJob', [
		'crawl_id' => $crawl_id,
		'domain_name' => $domain_name,
		'pattern' => $pattern,
		'term' => $term,
		'batch_id' => $batch_id
		]
	);

	// get the job status
	$client = $dispatcher->getClient()->getClient();
	$status = $client->jobStatus($job_handle);

	$records_done = 0;
	do {

		list($queued, $running, $processed, $total) = $status;

		if (!$queued) {
			break;
		}

		if ($processed != $records_done) {
			$records_done = $processed;
			echo 'PROCESSED ' . $records_done . ' of ' . $total . PHP_EOL;
		}

		usleep(50000);

	} while(true);

	echo 'COMPLETE';


}



