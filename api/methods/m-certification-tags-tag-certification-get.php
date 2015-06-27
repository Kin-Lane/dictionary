<?php
$route = '/certification/tags/:tag/certification/';
$app->get($route, function ($tag)  use ($app){

	$ReturnObject = array();
	
 	$request = $app->request(); 
 	$params = $request->params();		

	if(isset($_REQUEST['week'])){ $week = $params['week']; } else { $week = date('W'); }
	if(isset($_REQUEST['year'])){ $year = $params['year']; } else { $year = date('Y'); }	

	$Query = "SELECT b.* from tags t";
	$Query .= " JOIN certification_tag_pivot btp ON t.Tag_ID = btp.Tag_ID";
	$Query .= " JOIN certification b ON btp.Certification_ID = b.ID";
	$Query .= " WHERE WEEK(b.Post_Date) = " . $week . " AND YEAR(b.Post_Date) = " . $year . " AND Tag = '" . $tag . "'";

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());
	  
	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

			
		$certification_id = $Database['ID'];
		$post_date = $Database['Post_Date'];
		$title = $Database['Title'];
		$author = $Database['Author'];
		$summary = $Database['Summary'];
		$body = $Database['Body'];
		$footer = $Database['Footer'];
		$status = $Database['Status'];
		$curated_id = $Database['News_ID'];		
				
		// manipulation zone

		$host = $_SERVER['HTTP_HOST'];
		$certification_id = prepareIdOut($certification_id,$host);
		
		$F = array();
		$F['certification_id'] = $certification_id;
		$F['post_date'] = $post_date;
		$F['title'] = $title;
		$F['author'] = $author;
		$F['summary'] = $summary;
		$F['body'] = $body;
		$F['footer'] = $footer;
		$F['status'] = $status;
		$F['curated_id'] = $curated_id;
		
		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>