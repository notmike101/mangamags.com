<?php
	define("IN_MYBB",1);
	include("global.php");
	define('THIS_SCRIPT', 'testPage.php');

	global $headerinclude, $header, $theme, $footer, $templates, $lang, $user;
	if ($GLOBALS['mybb']->user['usergroup'] == "1") 
		$a='<br>Sorry you cannot do that';
	if ($GLOBALS['mybb']->user['usergroup'] == "4") 
		$a="<br> admin";

$template = '<html>
<head>
<title>' . $pages['name'] . '</title>
'.$headerinclude.'
<style>
#home .home {
    background: none repeat scroll 0% 0% rgb(255, 255, 255);
    padding: 18px 20px 17px;
    color: rgb(104, 120, 128);
    font-weight: bold;
}
</style>
</head>
<body id="home">
<script type="text/javascript">
if (document.body.id == 0) {document.body.id = "forums";}	
</script>

<div id="header">
<div id="header_wrap">

<div class="logo"><a href="'.$mybb->settings['bburl'].'/index.php"><img src="'.$theme['logo'].'" alt="'.$mybb->settings['bbname'].'" title="'.$mybb->settings['bbname'].'" /></a></div>

<div class="search_box">

<a href="search.php" class="adv_search">Advanced</a>

<form method="post" action="search.php" style="">
<input type="hidden" name="action" value="do_search" />
<input type="hidden" name="tid" value="'.$thread['tid'].'" />
<input type="text" class="textbox" name="keywords" value="Search..." onfocus="if(this.value == \'Search...\') { this.value = \'\'; }" onblur="if(this.value==\'\') { this.value=\'Search...\'; }" />

<input class="complete" name="search" type="image" src="'.$theme['imgdir'].'/search.png" value="'.$lang->search_thread.'">
</form>
</div>
<div id="bridge">
<a href="#" class="panel_toggle">
Toggle Panel
</a>
<div class="menu">
<ul>
<li><a href="'.$mybb->settings['bburl'].'/" class="home">Home</a></li>
<li><a href="'.$mybb->settings['bburl'].'/index.php" class="forums">Forums</a></li>
<li><a href="'.$mybb->settings['bburl'].'/search.php" class="search">'.$lang->toplinks_search.'</a></li>
<li><a href="'.$mybb->settings['bburl'].'/memberlist.php" class="memberlist">'.$lang->toplinks_memberlist.'</a></li>
<li><a href="'.$mybb->settings['bburl'].'/testPage.php" class="test">Test</a></li>
</ul>
</div>
</div>

</div>
</div>

	<div id="container">
		<a name="top" id="top"></a>
			<hr class="hidden" />
			<div id="panel">
				'.$welcomeblock.'
			</div>
		<hr class="hidden" />
		<div id="content">
			<navigation>
			'.$pm_notice.'
			'.$bannedwarning.'
			'.$bbclosedwarning.'
			'.$unreadreports.'
			'.$pending_joinrequests.'
			<br />
'.$errors.'
<table border="0" cellspacing="0" cellpadding="0" class="tborder2">
<tr>
<td class="trow1" valign="top" style="color: #FFFFFF; border-style:solid; border-color: #FF0000; border-width: 1px;" align="center">
<strong>Page is currently under construction</strong>
$a
</td>
</tr>
</table>

'.$footer.'
</body>
</html>';

$template = str_replace("\'", "'", addslashes($template));

add_breadcrumb('Home');

eval("\$page = \"" . $template . "\";");

output_page($page);

?>