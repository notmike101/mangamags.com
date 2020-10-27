<?php
	define('IS_IN_APP',1);
	include("../inc.php");

	$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);

	if($MyBBI->mybb->usergroup['cancp'] && isset($MyBBI->mybb->input['action']) && isset($MyBBI->mybb->input['adminz'])) {
		if($MyBBI->mybb->input['action'] == "delete" && isset($MyBBI->mybb->input['id'])) {
			if(mysqli_query($db,"DELETE FROM `mangacomments` WHERE id='".$MyBBI->mybb->input['id']."'")) {
				echo('1');
			} else {
				echo('2');
			}
		}
	} else if(isset($_GET['action']) && $_GET['action']=="postchap") {
		(!isset($_POST['chapter']) || !(isset($_POST['comment'])) || !(isset($_POST['author'])) || !(isset($_POST['parent'])) || !(isset($_POST['series']))) ? die('5') : NULL;
		$name = mysqli_real_escape_string($db,htmlentities($_POST['author']));
		$comment = mysqli_real_escape_string($db,htmlentities($_POST['comment']));
		$parent = abs(intval($_POST['parent']));
		$series = mysqli_real_escape_string($db,$_POST['series']);
		$chapter = abs(intval($_POST['chapter']));

		if(!mysqli_connect_errno()) {
			if(doesSeriesExist($series)) {
				if(mysqli_query($db,"INSERT INTO `mangachapcomments` (`id`, `author`, `comment`, `time`, `parentID`, `series`, `chapter`) VALUES (NULL, '".$name."', '".$comment."', CURRENT_TIMESTAMP, '".$parent."', '".$series."','".$chapter."');")) {
					echo('0');
				} else {
					echo('2');
				}
				mysqli_close($db);
			} else {
				echo('3');
			}
		} else {
			echo('4');
		}
	} else {
		/*
			Return errors
			8 - Asshat tried to comment without being logged in
			7 - Asshat tried to be clever (Changed his name in javascript query)
			6 - Post/Get parameters not set properly.
			5 - Comment less than 5 characters
			4 - Database connection error
			3 - Series does not exist
			2 - Insert query failed
			1 - Comment not at least 5 characters
			0 - Success
		*/

		(!(isset($_GET['action'])) || !(isset($_POST['comment'])) || !(isset($_POST['author'])) || !(isset($_POST['parent'])) || !(isset($_POST['series']))) ? die('5') : NULL;
		if($_GET['action'] != "post") die('6');

		if($userInfo['username'] != $_POST['author']) die('7');
		if(!$isLoggedIn) die('8');

		if(strlen($_POST['comment']) < 5) die('1');

		$name = mysqli_real_escape_string($db,htmlentities($_POST['author']));
		$comment = mysqli_real_escape_string($db,htmlentities($_POST['comment']));
		$parent = abs(intval($_POST['parent']));
		$series = mysqli_real_escape_string($db,$_POST['series']);

		if(!mysqli_connect_errno()) {
			if(doesSeriesExist($series)) {
				if(mysqli_query($db,"INSERT INTO `mangacomments` (`id`, `author`, `comment`, `time`, `parentID`, `series`) VALUES (NULL, '".$name."', '".$comment."', CURRENT_TIMESTAMP, '".$parent."', '".$series."');")) {
					echo('0');
				} else {
					echo('2');
				}
				mysqli_close($db);
			} else {
				echo('3');
			}
		} else {
			echo('4');
		}
	}
?>