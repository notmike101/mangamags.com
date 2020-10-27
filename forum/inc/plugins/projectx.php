<?php
/**
 * ProjectX beta 0.1.5
 * 
 * One, or even *the* best Admin Control Panel themes out there for MyBB installations.
 *
 * @package ProjectX beta 0.1.5
 * @author  Shade <legend_k@live.it>
 * @license http://opensource.org/licenses/mit-license.php MIT license
 * @version beta 0.1.5
 */

if (!defined('IN_MYBB')) {
    die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.');
}

if (!defined("PLUGINLIBRARY")) {
    define("PLUGINLIBRARY", MYBB_ROOT . "inc/plugins/pluginlibrary.php");
}

function projectx_info()
{
    return array(
        'name' => 'ProjectX theme',
        'description' => 'One, or even <b>the</b> best Admin Control Panel themes out there for MyBB installations.',
        'website' => 'http://idevicelab.net',
        'author' => 'Shade',
        'authorsite' => 'http://idevicelab.net',
        'version' => 'beta 0.1.5',
        'compatibility' => '16*',
        'guid' => '75de0ff376b2772bb900874f41ce176d'
    );
}

function projectx_is_installed()
{
    global $cache;
    
    $installed = $cache->read("shade_plugins");
    // check if our setting is there
    if ($installed['ProjectX']) {
        return true;
    }
}

function projectx_install()
{
    global $db, $PL, $lang, $mybb, $cache;
    
    if (!file_exists(PLUGINLIBRARY)) {
        flash_message("The selected theme could not be installed because <a href=\"http://mods.mybb.com/view/pluginlibrary\">PluginLibrary</a> is missing.", "error");
        admin_redirect("index.php?module=config-plugins");
    }
    
    $PL or require_once PLUGINLIBRARY;
    
    if (!$lang->projectx) {
        $lang->load('projectx');
    }
    
    // simple cache creation - useful for updates
    $info                      = projectx_info();
    $shade_plugins             = $cache->read('shade_plugins');
    $shade_plugins['ProjectX'] = array(
        'title' => 'ProjectX',
        'version' => $info['version']
    );
    $cache->update('shade_plugins', $shade_plugins);
    
    // rebuild settings
    rebuild_settings();
    
}

function projectx_uninstall()
{
    global $db, $PL, $cache;
    
    if (!file_exists(PLUGINLIBRARY)) {
        flash_message("The selected theme could not be uninstalled because <a href=\"http://mods.mybb.com/view/pluginlibrary\">PluginLibrary</a> is missing.", "error");
        admin_redirect("index.php?module=config-plugins");
    }
    
    $PL or require_once PLUGINLIBRARY;
    
    
    // delete the plugin from cache
    $shade_plugins = $cache->read('shade_plugins');
    unset($shade_plugins['ProjectX']);
    $cache->update('shade_plugins', $shade_plugins);
}

function projectx_activate()
{
    global $PL, $lang;
    
    if (!$lang->projectx) {
        $lang->load('projectx');
    }
    
    if (!file_exists(PLUGINLIBRARY)) {
        flash_message($lang->projectx_pluginlibrary_missing, "error");
        admin_redirect("index.php?module=config-plugins");
    }
    
    $PL or require_once PLUGINLIBRARY;
    
    $PL->settings('projectx', $lang->projectx_settings, $lang->projectx_settings_desc, array(
        'enabled' => array(
            'title' => $lang->projectx_settings_enable,
            'description' => $lang->projectx_settings_enable_desc,
            'value' => '1'
        ),
        'text_before' => array(
            'title' => $lang->projectx_settings_before,
            'description' => $lang->projectx_settings_before_desc,
            'value' => '',
            'optionscode' => 'text'
        ),
        'text_after' => array(
            'title' => $lang->projectx_settings_after,
            'description' => $lang->projectx_settings_after_desc,
            'value' => '',
            'optionscode' => 'text'
        )
    ));
    
    projectx_true_install();
}

function projectx_deactivate()
{
    global $PL, $lang;
    
    if (!$lang->projectx) {
        $lang->load('projectx');
    }
    
    if (!file_exists(PLUGINLIBRARY)) {
        flash_message($lang->projectx_pluginlibrary_missing, "error");
        admin_redirect("index.php?module=config-plugins");
    }
    
    $PL or require_once PLUGINLIBRARY;
    
    $PL->settings_delete('projectx');
    
    projectx_true_uninstall();
}

global $settings, $mybb;

// only trigger our true install/uninstall functions if the input is coming from our setting group and ProjectX is enabled
if ($mybb->input['upsetting']['projectx_enabled'] == "1") {
    projectx_true_install();
} elseif ($mybb->input['upsetting']['projectx_enabled'] == "0") {
    projectx_true_uninstall();
}

