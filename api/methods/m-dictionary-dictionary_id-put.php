<?php
$route = '/dictionary/:dictionary_id/';
$app->put($route, function ($dictionary_id) use ($app){

   $host = $_SERVER['HTTP_HOST'];
   $dictionary_id = prepareIdIn($dictionary_id,$host);

	$ReturnObject = array();
	
 	$request = $app->request();
 	$params = $request->params();

	if(isset($params['post_date'])){ $post_date = mysql_real_escape_string($params['post_date']); } else { $post_date = date('Y-m-d H:i:s'); }
	if(isset($params['title'])){ $title = mysql_real_escape_string($params['title']); } else { $title = 'No Title'; }
	if(isset($params['author'])){ $author = mysql_real_escape_string($params['author']); } else { $author = ''; }
	if(isset($params['summary'])){ $summary = mysql_real_escape_string($params['summary']); } else { $summary = ''; }
	if(isset($params['body'])){ $body = mysql_real_escape_string($params['body']); } else { $body = ''; }
	if(isset($params['footer'])){ $footer = mysql_real_escape_string($params['footer']); } else { $footer = ''; }
	if(isset($params['curated_id'])){ $curated_id = mysql_real_escape_string($params['curated_id']); } else { $curated_id = 0; }

  	$Query = "SELECT * FROM dictionary WHERE ID = " . $dictionary_id;
	//echo $Query . "<br />";
	$Database = mysql_query($Query) or die('Query failed: ' . mysql_error());

	if($Database && mysql_num_rows($Database))
		{
		$query = "UPDATE dictionary SET";

		$query .= " Title = '" . mysql_real_escape_string($title) . "'";
		$query .= ", Post_Date = '" . mysql_real_escape_string($post_date) . "'";

		if($post_date!='') { $query .= ", description = '" . $post_date . "'"; }
		if($author!='') { $query .= ", Author = '" . $author . "'"; }
		if($summary!='') { $query .= ", Summary = '" . $summary . "'"; }
		if($body!='') { $query .= ", Body = '" . $body . "'"; }
		if($footer!='') { $query .= ", Footer = '" . $footer . "'"; }
		if($curated_id!='') { $query .= ", News_ID = '" . $curated_id . "'"; }

		$query .= " WHERE dictionary_id = '" . $dictionary_id . "'";

		//echo $query . "<br />";
		mysql_query($query) or die('Query failed: ' . mysql_error());
		}

	$dictionary_id = prepareIdOut($dictionary_id,$host);

	$F = array();
	$F['dictionary_id'] = $dictionary_id;
	$F['post_date'] = $post_date;
	$F['title'] = $title;
	$F['author'] = $author;
	$F['summary'] = $summary;
	$F['body'] = $body;
	$F['footer'] = $footer;
	$F['curated_id'] = $curated_id;

	array_push($ReturnObject, $F);

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
