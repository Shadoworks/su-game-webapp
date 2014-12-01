<?php
define('secure', true);
require_once('config.php');

function genPW($length=8)
{
	$dummy = array_merge(range('0', '9'), range('a', 'z'), range('A', 'Z')); // array('#','&','@','$','_','%','?','+','-'));
	mt_srand((double)microtime()*1000000); // shuffle array
	for ($i = 1; $i <= (count($dummy)*2); $i++)
	{
		$swap		= mt_rand(0,count($dummy)-1);
		$tmp		= $dummy[$swap];
		$dummy[$swap]	= $dummy[0];
		$dummy[0]	= $tmp;
	}
	return substr(implode('',$dummy),0,$length); // get password
}

function checkMail()
{
	if(preg_match('/^[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+(?:\.[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+)*\@[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+(?:\.[^\x00-\x20()<>@,;:\\".[\]\x7f-\xff]+)+$/i', @$_POST["email"]))
	return true;
	return false;
}
?>
