<?php
	ini_set('error_reporting', E_ALL);
	ini_set("display_errors", 1);

	define('IS_IN_APP',1);

	require_once('inc.php');

	$templatelist = "index,index_whosonline,index_whosonline_memberbit,forumbit_depth1_cat,forumbit_depth2_cat,forumbit_depth2_forum,forumbit_depth1_forum_lastpost,forumbit_depth2_forum_lastpost,forumbit_moderators,forumbit_subforums";
	$templatelist .= ",index_birthdays_birthday,index_birthdays,index_loginform,index_logoutlink,index_stats,forumbit_depth3,forumbit_depth3_statusicon,index_boardstats";

	require_once $forumPath."/inc/functions_post.php";
	require_once $forumPath."/inc/functions_forumlist.php";
	require_once $forumPath."/inc/class_parser.php";
	$parser = new postParser;

	$plugins->run_hooks("index_start");

	// Load global language phrases
	$lang->load("index");

	if($mybb->user['uid'] == 0) {
		$query = $db->query("
			SELECT *
			FROM ".TABLE_PREFIX."forums
			WHERE active != 0
			ORDER BY pid, disporder
		");

		$forumsread = my_unserialize($mybb->cookies['mybb']['forumread']);
	} else {
		// Build a forum cache.
		$query = $db->query("
			SELECT f.*, fr.dateline AS lastread
			FROM ".TABLE_PREFIX."forums f
			LEFT JOIN ".TABLE_PREFIX."forumsread fr ON (fr.fid=f.fid AND fr.uid='{$mybb->user['uid']}')
			WHERE f.active != 0
			ORDER BY pid, disporder
		");
	}
	while($forum = $db->fetch_array($query)) {
		if($mybb->user['uid'] == 0) {
			if(!empty($forumsread[$forum['fid']])) {
				$forum['lastread'] = $forumsread[$forum['fid']];
			}
		}
		$fcache[$forum['pid']][$forum['disporder']][$forum['fid']] = $forum;
	}
	$forumpermissions = forum_permissions();
	if($mybb->settings['modlist'] != "off") {
		$moderatorcache = $cache->read("moderators");
	}
	$excols = "index";
	$permissioncache['-1'] = "1";
	$bgcolor = "trow1";

	if($mybb->settings['subforumsindex'] != 0) {
		$showdepth = 3;
	} else {
		$showdepth = 2;
	}

	$theme['imgdir'] = $fullSiteURL.'/'.$forumPath.'/'.$theme['imgdir'];

	$forum_list = build_forumbits();
	$forums = $forum_list['forum_list'];

	$plugins->run_hooks("index_end");

	add_breadcrumb("Forums", $fullSiteURL.'/forum.php');

	eval("\$index = \"".$templates->get("integrateForum")."\";");
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
		<title><?php echo($siteName); ?> - Forum</title>
		<meta property="og:title" content="<?php echo($siteName); ?> - Forum" />
		<meta property="og:site_name" content="Read Manga Here!" />
		<meta property="og:image" content="pageimage" />
		
		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo($fullSiteURL); ?>/resources/css/reset.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo($fullSiteURL); ?>/resources/css/theme.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo($fullSiteURL); ?>/resources/css/forum.css" />

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

		<script>
			stLight.options({publisher: "564e67a7-56cc-4c17-a06a-2e7599cfc532", doNotHash: false, doNotCopy: false, hashAddressBar: false});
		</script>

		<script type="text/javascript">
			$(document).ready(function() {
				
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
			<!-- ul class="bxslider">
			  	<li><img src="http://placehold.it/605x150&amp;text=Image1" /></li>
			  	<li><img src="http://placehold.it/605x150&amp;text=Image2" /></li>
			</ul -->
			<div id="forums">
				<?php output_page($index); ?>
			</div>
		</section>
		<footer>
			<p class="content">
				<?php include("./resources/footer.php"); ?>
			</p>
		</footer>
	</body>
</html>