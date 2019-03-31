<?php

$db = mysql_connect("localhost","llapi","gokevgo");
mysql_select_db("LLAPI", $db);

$myip = $_SERVER['REMOTE_ADDR'];
$elements = explode('/',preg_replace("/^\/api\//","",$_SERVER['REQUEST_URI']));

switch ($elements[0]) {
	case "addtower":
		$content = add_tower($myip,addslashes($elements[1]));
		break;
	case "addworker":
		$content = add_worker(
			$myip,
			$elements[1] = isset($elements[1]) ? addslashes($elements[1]) : '',
			$elements[2] = isset($elements[2]) ? addslashes($elements[2]) : '',
			$elements[3] = isset($elements[3]) ? addslashes($elements[3]) : '',
			$elements[4] = isset($elements[4]) ? addslashes($elements[4]) : '',
			$elements[5] = isset($elements[5]) ? addslashes($elements[5]) : ''
		);
		break;
	case "myinventory":
		$content = my_inv($myip,"json");
		break;
	case "myinvini":
		$content = my_inv($myip,"ini");
		break;
	default:
		$content = my_instructions($_SERVER['HTTP_HOST']);
		break;
}

header('Content-type:application/json;charset=utf-8');
?>
<?=$content?>
<?php

///////////////////////////////////////////////////////////////////////////////

function my_instructions($server){

$out =<<<ALLDONE
## TRY ONE OF THESE:

#### ADDING TOWER SERVER VIA SELF CHECKIN

#ADD TOWER
$server/api/addtower/003

#### ADDING WORKERS IN A FEW DIFFERENT WAYS

#ADD WORKER
Web node check in
$server/api/addworker/005/web/web01/ansible_host/1.2.3.4

#ADD WORKER WITH GROUP DEFINED AND A VARIABLE DEFINED
$server/api/addworker/userid/my-group/my-host/ansible_host/1.2.3.4/
$server/api/addworker/123456/WebGroup/web-123/variable/value/

#ADD WORKER WITH NO GROUP DEFINED AND A VARIABLE DEFINED
$server/api/addworker/userid/-/my-host/ansible_host/1.2.3.4/
$server/api/addworker/123456/-/web-123/variable/value/

#ADD WORKER WITH GROUP DEFINED BUT NO VARS
$server/api/addworker/userid/my-group/my-host/
$server/api/addworker/123456/WebGroup/web-123/

ALLDONE;

return $out;

}
///////////////////////////////////////////////////////////////////////////////

