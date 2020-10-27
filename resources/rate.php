<?php
	/* Return values:
		1 - Post values not set
		2 - Series does not exist
		3 - Could not connect to database
		4 - Error in SQL statement
		5 - Already voted
		6 - Cannot insert rate
		7 - Success
	*/

	define('IS_IN_APP',1);

	require_once('../inc.php');

	$resp = 0;

	if(!isset($_GET['series']) || !isset($_POST['rate']) || !isset($_POST['action']) || $_POST['action'] != 'rating' || !is_numeric($_POST['rate'])) $resp = '1';
	if(!doesSeriesExist($_GET['series'])) $resp = '2';

	if($resp == 0) {
		$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);

		$series = mysqli_real_escape_string($db,htmlentities($_GET['series']));
		$rate   = abs(intval($_POST['rate']));
		$rate   = $rate > 5 ? 5 : $rate;
		$storedRating = 0;
		$numRated = 0;
		$ip     = ip2long($_SERVER['REMOTE_ADDR']);

		if(!mysqli_connect_errno()) {
			if($result = mysqli_query($db,"SELECT * FROM mangarated WHERE (series = '".$series."' AND ip='".$ip."')")) {
				if(mysqli_num_rows($result) == 0) {
					$result5 = mysqli_query($db,"SELECT * FROM mangarating WHERE series = '".$series."'");
					while($row = mysqli_fetch_array($result5)) {
						$storedRating = $row['rate'];
						$numRated = $row['count'];
					}
					echo($storedRating.' '.$numRated);
					if($result2 = mysqli_query($db,"INSERT INTO mangarating VALUES ('".$series."','".$rate."',1) ON DUPLICATE KEY UPDATE rate = (rate*count+".$rate.")/(count+1), count=count+1;") && $result3 = mysqli_query($db,"INSERT INTO mangarated (id,series,ip) VALUES (NULL,'".$series."','".$ip."')")) {
						$resp = '7';
					} else {
						$resp = '6';
					}
				} else {
					$resp = '5';
				}
			} else {
				$resp = '4';
			}
		} else {
			$resp = '3';
		}
		mysqli_free_result($result);
		mysqli_free_result($result2);
		mysqli_free_result($result3);
		mysqli_free_result($result4);
		mysqli_free_result($result5);

		mysqli_close($db);
	}

	$aResponse['message'] = $resp;
	echo json_encode($aResponse);
?>