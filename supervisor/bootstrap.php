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

// @todo - keep looking for new jobs in dameonized mode
$crawl_jobs = ORM::for_table('crawl_jobs')
	->table_alias('cj')
	->select('cj.*')
	->where('cj.status', 'ready')
	->find_many();

foreach ($crawl_jobs as $row) {

	// intialize some variables
	$crawl_id = $row->id;
	$domain_name = $row->domain_name;
	$pattern = array();
	$pattern = unserialize($row->pattern);
	$term = $row->term;
	$batch_id = $row->batch_id;

	// change status to running
	$update_tbl = ORM::for_table('crawl_jobs')->find_one($crawl_id);
	$update_tbl->set('status', 'running');

	/*

		Pattern will look like:

		Array
		(
		    [search_url] => http://list.qoo10.sg/s/?gdlc_cd=&gdmc_cd=&gdsc_cd=&delivery_group_no=&sell_cust_no=&bundle_delivery=&bundle_policy=&keywordArrLength=1&keyword=&within_keyword_auto_change=&keyword_hist=[[CRAWL_SEARCH_TERM]]&hidden_div_height=69&curPage=&filterDelivery=NNNNNNNN&flt_pri_idx=0&flt_tab_idx=0&sortType=SORT_RANK_POINT&dispType=LIST&pageSize=60&partial=off&paging_value=[[CRAWL_SEARCH_PAGE_NUMBER]]
		    [type] => PAGINATED_SEARCH_MAX_PAGE
		    [max_page] => 276
		    [wrapper] => .bd_lst dd tbody
		    [item] => td.details p.subject a
		    [seller] => td.seller a.name
		)
	*/

    // if this is a crawl that has a max page listed then we can use
    // a for loop because we have been provided with a max page
    // this is the ideal type of crawl for in-site search results

    if ($pattern['type'] == 'PAGINATED_SEARCH_MAX_PAGE') {

        if (isset($pattern['max_page'])) {

        	// the idea of the supervisor here is it can pass urls on to the works
        	// the workers can then crawl the urls in a async manner

            // we will be looping through the in-store search
            // from the first page unless one is explictly sent
            $start_page = 1;
            if (isset($pattern['start_page'])) {
                $start_page = $pattern['start_page'];
            }

            // loop through the search pagination
            for ($pg = $start_page; $pg <= (int)$pattern['max_page']; $pg++) {

            	$url = $pattern['search_url'];

                // check search url pattern for replace words
                if (strpos($url,'[[CRAWL_SEARCH_TERM]]') !== false) {
                    $url = str_replace('[[CRAWL_SEARCH_TERM]]', $term, $url);
                }
                if (strpos($url,'[[CRAWL_SEARCH_PAGE_NUMBER]]') !== false) {
                    $url = str_replace('[[CRAWL_SEARCH_PAGE_NUMBER]]', $pg, $url);
                }

                /*

	                allow for extra query string params
	                @todo revist for advanced operations
	                for now this just supports something like this:

	                [[CRAWL_EXTRA_RULED_PARAM:paging_value|SUBTRACT:CRAWL_SEARCH_PAGE_NUMBER:1]]

                */

	            $extra_param_pos = strpos($url,'[[CRAWL_EXTRA_RULED_PARAM');
                if ($extra_param_pos !== false) {

                	$tmp = explode('[[CRAWL_EXTRA_RULED_PARAM', $url);
                	$tmp = $tmp[1];
                	$tmp = explode('|', $tmp);
                	$query_param = ltrim($tmp[0], ':');

                	$tmp = explode(':', $tmp[1]);
                	$query_param_cmd = $tmp[0];

                	$query_cmd_x = $tmp[1];
                	$query_cmd_y = ltrim($tmp[2], ']]');

                	if ($query_cmd_x == 'CRAWL_SEARCH_PAGE_NUMBER') {
                		$query_cmd_x = $pg;
                	} elseif ($query_cmd_y == 'CRAWL_SEARCH_PAGE_NUMBER') {
                		$query_cmd_y = $pg;
                	}

                	if ($query_param_cmd == 'SUBTRACT') {

                		echo 'query_cmd_x: ' . $query_cmd_x . PHP_EOL;
                		echo 'query_cmd_y: ' . $query_cmd_y . PHP_EOL;

                		$query_param_val = $query_cmd_x - $query_cmd_y;
                	}

                	// find the end of extra param
                	$extra_param_eos = strpos($url,']]', $extra_param_pos);

                	// replace the string with new one
                	$url = substr_replace($url, $query_param . '=' . $query_param_val, $extra_param_pos, $extra_param_pos);
               	
            	}

            	echo $pg . ' of  ' . (int)$pattern['max_page'] . ' - ' . $url . PHP_EOL;

				// send the url a job details to the workers to fight over
				$job_handle = $dispatcher->background('CrawlJob', [
					'crawl_id' => $crawl_id,
					'url' => $url,
					'domain_name' => $domain_name,
					'wrapper' => $pattern['wrapper'],
					'item' => $pattern['item'],
					'seller' => $pattern['seller'],
					'term' => $term,
					'batch_id' => $batch_id
					]
				);

				// get the job status
				$client = $dispatcher->getClient()->getClient();
				
				/*
				print_r($status);


					The first array element is a boolean indicating whether the job is even known, 
					the second is a boolean indicating whether the job is still running, 
					and the third and fourth elements correspond to the numerator 
					and denominator of the fractional completion percentage, respectively.
				*/
			
				$records_done = 0;
				do {

					$status = $client->jobStatus($job_handle);
					list($queued, $running, $processed, $total) = $status;

					if (!$queued) {
						break;
					}

					if ($processed != $records_done) {
						$records_done = $processed;
						echo 'PROCESSED ' . $records_done . ' of ' . $total . PHP_EOL;
					} else {

						echo 'COMPLETE PG #' . $pg . ' OF IN-STORE SEARCH' . PHP_EOL;

					}

					usleep(50000);

				} while(true);

            }

        } else {
        	echo 'ERROR_MAX_PAGE_NOT_SET';
        }

    } else {
    	echo 'ERROR_UNSUPPORTED_CRAWL_TYPE';
    }


}
