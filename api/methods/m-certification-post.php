<?php
$route = '/certification/';	
$app->post($route, function () use ($app){
	
	$Add = 1;
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

  	$Query = "SELECT * FROM certification WHERE Title = '" . $title . "' AND Author = '" . $author . "'";
	//echo $Query . "<br />";
	$Database = mysql_query($Query) or die('Query failed: ' . mysql_error());
	
	if($Database && mysql_num_rows($Database))
		{	
		$ThisCertification = mysql_fetch_assoc($Database);	
		$certification_id = $ThisCertification['ID'];
		}
	else 
		{
		$Query = "INSERT INTO certification(Post_Date,Title,Author,Summary,Body,Footer,News_ID)";
		$Query .= " VALUES(";
		$Query .= "'" . mysql_real_escape_string($post_date) . "',";
		$Query .= "'" . mysql_real_escape_string($title) . "',";
		$Query .= "'" . mysql_real_escape_string($author) . "',";
		$Query .= "'" . mysql_real_escape_string($summary) . "',";
		$Query .= "'" . mysql_real_escape_string($body) . "',";
		$Query .= "'" . mysql_real_escape_string($footer) . "',";
		$Query .= mysql_real_escape_string($curated_id);
		$Query .= ")";
		//echo $Query . "<br />";
		mysql_query($Query) or die('Query failed: ' . mysql_error());
		$certification_id = mysql_insert_id();			
		}

	$host = $_SERVER['HTTP_HOST'];
	$certification_id = prepareIdOut($certification_id,$host);
	$ReturnObject['certification_id'] = $certification_id;
	
	$app->response()->header("Content-Type", "application/json");
	echo format_json(json_encode($ReturnObject));

	});	
?>