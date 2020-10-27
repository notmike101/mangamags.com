<?php
	ini_set('error_reporting', E_ALL);

	define("IN_MYBB",1);
	include("global.php");
	include("mangaFunctions.php");
	require_once("./inc/class_parser.php");
	define('THIS_SCRIPT', 'series.php');
	global $headerinclude, $header, $theme, $footer, $templates, $lang, $user;

	if(!isset($mybb->input['series'])) header("Location: ".$mybb->settings['bburl']);
	if(!doesSeriesExist($mybb->input['series'])) header("Location: ".$mybb->settings['bburl']);

	$seriesInfo = getSeriesInfo($mybb->input['series']);
	$chapters = getChapters($mybb->input['series']);

	$rate = explode('.',$seriesInfo['rate']['rating']);
	$rating_p1 = $rate[0];
	$rating_p2 = $rate[1];

	$comments = getComments($mybb->input['series']);

	$parser = new postParser(); 
	$parser_options = array(
	    'allow_html' => 'no',
	    'allow_mycode' => 'yes',
	    'allow_smilies' => 'yes',
	    'allow_imgcode' => 'yes',
	    'filter_badwords' => 'yes',
	    'nl2br' => 'no'
	);

	function printComments(&$putWhere, array $comments, $level = 0) {
		global $parser,$parser_options;

		$i = '1';

		foreach ($comments as $info) {
			$comment = $parser->parse_message($info['comment'],$parser_options);
			$putWhere .= '
				<div name="post_'.$i.'">
					<tr style="margin:0px;padding:0px;">
						<td style="margin:0px;padding:0px;">
							<span>'.htmlentities($info['author']).'</span>&nbsp;<span style="color:rgba(0,0,0,0.5);">('.$info['time'].')</span>
						</td>
						<td style="text-align:right;margin:0px;padding:0px;">
							<a id="replyto_'.$info['id'].'" style="cursor:pointer;" onclick="jQuery(\'#message\').val(jQuery(\'#message\').val()+\'\r\n\'+jQuery(\'#comment_'.$i.'\').html()+\'\r\n\')">Reply</a>
						</td>
					</tr>
					<tr style="margin:0px;padding:0px;width:100%;">
						<td colspan="2">
							<div style="margin-left:10px;">'.$comment.'</div>
							<div style="display:none" id="comment_'.$i.'">[quote='.$info['author'].']'.$info['comment'].'[/quote]</div>
						</td>
					</tr>
				</div>';

			if (!empty($info['children'])) {
				printComments($putWhere,$info['children'], $level + 1);
			}
		}
	}

$template = '<html>
<head>
<title>MangaMags - Home</title>
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
'.$header.'
			<table border="0" cellspacing="2" cellpadding="10" class="tborder" style="width:100%;">
				<thead>
					<tr>
						<td colspan="2" class="thead">
							<div class="expcolimage">
								<img class="expander" title="[-]" alt="[-]" src="'.$mybb->settings['bburl'].'/images/tranquil/collapse.gif"></img>
							</div>
							<div>
								<strong><a>Chapters</a></strong>
							</div>
						</td>
					</tr>
				</thead>
				<tbody>
				';
				
				for($i = 0;$i < count($chapters);++$i) {
					$template .= '
					<tr>
						<td style="width:350px;padding:0px;"
							<span><a href="'.$mybb->settings['bburl'].'/reader.php?series='.$seriesInfo['name'].'&chapter='.$i.'&page=1">Chapter '.$i.($chapters[$i]['name'] == '' ? NULL : ' : '.$chapters[$i]['name']).'</a></span>
						</td>
						<td style="padding:0px;">
							<span><script>document.write(moment("'.$chapters[$i]['upped'].'", "YYYY-MM-DD HH:mm:ss").fromNow());</script></span>
						</td>
					</tr>
					';
				}

				$template .= '
				</tbody>
			</table>
			<table border="0" cellspacing="2" cellpadding="10" class="tborder" style="width:100%;">
				<thead>
					<tr>
						<td colspan="2" class="thead">
							<div class="expcolimage">
								<img class="expander" title="[-]" alt="[-]" src="'.$mybb->settings['bburl'].'/images/tranquil/collapse.gif"></img>
							</div>
							<div>
								<strong><a>Comments</a></strong>
							</div>
						</td>
					</tr>
				</thead>
				<tbody>
				';
				
				printComments($template,$comments);

					$template .= '
				</tbody>
			</table>
			<table class="tborder" border="0" cellpadding="10" cellspacing="2">
			<thead>
				<tr>
					<td class="thead" colspan="2">
						<div class="expcolimage"><img style="cursor: pointer;" src="images/tranquil/collapse.gif" id="quickreply_img" class="expander" alt="[-]" title="[-]"></div>
						<div><strong>Leave A Comment</strong></div>
					</td>
				</tr>
			</thead>
			<tbody style="" id="quickreply_e">
				<tr>
					<td class="trow1" valign="top" width="22%">
						<strong>Message</strong><br>
						<span class="smalltext">Type your reply to this message here.<br><br>
						<input name="postoptions[signature]" value="0" type="hidden" />
						<label><input class="checkbox" name="postoptions[disablesmilies]" value="1" type="checkbox">&nbsp;<strong>Disable Smilies</strong></label><br />
					</td>
					<td class="trow1">
						<div style="width: 95%">
							<textarea style="width: 100%; padding: 4px; margin: 0;" rows="8" cols="80" name="message" id="message" tabindex="1"></textarea>
						</div>
						<div class="editor_control_bar" style="width: 95%; padding: 4px; margin-top: 3px; display: none;" id="quickreply_multiquote">
							<span class="smalltext">
								You have selected one or more posts to quote. <a href="./newreply.php?tid=1&amp;load_all_quotes=1" onclick="return Thread.loadMultiQuoted();">Quote these posts now</a> or <a href="javascript:Thread.clearMultiQuoted();">deselect them</a>.
							</span>
						</div>
					</td>
				</tr>
				
				<tr>
					<td colspan="2" class="tfoot" align="center"><input class="button" value="Post Reply" tabindex="2" accesskey="s" id="quick_reply_submit" type="submit"> <input class="button" name="previewpost" value="Preview Post" tabindex="3" type="submit"></td>
				</tr>
			</tbody>
		</table>
		'.$footer.'
	</body>
</html>';

$template = str_replace("\'", "'", addslashes($template));

add_breadcrumb("Mangas",$mybb->settings['bburl']);
add_breadcrumb($seriesInfo['name']);

eval("\$page = \"" . $template . "\";");

output_page($page);

?>