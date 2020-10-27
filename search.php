<?php
	define('IS_IN_APP',1);

	require_once('inc.php');

	$newest = getLatestUploads(5);

	$searchVal = "";

	if(isset($_GET['value'])) {
		$searchVal = $_GET['value'];
	}
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
		<title><?php echo($siteName); ?> - Search</title>
		<meta property="og:title" content="<?php echo($siteName); ?> - Search" />
		<meta property="og:site_name" content="Read Manga Here!" />
		<meta property="og:image" content="pageimage" />
		
		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo($fullSiteURL); ?>/resources/css/reset.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo($fullSiteURL); ?>/resources/css/theme.css" />

		<!-- Fonts -->
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,300italic,400italic,700,700italic,900,900italic" />
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lobster" />
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Fugaz+One" />
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Patua+One" />

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
		<script>
			stLight.options({publisher: "564e67a7-56cc-4c17-a06a-2e7599cfc532", doNotHash: false, doNotCopy: false, hashAddressBar: false});
		</script>
		<script type="text/javascript">
			var searchPref = {
				author:  <?php echo(((isset($_GET['type']) && ($_GET['type'] == "author" || $_GET['type'] == "all")) || !isset($_GET['type'])) ? 'true' : 'false'); ?>,
				genre:   <?php echo(((isset($_GET['type']) && ($_GET['type'] == "genre" || $_GET['type'] == "all")) || !isset($_GET['type'])) ? 'true' : 'false'); ?>,
				titles:  <?php echo(((isset($_GET['type']) && ($_GET['type'] == "all")) || !isset($_GET['type'])) ? 'true' : 'false'); ?>
			};

			$(document).ready(function() {
				function doSearch(searchterm,add2,searchPrefs) {
					$.ajax({
						url: '<?php echo($fullSiteURL); ?>/resources/search.php',
						type: 'POST',
						data: {
							query: searchterm,
							authors: searchPrefs.author,
							genres:  searchPrefs.genre,
							titles:  searchPrefs.titles
						}
						//dataType: 'json'
					}).success(function(resp) {
						add2.html('');

						var obj = $.parseJSON(resp);

						if(obj['num_response'] != 0) {
							for(var i = 0;i<obj['responses'].length;++i) {
								var tagz = obj['responses'][i]['genre'].split(',');
								var tags = '';
								j = 0;
								tagz.forEach(function(t) {
									tags += '<a href="<?php echo($fullSiteURL); ?>/genre/'+encodeURIComponent(t)+'">'+t+'</a>';
									if(j++ < (tagz.length - 1)) {
										tags += ',';
									}
								});
								add2.append(' \
<div class="box manga"> \
	<div class="images"> \
		<a href=<?php echo($fullSiteURL); ?>/manga/'+obj['responses'][i]['directory']+'"><img src="http://s2.medemedia.com/scranga/'+obj['responses'][i]['directory']+'/cover.jpg" alt="" /></a> \
	</div> \
	<div id="content"> \
		<a href="<?php echo($fullSiteURL); ?>/manga/'+obj['responses'][i]['directory']+'" class="title">'+obj['responses'][i]['name']+'</a> \
		<div class="clear"></div> \
		<div class="author">Author: <span class="info"><a href="<?php echo($fullSiteURL); ?>/author/'+encodeURIComponent(obj['responses'][i]['author'])+'">'+obj['responses'][i]['author']+'</a></span></div> \
		<div class="year">Year: <span class="info">'+obj['responses'][i]['year']+'</span></div> \
		<div class="clear"></div> \
		<hr /> \
		<div class="contentBottom"> \
			<div class="chapters" style="float:left">Chapters: <span class="info">'+obj['responses'][i]['chapters']+'</span></div> \
			<div class="clear"></div> \
			<div class="direction" style="float: left">Read Direction: <span class="info">'+obj['responses'][i]['direction']+'</span></div> \
			<div class="status" style="float:right">Status: <span class="info">'+obj['responses'][i]['status']+'</span></div> \
			<div class="clear"></div> \
			<div class="tags">Tags: <span class="info">'+tags+'</span></div> \
		</div> \
	</div> \
	<div class="clear"></div> \
</div> \
									');
							}
						} else {
							add2.append('<div class="box manga">No results found!</div>');
						}
					});
				}

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

				$("#searchBar").on('keyup',$.debounce(200,function() {
					if($('#searchBar').val() != '' && $('#searchBar').val().length >= 3) {
						doSearch($('#searchBar').val(),$('#searchResults'),searchPref);
					} else {
						$('#searchResults').html('You can search for titles, authors, artists, and even genre!<br />Just type in the box above to start.');
					}
				}));

				if(<?php echo(($searchVal != '' && strlen($searchVal) >= 3) ? 'true' : 'false'); ?>) {
					doSearch('<?php echo($searchVal == '' ? NULL : $searchVal); ?>',$('#searchResults'),searchPref);
				}
			});
		</script>
	</head>
	<body>
		<header>
			<div id="top">
				<div id="logo">
					<a href="<?php echo($fullSiteURL); ?>"><?php echo($siteName); ?></a>
				</div>
			</div>
			<?php include("./resources/nav.php"); ?>
		</header>
		<section id="main">
			<section class="left">
				<div id="search" style="margin-top:5px;">
					<input type="text" name="search" id="searchBar" placeholder="Search..." value="<?php echo($searchVal); ?>" />
					
					<!-- <label for="author" style="line-height:15px;vertical-align:center;">Authors Only:</label>
					<input type="checkbox" name="author" id="searchA" checked="checked" onchange="searchPref.author = this.checked" style="line-height:15px;vertical-align:center;" /><br />
					<label for="genre" style="line-height:15px;vertical-align:center;">Genres Only:</label>
					<input type="checkbox" name="genre" id="searchG" checked="checked" onchange="searchPref.genre = this.checked" style="line-height:15px;vertical-align:center;" /><br />
					<label for="title" style="line-height:15px;vertical-align:center;">Titles Only:</label>
					<input type="checkbox" name="title" id="searchT" checked="checked" onchange="searchPref.title = this.checked" style="line-height:15px;vertical-align:center;" /><br />
					<label for="everything" style="line-height:15px;vertical-align:center;">Everything:</label>
					<input type="checkbox" name="everything" id="everything" checked="checked" onchange="searchPref.author,searchPref,genre,searchPref.title = this.checked;$('#searchA').checked=true" style="line-height:15px;vertical-align:center;" /><br /> -->
				</div>
				<div id="searchResults">
					You can search for titles, authors, artists, and even genre!
					<br />
					Just type in the box above to start.
				</div>
			</section>
			<?php include("./resources/rightBox.php"); ?>
		    <div class="clear"></div>
		</section>
		<footer>
			<p class="content">
				<?php include("./resources/footer.php"); ?>
			</p>
		</footer>
	</body>
</html>