function add_tower($myip,$myuid){
	global $db;

	$output = <<<ALLDONE
added tower.

IP:	$myip
USERID:	$myuid

ALLDONE;

	$query = mysql_query("
		INSERT INTO `all_tower_servers`
		SET `ip` = '$myip',
		`userid` = '$myuid'
	", $db);

	return $output;

}

///////////////////////////////////////////////////////////////////////////////

function add_worker($myip,$myuid,$mygroup,$myhost,$myvar,$myval){
	global $db;
	$myhost = preg_replace("/^-$/","",$myhost);
	$mygroup = preg_replace("/^-$/","",$mygroup);
	$myvar = preg_replace("/^-$/","",$myvar);
	$myval = preg_replace("/^-$/","",$myval);

	$output = <<<ALLDONE
added worker.

IP:	$myip
USERID:	$myuid
GROUP:	$mygroup
HOST:	$myhost
MYVAR:	$myvar
MYVAL:	$myval

ALLDONE;

	$query = mysql_query("
		INSERT INTO `all_host_requests`
		SET `request_ip` = '$myip',
		`userid` = '$myuid',
		`mygroup` = '$mygroup',
		`myhost` = '$myhost',
		`myvar` = '$myvar',
		`myval` = '$myval'
	", $db);

	return $output;
}

///////////////////////////////////////////////////////////////////////////////

function my_inv($ip,$format){
	global $db;
	$inv = array();

	// WE GET THE UID LINKED WITH THIS IP
	$query = mysql_query("
		SELECT `userid`
		FROM `all_tower_servers`
		WHERE `ip` = '$ip'
		LIMIT 1
	", $db);

	list($myuid) = mysql_fetch_array($query);

	if ( isset($myuid)){
		if ($myuid == 0){
			if ($format == "ini"){
				return admin_invini($myuid);
			}else{
				return admin_inv($myuid);
			}
		}else{
			if ($format == "ini"){
				return user_invini($myuid);
			}else{
				return user_inv($myuid);
			}
		}
	}
	return "ACCESS DENIED";
}

function admin_inv($myuid){
	global $db;
	$inv = array();

	// WE GET THE GROUP VARS
	$query = mysql_query("
		SELECT `mygroup`,`myvar`,`myval`,`userid`
		FROM `all_host_requests`
		WHERE `myhost` = ''
		ORDER by `userid`
	", $db);

	while(list($grp,$var,$val,$userid) = mysql_fetch_array($query)){
		$grp = strtolower($grp);
		$var = strtolower($var);

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
		$grp = strtolower($grp);
		$host = strtolower($host);

		$host = "user" . $userid . "_" . $host;
		$grp = "user" . $userid . "_" . $grp;
		$inv['user-' . $userid]['hosts'][] = strtolower($host);
		$inv[$grp]['hosts'][] = strtolower($host);
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
		$grp = strtolower($grp);
		$host = strtolower($host);
		$host = "user" . $userid . "_" . $host;
		$inv['_meta']['hostvars'][$host][$var] = $val;
	}

	if ( ! empty($inv) ){
		$json = json_encode($inv,JSON_PRETTY_PRINT);
		return $json;
	}
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
		$grp = strtolower($grp);
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
		$grp = strtolower($grp);
		$host = strtolower($host);
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
		$host = strtolower($host);
		$inv['_meta']['hostvars'][$host][$var] = $val;
	}

	if ( ! empty($inv) ){
		$json = json_encode($inv,JSON_PRETTY_PRINT);
		return $json;
	}
}

///////////////////////////////////////////////////////////////////////////////


function user_invini($myuid){
	global $db;
	$invgrpvar = array();
	$invhosts = array();
	$out = "## Ansible INI style inventory for user $myuid\n";

	// WE GET THE GROUP VARS
	$query = mysql_query("
		SELECT `mygroup`,`myvar`,`myval`
		FROM `all_host_requests`
		WHERE `myhost` = ''
		AND `userid` = '$myuid'
	", $db);

	while(list($grp,$var, $val) = mysql_fetch_array($query)){
		$grp = strtolower($grp);
		$invgrpvar['groupvars'][$grp][$var] = $val;
	}

	// WE GET ALL THE HOST VARS
	$query = mysql_query("
		SELECT `userid`,`mygroup`,`myhost`,`myvar`,`myval`
		FROM `all_host_requests`
		WHERE `myhost` <> ''
		AND `userid` = '$myuid'
	", $db);

	while(list($uid,$grp,$host,$var, $val) = mysql_fetch_array($query)){
		$grp = strtolower($grp);
		$host = strtolower($host);
		$invhosts[$grp][$host][$var] = $val;
	}

	// WE GET ALL THE HOST VARS
	$query = mysql_query("
		SELECT `userid`,`mygroup`,`myhost`,`myvar`,`myval`
		FROM `all_host_requests`
		WHERE `myhost` <> ''
		AND `userid` = '$myuid'
	", $db);

	while(list($uid,$grp,$host,$var, $val) = mysql_fetch_array($query)){
		$grp = strtolower($grp);
		$host = strtolower($host);
		$invhosts['user'.$uid][$host][$var] = $val;
	}


	if(count($invgrpvar) > 0){
		foreach($invgrpvar['groupvars'] as $key => $value){
			$groupname = (strlen($key) > 0) ? $key : 'all';
			$out .= "\n[$groupname:vars]\n";
			foreach($invgrpvar['groupvars'][$key] as $akey => $avalue){
				$out .= "$akey=\"$avalue\"\n";
			}
			$out .= "\n";
		}
	}

	if(count($invhosts) > 0){
		foreach($invhosts as $key => $value){
			$groupname = (strlen($key) > 0) ? $key : 'all';
			$out .= "\n[$groupname]";
			foreach($invhosts[$key] as $akey => $avalue){
				$out .= "\n$akey ";
				foreach($invhosts[$key][$akey] as $bkey => $bvalue){
					if (preg_match("/\s+/",$bvalue)){
						$out .= "$bkey=\"$bvalue\" ";
					}else{
						$out .= "$bkey=$bvalue ";
					}
				}
			}
			$out .= "\n";
		}
	}
	print "$out\n";
}

///////////////////////////////////////////////////////////////////////////////


function admin_invini($myuid){
	global $db;
	$invgrpvar = array();
	$invhosts = array();
	$out = "## Ansible INI style inventory for ADMIN USER\n";

	// WE GET THE GROUP VARS
	$query = mysql_query("
		SELECT `mygroup`,`myvar`,`myval`
		FROM `all_host_requests`
		WHERE `myhost` = ''
	", $db);

	while(list($grp,$var, $val) = mysql_fetch_array($query)){
		$grp = strtolower($grp);
		$invgrpvar['groupvars'][$grp][$var] = $val;
	}

	// WE GET ALL THE HOST VARS
	$query = mysql_query("
		SELECT `userid`,`mygroup`,`myhost`,`myvar`,`myval`
		FROM `all_host_requests`
		WHERE `myhost` <> ''
		AND `userid` = '$myuid'
		ORDER by `mygroup`,`userid`
	", $db);

	while(list($uid,$grp,$host,$var, $val) = mysql_fetch_array($query)){
		$grp = strtolower($grp);
		$host = strtolower($host);
		$invhosts[$grp][$host][$var] = $val;
	}

	// WE GET ALL THE HOST VARS FOR EVERY USER
	$query = mysql_query("
		SELECT `userid`,`mygroup`,`myhost`,`myvar`,`myval`
		FROM `all_host_requests`
		WHERE `myhost` <> ''
		ORDER by `mygroup`,`userid`
	", $db);

	while(list($uid,$grp,$host,$var, $val) = mysql_fetch_array($query)){
		$grp = strtolower($grp);
		$host = strtolower($host);
		$invhosts['user'.$uid][$host][$var] = $val;
	}

	if(count($invgrpvar) > 0){
		foreach($invgrpvar['groupvars'] as $key => $value){
			$groupname = (strlen($key) > 0) ? $key : 'all';
			$out .= "\n[$groupname:vars]\n";
			foreach($invgrpvar['groupvars'][$key] as $akey => $avalue){
				$out .= "$akey=\"$avalue\"\n";
			}
			$out .= "\n";
		}
	}

	if(count($invhosts) > 0){
		foreach($invhosts as $key => $value){
			$groupname = (strlen($key) > 0) ? $key : 'all';
			$out .= "\n[$groupname]";
			foreach($invhosts[$key] as $akey => $avalue){
				$out .= "\n$akey ";
				foreach($invhosts[$key][$akey] as $bkey => $bvalue){
					if (preg_match("/\s+/",$bvalue)){
						$out .= "$bkey=\"$bvalue\" ";
					}else{
						$out .= "$bkey=$bvalue ";
					}
				}
			}
			$out .= "\n";
		}
	}
	print "$out\n";
}

///////////////////////////////////////////////////////////////////////////////

