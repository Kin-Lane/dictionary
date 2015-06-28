<?php
$route = '/dictionary/:dictionary_id/tags/:tag/';
$app->delete($route, function ($dictionary_id,$tag)  use ($app){

	$host = $_SERVER['HTTP_HOST'];
	$dictionary_id = prepareIdIn($dictionary_id,$host);

	$ReturnObject = array();

 	$request = $app->request();
 	$param = $request->params();

	if($tag != '')
		{

		$dictionary_id = trim(mysql_real_escape_string($dictionary_id));
		$tag = trim(mysql_real_escape_string($tag));

		$CheckTagQuery = "SELECT Tag_ID FROM tags where Tag = '" . $tag . "'";
		$CheckTagResults = mysql_query($CheckTagQuery) or die('Query failed: ' . mysql_error());
		if($CheckTagResults && mysql_num_rows($CheckTagResults))
			{
			$Tag = mysql_fetch_assoc($CheckTagResults);
			$tag_id = $Tag['Tag_ID'];

			$DeleteQuery = "DELETE FROM dictionary_tag_pivot where Tag_ID = " . trim($tag_id) . " AND Certification_ID = " . trim($dictionary_id);
			$DeleteResult = mysql_query($DeleteQuery) or die('Query failed: ' . mysql_error());
			}

		$tag_id = prepareIdOut($tag_id,$host);

		$F = array();
		$F['tag_id'] = $tag_id;
		$F['tag'] = $tag;
		$F['dictionary_count'] = 0;

		array_push($ReturnObject, $F);

		}

		$app->response()->header("Content-Type", "application/json");
		echo format_json(json_encode($ReturnObject));
	});
?>
