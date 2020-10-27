<?php
	require_once("./resources/apc.php");
	$oCache = new CacheAPC();

	if($oCache->delData($_GET['input']))
		echo("Done");
	else
		echo("Fail");
?>