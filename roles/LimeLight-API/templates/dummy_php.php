<?php
header('Content-type:application/json;charset=utf-8');

$inv = array();
$inv['group001']['hosts'] = ['host1'] = "";
$inv['group001']['vars']['var1'] = "true";

/*
{
    "group001": {
        "hosts": ["host001", "host002"],
        "vars": {
            "var1": true
        },
	"children": ["group002"]
    },
    "group002": {
        "hosts": ["host003","host004"],
        "vars": {
            "var2": 500
        },
	"children":[]
    }

}

*/


$page = json_encode($inv,JSON_PRETTY_PRINT);

?>
<?=$page?>

