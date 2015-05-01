<?php
/**
 * Job
 *
 * @package  supervisor
 * @author   Tegan Snyder <tsnyder@tegdesign.com>
 */
namespace Crawler\Worker;
require_once('vendor/autoload.php');
use veqryn\Curl\Curl;
use veqryn\Curl\CurlResponse;
use veqryn\Curl\CurlException;
use PHPHtmlParser\Dom;

/*

Example:

    array (
      'wrapper' => '.bd_lst dd tbody',
      'item' => 'td.details p.subject a',
      'seller' => 'td.seller a.name',
    )

*/

$crawl_data = array();
$crawl_data['url'] = 'http://list.qoo10.sg/s/?gdlc_cd=&gdmc_cd=&gdsc_cd=&delivery_group_no=&sell_cust_no=&bundle_delivery=&bundle_policy=&keywordArrLength=1&keyword=&within_keyword_auto_change=&keyword_hist=test&hidden_div_height=69&curPage=&filterDelivery=NNNNNNNN&flt_pri_idx=0&flt_tab_idx=0&sortType=SORT_RANK_POINT&dispType=LIST&pageSize=60&partial=off&paging_value=1';
$crawl_data['wrapper'] = '.bd_lst dd tbody';
$crawl_data['item'] = 'td.details p.subject a';
$crawl_data['seller'] = 'td.seller a.name';


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

    echo 'On product #' . $product_counter . ' - ' . $product_name . PHP_EOL;

    $product_counter = $product_counter + 1;


}



       