<?php
	if(!defined('secure')) die('Der direkte Zugriff ist verboten!');
	$server = "localhost";
	$db_name = "Databasename";
	$db_user = "Databaseuser";
	$db_pw = "Databasepassword";

	$db = new mysqli($server,$db_user,$db_pw,$db_name);

	if (mysqli_connect_errno()) {
		printf("Verbindung fehlgeschlagen: %s\n", mysqli_connect_error());
		exit();
	}

	$salt = "Lpdo79qnFCudEFdeSr4talB8";
	$hash = "lpfvL2jxdbfOYPaGKV2j7zdw";
?>
