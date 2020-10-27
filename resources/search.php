<?php
	define('IS_IN_APP',1);

	require_once('../inc.php');

	if(!isset($_POST['query'])) die();
	
	$arr = array();
	$num = 0;
	$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);

	$search = mysqli_real_escape_string($db,htmlentities($_POST['query']));
	$searchAuthors = $_POST['authors'];
	$searchGenres  = $_POST['genres'];
	$searchTitles  = $_POST['titles'];
	$query = "SELECT * FROM mangalisting WHERE ( ";

	if($searchAuthors == "true") {
		$query .= " author LIKE '%".$search."%' ";
	}
	if($searchGenres == "true") {
		if($searchAuthors == "true") {
			$query .= "OR";
		}
		$query .= " genre LIKE '%".$search."%' ";
	}
	if($searchTitles == "true") {
		if($searchAuthors == "true" || $searchGenres == "true") {
			$query .= "OR";
		}
		$query .= " name LIKE '%".$search."%' ";
	}
	$query .= " )";

	if(!mysqli_connect_errno()) {
		if($result = mysqli_query($db,$query)) {
			
			$arr['num_response'] = json_encode(mysqli_num_rows($result));

			if(mysqli_num_rows($result) > 0) {
				while($row = mysqli_fetch_array($result)) {
					$arr['responses'][$num++] = array(
						'name'        => $row['name'],
						'description' => ($row['description'] == '' ? 'No description has been written yet.' : $row['description']),
						'directory'   => $row['directory'],
						'author'      => ($row['author'] == '' ? 'Unknown' : $row['author']),
						'artist'      => ($row['artist'] == '' ? 'Unknown' : $row['artist']),
						'chapters'    => $row['chapters'],
						'year'        => ($row['year'] == '' ? 'Unknown' : $row['year']),
						'direction'   => ($row['direction'] == '' ? 'Unknown' : $row['direction']),
						'status'      => ($row['status'] == '' ? 'Unknown' : $row['status']),
						'genre'       => ($row['genre'] == '' ? 'None' : $row['genre']),
						'alternate'   => ($row['alternate'] == '' ? 'None' : $row['alternate'])
					);
				}
			}
			mysqli_free_result($result);
		}
	}

	mysqli_close($db);

	echo(json_encode($arr));
?>