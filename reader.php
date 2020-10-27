<?php
	define("IS_IN_APP",1);

	require_once('inc.php');
	if(!doesChapterExist($_GET['series'],$_GET['chapter'])) {
		header("Location: ".$fullSiteURL."/manga/".$_GET['series']);
	}

	$maxPages = getChapterPages($_GET['series'],$_GET['chapter']);
	$maxChapters = count(getChapters($_GET['series']));

	$currentPage = 1;
	if(intval($_GET['page']) > $maxPages) {
		$currentPage = $maxPages;
	} else if(intval($_GET['page']) < 1) {
		$currentPage = 1;
	} else {
		$currentPage = $_GET['page'];
	}
	$currentChap = intval($_GET['chapter']);
	$currentSeries = htmlentities($_GET['series']);

	$seriesInfo = getSeriesInfo($currentSeries);
	$chapterInfo = getChapters($currentSeries);

	if($isLoggedIn) setHistory($userInfo['uid'],$seriesInfo['name'],$seriesInfo['directory'],$currentChap);

	$comments = getChapterComments($seriesInfo['directory'],$currentChap);

	function printComments(array $comments, $level = 0) {
		global $isLoggedIn,$MyBBI,$fullSiteURL,$forumPath;

		foreach ($comments as $info) {
			$query = $MyBBI->db->simple_select("users","*","username='".$info['author']."'");
			$uid = $MyBBI->db->fetch_field($query, "uid");
			$commentUserInfo = $MyBBI->getUser($uid);

			echo('<div style="margin-left:'.(20 * $level).'px;color:rgba(200,200,200,1);" id="comment_'.$info['id'].'">
					<div style="border:1px solid rgba(0,0,0,0.1);background-color:rgba(0,0,0,0.1);padding:0px 5px 0px 5px;border-radius:5px;">
						<img src="'.$fullSiteURL.'/'.$forumPath.'/'.$commentUserInfo['avatar'].'" style="height:50px;width:50px;float:left;margin-right:10px;" />
						<span style="line-height:50px;vertical-align:middle;font-weight:bold;">'.htmlentities($info['author']).'</span>&nbsp;&nbsp;<span style="color:rgba(250,250,250,0.5);line-height:50px;vertical-align:middle;">('.$info['time'].')</span>
						'.($isLoggedIn ? '<a id="replyto_'.$info['id'].'" style="float:right;text-decoration:none;cursor:pointer;margin-left:5px;line-height:50px;">Reply</a>' : NULL));
						if($MyBBI->mybb->usergroup['cancp']) {
							echo('&nbsp;<span style="float:right;line-height:50px;">|</span>&nbsp;<a id="delete_'.$info['id'].'" style="float:right;text-decoration:none;cursor:pointer;margin-right:5px;line-height:50px;">Delete Comment</a>
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
<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="Shortcut Icon" href="favicon.ico" type="image/x-icon" />

		<meta name="keywords" content="" />
		<meta name="description" content="" />
		<title><?php echo($siteName); ?> - Read <?php echo($seriesInfo['name']); ?></title>
		<meta content="utf-8" http-equiv="encoding">
		<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
		
		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo($fullSiteURL); ?>/resources/css/reset.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo($fullSiteURL); ?>/resources/css/reader.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo($fullSiteURL); ?>/resources/css/toastr.min.css" />

		<!-- Fonts -->
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,300italic,400italic,700,700italic,900,900italic" />
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lobster" />
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Fugaz+One" />

		<!-- jQuery/JavaScript -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="<?php echo($fullSiteURL); ?>/resources/js/jquery.noisy.min.js"></script>
		<script src="<?php echo($fullSiteURL); ?>/resources/js/toastr.min.js"></script>
		<script src="<?php echo($fullSiteURL); ?>/resources/js/jquery.ba-throttle-debounce.min.js"></script>
		<script src="//w.sharethis.com/button/buttons.js"></script>
		<script>
			stLight.options({
				publisher: "564e67a7-56cc-4c17-a06a-2e7599cfc532",
				doNotHash: false, 
				doNotCopy: false, 
				hashAddressBar: false
			});
		</script>

		<script type="text/javascript">
			$(document).ready(function() {
				$('#submitcomment_main').removeAttr('disabled');
				var maxChapters = <?php echo($maxChapters); ?>;
				var maxPages    = <?php echo($maxPages); ?>;
				var currentChap = <?php echo($currentChap); ?>;
				var currentPage = <?php echo($currentPage); ?>;
				var currentSeries = "<?php echo($currentSeries); ?>";
				var maxComments = '<?php echo(count($comments)); ?>';

				$("#chapterList").change(function() {
					if($("#chapterList").val()[0] != "S" && $("#chapterList").val()[0] != "-")
						window.location.href = "<?php echo($fullSiteURL); ?>/read/<?php echo($currentSeries); ?>/chapter-"+$("#chapterList").val()+"/1#navigation";
				});
				$("#pageList").change(function() {
					if($("#pageList").val()[0] != "S" && $("#pageList").val()[0] != "-")
						window.location.href = "<?php echo($fullSiteURL); ?>/read/<?php echo($currentSeries); ?>/chapter-<?php echo($currentChap); ?>/"+$('#pageList').val()+"#navigation";
				});
				$(document).keydown(function(e) {
				    if (e.keyCode == 37) {
				    	if(currentPage - 1 < 1) {
				       		window.location.href = "<?php echo($fullSiteURL); ?>/read/<?php echo($currentSeries); ?>/chapter-<?php echo($currentChap - 1 <= 1 ? $currentChap : ($currentChap - 1)); ?>/1#navigation";
		       			} else {
				       		window.location.href = "<?php echo($fullSiteURL); ?>/read/<?php echo($currentSeries); ?>/chapter-<?php echo($currentChap); ?>/<?php echo(($currentPage - 1 < 1) ? 1 : $currentPage - 1); ?>#navigation";
		       			}
		       			return false;
				    }
				    if (e.keyCode == 39) { 
				    	if(currentPage + 1 > maxPages) {
				       		window.location.href = "<?php echo($fullSiteURL); ?>/read/<?php echo($currentSeries); ?>/chapter-<?php echo($currentChap + 1); ?>/1";
			       		} else {
				       		window.location.href = "<?php echo($fullSiteURL); ?>/read/<?php echo($currentSeries); ?>/chapter-<?php echo($currentChap); ?>/<?php echo(($currentPage + 1 > $maxPages) ? $currentPage : $currentPage + 1); ?>";
			       		}
			       		return false;
				    }
				});

				if(<?php echo(hasBeenInReader() == true ? 'false' : 'true'); ?>) {
					document.cookie="had_been_2_reader=true";

					toastr.options = {
						"closeButton": true,
						"debug": false,
						"positionClass": "toast-bottom-full-width",
						"onclick": null,
						"showDuration": "-1",
						"hideDuration": "-1",
						"timeOut": "-1",
						"extendedTimeOut": "-1",
						"showEasing": "swing",
						"hideEasing": "linear",
						"showMethod": "fadeIn",
						"hideMethod": "fadeOut"
					};
					toastr.info("<span style='font-weight:bold;'>You can navigate pages with your arrow keys!</span>","<span style='font-size:25px;'>Did you know...?</span>");
				}

				$('#submitcomment_main').click(function() {
					$(this).attr('disabled','disabled');

					var author = '<?php echo($userInfo["username"]); ?>';
					var comment = $('#mainCommentBox').val();
					var parent = '0';

					if($('#mainCommentBox').val().length > 5) {
						$.ajax({
							url: '<?php echo($fullSiteURL); ?>/resources/comment.php?action=postchap',
							type: 'POST',
							data: {
								author:  author,
								comment: comment,
								parent:  parent,
								series:  currentSeries,
								chapter: currentChap
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
						$('#submitcomment_main').removeAttr('disabled');
					}
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
					$('#submitcomment_'+i).removeAttr('disabled');

					$('#submitcomment_'+i).click(function() {
						$(this).attr('disabled','disabled');

						var author  = '<?php echo($userInfo["username"]); ?>';
						var comment = $(this).parent().find('textarea').val();
						var parent  = $(this).parent().find('textarea').attr('reply2');

						if($(this).parent().find('textarea').val().length > 5) {
							$.ajax({
								url: '<?php echo($fullSiteURL); ?>/resources/comment.php?action=postchap',
								type: 'POST',
								data: {
									author:  author,
									comment: comment,
									parent:  parent,
									series:  currentSeries,
									chapter: currentChap
								}
								//dataType: 'json'
							}).success(function(resp) {
								if(resp == '0') {
									location.reload();
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
		<!-- div id="ads">
			Ad here
		</div -->
		<div id="pageInfo">
			<span style="color:orange">Page <?php echo($currentPage); ?></span> - Chapter <?php echo($currentChap); ?> <?php echo($chapterInfo[$currentChap - 1]); ?> - <a href="<?php echo($fullSiteURL); ?>/manga/<?php echo($currentSeries); ?>" style="text-decoration:none;color:rgba(225,225,225,1);" onmouseover="$(this).css({'text-decoration':'underline'})" onmouseout="$(this).css({'text-decoration':'none'})"><span><?php echo($seriesInfo['name']); ?></span></a>
		</div>
		<div id="magic">
			<img src="http://placehold.it/800x150&text=Placeholder for Ad!" />
		</div>
		<div id="navigation">
			<?php if($_GET['page'] > 1) { ?>
				<a class="left" href="<?php echo($fullSiteURL); ?>/read/<?php echo($currentSeries); ?>/chapter-<?php echo($_GET['chapter']); ?>/<?php echo($_GET['page']-1); ?>">Previous</a>
			<?php } if($_GET['page'] < $maxPages) { 
				if($_GET['page'] > 1) { ?>
					<a class="right" href="<?php echo($fullSiteURL); ?>/read/<?php echo($currentSeries); ?>/chapter-<?php echo($_GET['chapter']); ?>/<?php echo($_GET['page']+1); ?>">Next</a>
				<?php } else { ?>
					<a class="right" href="<?php echo($fullSiteURL); ?>/read/<?php echo($currentSeries); ?>/chapter-<?php echo($_GET['chapter']); ?>/<?php echo('2'); ?>">Next</a>
				<?php } ?>
			<?php } else if($_GET['page'] >= $maxPages) { ?>
				<a class="right" href="<?php echo($fullSiteURL); ?>/manga/<?php echo($currentSeries); ?>">Back To Chapter Listing</a>
			<?php } ?>
			<div id="pickers">
				<select id="chapterList" class="picker">
					<option>Select Chapter</option>
					<option>--------------</option>
					<?php 
						for($i=1;$i<=$maxChapters;++$i) {
							if($i == $currentChap) {
								echo('<option value="'.$i.'" selected="selected">Chapter '.$i.'</option>');
							} else {
								echo('<option value="'.$i.'">Chapter '.$i.'</option>');
							}
						}
	  				?>
	 			</select>
	 			<select id="pageList" class="picker">
	 				<option>Select Page</option>
					<option>-----------</option>
					<?php 
						for($i=1;$i<=$maxPages;++$i) {
							if($i == $currentPage) {
								echo('<option value="'.$i.'" selected="selected">Page '.$i.'</option>');
							} else {
								echo('<option value="'.$i.'">Page '.$i.'</option>');
							}
						}
	  				?>
				</select>
			</div>
		</div>
		<div class="clear"></div>
		<div id="container">
			<div id="mainColumn">
				<div id="info">
				<?php

				?>
				</div>
				<?php
					if($currentPage < $maxPages) {
						echo('<a href="'.$fullSiteURL.'/read/'.$currentSeries.'/chapter-'.$currentChap.'/'.($currentPage+1).'#navigation"><img src="//s2.medemedia.com/scranga/'.$currentSeries.'/'.$currentChap.'/'.$currentPage.'.jpg" id="page" /></a>');
					} else {
						echo('<a href="'.$fullSiteURL.'/read/'.$currentSeries.'/chapter-'.($currentChap+1).'/1#navigation"><img src="//s2.medemedia.com/scranga/'.$currentSeries.'/'.$currentChap.'/'.$currentPage.'.jpg" id="page" /></a>');
					}
				?>
			</div>
		</div>
		<div class="box" id="socialShares" style="text-align:center;">
			<span class='st_facebook_hcount' displayText='Facebook'></span>
			<span class='st_fblike_hcount' displayText='Facebook Like'></span>
			<span class='st_twitter_hcount' displayText='Tweet'></span>
			<span class='st_sharethis_hcount' displayText='ShareThis'></span>
		</div>
		<div class="box" id="comments">
			<?php if($isLoggedIn) { ?>
				<textarea style="margin:0px 5px 5px 5px;padding:5px;width:calc(100% - 20px);resize:none;" id="mainCommentBox" placeholder="Write your comment..."></textarea>
				<button id="submitcomment_main" style="margin:0px 5px 5px 5px;float:right;padding:5px;width:300px;" value="Submit">Submit</button><div class="clear"></div>
			<?php } ?>
			<?php printComments($comments); ?>
		</div>
		<div class="box" id="seriesInfo">
			<div id="description"><span>Description:</span><br /><?php echo($seriesInfo['description']); ?></div>
			<div id="alternate"><span>Alternate Titles:</span> <?php echo($seriesInfo['alternate']); ?></div>
			<div id="genre"><span>Genre(s):</span> <?php echo($seriesInfo['genre']); ?></div>
			<div id="author"><span>Author(s):</span> <?php echo($seriesInfo['author']); ?></div>
			<div id="artist"><span>Artist(s):</span> <?php echo($seriesInfo['artist']); ?></div>
			<div id="status"><span>Status:</span> <?php echo($seriesInfo['status']); ?></div>
			<div id="direction"><span>Read Direction:</span> <?php echo($seriesInfo['direction']); ?></div>
			<div id="year"><span>Starting year:</span> <?php echo($seriesInfo['year']); ?></div>
		</div>
		<div class="box" id="socialShares" style="text-align:center;">
			<span class='st_facebook_hcount' displayText='Facebook'></span>
			<span class='st_fblike_hcount' displayText='Facebook Like'></span>
			<span class='st_twitter_hcount' displayText='Tweet'></span>
			<span class='st_sharethis_hcount' displayText='ShareThis'></span>
		</div>
		<br />
		<div id="magic">
			<img src="http://placehold.it/800x150&text=Placeholder for Ad!" />
		</div>
		<footer>
			<div>
				<?php include("./resources/footer.php"); ?>
			 </div>
		</footer>
		<?php
			if($currentPage + 1 <= $maxPages) {
				echo('<img id="preload_next" src="//s2.medemedia.com/scranga/'.$currentSeries.'/'.$currentChap.'/'.($currentPage + 1).'.jpg" style="display:none;position:absolute;top:-999%;left:-999%;" />');
			} else {
				if($currentChap + 1 <= $maxChapters) {
					echo('<img id="preload_next" src="//s2.medemedia.com/scranga/'.$currentSeries.'/'.($currentChap + 1).'/1.jpg" style="display:none;position:absolute;top:-999%;left:-999%;" />');
				}
			}
		?>
	</body>
</html>