<?php
 
$route = '/certification/';
$app->get($route, function ()  use ($app){

	$ReturnObject = array();

 	$request = $app->request(); 
 	$params = $request->params();	

	if(isset($params['query'])){ $query = trim(mysql_real_escape_string($params['query'])); } else { $query = '';}
	if(isset($params['page'])){ $page = trim(mysql_real_escape_string($params['page'])); } else { $page = 0;}
	if(isset($params['count'])){ $count = trim(mysql_real_escape_string($params['count'])); } else { $count = 250;}
	if(isset($params['sort'])){ $sort = trim(mysql_real_escape_string($params['sort'])); } else { $sort = 'Title';}
	if(isset($params['order'])){ $order = trim(mysql_real_escape_string($params['order'])); } else { $order = 'DESC';}	
			
	// Pull from MySQL
	if($query!='')
		{
		$Query = "SELECT * FROM certification WHERE Title LIKE '%" . $query . "%'";
		}
	else 
		{
		$Query = "SELECT * FROM certification";		
		}
	$Query .= " ORDER BY " . $sort . " " . $order . " LIMIT " . $page . "," . $count;
	//echo $Query . "<br />";
	
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
		$buildpage = $Database['Build_Page'];
		$showonsite = $Database['Show_On_Site'];
		$image = $Database['Feature_Image'];		
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
		$F['image'] = $image;
		$F['build_page'] = $buildpage;
		$F['show_on_site'] = $showonsite;
		$F['curated_id'] = $curated_id;
		
		array_push($ReturnObject, $F);
		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>