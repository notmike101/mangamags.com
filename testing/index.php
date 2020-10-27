<?php
	define('IS_IN_APP',1);

	require_once('../inc.php');

	$totalpages = getPagesForListing('main',NULL);

	$mangas = getAllSeries($page);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<base href="<?php echo($fullSiteURL); ?>" />

		<meta content="utf-8" http-equiv="encoding">
		<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
		<link rel="Shortcut Icon" href="favicon.ico" type="image/x-icon" />

		<meta name="keywords" content="" />
		<meta name="description" content="" />
		<title><?php echo($siteName); ?> - Home</title>
		<meta property="og:title" content="<?php echo($siteName); ?> - Home" />
		<meta property="og:site_name" content="Read Manga Here!" />
		<meta property="og:image" content="pageimage" />

		<!-- Fonts -->
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800&amp;subset=latin,greek-ext' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Oswald:400,700,300' rel='stylesheet' type='text/css'>
		
		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo($fullSiteURL); ?>/../resources/css/reset.css" />
		<style>
			body {
				background-color: rgba(229, 225, 209, 1);
			}
			body > header {
				background-color: rgba(82, 97, 109, 1);
				font-family: 'Oswald', sans-serif;
				height: 250px;
			}
			body > header > #container {
				width: 800px;
				margin: 0px auto 0px auto;
				padding: 100px 0px 0px 0px;
			}
			body > header > #container > a#logo,body > header > #container > a#logo:hover,body > header > #container > a#logo:visited {
				color: rgba(243, 241, 233, 1);
				text-decoration: none;
				text-transform: uppercase;
				font-size: 30px;
				font-weight: 400;
			}
			body > header > #container > nav {
				list-style: none;
				background-color: rgba(243, 241, 233, 1);
			}
			body > header > #container > nav > li {
			 	display: inline-block;
			 	border-right: 1px solid rgba(153, 150, 139, 1);
			 	border-left: 1px solid rgba(153, 150, 139, 1);
			 	padding: 0px;
			 	margin: 0px;
			 	width: 100px;
			 }
			body > #container {
				background-color: rgba(243, 241, 233, 1);
				width: 800px;
				margin: 0px auto 0px auto;
			}
		</style>

		<!-- jQuery & Stuff -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="<?php echo($fullSiteURL); ?>/resources/js/jRating.jquery.min.js"></script>
		<script src="<?php echo($fullSiteURL); ?>/resources/js/jquery.noisy.min.js"></script>
		<script src="<?php echo($fullSiteURL); ?>/resources/js/toastr.min.js"></script>
		<script src="<?php echo($fullSiteURL); ?>/resources/js/jquery.bxslider.min.js"></script>
		<script src="<?php echo($fullSiteURL); ?>/resources/js/jquery.sticky-kit.min.js"></script>
		<script src="<?php echo($fullSiteURL); ?>/resources/js/jquery.ba-throttle-debounce.min.js"></script>
		<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
	</head>
	<body>
		<header>
			<div id="container">
				<a id="logo" href="<?php echo($fullSiteURL); ?>"><?php echo($siteName); ?></a>
				<nav>
					<li>Home</li>
					<li>Forum</li>
					<li>Search</li>
				</nav>
			</div>
		</header>
		<section id="main">
			<section class="left">
				<!-- ul class="bxslider">
			  		<li><img src="http://placehold.it/605x150&amp;text=Image1" /></li>
			  		<li><img src="http://placehold.it/605x150&amp;text=Image2" /></li>
				</ul -->
				<div id="search" style="margin-top:5px;">
					<input type="text" name="search" id="searchBar" placeholder="Search..." />
				</div>
				<div id="searchResults"></div>
				<div id="mangaListing">
					<?php
						for($i=(count($mangas)-1);$i>=0;--$i) {
							echo('
<div class="box manga">
	<div id="images">
		<a href="'.$fullSiteURL.'/manga/'.$mangas[$i]['directory'].'"><img src="http://s2.medemedia.com/scranga/'.$mangas[$i]['directory'].'/cover.jpg" alt="" /></a>
	</div>
	<div id="content">
		<a href="'.$fullSiteURL.'/manga/'.$mangas[$i]['directory'].'" class="title">'.$mangas[$i]['name'].'</a>
		<div class="author">Author/Artist: <span class="info"><a href="'.$fullSiteURL.'/author/'.urlencode($mangas[$i]['author']).'">'.$mangas[$i]['author'].'</a></span></div>
		<hr />
		<div class="chapters" style="float:left">Chapters: <span class="info">'.$mangas[$i]['chapters'].'</span></div>
		<div class="year" style="float:right">Year: <span class="info">'.$mangas[$i]['year'].'</span></div>
		<div class="clear"></div>
		<div class="direction" style="float: left">Read Direction: <span class="info">'.$mangas[$i]['direction'].'</span></div>
		<div class="status" style="float:right">Status: <span class="info">'.$mangas[$i]['status'].'</span></div>
		<div class="clear"></div>
		<div class="tags">Tags: <span class="info">');
		$genres = explode(',',$mangas[$i]['genre']);
		$j = 0;
		foreach($genres as $tags) {
			echo('<a href="'.$fullSiteURL.'/genre/'.$tags.'">'.$tags.'</a>');
			if($j++ < (count($genres)-1)) {
				echo(',');
			}
		}
		echo('</span></div>
	</div>
	<div class="clear"></div>
</div>
							');
						}
					?>
				</div>
				<div class="box" id="pageList">
					<div style="float:left;">
						Showing Page <?php echo($page); ?> of <?php echo($totalpages); ?>
					</div>
					<div style="float:right" id="pages">
						<?php
							$add2end = 0;
							$add2beginning = 0;
							$links = array();

							for($i=$page-2;$i<$page+3;++$i) {
								if($i <= 0) {
									++$add2end;
								} else if($i > $totalpages) {
									++$add2beginning;
								} else {
									if($i == $page) {
										array_push($links,'<a href="'.$fullSiteURL.'/page/'.($i).'" class="pageLink active">'.($i).'</a>');
									} else {
										array_push($links,'<a href="'.$fullSiteURL.'/page/'.($i).'" class="pageLink">'.($i).'</a>');
									}
								}
							}
							for($i=$page+2;$add2end>0;++$i,--$add2end) {
								array_push($links,'<a href="'.$fullSiteURL.'/page/'.($i+1).'" class="pageLink">'.($i+1).'</a>');
							}
							for($i=$page;$add2beginning>0;--$i,--$add2beginning) {
								array_unshift($links,'<a href="'.$fullSiteURL.'/page/'.($i).'" class="pageLink">'.($i).'</a>');
							}

							for($i=0;$i<count($links);++$i) {
								echo($links[$i]);
							}
						?>
					</div>
					<div class="clear"></div>
				</div>
			</section>
			<?php include("./resources/rightBox.php"); ?>
		    <div class="clear"></div>
		</section>
		<footer>
			<p class="content">
				<?php 
					 $time = time () ; 
					 $year= date("Y",$time) . "<br>"; 
					 echo("Copyright and trademarks for the manga, and other promotional materials are here by their respective owners and their use is allowed under the fair use clause of the Copyright law.  © ".$siteName." 2013 - " . $year);
				 ?>
			</p>
		</footer>
	</body>
</html>