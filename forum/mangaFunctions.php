<?php
	require_once("./inc/apc.php");
	require_once("./global.php");
	global $mybb;

	ini_set('error_reporting', E_ALL);

	/* Configuration */

	$page_limit = 30;

	$pageListing_SidePages = 2;

	$dbInfo             = Array();
	//$dbInfo['username'] = 'root';
	//$dbInfo['password'] = 'popcorn';
	$dbInfo['username'] = 'mangauser';
	$dbInfo['password'] = 'UZbfU5nEt9';

	$dbInfo['host']     = ($_SERVER['SERVER_ADDR'] == '127.0.0.1' ? 'd1.medemedia.com' : 'localhost');
	$dbInfo['db']       = 'mangasite';

	$fullSiteURL        = ($_SERVER['SERVER_ADDR'] == '127.0.0.1' ? '//127.0.0.1/mangamags.com' : '//mangamags.com');
	$forumPath          = 'forum';

	/* End Configuration */
	
	if(!isset($_COOKIE['had_been_2_reader'])) {
		setcookie('had_been_2_reader','false',time()+60*60*24*7);
	}

	header("Content-Security-Policy: ");

	$isLoggedIn = ($mybb->user['uid'] != 0);

	function setHistory($uid,$series,$dir,$chapter = 1) {
		global $dbInfo;

		$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);

		$uid = abs(intval($uid));
		$seires = mysqli_real_escape_string($db,htmlentities($series));
		$return = 5;
		$lastHistory = getHistory($uid,1);
		$chapter = abs(intval($chapter));

		if(!mysqli_connect_errno()) {
			if(count($lastHistory) != 0) {
				if($lastHistory[0]['series'] != $series || $lastHistory[0]['chapter'] != $chapter) {
					if($result = @mysqli_query($db,"INSERT INTO mangahistory (uid,series,dir) VALUES ('".$uid."','".$series.";".$chapter."','".$dir."')")) {
						$return = 1;
					} else {
						$return = 2;
					}
				} else {
					$return = 3;
				}
			} else {
				if($result = @mysqli_query($db,"INSERT INTO mangahistory (uid,series,dir) VALUES ('".$uid."','".$series.";".$chapter."','".$dir."')")) {
					$return = 1;
				} else {
					$return = 2;
				}
			}
		} else {
			$return = 3;
		}
		mysqli_free_result($result);

		mysqli_close($db);

		return $return;
	}

	function getHistory($uid,$limit = 5) {
		global $dbInfo;

		$history = Array();
		$i = 0;

		$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);

		$uid = abs(intval($uid));
		if(!mysqli_connect_errno()) {
			if($result = @mysqli_query($db,"SELECT * FROM mangahistory WHERE uid='".$uid."' ORDER BY date DESC LIMIT ".$limit)) {
				while($row = mysqli_fetch_array($result)) {
					$history[$i] = $row;
					$splitter = explode(';',$history[$i]['series']);
					$history[$i][2] = $splitter[0];
					$history[$i]['series'] = $splitter[0];
					$history[$i][5] = intval($splitter[1]);
					$history[$i]['chapter'] = intval($splitter[1]);
					++$i;
				}
			}
		}
		mysqli_free_result($result);

		mysqli_close($db);

		return $history;
	}

	function getTags() {
		global $dbInfo;
		$oCache = new CacheAPC();

		$tags = Array();
		$mangas = Array();

		$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);
		if(!mysqli_connect_errno()) {
			if($result = @mysqli_query($db,"SELECT * FROM mangalisting ORDER BY RAND() LIMIT 1")) {
				while($row = mysqli_fetch_array($result)) {
					$manga = $row;
				}
			}
		}
		mysqli_free_result($result);

		mysqli_close($db);

		return $tags;
	}

	function randomManga() {
		global $dbInfo;

		$manga = Array();

		$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);

		if(!mysqli_connect_errno()) {
			if($result = @mysqli_query($db,"SELECT * FROM mangalisting ORDER BY RAND() LIMIT 1")) {
				while($row = mysqli_fetch_array($result)) {
					$manga = $row;
				}
			}
		}
		mysqli_free_result($result);

		mysqli_close($db);

		return $manga;
	}

	function getComments($series) {
		global $dbInfo;
		$oCache = new CacheAPC();

		$comments = Array();

		if($oCache->getData('mangasite_comments_'.$seires)) {
			$comments = $oCache->getData('mangasite_comments_'.$seires);
		} else {
			$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);
			$series = mysqli_real_escape_string($db,$series);

			if(!mysqli_connect_errno()) {
				if($result = @mysqli_query($db,"SELECT * FROM mangacomments WHERE series='".$series."'")) {
					while($row = mysqli_fetch_array($result)) {
						$row['children'] = Array();
						$comments[$row['id']] = $row;
					}
				}
			}
			mysqli_free_result($result);

			mysqli_close($db);

			foreach($comments as $k=>&$v) {
				if($v['parentID'] != 0) {
					$comments[$v['parentID']]['children'][] =& $v;
				}
				unset($v);
			}
			foreach ($comments as $k => $v) {
		  		if ($v['parentID'] != 0) {
		    		unset($comments[$k]);
		  		}
			}
			$oCache->setData('mangasite_comments_'.$series, $uploads);
		}

		return $comments;
	}

	function getHighestRated($amount = 5) {
		global $dbInfo;
		$oCache = new CacheAPC(300);

		$uploads = Array();
		$amount = abs(intval($amount));

		if($oCache->getData('mangasite_highest_'.$amount)) {
			$uploads = $oCache->getData('mangasite_highest_'.$amount);
		} else {
			$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);
			if(mysqli_connect_errno()) {
				return 0;
			}
			$amount = abs(intval($amount));

			if(!$result = @mysqli_query($db,"SELECT * FROM mangarating ORDER BY rate DESC LIMIT 0,".$amount)) {
				return 0;
			}
			
			$i = 0;
			while($row = mysqli_fetch_array($result)) {
				if(!in_array($row['series'],$uploads))
					$uploads[$i++] = $row;
			}
			mysqli_free_result($result);

			mysqli_close($db);

			$oCache->setData('mangasite_highest_'.$amount, $uploads);
		}

		return $uploads;
	}

	function getLatestUploadsEpisodes($amount = 5) {
		global $dbInfo;
		$oCache = new CacheAPC(300);

		$uploads = Array();
		$amount = abs(intval($amount));

		if($oCache->getData('mangasite_latestUpsEps_'.$amount)) {
			$uploads = $oCache->getData('mangasite_latestUpsEps_'.$amount);
		} else {
			$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);
			if(mysqli_connect_errno()) {
				return 0;
			}
			$amount = abs(intval($amount));

			if(!$result = @mysqli_query($db,"SELECT * FROM mangaseries ORDER BY id DESC LIMIT 0,".$amount)) {
				return 0;
			}
			
			$i = 0;
			while($row = mysqli_fetch_array($result)) {
				$uploads[$i++] = $row;
			}
			mysqli_free_result($result);

			mysqli_close($db);

			$oCache->setData('mangasite_latestUpsEps_'.$amount, $uploads);
		}

		return $uploads;
	}

	function getLatestUploadsSeries($amount = 5) {
		global $dbInfo;
		$oCache = new CacheAPC();

		$uploads = Array();
		$amount = abs(intval($amount));

		if($oCache->getData('mangasite_latestUps_'.$amount)) {
			$uploads = $oCache->getData('mangasite_latestUps_'.$amount);
		} else {
			$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);
			if(mysqli_connect_errno()) {
				return 0;
			}
			$amount = abs(intval($amount));

			if(!$result = @mysqli_query($db,"SELECT * FROM mangalisting ORDER BY id DESC LIMIT 0,".$amount)) {
				return 0;
			}
			
			$i = 0;
			while($row = mysqli_fetch_array($result)) {
				$uploads[$i++] = $row;
			}
			mysqli_free_result($result);

			mysqli_close($db);

			$oCache->setData('mangasite_latestUps_'.$amount, $uploads);
		}

		return $uploads;
	}
	function hasBeenInReader() {
		global $_COOKIE;
		$ret = false;

		if(isset($_COOKIE['had_been_2_reader']))
			if($_COOKIE['had_been_2_reader'] == "true")
				$ret = true;

		return $ret;
	}
	function limit_text($text, $limit, $end) {
  		if (str_word_count($text, 0) > $limit) {
	  		$words = str_word_count($text, 2);
      		$pos = a;
      		rray_keys($words);
      		$text = substr($text, 0, $pos[$limit]) . $end;
      	}
      	return $text;
	}
	function doesSeriesExist($series) {
		global $dbInfo;

		$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);

		$series = mysqli_real_escape_string($db,htmlentities($series));

		$exists = false;

		if(!mysqli_connect_errno()) {
			if($result = mysqli_query($db,"SELECT directory FROM mangalisting WHERE directory='".$series."'")) {
				$exists = (mysqli_num_rows($result) > 0 ? true : false);
			}
			mysqli_free_result($result);
		}
		mysqli_close($db);

		return $exists;
	}
	function doesChapterExist($series,$chapter) {
		global $dbInfo;

		$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);

		$series = mysqli_real_escape_string($db,htmlentities($series));
		$chapter = abs(intval($chapter));

		$exists = false;

		if(!mysqli_connect_errno()) {
			if($result = mysqli_query($db,"SELECT * FROM mangaseries WHERE series='".$series."' AND chapter='".$chapter."'")) {
				$exists = (mysqli_num_rows($result) > 0 ? true : false);
			}
			mysqli_free_result($result);
		}
		mysqli_close($db);

		return $exists;
	}
	function getPagesForListing($type,$series) {
		global $dbInfo,$page_limit;

		$numPages = 0;

		$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);

		$series = mysqli_real_escape_string($db,htmlentities($series));

		if(!mysqli_connect_errno()) {
			if($type == 'series') {
				if($result = @mysqli_query($db,"SELECT * FROM mangaseries WHERE series='".$series."'")) {
					$numPages = ceil(mysqli_num_rows($result)/$page_limit);
				}
			} else if($type == 'main') {
				if($result = @mysqli_query($db,"SELECT * FROM mangalisting")) {
					$numPages = ceil(mysqli_num_rows($result)/$page_limit);
				}
			}
		}
		mysqli_free_result($result);
		mysqli_close($db);

		return $numPages;
	}
	function getSeriesRate($series) {
		global $dbInfo;
		$oCache = new CacheAPC();

		if($oCache->getData('mangasite_'.$series.'_rating')) {
			$info = $oCache->getData('mangasite_'.$series.'_rating');
		} else {
			$info = array(
				'rating' => ('0'.'.'.'00'),
				'numRates' => 0
			);

			$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);
			if(!mysqli_connect_errno()) {
				$series = mysqli_real_escape_string($db,htmlentities($series));
				if($result = @mysqli_query($db,"SELECT * FROM mangarating WHERE series='".$series."'")) {
					if(mysqli_num_rows($result) > 0) {
						$info['numRates'] = mysqli_num_rows($result);
						while($row = mysqli_fetch_array($result)) {
							$info['rating'] += $row['rate'];
						}
					}
				}
				mysqli_free_result($result);
				mysqli_close($db);
			}

			if($info['numRates'] != 0) {
				$number = $info['rating'] / $info['numRates'];
				$temp = explode('.',$number);
				$temp[1] = str_pad(((isset($temp[1])) ? $temp[1] : '00'),2,'0');
				//$decimal = str_pad($temp[1],2,'0');

	       		$info['rating'] = (float)$temp[0].'.'.$temp[1];
			}

			$oCache->setData('mangasite_'.$series.'_rating', $info);
		}
		
		return $info;
	}
	function getSeriesInfo($series) {
		global $dbInfo;
		$oCache = new CacheAPC();

		$info = Array();

		if($oCache->getData('mangasite_'.$series.'_info')) {
			$info = $oCache->getData('mangasite_'.$series.'_info');
		} else {
			$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);
			if(mysqli_connect_errno()) {
				return 0;
			}
			$series = mysqli_real_escape_string($db,htmlentities($series));

			if(!$result = @mysqli_query($db,"SELECT * FROM mangalisting WHERE directory='".$series."'")) {
				return 0;
			}

			while($row = mysqli_fetch_array($result)) {
				$info['id']          = $row['id'];
				$info['name']        = $row['name'];
				$info['directory']   = $row['directory'];
				$info['name']        = $row['name'];
				$info['author']      = $row['author'];
				$info['artist']      = $row['artist'];
				$info['chapters']    = $row['chapters'];
				$info['year']        = $row['year'];
				$info['direction']   = $row['direction'];
				$info['status']      = $row['status'];
				$info['genre']       = $row['genre'];
				$info['description'] = $row['description'];
				$info['alternate']   = $row['alternate'];
				$info['rate']      = getSeriesRate($row['directory']);
			}
			mysqli_free_result($result);

			mysqli_close($db);

			$oCache->setData('mangasite_'.$series.'_info', $info);
		}
		return $info;
	}
	function getChapters($series) {
		global $dbInfo;
		$oCache = new CacheAPC();

		$chapters = Array();

		if($oCache->getData('mangasite_'.$series.'_chapters')) {
			$chapters = $oCache->getData('mangasite_'.$series.'_chapters');
		} else {
			$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);
			if(mysqli_connect_errno()) {
				return 0;
			}

			$series = mysqli_real_escape_string($db,htmlentities($series));
			if(!$result = @mysqli_query($db,"SELECT * FROM mangaseries WHERE series='".$series."'")) {
				return 0;
			}

			$index = 0;
			while($row = mysqli_fetch_array($result)) {
				$chapters[$index++] = $row['name'];
			}
			mysqli_free_result($result);

			mysqli_close($db);

			$oCache->setData('mangasite_'.$series.'_chapters', $chapters);
		}
		return $chapters;
	}
	function getChapterPages($series,$chapter) {
		global $dbInfo;
		$oCache = new CacheAPC();

		$pages = 0;
		if($oCache->getData('mangasite_'.$series.'_chapter'.$chapter.'_pages')) {
			$pages = $oCache->getData('mangasite_'.$series.'_chapter'.$chapter.'_pages');
		} else {
			$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);

			$series = mysqli_real_escape_string($db,htmlentities($series));
			$chapter = abs(intval($chapter));

			if(!mysqli_connect_errno()) {
				if(!$result = @mysqli_query($db,"SELECT pages FROM mangaseries WHERE series='".$series."' AND chapter='".$chapter."'")) {
					$pages = 0;
				} else {
					$row = mysqli_fetch_array($result);
					$pages = $row['pages'];
					mysqli_free_result($result);
				}
				mysqli_close($db);
			}

			$oCache->setData('mangasite_'.$series.'_chapter'.$chapter.'_pages', $pages);
		}

		return $pages;
	}
	function getAllSeries($start) {
		global $dbInfo,$page_limit;
		$oCache = new CacheAPC();

		$mangas = Array();

		if($oCache->getData('mangasite_mangas_'.$start)) {
			$mangas = $oCache->getData('mangasite_mangas'.$start);
		} else {
			$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);
			if(mysqli_connect_errno()) {
				return 0;
			}

			if(!$result = @mysqli_query($db,"SELECT * FROM mangalisting")) {
				return 0;
			}
			$maxPage = ceil(mysqli_num_rows($result)/$page_limit);
			mysqli_free_result($result);

			$start = abs(intval($start));
			if(ceil($start/$page_limit) >= $maxPage) {
				$start = intval($maxPage) - $page_limit;
				if($start < 0) $start = 0;
			}
			if(!$result = @mysqli_query($db,"SELECT * FROM mangalisting LIMIT ".$start.",".$page_limit)) {
				return 0;
			}

			$index = 0;
			while($row = mysqli_fetch_array($result)) {
				$mangas[$index] = Array();

				$mangas[$index]['directory']   = $row['directory'];
				$mangas[$index]['name']        = $row['name'];
				$mangas[$index]['author']      = $row['author'];
				$mangas[$index]['artist']      = $row['artist'];
				$mangas[$index]['chapters']    = $row['chapters'];
				$mangas[$index]['year']        = $row['year'];
				$mangas[$index]['direction']   = $row['direction'];
				$mangas[$index]['status']      = $row['status'];
				$mangas[$index]['genre']       = $row['genre'];
				$mangas[$index]['rating']      = $row['rate'];
				$mangas[$index]['description'] = $row['description'];
				$mangas[$index]['alternate']   = $row['alternate'];

				++$index;
			}
			mysqli_free_result($result);

			mysqli_close($db);

			$oCache->setData('mangasite_mangas'.$start, $mangas);
		}

		return $mangas;
	}

	function show404() {
		include('resources/404');
	}
?>