<?php
$route = '/dictionary/build/using/apisjson/';
$app->post($route, function ()  use ($app){

	$ReturnObject = array();

	$ReturnObject['paths'] = array();
	$ReturnObject['path_params'] = array();
	$ReturnObject['definitions'] = array();
	$ReturnObject['definition_params'] = array();

	$ask_date = date('Y-m-d H:i:s');

 	$request = $app->request();
 	$param = $request->params();

	if(isset($param['apisjson_url'])){ $apisjson_url = $param['apisjson_url']; } else { $apisjson_url = '';}

	$ReturnObject['apisjson_url'] = $apisjson_url;

	$ObjectText = file_get_contents($apisjson_url);
	$ObjectResult = json_decode($ObjectText,true);

	if(isset($ObjectResult) && is_array($ObjectResult))
		{

		$api_json_name = $ObjectResult['name'];

		$api_description = $ObjectResult['description'];
		$api_image = $ObjectResult['image'];
		$api_tags = $ObjectResult['tags'];
		$api_created = $ObjectResult['created'];
		$api_modified = $ObjectResult['modified'];

		$SwaggerPathQuery = "SELECT dictionary_id FROM dictionary WHERE url = '" . $apisjson_url . "'";
		//echo $SwaggerQuery . "<br />";
		$SwaggerPathResults = mysql_query($SwaggerPathQuery) or die('Query failed: ' . mysql_error());

		if($SwaggerPathResults && mysql_num_rows($SwaggerPathResults))
			{
			$SwaggerPath = mysql_fetch_assoc($SwaggerPathResults);
			$dictionary_id = $SwaggerPath['dictionary_id'];
			}
		else
			{
			$InsertQuery = "INSERT INTO dictionary_id(";

			$InsertQuery .= "title,";
			$InsertQuery .= "body,";
			$InsertQuery .= "url,";
			$InsertQuery .= "featured_image";

			$InsertQuery .= ") VALUES(";

			$InsertQuery .= "'" . mysql_real_escape_string($api_json_name) . "',";
			$InsertQuery .= "'" . mysql_real_escape_string($api_description) . "',";
			$InsertQuery .= "'" . mysql_real_escape_string($apisjson_url) . "',";
			$InsertQuery .= "'" . mysql_real_escape_string($api_image) . "'";

			$InsertQuery .= ")";
			//echo $InsertQuery;
			mysql_query($InsertQuery) or die('Query failed: ' . mysql_error());
			$dictionary_id = mysql_insert_id();
			}

		$apis = $ObjectResult['apis'];

		foreach($apis as $api)
			{

			$api_name = $api['name'];

			foreach($api['properties'] as $property)
				{
				$swagger_type = $property['type'];
				$url = $property['url'];

				if(strtolower($swagger_type)=='swagger')
					{
					$swagger_url = $url;
					$SwaggerText = file_get_contents($swagger_url);
					$SwaggerObject = json_decode($SwaggerText,true);

					$Swagger_Info_Title = $SwaggerObject['info']['title'];

					$SwaggerAPIs = array();
					if(isset($SwaggerObject['paths']))
						{
						$SwaggerAPIs = $SwaggerObject['paths'];
						}

					foreach($SwaggerAPIs as $key => $value)
						{
						$path = $key;

						$SwaggerPathQuery = "SELECT * FROM paths WHERE dictionary_id = " . $dictionary_id . " AND entry = '" . $path . "'";
						//echo $SwaggerQuery . "<br />";
						$SwaggerPathResults = mysql_query($SwaggerPathQuery) or die('Query failed: ' . mysql_error());

						if(mysql_num_rows($SwaggerPathResults)==0)
							{
							$InsertQuery = "INSERT INTO paths(";
							$InsertQuery .= "dictionary_id,";
							$InsertQuery .= "entry";
							$InsertQuery .= ") VALUES(";
							$InsertQuery .= $api_definition_id . ",";
							$InsertQuery .= "'" . mysql_real_escape_string($path) . "'";
							$InsertQuery .= ")";
							//echo $InsertQuery;
							mysql_query($InsertQuery) or die('Query failed: ' . mysql_error());
							$Swagger_Path_ID = mysql_insert_id();
							}

						foreach($value as $key2 => $operation)
							{
							$method = $key2;
							$type = $method;

							if($path!='')
								{
								$parameters = "";
								if(isset($operation['parameters']))
									{
									$parameters = $operation['parameters'];

									foreach($parameters as $parameter)
										{
										$parameter_name = $parameter['name'];

										$SwaggerPathQuery = "SELECT * FROM path_params WHERE dictionary_id = " . $dictionary_id . " AND entry = '" . $parameter_name . "'";
										//echo $SwaggerQuery . "<br />";
										$SwaggerPathResults = mysql_query($SwaggerPathQuery) or die('Query failed: ' . mysql_error());

										if(mysql_num_rows($SwaggerPathResults)==0)
											{
											$InsertQuery = "INSERT INTO path_params(";
											$InsertQuery .= "dictionary_id,";
											$InsertQuery .= "entry";
											$InsertQuery .= ") VALUES(";
											$InsertQuery .= $api_definition_id . ",";
											$InsertQuery .= "'" . mysql_real_escape_string($parameter_name) . "'";
											$InsertQuery .= ")";
											//echo $InsertQuery;
											mysql_query($InsertQuery) or die('Query failed: ' . mysql_error());
											$Swagger_Path_ID = mysql_insert_id();
											}

										}
									}
								}
							}
						}

					$SwaggerModels = array();
					if(isset($SwaggerObject['definitions']))
						{
						$SwaggerModels = $SwaggerObject['definitions'];
						}

						// Data Models
						if(isset($SwaggerModels))
							{
							foreach($SwaggerModels as $key => $value)
								{

								$name = $key;

								$SwaggerPathQuery = "SELECT * FROM defintions WHERE dictionary_id = " . $dictionary_id . " AND entry = '" . $name . "'";
								//echo $SwaggerQuery . "<br />";
								$SwaggerPathResults = mysql_query($SwaggerPathQuery) or die('Query failed: ' . mysql_error());

								if(mysql_num_rows($SwaggerPathResults)==0)
									{
									$InsertQuery = "INSERT INTO defintions(";
									$InsertQuery .= "dictionary_id,";
									$InsertQuery .= "entry";
									$InsertQuery .= ") VALUES(";
									$InsertQuery .= $api_definition_id . ",";
									$InsertQuery .= "'" . mysql_real_escape_string($name) . "'";
									$InsertQuery .= ")";
									//echo $InsertQuery;
									mysql_query($InsertQuery) or die('Query failed: ' . mysql_error());
									$Swagger_Path_ID = mysql_insert_id();
									}

								if(isset( $value['properties']))
									{
									$model_properties = $value['properties'];

									foreach($model_properties as $key => $value)
										{
										$property_name = $key;
										$property_type = "";
										if(isset($value['type']))
											{
											$property_type = $value['type'];
											}

										$SwaggerPathQuery = "SELECT * FROM defintion_params WHERE dictionary_id = " . $dictionary_id . " AND entry = '" . $property_name . "'";
										//echo $SwaggerQuery . "<br />";
										$SwaggerPathResults = mysql_query($SwaggerPathQuery) or die('Query failed: ' . mysql_error());

										if(mysql_num_rows($SwaggerPathResults)==0)
											{
											$InsertQuery = "INSERT INTO defintion_params(";
											$InsertQuery .= "dictionary_id,";
											$InsertQuery .= "entry";
											$InsertQuery .= ") VALUES(";
											$InsertQuery .= $api_definition_id . ",";
											$InsertQuery .= "'" . mysql_real_escape_string($property_name) . "'";
											$InsertQuery .= ")";
											//echo $InsertQuery;
											mysql_query($InsertQuery) or die('Query failed: ' . mysql_error());
											$Swagger_Path_ID = mysql_insert_id();
											}

										}
									}
								}
							}

					}
				}
			}
		}

	$ReturnObject['paths'] = array();
	$ReturnObject['path_params'] = array();
	$ReturnObject['definitions'] = array();
	$ReturnObject['definition_params'] = array();

	$Query = "SELECT entry FROM paths WHERE dictionary_id = " . $dictionary_id . " ORDER BY entry ASC";
	//echo $Query . "<br />";
	$Results = mysql_query($Query) or die('Query failed: ' . mysql_error());
	while ($Result = mysql_fetch_assoc($Results))
		{
		$entry = array();
		$entry = $Result['entry'];
		array_push($SwaggerPathTypeDetail['paths'], $entry);
		}

	$Query = "SELECT entry FROM path_params WHERE dictionary_id = " . $dictionary_id . " ORDER BY entry ASC";
	//echo $Query . "<br />";
	$Results = mysql_query($Query) or die('Query failed: ' . mysql_error());
	while ($Result = mysql_fetch_assoc($Results))
		{
		$entry = array();
		$entry = $Result['entry'];
		array_push($SwaggerPathTypeDetail['path_params'], $entry);
		}

	$Query = "SELECT entry FROM definitions WHERE dictionary_id = " . $dictionary_id . " ORDER BY entry ASC";
	//echo $Query . "<br />";
	$Results = mysql_query($Query) or die('Query failed: ' . mysql_error());
	while ($Result = mysql_fetch_assoc($Results))
		{
		$entry = array();
		$entry = $Result['entry'];
		array_push($SwaggerPathTypeDetail['definitions'], $entry);
		}

	$Query = "SELECT entry FROM definition_params WHERE dictionary_id = " . $dictionary_id . " ORDER BY entry ASC";
	//echo $Query . "<br />";
	$Results = mysql_query($Query) or die('Query failed: ' . mysql_error());
	while ($Result = mysql_fetch_assoc($Results))
		{
		$entry = array();
		$entry = $Result['entry'];
		array_push($SwaggerPathTypeDetail['definition_params'], $entry);
		}

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($CleanObject)));

	});
?>
