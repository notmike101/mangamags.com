<?php
	define("IS_IN_APP",1);

	require_once("inc.php");
	$manga = randomManga();

	header("Location: ".$fullSiteURL."/manga/".$manga['directory']);
?>