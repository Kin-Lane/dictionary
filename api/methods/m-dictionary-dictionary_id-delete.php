<?php
$route = '/dictionary/:dictionary_id/';
$app->delete($route, function ($dictionary_id) use ($app){
	
	$host = $_SERVER['HTTP_HOST'];
	$dictionary_id = prepareIdIn($dictionary_id,$host);

	$Add = 1;
	$ReturnObject = array();

 	$request = $app->request();
 	$_POST = $request->params();

	$query = "DELETE FROM dictionary WHERE ID = " . $dictionary_id;
	//echo $query . "<br />";
	mysql_query($query) or die('Query failed: ' . mysql_error());

	});
?>
