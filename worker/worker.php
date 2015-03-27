<?php
/**
 * Worker Crawler
 *
 * @package  worker
 * @author   Tegan Snyder <tsnyder@tegdesign.com>
 */
require_once('vendor/autoload.php');
use veqryn\Curl\Curl;
use veqryn\Curl\CurlResponse;
use veqryn\Curl\CurlException;
use PHPHtmlParser\Dom;

set_time_limit(0);
error_reporting(E_ALL | E_STRICT);
error_reporting(error_reporting() & ~E_NOTICE);

$curl = new Curl();
$curl->referer = 'http://google.com';
$curl->user_agent = 'Googlebot/2.1';

$curl->follow_redirects = false;
$curl->options['curlopt_connecttimeout'] = 0;
$curl->options['curlopt_timeout'] = 400;



$response = $curl->get('http://www.tegdesign.com');

if ($response->headers['Status-Code'] != 200) {
	continue;
}

$html = $response->body;

echo $html;