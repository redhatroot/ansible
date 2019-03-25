<?php

$db = mysql_connect("localhost","llapi","gokevgo");
mysql_select_db("LLAPI", $db);

$myip = $_SERVER['REMOTE_ADDR'];
$elements = explode('/',preg_replace("/^\/api\//","",$_SERVER['REQUEST_URI']));


switch ($elements[0]) {
//	case "addhost":
//		$host = addslashes($elements[1]);
//		$extras = addslashes($elements[2]);
//		$content = add_host($myip,$host,$extras);
//		break;
	case "myinventory":
		$content = my_inv($myip);
		break;
	default:
		$content = "DEFAULT";
		break;
}
?>
<pre><?=$content?></pre>

<?php

///////////////////////////////////////////////////////////////////////////////

function my_inv($ip){
	global $db;
	$inv = array();

	// WE GET THE UID LINKED WITH THIS IP
	$query = mysql_query("
		SELECT `userid`
		FROM `requestors`
		WHERE `ip` = '$ip'
		LIMIT 1
	", $db);

	list($myuid) = mysql_fetch_array($query);

	if ($myuid == 0){
		return admin_inv($myuid);
	}else{
		return user_inv($myuid);
	}
}

function admin_inv($myuid){
	global $db;

	// WE GET THE GROUP VARS
	$query = mysql_query("
		SELECT `mygroup`,`myvar`,`myval`,`userid`
		FROM `all_host_requests`
		WHERE `myhost` = ''
		ORDER by `userid`
	", $db);

	while(list($grp,$var,$val,$userid) = mysql_fetch_array($query)){
		$grp = "user" . $userid . "_" . $grp;
		$inv[$grp]['vars'][$var] = $val;
	}

	// WE GET ALL THE HOSTS
	$query = mysql_query("
		SELECT `mygroup`,`myhost`,`userid`
		FROM `all_host_requests`
		WHERE `myhost` <> ''
		GROUP BY `userid`,`mygroup`,`myhost`
		ORDER by `userid`
	", $db);

	while(list($grp,$host,$userid) = mysql_fetch_array($query)){
		$host = "user" . $userid . "_" . $host;
		$grp = "user" . $userid . "_" . $grp;
		$inv[$grp]['hosts'][] = $host;
	}

	// WE GET ALL THE HOST VARS
	$query = mysql_query("
		SELECT `myhost`,`myvar`,`myval`,`userid`
		FROM `all_host_requests`
		WHERE `myhost` <> ''
		AND `myvar` <> ''
		AND `myval` <> ''
		ORDER by `userid`,`myhost`,`myvar`
	", $db);

	while(list($host,$var, $val,$userid) = mysql_fetch_array($query)){
		$host = "user" . $userid . "_" . $host;
		$inv['_meta']['hostvars'][$host][$var] = $val;
	}

	$json = json_encode($inv,JSON_PRETTY_PRINT);

	print "<pre>";
	print_r($json);
}

///////////////////////////////////////////////////////////////////////////////

function user_inv($myuid){
	global $db;

	// WE GET THE GROUP VARS
	$query = mysql_query("
		SELECT `mygroup`,`myvar`,`myval`
		FROM `all_host_requests`
		WHERE `myhost` = ''
		AND `userid` = '$myuid'
	", $db);

	while(list($grp,$var, $val) = mysql_fetch_array($query)){
		$inv[$grp]['vars'][$var] = $val;
	}

	// WE GET ALL THE HOSTS
	$query = mysql_query("
		SELECT `mygroup`,`myhost`
		FROM `all_host_requests`
		WHERE `myhost` <> ''
		AND `userid` = '$myuid'
		GROUP BY `mygroup`,`myhost`
		ORDER by `myhost`
	", $db);

	while(list($grp,$host) = mysql_fetch_array($query)){
		$inv[$grp]['hosts'][] = $host;
	}

	// WE GET ALL THE HOST VARS
	$query = mysql_query("
		SELECT `myhost`,`myvar`,`myval`
		FROM `all_host_requests`
		WHERE `myhost` <> ''
		AND `userid` = '$myuid'
	", $db);

	while(list($host,$var, $val) = mysql_fetch_array($query)){
		$inv['_meta']['hostvars'][$host][$var] = $val;
	}

	if ( ! empty($inv) ){
		$json = json_encode($inv,JSON_PRETTY_PRINT);
		print "<pre>";
		print_r($json);
	}
}

///////////////////////////////////////////////////////////////////////////////


function add_host($ip,$host,$extras){
	global $db;
/*
	$query = mysql_query("
		SELECT `id` , `type`
		FROM categories
		ORDER by `type`
	", $db);
*/



	$out =<<<ALLDONE
INSERT into ans_group_hosts
	host = $host

ALLDONE;

	return $out;
}

