<?php
	include("apc.php");
	$oCache = new CacheAPC();

	$oCache->delData($_GET['query']);
?>