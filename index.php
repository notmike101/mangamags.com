<?php
	ini_set('error_reporting', E_ALL);
	ini_set("display_errors", 1);

	define('IS_IN_APP',1);

	require_once('inc.php');

	if(isset($_GET['die']) && $_GET['die'] == "404") { show404();die(); }

	$totalpages = getPagesForListing('main',NULL);

	$page = (isset($_GET['page']) ? (intval($_GET['page']) > $totalpages ? $totalpages : (intval($_GET['page']) < 1 ? 1 : intval($_GET['page']))) : 1);
	/*
	if(isset($_GET['page'])) {
		if(intval($_GET['page']) > $totalpages) {
			$page = $totalpages
		} else if(intval($_GET['page']) < 1) {
			$page = 1;
		} else {
			$page = intval($_GET['page'])
		}
	}
	$page = (isset($_GET['page']) ? (intval($_GET['page']) > $totalpages ? $totalpages : intval($_GET['page'])) : 1);
	*/
	$mangas = getAllSeries($page);
?>
<!DOCTYPE html>
<html>
	<head>
		<base href="<?php echo($fullSiteURL); ?>" />

		<meta content="utf-8" http-equiv="encoding" />
		<meta content="text/html;charset=utf-8" http-equiv="Content-Type" />
		<link rel="Shortcut Icon" href="favicon.ico" type="image/x-icon" />

		<meta name="keywords" content="" />
		<meta name="description" content="" />
		<title><?php echo($siteName); ?> - Home</title>
		<meta property="og:title" content="<?php echo($siteName); ?> - Home" />
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
			$(document).ready(function() {
				function htmlentities(e,t,n,r){var i=this.get_html_translation_table("HTML_ENTITIES",t),s="";e=e==null?"":e+"";if(!i){return false}if(t&&t==="ENT_QUOTES"){i["'"]="&#039;"}if(!!r||r==null){for(s in i){if(i.hasOwnProperty(s)){e=e.split(s).join(i[s])}}}else{e=e.replace(/([\s\S]*?)(&(?:#\d+|#x[\da-f]+|[a-zA-Z][\da-z]*);|$)/g,function(e,t,n){for(s in i){if(i.hasOwnProperty(s)){t=t.split(s).join(i[s])}}return t+n})}return e}
				var searchPref = {
					author:  true,
					genre:   true,
					titles:  true
				};

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
				    intensity: 0.8,
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
					if($("#searchBar").val() == "" || $("#searchBar").val().length < 3) {
						$("#mangaListing").show();
						$("#pageList").show();
						$("#searchResults").html('').hide();
					} else {
						$("#mangaListing").hide();
						$("#pageList").hide();
						$("#searchResults").show();

						doSearch($('#searchBar').val(),$('#searchResults'),searchPref);
					}
				}));
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
	<div class="images">
		<a href="'.$fullSiteURL.'/manga/'.$mangas[$i]['directory'].'"><img src="http://s2.medemedia.com/scranga/'.$mangas[$i]['directory'].'/cover.jpg" alt="" /></a>
	</div>
	<div id="content">
		<a href="'.$fullSiteURL.'/manga/'.$mangas[$i]['directory'].'" class="title">'.$mangas[$i]['name'].'</a>
		<div class="clear"></div>
		<div class="author">Author: <span class="info"><a href="'.$fullSiteURL.'/author/'.urlencode($mangas[$i]['author']).'">'.$mangas[$i]['author'].'</a></span></div>
		<div class="year">Year: <span class="info">'.$mangas[$i]['year'].'</span></div>
		<div class="clear"></div>
		<hr />
		<div class="contentBottom">
			<div class="chapters" style="float:left">Chapters: <span class="info">'.$mangas[$i]['chapters'].'</span></div>
			<div class="clear"></div>
			<div class="direction" style="float: left">Read Direction: <span class="info">'.$mangas[$i]['direction'].'</span></div>
			<div class="status" style="float:right">Status: <span class="info">'.$mangas[$i]['status'].'</span></div>
			<div class="clear"></div>
			<div class="tags">Tags: <span class="info">');
			$genres = explode(',',$mangas[$i]['genre']);
			$j = 0;
			foreach($genres as $tags) {
				echo('<a href="'.$fullSiteURL.'/genre/'.urlencode($tags).'">'.$tags.'</a>');
				if($j++ < (count($genres)-1)) {
					echo(',');
				}
			}
			echo('</span></div>
		</div>
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
							for($i=$page-2;$add2beginning>0;--$i,--$add2beginning) {
								array_unshift($links,'<a href="'.$fullSiteURL.'/page/'.($i-1).'" class="pageLink">'.($i-1).'</a>');
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
			<?php include("./resources/footer.php"); ?>
		</footer>
	</body>
</html>