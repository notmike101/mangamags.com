<?php
/*
	Default Profile Plugin
	
	Gives new users who register a default avatar, signature, profile.
	For MyBB 1.6.x.
	
	Copyright © 2006 - 2011 Dennis Tsang (http://dennistt.net)and Conor Calby (http://shockinghost.com)
	
	==========
	LICENSE
	==========
	This plugin by Dennis Tsang and updated by Conor Calby is licensed under the
	Creative Commons Attribution-No Derivative Works 2.5 Canada License
	
	Summary: http://creativecommons.org/licenses/by-nd/2.5/ca/
	Legal Code: http://creativecommons.org/licenses/by-nd/2.5/ca/legalcode.en
	
	A summary follows for convenience:
	
	You are free:
		To use the software
		To share -- to copy, distribute and transmit the work as the original whole

	Under the following conditions:
		Attribution.  You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).  All author and copyright notices must be left intact at ALL times while using the software, and in any redistributed package.
		No Derivative Works.  You may not release altered, transformed, or otherwise modified versions of this work.  However, you may modify the software for use on your forum only.  Translations must be done using external .lang.php files.
		
	- For any reuse or distribution, you must make clear to others the licence terms of this work.
	- The author's moral rights are retained in this licence.

*/

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

function defaultprofile_info()
{
	global $lang;
	defaultprofile_load_language();
	
	return array(
		"name"			=> $lang->defaultprofile,
		"description"	=> $lang->defaultprofile_desc,
		"website"		=> "http://www.dennistt.net",
		"author"		=> "DennisTT & Conor Calby",
		"authorsite"	=> "http://www.mybb.com",
		"version"		=> "2.0",
		"guid"			  => "94c45014770d938ae5217c534bb9417f",
		"compatibility"   => "16*",
		
		// DennisTT custom info
		"codename" => 'defaultprofile',
	);
}

// Helper function to load the language variables
function defaultprofile_load_language()
{
	global $lang;
	if(!defined('DENNISTT_DEFAULTPROFILE_LANG_LOADED'))
	{
		$lang->load('defaultprofile', false, true);
		
		if(!isset($lang->defaultprofile))
		{
			$lang->defaultprofile = 'Default Profile';
			$lang->defaultprofile_desc = 'Gives new users who register a default avatar, signature, and profile options. For MyBB 1.6.x.';
			
		}
		
		define('DENNISTT_DEFAULTPROFILE_LANG_LOADED', 1);
	}
}

function defaultprofile_activate()
{
	global $db;
	$info = defaultprofile_info();
	
	// Remove old plugin stuff
	defaultprofile_deactivate();

	$setting_group_array = array(
		'name' => str_replace(' ', '_', 'dennistt_'.strtolower($info['codename'])),
		'title' => "$info[name]",
		'description' => "Settings for the $info[name] plugin",
		'disporder' => 1,
		'isdefault' => '0',
		);
	$db->insert_query('settinggroups', $setting_group_array);
	$group = $db->insert_id();

	$settings = array(
		'defaultprofile_avatar' => array('Avatar URL', 'The URL to the default avatar (eg. images/avatars/php.gif or http://anothersite.com/someimage.gif)', 'text', './images/avatars/php.gif'),
		'defaultprofile_signature' => array('Signature', 'This will be the user\'s default signature.<br /><br />The following codes will be replaced:<br />{username} - username<br />{regdate} - Registration date<br />{bbname} - Board name', 'textarea', '{username}, proud to be a member of {bbname} since {regdate}.'),
		'defaultprofile_additionalgroups' => array('Additional Usergroups', 'Any additional group IDs the user should be part of.  (Comma separated values)', 'text', ''),
		'defaultprofile_website' => array('Website URL', 'Default profile website URL', 'text', ''),
		'defaultprofile_showsigs' => array('Show Signatures', 'Show signatures in posts?', 'yesno', '1'),
		'defaultprofile_showavatars' => array('Show Avatars', 'Show avatars in posts?', 'yesno', '1'),
		'defaultprofile_showquickreply' => array('Show Quick Reply', 'Show quick reply in threads?', 'yesno', '0'),
		'defaultprofile_showredirect' => array('Show Redirect', 'Show friendly redirects?', 'yesno', '1'),
		'defaultprofile_tpp' => array('Threads Per Page', 'Number of threads shown per page.  Enter zero \'0\' for board setting.', 'text', '0'),
		'defaultprofile_ppp' => array('Posts Per Page', 'Number of posts shown per page.  Enter zero \'0\' for board setting.', 'text', '0'),
		'defaultprofile_style' => array('Theme Selection', 'Theme ID for new users to use.  Enter zero \'0\' for board default.', 'text', '0'),
		'defaultprofile_threadmode' => array('Threaded View Mode?', 'Select \'yes\' to use threaded viewing mode.  Select \'no\' to use linear viewing mode.', 'yesno', '0'),
		'defaultprofile_daysprune' => array('Thread Listing Limit', 'Show threads from X days and after.  Enter zero \'0\' for board setting. Enter \'all\' to show all threads regardless of date', 'text', 'all'),
		'defaultprofile_notepad' => array('User Notepad', 'Notepad content', 'textarea', ''),
		'defaultprofile_classicpostbit' => array('Use Classic Postbit?', 'The classic postbit is the vertical postbit', 'yesno', '0'),
		);

	$i = 1;
	foreach($settings as $name => $sinfo)
	{
		$insert_array = array(
			'name' => $name,
			'title' => $db->escape_string($sinfo[0]),
			'description' => $db->escape_string($sinfo[1]),
			'optionscode' => $db->escape_string($sinfo[2]),
			'value' => $db->escape_string($sinfo[3]),
			'gid' => $group,
			'disporder' => $i,
			'isdefault' => 0,
			);
		$db->insert_query('settings', $insert_array);
		$i++;
	}
	rebuild_settings();
}

