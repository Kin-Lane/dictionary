<?php
$route = '/dictionary/:dictionary_id/';
$app->get($route, function ($dictionary_id)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$dictionary_id = prepareIdIn($dictionary_id,$host);

	$ReturnObject = array();

	$Query = "SELECT * FROM dictionary WHERE ID = " . $dictionary_id;

	$DatabaseResult = mysql_query($Query) or die('Query failed: ' . mysql_error());

	while ($Database = mysql_fetch_assoc($DatabaseResult))
		{

		$dictionary_id = $Database['ID'];
		$post_date = $Database['Post_Date'];
		$title = $Database['Title'];
		$author = $Database['Author'];
		$summary = $Database['Summary'];
		$body = $Database['Body'];
		$footer = $Database['Footer'];
		$status = $Database['Status'];
		$buildpage = $Database['Build_Page'];
		$showonsite = $Database['Show_On_Site'];
		$image = $Database['Feature_Image'];
		$curated_id = $Database['News_ID'];

		// manipulation zone

		$dictionary_id = prepareIdOut($dictionary_id,$host);

		$F = array();
		$F['dictionary_id'] = $dictionary_id;
		$F['post_date'] = $post_date;
		$F['title'] = $title;
		$F['author'] = $author;
		$F['summary'] = $summary;
		$F['body'] = $body;
		$F['footer'] = $footer;
		$F['status'] = $status;
		$F['image'] = $image;
		$F['build_page'] = $buildpage;
		$F['show_on_site'] = $showonsite;
		$F['curated_id'] = $curated_id;

		$ReturnObject = $F;
		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>
