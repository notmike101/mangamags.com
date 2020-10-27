<?php
	define('IS_IN_APP',1);

	require_once('inc.php');

	if(isset($_GET['die']) && $_GET['die'] == "404") { show404();die(); }

	$newestEpisodes = getLatestUploadsEpisodes(50);
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
		<title><?php echo($siteName); ?> - Listing</title>
		<meta property="og:title" content="<?php echo($siteName); ?> - Listing" />
		<meta property="og:site_name" content="Read Manga Here!" />
		<meta property="og:image" content="pageimage" />
		
		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo($fullSiteURL); ?>/resources/css/reset.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo($fullSiteURL); ?>/resources/css/theme.css" />

		<!-- Fonts -->
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,300italic,400italic,700,700italic,900,900italic" />
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lobster" />
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Fugaz+One" />

		<!-- jQuery & Stuff -->
		<link rel="stylesheet" type="text/css" href="<?php echo($fullSiteURL); ?>/resources/css/toastr.min.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo($fullSiteURL); ?>/resources/css/jquery.bxslider.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo($fullSiteURL); ?>/resources/css/jRating.jquery.css" />

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="<?php echo($fullSiteURL); ?>/resources/js/jRating.jquery.min.js"></script>
		<script src="<?php echo($fullSiteURL); ?>/resources/js/jquery.noisy.min.js"></script>
		<script src="<?php echo($fullSiteURL); ?>/resources/js/toastr.min.js"></script>
		<script src="<?php echo($fullSiteURL); ?>/resources/js/jquery.bxslider.min.js"></script>
		<script src="<?php echo($fullSiteURL); ?>/resources/js/jquery.sticky-kit.min.js"></script>
		<script src="<?php echo($fullSiteURL); ?>/resources/js/jquery.ba-throttle-debounce.min.js"></script>
		<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>

		<script type="text/javascript">
			$(document).ready(function() {
				function htmlentities(e,t,n,r){var i=this.get_html_translation_table("HTML_ENTITIES",t),s="";e=e==null?"":e+"";if(!i){return false}if(t&&t==="ENT_QUOTES"){i["'"]="&#039;"}if(!!r||r==null){for(s in i){if(i.hasOwnProperty(s)){e=e.split(s).join(i[s])}}}else{e=e.replace(/([\s\S]*?)(&(?:#\d+|#x[\da-f]+|[a-zA-Z][\da-z]*);|$)/g,function(e,t,n){for(s in i){if(i.hasOwnProperty(s)){t=t.split(s).join(i[s])}}return t+n})}return e}
				var searchPref = {
					author:  true,
					genre:   true,
					titles:  true
				};

				stLight.options({publisher: "564e67a7-56cc-4c17-a06a-2e7599cfc532", doNotHash: false, doNotCopy: false, hashAddressBar: false});

				// Fix box element margins
				$('.left > .box:first').css({
					'margin-top':'0px'
				});
				$('.right > .box:first').css({
					'margin-top':'0px'
				});
				$('.right > .box:last').css({
					'margin-bottom':'0px'
				});

				$('body').noisy({
				    intensity: 0.5,
				    size: 200,
				    opacity: 0.05,
				    fallback: '',
				    randomColors: true
				});
				
		  		$('.bxslider').bxSlider({
		  			mode: 'horizontal',
		  			auto: 'true',
		  			infiniteLoop: 'true',
		  			controls: 'false'
		  		});
				
				$(".right").stick_in_parent();
			});
		</script>
	</head>
	<body>
		<header>
			<div id="top">
				<a id="logo" href="<?php echo($fullSiteURL); ?>"><?php echo($siteName); ?></a>
			</div>
			<?php include("./resources/nav.php"); ?>
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