function defaultprofile_deactivate()
{
	global $db;
	$info = defaultprofile_info();
	$result = $db->simple_select('settinggroups', 'gid', "name = '".str_replace(' ', '_', 'dennistt_'.strtolower($info['codename']))."'", array('limit' => 1));
	$group = $db->fetch_array($result);
	
	if(!empty($group['gid']))
	{
		$db->delete_query('settinggroups', "gid='{$group['gid']}'");
		$db->delete_query('settings', "gid='{$group['gid']}'");
		rebuild_settings();
	}
}

$plugins->add_hook("member_do_register_start", "defaultprofile_register_start");
function defaultprofile_register_start()
{
	define('DEFAULTPROFILE_REGISTERING', true);
}

$plugins->add_hook("datahandler_user_insert", "defaultprofile_insert");
function defaultprofile_insert($user)
{
	global $db, $mybb;
	if(defined('DEFAULTPROFILE_REGISTERING'))
	{
		if(!empty($mybb->settings['defaultprofile_avatar']))
		{
			$user->user_insert_data['avatar'] = $db->escape_string($mybb->settings['defaultprofile_avatar']);
			$user->user_insert_data['avatartype'] = 'remote';
		}
		if(!empty($mybb->settings['defaultprofile_signature']))
		{
			$default_signature = $mybb->settings['defaultprofile_signature'];
			$default_signature = str_replace("{username}", $user->user_insert_data['username'], $default_signature);
			$default_signature = str_replace("{regdate}", date($mybb->settings['regdateformat'], time()), $default_signature);
			$default_signature = str_replace("{bbname}", $mybb->settings['bbname'], $default_signature);
			$user->user_insert_data['signature'] = $db->escape_string($default_signature);
		}
		if(!empty($mybb->settings['defaultprofile_additionalgroups']))
		{
			$user->user_insert_data['additionalgroups'] = $db->escape_string($mybb->settings['defaultprofile_additionalgroups']);
		}
		if(!empty($mybb->settings['defaultprofile_website']))
		{
			$user->user_insert_data['website'] = $db->escape_string($mybb->settings['defaultprofile_website']);
		}
		if($mybb->settings['defaultprofile_showsigs'] == 1)
		{
			$user->user_insert_data['showsigs'] = 1;
		}
		else
		{
			$user->user_insert_data['showsigs'] = 0;
		}
		if($mybb->settings['defaultprofile_showavatars'] == 1)
		{
			$user->user_insert_data['showavatars'] = 1;
		}
		else
		{
			$user->user_insert_data['showavatars'] = 0;
		}
		if($mybb->settings['defaultprofile_showquickreply'] == 1)
		{
			$user->user_insert_data['showquickreply'] = 1;
		}
		else
		{
			$user->user_insert_data['showquickreply'] = 0;
		}
		if($mybb->settings['defaultprofile_showredirect'] == 1)
		{
			$user->user_insert_data['showredirect'] = 1;
		}
		else
		{
			$user->user_insert_data['showredirect'] = 0;
		}
		if(!empty($mybb->settings['defaultprofile_tpp']))
		{
			$user->user_insert_data['tpp'] = intval($mybb->settings['defaultprofile_tpp']);
		}
		if(!empty($mybb->settings['defaultprofile_ppp']))
		{
			$user->user_insert_data['ppp'] = intval($mybb->settings['defaultprofile_ppp']);
		}
		if(!empty($mybb->settings['defaultprofile_style']))
		{
			$user->user_insert_data['style'] = intval($mybb->settings['defaultprofile_style']);
		}
		if($mybb->settings['defaultprofile_threadmode'] == 'yes')
		{
			$user->user_insert_data['threadmode'] = 'threaded';
		}
		else
		{
			$user->user_insert_data['threadmode'] = 'linear';
		}
		if(!empty($mybb->settings['defaultprofile_daysprune']))
		{
			$user->user_insert_data['daysprune'] = intval($mybb->settings['defaultprofile_daysprune']);
		}
		if(!empty($mybb->settings['defaultprofile_notepad']))
		{
			$user->user_insert_data['notepad'] = $db->escape_string($mybb->settings['defaultprofile_notepad']);
		}
		if($mybb->settings['defaultprofile_classicpostbit'] == 1)
		{
			$user->user_insert_data['classicpostbit'] = 1;
		}
		else
		{
			$user->user_insert_data['classicpostbit'] = 0;
		}
	}
}
?>