function projectx_true_install()
{
    global $PL, $lang, $mybb, $db;
    if (!$lang->projectx) {
        $lang->load('projectx');
    }
    
    if (!file_exists(PLUGINLIBRARY)) {
        flash_message($lang->projectx_pluginlibrary_missing, "error");
        admin_redirect("index.php?module=config-plugins");
    }
    
    $PL or require_once PLUGINLIBRARY;
    
    $PL->edit_core('ProjectX', 'admin/jscripts/tabs.js', array(
        // needed for submenu
        array(
            'search' => 'setClassOnContainer: false,',
            'replace' => 'setClassOnContainer: true,'
        )
    ), true);
    
    $PL->edit_core('ProjectX', 'admin/modules/home/module_meta.php', array(
        // At the current stages ProjectX isn't capable of displaying correctly online admins. So, just hide variables
        array(
            'search' => '$sidebar = new SidebarItem($lang->online_admins);
		$sidebar->set_contents($online_users);

		$page->sidebar .= $sidebar->get_markup();',
            'replace' => ''
        )
    ), true);
    
    $PL->edit_core('ProjectX', 'admin/modules/config/module_meta.php', array(
        // Split the menu into two rows - hide present elements
        array(
            'search' => '$sub_menu[\'110\'] = array("id" => "attachment_types", "title" => $lang->attachment_types, "link" => "index.php?module=config-attachment_types");
	$sub_menu[\'120\'] = array("id" => "mod_tools", "title" => $lang->moderator_tools, "link" => "index.php?module=config-mod_tools");
	$sub_menu[\'130\'] = array("id" => "spiders", "title" => $lang->spiders_bots, "link" => "index.php?module=config-spiders");
	$sub_menu[\'140\'] = array("id" => "calendars", "title" => $lang->calendars, "link" => "index.php?module=config-calendars");
	$sub_menu[\'150\'] = array("id" => "warning", "title" => $lang->warning_system, "link" => "index.php?module=config-warning");
	$sub_menu[\'160\'] = array("id" => "thread_prefixes", "title" => $lang->thread_prefixes, "link" => "index.php?module=config-thread_prefixes");
	
	$sub_menu = $plugins->run_hooks("admin_config_menu", $sub_menu);',
            'replace' => '$sub_menu = $plugins->run_hooks("admin_config_menu_sup", $sub_menu);'
        ),
        // Split the menu into two rows - make them appear again!
        array(
            'search' => '$actions = $plugins->run_hooks("admin_config_action_handler", $actions);',
            'after' => '$sub_menu = array();
	$sub_menu[\'10\'] = array("id" => "attachment_types", "title" => $lang->attachment_types, "link" => "index.php?module=config-attachment_types");
	$sub_menu[\'20\'] = array("id" => "mod_tools", "title" => $lang->moderator_tools, "link" => "index.php?module=config-mod_tools");
	$sub_menu[\'30\'] = array("id" => "spiders", "title" => $lang->spiders_bots, "link" => "index.php?module=config-spiders");
	$sub_menu[\'40\'] = array("id" => "calendars", "title" => $lang->calendars, "link" => "index.php?module=config-calendars");
	$sub_menu[\'50\'] = array("id" => "warning", "title" => $lang->warning_system, "link" => "index.php?module=config-warning");
	$sub_menu[\'60\'] = array("id" => "thread_prefixes", "title" => $lang->thread_prefixes, "link" => "index.php?module=config-thread_prefixes");
	
	$sub_menu = $plugins->run_hooks("admin_config_menu", $sub_menu);
	
	$sidebar = new SidebarItem($lang->config);
	$sidebar->add_menu_items($sub_menu, $actions[$action][\'active\']);
	
	$page->sidebar .= $sidebar->get_markup();'
        )
    ), true);
    
    $db->update_query('settings', array(
        'value' => $db->escape_string("ProjectX")
    ), "name = 'cpstyle'");
    $db->update_query('adminoptions', array(
        'cpstyle' => $db->escape_string("ProjectX")
    ));
}

function projectx_true_uninstall()
{
    global $PL, $lang, $db;
    
    if (!$lang->projectx) {
        $lang->load('projectx');
    }
    
    if (!file_exists(PLUGINLIBRARY)) {
        flash_message($lang->projectx_pluginlibrary_missing, "error");
        admin_redirect("index.php?module=config-plugins");
    }
    
    $PL or require_once PLUGINLIBRARY;
    
    $PL->edit_core('ProjectX', 'admin/jscripts/tabs.js', array(), true);
    
    $PL->edit_core('ProjectX', 'admin/modules/home/module_meta.php', array(), true);
    
    $PL->edit_core('ProjectX', 'admin/modules/config/module_meta.php', array(), true);
    
    $db->update_query('settings', array(
        'value' => $db->escape_string("default")
    ), "name = 'cpstyle'");
    $db->update_query('adminoptions', array(
        'cpstyle' => $db->escape_string("default")
    ));
    
}