<?php
	define('IS_IN_APP',1);

	require_once('inc.php');

	if(!isset($_GET['series'])) header("Location: ".$fullSiteURL);
	$series = $_GET['series'];
	$chapters = getChapters($series);
	$seriesInfo = getSeriesInfo($series);

	if(!doesSeriesExist($series)) { show404();die(); }

	$newest = getLatestUploads(5);

	$rate = explode('.',$seriesInfo['rate']['rating']);
	$rating_p1 = $rate[0];
	$rating_p2 = $rate[1];

	$comments = getComments($seriesInfo['directory']);
	$commentCount = 0;

	function countComments(array $comments) {
		$i = 0;
		foreach($comments as $info) {
			if (!empty($info['children'])) {
				$i += countComments($info['children']);
			}
			++$i;
		}
		return $i;
	}

	$commentCount = countComments($comments);

	function printComments(array $comments, $level = 0) {
		global $isLoggedIn,$MyBBI;

		foreach ($comments as $info) {
			echo('<div style="margin-left:'.(10 * $level).'px;" id="comment_'.$info['id'].'">
					<div style="border:1px solid rgba(0,0,0,0.1);background-color:rgba(0,0,0,0.04);padding:0px 5px 0px 5px;border-radius:5px;">
						<span>'.htmlentities($info['author']).'</span>&nbsp;&nbsp;<span style="color:rgba(0,0,0,0.5);">('.$info['time'].')</span>
						'.($isLoggedIn ? '<a id="replyto_'.$info['id'].'" style="float:right;text-decoration:none;cursor:pointer;margin-left:5px;">Reply</a>' : NULL));
						if($MyBBI->mybb->usergroup['cancp']) {
							echo('&nbsp;<span style="float:right">|</span>&nbsp;<a id="delete_'.$info['id'].'" style="float:right;text-decoration:none;cursor:pointer;margin-right:5px;">Delete Comment</a>
							<script>
								$("#delete_'.$info['id'].'").click(function(){
									$.ajax({
										url: "'.$fullSiteURL.'/resources/comment.php?adminz=true&action=delete&id='.$info['id'].'",
										type: "GET"
									}).success(function(resp) {
										if(resp == "1") {
											location.reload();
										}
									});
								});
							</script>');
						}
						echo('<div class="clear"></div>
					</div>
					<div style="padding:2px 0px 10px 0px;margin-left:5px;">
						<p>'.htmlentities($info['comment']).'</p>
					</div>
					');
			if($isLoggedIn)
				echo('<textarea id="comment4_'.$info['id'].'" reply2="'.$info['id'].'" style="margin:0px 5px 5px 5px;padding:5px;width:calc(100% - 20px);display:none;resize:none;" placeholder="Write your reply here..."></textarea>
					<button id="submitcomment_'.$info['id'].'" style="margin:0px 5px 5px 5px;float:right;padding:5px;width:300px;display:none;" value="Submit">Submit</button><div class="clear"></div>');
			echo('</div>');
			if (!empty($info['children'])) {
				printComments($info['children'], $level + 1);
			}
		}
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
		<title><?php echo($siteName); ?> - Read <?php echo($seriesInfo['name']); ?></title>
		<meta property="og:title" content="<?php echo($siteName); ?> - Read <?php echo($seriesInfo['name']); ?>" />
		<meta property="og:site_name" content="Read Manga Here!" />
		<meta property="og:image" content="pageimage" />
		
		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo($fullSiteURL); ?>/resources/css/reset.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo($fullSiteURL); ?>/resources/css/theme.css" />

		<!-- Fonts -->
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,300italic,400italic,700,700italic,900,900italic" />
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lobster" />
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Fugaz+One" />
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Rum+Raisin" ?>
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
		<script type="text/javascript" src="//w.sharethis.com/button/buttons.js"></script>

		<script>
			stLight.options({publisher: "564e67a7-56cc-4c17-a06a-2e7599cfc532", doNotHash: false, doNotCopy: false, hashAddressBar: false});
		</script>

		<script type="text/javascript">
			$(document).ready(function() {
				var maxComments = '<?php echo(count($comments)); ?>';
				var curSeries = '<?php echo($series); ?>';
				var switchTo5x=true;

				// Fix box element margins
				$('.left > .box:first').css({
					'margin-top':'0px'
				});
				$('.left > .box:last').css({
					'margin-bottom':'0px'
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
 
				// more complex jRating call
				$("#rating").jRating({
					bigStarsPath: '<?php echo($fullSiteURL); ?>/resources/img/stars.png',
					phpPath: '<?php echo($fullSiteURL); ?>/resources/rate.php?series=<?php echo($series); ?>',
					length: 5,
					step: true,
					canRateAgain: false,
					decimalLength: 1,
					rateMax: 5,
					rateMin: 1
				});

				$('#chapterTab').click(function() {
					$('#chapterTab').css({
						'background-color':'rgba(48,150,229,0.2)'
					});
					$('#commentTab').css({
						'background-color':'rgba(48,150,229,0.1)'
					});
					$('#comments').hide();
					$('#chapterList').show();
				});
				$('#commentTab').click(function() {
					$('#commentTab').css({
						'background-color':'rgba(48,150,229,0.2)'
					});
					$('#chapterTab').css({
						'background-color':'rgba(48,150,229,0.1)'
					});
					$('#chapterList').hide();
					$('#comments').show();
				});

				$('#chapterHider').click(function(){
					$('#chapterHiderNew').show();
					$('#chapterList').hide('fast');
					$(this).hide();
				});
				$('#chapterHiderNew').click(function(){
					$('#chapterHider').show();
					$('#chapterList').show('fast');
					$(this).hide();
				});

				for(var i=1;i<maxComments+1;++i) {
					$('#replyto_'+i).click(function() {
						if($(this).html() == "Reply") {
							$(this).html("Hide");
							$(this).parent().parent().find('button').show();
							$(this).parent().parent().find('textarea').show();
						} else {
							$(this).html("Reply");
							$(this).parent().parent().find('button').hide();
							$(this).parent().parent().find('textarea').hide();
						}
					});
				}

				for(var i = 0;i<maxComments+1;++i) {
					$('#submitcomment_'+i).click(function() {
						$(this).attr('disabled','disabled');

						var author  = '<?php echo($userInfo["username"]); ?>';
						var comment = $(this).parent().find('textarea').val();
						var parent  = $(this).parent().find('textarea').attr('reply2');

						if($(this).parent().find('textarea').val().length > 5) {
							$.ajax({
								url: '<?php echo($fullSiteURL); ?>/resources/comment.php?action=post',
								type: 'POST',
								data: {
									author:  author,
									comment: comment,
									parent:  parent,
									series:  curSeries
								}
								//dataType: 'json'
							}).success(function(resp) {
								if(resp == '0') {
									alert("Your comment will be added within 24 hours.");
								} else {
									alert("There was an error adding your comment.");
									$('#submitcomment_'+i).removeAttr('disabled');
								}
							});
						} else {
							alert("Your comment isn't long enough!  Minimum is 5 characters");
							$('#submitcomment_'+i).removeAttr('disabled');
						}
					});
				}

				$('#submitcomment_main').click(function() {
					$(this).attr('disabled','disabled');

					var author = '<?php echo($userInfo["username"]); ?>';
					var comment = $('#mainCommentBox').val();
					var parent = '0';

					if($('#mainCommentBox').val().length > 5) {
						$.ajax({
							url: '<?php echo($fullSiteURL); ?>/resources/comment.php?action=post',
							type: 'POST',
							data: {
								author:  author,
								comment: comment,
								parent:  parent,
								series:  curSeries
							}
							//dataType: 'json'
						}).success(function(resp) {
							if(resp == '0') {
								location.reload();
							} else {
								alert("There was an error adding your comment.");
								$('#submitcomment_main').removeAttr('disabled');
							}
						});
					} else {
						alert("Your comment isn't long enough!  Minimum is 5 characters");
						$('#submitcomment_'+i).removeAttr('disabled');
					}
				});

				/*
				if(window.location.hash != "") {
					if(window.location.hash.indexOf("comments") != -1) {
						$('#commentTab').css({
							'background-color':'rgba(48,150,229,0.2)'
						});
						$('#chapterTab').css({
							'background-color':'rgba(48,150,229,0.1)'
						});
						$('#chapterList').hide();
						$('#comments').show();
					} else {
						$('#chapterTab').css({
							'background-color':'rgba(48,150,229,0.2)'
						});
						$('#commentTab').css({
							'background-color':'rgba(48,150,229,0.1)'
						});
						$('#comments').hide();
						$('#chapterList').show();
					}
				} else {
					$('#commentTab').css({
						'background-color':'rgba(48,150,229,0.1)'
					});
					$('#chapterTab').css({
						'background-color':'rgba(48,150,229,0.2)'
					});
					$('#chapterList').show();
					$('#comments').hide();
				}
				*/
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
				<div class="box" style="border-bottom:1px dashed rgba(200,200,200,1)" id="mangaInfo">
					<img src="http://s2.medemedia.com/scranga/<?php echo($seriesInfo['directory']); ?>/cover.jpg" style="float:left;height:350px;min-width:225px;max-width:225px;min-height:350px;margin:0px 10px 0px 0px;border: 1px solid rgba(0,0,0,0.1);" title="Cover image for <?php echo($seriesInfo['name']); ?>" id="coverImage" />
					<div style="float:right;width:410px;" id="infoRight">
						<div id="title"><?php echo($seriesInfo['name']); ?></div>
						<span id="ratingNum">
							<span id="p1"><?php echo($rating_p1); ?></span><span id="p2">.<?php echo($rating_p2); ?></span>
						</span>
						<div id="rating" class="basic" data-average="<?php echo($seriesInfo['rate']['rating']); ?>" data-id="1"></div>
						<span style="height:20px;line-height:20px;vertical-align:middle;position:relative;top:-15px;">(<?php echo($seriesInfo['rate']['numRates']); ?> ratings)</span>
						<div id="alternate" style="margin-bottom:5px;"><span class="label">Alternate Titles:</span> <?php echo($seriesInfo['alternate']); ?></div>
						<div id="genre" style="margin-bottom:5px;"><span class="label">Genre(s):</span> <?php echo($seriesInfo['genre']); ?></div>
						<div id="author" style="margin-bottom:5px;"><span class="label">Author(s):</span> <?php echo($seriesInfo['author']); ?></div>
						<div id="artist" style="margin-bottom:5px;"><span class="label">Artist(s):</span> <?php echo($seriesInfo['artist']); ?></div>
						<div id="status" style="margin-bottom:5px;"><span class="label">Status:</span> <?php echo($seriesInfo['status']); ?></div>
						<div id="year" style="margin-bottom:5px;"><span class="label">Starting Year:</span> <?php echo($seriesInfo['year']); ?></div>
						<div id="direction" style="margin-bottom:5px;"><span class="label">Read Direction:</span> <?php echo($seriesInfo['direction']); ?></div>
					</div>
					<div class="clear"></div>
					<div class="box" style="background-color:rgba(0,255,0,0.05);">
						<div id="description" style="margin-bottom:5px;"><span class="label" style="color:rgba(111,51,51,1)">Description: </span><br /><div style="margin:0px 14px 0px 14px;;text-align:justify;"><?php echo($seriesInfo['description']); ?></div></div>
					</div>
					<div class="box" id="socialShares" style="text-align:center;border:0px;">
						<span class='st_facebook_hcount' displayText='Facebook'></span>
						<span class='st_fblike_hcount' displayText='Facebook Like'></span>
						<span class='st_twitter_hcount' displayText='Tweet'></span>
						<span class='st_plusone_hcount' displayText='Google +1'></span>
						<span class='st_sharethis_hcount' displayText='ShareThis'></span>
					</div>
				</div>
				<!-- div id="magic" style="text-align:center;">
					<img src="http://placehold.it/600x150&text=Placeholder for Ad!" />
				</div -->
				<!-- div id="tabs" style="width:100%;">
					<ul>
						<li href="#chapters" id="chapterTab" style="border-radius:5px 5px 0px 0px;display:inline-block;min-width:150px;padding:5px;background-color:rgba(48,150,229,0.2);text-align:center;cursor:pointer;">Chapters</li>
						<li href="#comments" id="commentTab" style="border-radius:5px 5px 0px 0px;display:inline-block;min-width:150px;padding:5px;background-color:rgba(48,150,229,0.2);text-align:center;cursor:pointer;">Comments (<?php echo($commentCount); ?>)</li>
					</ul>
				</div -->
				<div id="chapterHider">Hide Chapters</div>
				<div id="chapterHiderNew" style="display:none;">Show Chapters</div>
				<div class="box" style="border:0px;box-shadow:none;" id="chapterList">
					<ul id="chaptersListUL" style="list-style-type:none;">
						<?php
							for($i=0;$i<=count($chapters)-1;++$i) {
								echo('<li><div class="chaptersListULArrow"></div><a style="color:rgba(70,70,70,1);text-decoration:none;" href="'.$fullSiteURL.'/read/'.$seriesInfo['directory'].'/chapter-'.($i+1).'/1">'.$seriesInfo['name'].' #'.($i+1).' :</a> '.$chapters[$i].'</li><div class="clear"></div>');
							}
						?>
					</ul>
				</div>
				<hr />
				<div class="box" id="comments" style="box-shadow:none;border:0px;">
					<?php if($isLoggedIn) { ?>
						<textarea style="margin:0px 5px 5px 5px;padding:5px;width:calc(100% - 20px);resize:none;" id="mainCommentBox" placeholder="Write your comment..."></textarea>
						<button id="submitcomment_main" style="margin:0px 5px 5px 5px;float:right;padding:5px;width:300px;" value="Submit">Submit</button><div class="clear"></div>
					<?php } else { ?>
						<span style="text-align:center;">You must <a href="<?php echo($fullSiteURL); ?>/login.php" style="color:rgba(0,0,255,0.7);text-decoration:none;">LOG IN</a> to post comments.</span>
					<?php } ?>
					<?php
						printComments($comments)
					?>
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
		<img src="http://s2.medemedia.com/scranga/<?php echo($series); ?>/chapter-1/1.jpg" style="display:none;position:absolute;top:-99999%;left:-9999%;" />
	</body>
</html>