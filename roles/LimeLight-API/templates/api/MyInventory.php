<?php
header('Content-type:application/json;charset=utf-8');


$hosts_web = array(
	"web1","web2","web3"
);
$hostvars_web1 = array(
	"ansible_host" => "1.1.1.1"
);


$vars_web = array(
	"ansible_ssh_user" => "user1234",
	"ansible_ssh_pass" => "ansibleftw",
	"ansible_port" => "22",
	"ansible_ssh_private_key_file" => "~/.ssh/mykey.pem"
);

$meta_hostvars['web1'] = (
	$hostvars_web1
);



$inv = array();
$inv ['web']['hosts'] = $hosts_web;
$inv ['web']['vars'] = $vars_web;
$inv ['_meta']['hostvars'] = $meta_hostvars;

$json = json_encode($inv,JSON_PRETTY_PRINT);
?>
<?=$json?>
?>

/*

{
	"web": {
		"hosts": [
			"web-a",
			"web-b",
			"web-c"
		],
		"vars": {
			"ansible_ssh_user": "user1234",
			"ansible_ssh_pass": "ansibleftw",
			"ansible_port": "22",
			"ansible_ssh_private_key_file": "~/.ssh/mykey.pem"
		}
	},
	"_meta": {
		"hostvars": {
			"web-1": {
				"ansible_host": "1.1.1.1"
			},
			"web-b": {
				"ansible_host": "2.2.2.2"
			},
			"web-c": {
				"ansible_host": "3.3.3.3"
			}
		}
	}
*/
