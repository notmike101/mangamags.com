<nav>
	<ul>
		<div id="leftNav">
			<a href="<?php echo($fullSiteURL); ?>/" class="left"><li>Home</li></a>
			<!-- a href="<?php echo($fullSiteURL); ?>/all/" class="left"><li>Manga List</li></a -->
			<a href="<?php echo($fullSiteURL); ?>/random/" class="left"><li>Random Manga</li></a>
			<a href="<?php echo($fullSiteURL); ?>/search/" class="left"><li>Search</li></a>
			<a href="<?php echo($fullSiteURL); ?>/forum.php" class="left"><li>Forums</li></a>
			<a href="<?php echo($fulLSiteURL); ?>/upload.php" class="left"><li>Upload</li></a>
		</div>

		<div id="rightNav">
		<?php if(!$isLoggedIn) { ?>
			<a href="<?php echo($fullSiteURL); ?>/login.php?action=register" style="float:right;margin-left:3px;" class="right"><li>Register</li></a>
			<a href="<?php echo($fullSiteURL); ?>/login/" style="float:right;" class="right"><li>Login</li></a>
		<?php } else { ?>
			<a href="<?php echo($fullSiteURL); ?>/login.php?action=logout" style="float:right;margin-left:3px;" class="right"><li>Logout</li></a>
			<a href="<?php echo($fullSiteURL); ?>/<?php echo($forumPath); ?>/usercp.php" style="float:right" class="right"><li>Welcome, <?php echo($userInfo['username']); ?></li></a>
		<?php } ?>
		</div>
		<div class="clear"></div>
	</ul>
</nav>