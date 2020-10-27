<?php
	$time = time () ; 
	$year= date("Y",$time) . "<br>"; 
	echo("
		<div class='content'>
			<nav id='bottom'>
				<li><a href='".$fullSiteURL."'>Home</a></li>
				<li><a href='".$fullSiteURL."/login.php?action=register'>Register</a></li>
				<li><a href='".$fullSiteURL."/".$forumPath."'>Forum</a></li>
				<li><a href='".$fullSiteURL."/request.php'>Takedown Requests</a></li>
			</nav>
			<div id='copyright'>
				Copyright and trademarks for the manga, and other promotional materials are here by their respective owners and their use is allowed under the fair use clause of the Copyright law.
				<br />
				<span style='float:right;'>Website Created By © ".$siteName." 2013 - ".$year."</span>
				<div class='clear'></div>
			</div>
			<div class='clear'></div>
		</div>
	");
 ?>