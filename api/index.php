<?php 
date_default_timezone_set('America/Los_Angeles');
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once('config.php');
require_once('../Slim/Slim.php');
require_once('../3scale/ThreeScaleClient.php');
require_once('../client/GitHubClient.php');
require_once('../parse/index.php');
require_once('../libraries/common.php');
require_once('/var/www/html/system/class-amazon-s3.php');

$gclient = new GitHubClient();
$gclient->setCredentials($guser, $gpass);

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

// Incoming Keys
$request = $app->request(); 
$Params = $request->params();	

//var_dump($_GET);

if(isset($Params['appid'])){ $appid = $Params['appid']; } else { $appid = ""; }
if(isset($Params['appkey'])){ $appkey = $Params['appkey']; } else { $appkey = ""; }

$client = new ThreeScaleClient($three_scale_provider_key);

//echo "appid: " . $appid . "<br />";
//echo "appkey: " . $appkey . "<br />";

if ($appid!='' && $appkey!='') 
	{	
	// Auth the application
	$response = $client->authorize($appid, $appkey);
	
	$Plan = $response->getPlan();
	$Plan = str_replace(" (custom)","",$Plan);
	//echo $Plan . "<br />";
	
	$usageReports = $response->getUsageReports();
	$usageReport  = $usageReports[0];
	$Exceeded = $usageReport->isExceeded();

	if ($response->isSuccess()) 
		{		
		$usageReports = $response->getUsageReports();
		
		//echo "Success:";
		//echo "  Plan: " .          $response->getPlan();
		//echo "  Usage reports: " . var_export($usageReports, true);
	
		if($Plan=="Internal" || $Plan=="Platform")
			{
			include "methods/platform.php";		
			}
		elseif($Plan=="Personal")
			{
			include "methods/personal.php";		
			}				
		elseif($Plan=="Partner")
			{
			include "methods/partner.php";		
			}
		elseif($Plan=="Trusted")
			{
			include "methods/trusted.php";		
			}
		elseif($Plan=="Retail")
			{
			include "methods/retail.php";		
			}									
		else 
			{			
			include "methods/public.php";		
			}
		
		$app->run();	
		
		// Report some usages
		$response = $client->report(
			array(
			  array('app_id' => $appid, 'usage' => array('screen-capture' => 1))
			)
		);		
	
		} 
	else 
		{
		include "methods/public.php";						
		$app->run();	
		}
	} 
else 	
	{
	include "methods/public.php";						
	$app->run();			
	}
?>