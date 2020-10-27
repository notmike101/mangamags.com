<?php
	$history = getHistory($userInfo['uid'],10);
	$newest = getLatestUploads(10);
?>
			<section class="right">
				<?php if($isLoggedIn) { ?>
					<div class="box">
						<img src="<?php echo($fullSiteURL.'/'.$forumPath.'/'.$userInfo['avatar']); ?>" height="80" width="80" alt="" title="Avatar" style="float:left;margin:0px 0px 0px 0px;box-shadow: 2px 2px 5px #888888;" />
						<div style="color:rgba(0,0,255,0.75);font-size:16px;float:left;height:80px;vertical-align:center;margin:0px 0px 0px 10px;width:calc(100% - 100px);">
							<a href="<?php echo($fullSiteURL); ?>/<?php echo($forumPath); ?>/usercp.php" style="text-decoration:none;color:blue;">User CP</a><br />
							<?php if($MyBBI->mybb->usergroup['cancp']) { ?><a href="<?php echo($fullSiteURL); ?>/<?php echo($forumPath); ?>/admin/" style="text-decoration:none;color:blue;">Admin CP</a><br /><?php } ?>
						</div>
						<div class="clear"></div>
					</div>
					<div class="box">
						<p style="text-align:center;font-size:18px;color:rgba(0,0,0,0.75);text-decoration:none;font-family:'Patua One',cursive;font-weight:400;">History</p>
						<hr style="margin:5px 0px 5px 0px;padding:0px;" />
						<?php
							if(count($history) == 0) {
								echo("You have not viewed any mangas!");
							} else {
								for($i = 0;$i < count($history);++$i) {
									echo('<a href="'.$fullSiteURL.'/read/'.$history[$i]['dir'].'/chapter-'.$history[$i]['chapter'].'/1" style="text-decoration:none;color:blue;"">'.$history[$i]['series'].': Chapter '.$history[$i]['chapter'].'</a><br />');
								}
							}
						?>
					</div>
				<?php } else { ?>
					<div class="box" id="welcomeBox">
						<p style="text-align:left;font-size:18px;font-weight:bold;margin:5px 0px 10px 0px;color:rgba(30,90,0,1);">Read Manga Online</p>
						<p style="text-align:justify;">
							Welcome to <?php echo($siteName); ?>!  The best place to read your favorite mangas series online!
							<br />
							<br />
							Manga is the Japanese comics with a unique story line and style. 
							In Japan people of all ages read manga, manga does not target younger audiences like american comics. 
							The genre includes a broad range of subjects. Here you will find 1000s of free english translated manga scans to read online.
						</p>
						<div style="width:0px;height:0px;margin:5px 0px 0px 0px;"></div>
						<span class='st_facebook_hcount' displayText='Facebook'></span>
						<span class='st_fblike_hcount' displayText='Facebook Like'></span>
						<span class='st_twitter_hcount' displayText='Tweet'></span>
					</div>
				<?php } ?>
				<div class="box">
					<p style="color:rgba(0,0,0,0.75);text-align:center;font-size:18px;font-family:'Patua One',cursive;">Latest Mangas</p>
					<hr style="margin:5px 0px 5px 0px;padding:0px;" />
					<?php
						for($i=0;$i<count($newest);++$i) {
							echo('<a href="'.$fullSiteURL.'/manga/'.$newest[$i]['directory'].'" style="text-decoration:none;color:blue;">'.$newest[$i]['name'].'</a><br />');
						}
					?>
				</div>
				<div class="box">
					<img src="http://placehold.it/300x250&amp;text=Placeholder+Ad" />
				</div>
				<!-- div class="box">
					Search / Category listing
				</div -->
			</section>