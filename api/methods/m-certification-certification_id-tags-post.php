<?php
$route = '/certification/:certification_id/tags/';
$app->post($route, function ($Certification_ID)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$certification_id = prepareIdIn($certification_id,$host);

	$ReturnObject = array();
		
 	$request = $app->request(); 
 	$param = $request->params();	
	
	if(isset($param['tag']))
		{
		$tag = trim(mysql_real_escape_string($param['tag']));
			
		$CheckTagQuery = "SELECT Tag_ID FROM tags where Tag = '" . $tag . "'";
		$CheckTagResults = mysql_query($CheckTagQuery) or die('Query failed: ' . mysql_error());		
		if($CheckTagResults && mysql_num_rows($CheckTagResults))
			{
			$Tag = mysql_fetch_assoc($CheckTagResults);		
			$Tag_ID = $Tag['Tag_ID'];
			}
		else
			{

			$query = "INSERT INTO tags(Tag) VALUES('" . trim($_POST['Tag']) . "'); ";
			mysql_query($query) or die('Query failed: ' . mysql_error());	
			$Tag_ID = mysql_insert_id();			
			}

		$CheckTagPivotQuery = "SELECT * FROM certification_tag_pivot where Tag_ID = " . $tag_id . " AND Certification_ID = " . trim($certification_id);
		$CheckTagPivotResult = mysql_query($CheckTagPivotQuery) or die('Query failed: ' . mysql_error());
		
		if($CheckTagPivotResult && mysql_num_rows($CheckTagPivotResult))
			{
			$CheckTagPivot = mysql_fetch_assoc($CheckTagPivotResult);		
			}
		else
			{
			$query = "INSERT INTO certification_tag_pivot(Tag_ID,Certification_ID) VALUES(" . $tag_id . "," . $certification_id . "); ";
			mysql_query($query) or die('Query failed: ' . mysql_error());					
			}

		$tag_id = prepareIdOut($tag_id,$host);

		$F = array();
		$F['tag_id'] = $tag_id;
		$F['tag'] = $tag;
		$F['certification_count'] = 0;
		
		array_push($ReturnObject, $F);

		}		

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>