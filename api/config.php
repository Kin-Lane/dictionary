<?php

$dbserver = "laneworks-2.cjgvjastiugl.us-east-1.rds.amazonaws.com";
$dbname = "stack_network_kinlane_certificationapi";
//$dbname = "apievangelist";
$dbuser = "kinlane";
$dbpassword = "ap1stack!";

// Make a database connection
mysql_connect($dbserver,$dbuser,$dbpassword) or die('Could not connect: ' . mysql_error());
mysql_select_db($dbname);

$datastore = "mysql"; // mysql or github JSON currently

$githuborg = "Kin-Lane";
$githubrepo = "certification";

$guser = "kinlane";
$gpass = "kpawwjN4dnJy4j";

$three_scale_provider_key = "9c72d79253c63772cc2a81d4e4bd07f8";

?>