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

						$P = array();
						$P = $path;
						$Already = 0;
						foreach($ReturnObject['paths'] as $CheckPath)
								{
								if(trim($CheckPath)==trim($path))
									{
									$Already = 1;
									}
								}
						if($Already==0)
							{
							array_push($ReturnObject['paths'], $P);
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

										$P = array();
										$P = $parameter_name;
										$Already = 0;
										foreach($ReturnObject['path_params'] as $CheckPathParam)
												{
												if(trim($CheckPathParam)==trim($parameter_name))
													{
													$Already = 1;
													}
												}
										if($Already==0)
											{
											array_push($ReturnObject['path_params'], $P);
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

								$P = array();
								$P = $name;
								$Already = 0;
								foreach($ReturnObject['definitions'] as $CheckDefinitions)
										{
										if(trim($CheckDefinitions)==trim($name))
											{
											$Already = 1;
											}
										}
								if($Already==0)
									{
									array_push($ReturnObject['definitions'], $P);
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

										$P = array();
										$P = $property_name;
										$Already = 0;
										foreach($ReturnObject['definitions'] as $CheckDefinitionParam)
												{
												if(trim($CheckDefinitionParam)==trim($property_name))
													{
													$Already = 1;
													}
												}
										if($Already==0)
											{
											array_push($ReturnObject['definition_params'], $P);
											}

										}
									}
								}
							}

					}
				}
			}
		}
		
	asort($ReturnObject['paths']);
	asort($ReturnObject['path_params']);
	asort($ReturnObject['definitions']);
	asort($ReturnObject['definition_params']);

	$app->response()->header("Content-Type", "application/json");
	echo stripslashes(format_json(json_encode($ReturnObject)));

	});
?>
