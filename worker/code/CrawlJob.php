<?php
/**
 * Job
 *
 * @package  supervisor
 * @author   Tegan Snyder <tsnyder@tegdesign.com>
 */
namespace Crawler\Worker;
use ORM;
use veqryn\Curl\Curl;
use veqryn\Curl\CurlResponse;
use veqryn\Curl\CurlException;
use PHPHtmlParser\Dom;
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

        /*

        @todo Establish MySQL connection

            $db_server = getenv('DATABASE_PORT_3306_TCP_ADDR');
            $db_name = 'crawl_operations';

            ORM::configure(array(
                'connection_string'  => 'mysql:host='.$db_server.';dbname=' . $db_name,
                'username'           => 'root',
                'password'           => '',
                'return_result_sets' => true,
                'driver_options'     => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'),
            ));

        */


        $workload = $job->workload();
        $crawl_data = unserialize($workload);

        /*

        Example:

            array (
              'wrapper' => '.bd_lst dd tbody',
              'item' => 'td.details p.subject a',
              'seller' => 'td.seller a.name',
            )

        */
        
        // basic validation
        if (!isset($crawl_data['crawl_id'])) {
            return 'JOB_VALIDATION_FAILED';
        }
        if (!isset($crawl_data['url'])) {
            return 'JOB_VALIDATION_FAILED';
        }
        if (!isset($crawl_data['term'])) {
            return 'JOB_VALIDATION_FAILED';
        }
        if (!isset($crawl_data['wrapper'])) {
            return 'JOB_VALIDATION_FAILED';
        }
        if (!isset($crawl_data['item'])) {
            return 'JOB_VALIDATION_FAILED';
        }

        echo "crawl_id: " . $crawl_data['crawl_id'] . PHP_EOL;
        echo "domain_name: " . $crawl_data['domain_name'] . PHP_EOL;
        echo "term: " . $crawl_data['term'] . PHP_EOL;
        echo "batch_id: " . $crawl_data['batch_id'] . PHP_EOL;
        echo "url: " . $crawl_data['url'] . PHP_EOL;
        echo "wrapper: " . $crawl_data['wrapper'] . PHP_EOL;
        echo "item: " . $crawl_data['item'] . PHP_EOL;
        echo "seller: " . $crawl_data['seller'] . PHP_EOL;


        // setup the crawler veqryn/curl
        $curl = new Curl();

        // acting like we are google bot
        $curl->referer = 'http://google.com';
        $curl->user_agent = 'Googlebot/2.1';

        // we are not following redirects
        $curl->follow_redirects = false;

        // we have some timeout thresholds
        $curl->options['curlopt_connecttimeout'] = 0;
        $curl->options['curlopt_timeout'] = 400;

        // retry if effected by failures
        $curl->exception_retry_attempts = 10;

        // curl the url
        $response = $curl->get($crawl_data['url']);

        // HTTP response code 200 = Okay. Page Found.
        if ($response->headers['Status-Code'] != 200) {
            return 'ERROR_HTTP_STATUS_CODE_' . $response->headers['Status-Code'];
        }

        // place the html code into the dom parse
        $html = $response->body;
        $dom = new Dom;
        $dom->load($html);


        // basically if a product is wrapped in a div or something its
        // useful to target it by this div so you can get any other 
        // information like the image, or if its a marketplace the seller name
        $product_wrappers = $dom->find($crawl_data['wrapper']);

        // setup some progress report variables
        $product_counter = 0;
        $max_products = count($product_wrappers);

        foreach ($product_wrappers as $product_wrapper) {

            // grab the html of the product wrapper for parsing
            $dom->load($product_wrapper->innerHtml);

            // find the product item (name, href)
            $product = $dom->find($crawl_data['item'])[0];

            // clean it up a bit
            $product_name = trim($product->text);
            $product_name = str_replace("\r", "", $product_name);
            $product_name = str_replace("\n", "", $product_name);
            $product_name = html_entity_decode($product_name);

            // grab the url
            $product_url = $product->getAttribute('href');

            // is this a marketplace ecommerce website
            // if so check to see if we are also crawling for the seller name
            if (isset($crawl_data['seller'])) {

                $seller = $dom->find($crawl_data['seller'])[0];
                $seller_name = trim($seller->text);

                // grab seller url
                $seller_url = $seller->getAttribute('href');

            }

            echo 'On product #' . $product_counter . ' of ' . $max_products . ' - ' . $product_name . PHP_EOL;

            /* 
                
            @todo - store results in database
                
                $crawl_tbl = ORM::for_table('crawl_results')->create();
                $crawl_tbl->job_id = $crawl_data['crawl_id'];
                $crawl_tbl->product_name = $product_name;
                $crawl_tbl->product_url = $product_url;
                $crawl_tbl->marketplace_seller_name = $seller_name;
                $crawl_tbl->marketplace_seller_url = $seller_url
                $crawl_tbl->save();

            */

            $product_counter = $product_counter + 1;

            $job->sendStatus($product_counter, $max_products);
            
            usleep(50000);

        }

        return "proccesed: " . $crawl_data['url'] . PHP_EOL;

    }

} 