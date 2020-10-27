<?php
/**
 * This is an example style file for Admin CP styles.
 *
 * It allows you to override our existing layout generation
 * classes with your own to further customise the Admin CP
 * layout beyond CSS.
 *
 * Your class name      Should extend
 * ---------------      -------------
 * Page                 DefaultPage
 * SidebarItem          DefaultSidebarItem
 * PopupMenu            DefaultPopupMenu
 * Table                DefaultTable
 * Form                 DefaultForm
 * FormContainer        DefaultFormContainer
 *
 * For example, to output your own custom header:
 *
 * class Page extends DefaultPage
 * {
 *   function output_header($title)
 *   {
 *      echo "<h1>{$title}</h1>";
 *   }
 * }
 *
 */

// Disallow direct access to this file for security reasons
if (!defined("IN_MYBB")) {
    die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

class Page extends DefaultPage
{
    function output_header($title = "")
    {
        global $mybb, $admin_session, $lang, $plugins;
        
        $plugins->run_hooks("admin_page_output_header");
        
        if (!$title) {
            $title = $lang->mybb_admin_panel;
        }
        
        $rtl = "";
        if ($lang->settings['rtl'] == 1) {
            $rtl = " dir=\"rtl\"";
        }
        
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
        echo "<html xmlns=\"http://www.w3.org/1999/xhtml\"{$rtl}>\n";
        echo "<head profile=\"http://gmpg.org/xfn/1\">\n";
        echo "	<title>" . $title . "</title>\n";
        echo "	<meta name=\"author\" content=\"MyBB Group\" />\n";
        echo "	<meta name=\"copyright\" content=\"Copyright " . COPY_YEAR . " MyBB Group.\" />\n";
        echo "	<link rel=\"stylesheet\" href=\"styles/" . $this->style . "/main.css\" type=\"text/css\" />\n";
        echo "	<link href=\"http://fonts.googleapis.com/css?family=Open+Sans:300|Oswald\" rel=\"stylesheet\" type=\"text/css\">\n";
        
        // Load stylesheet for this module if it has one
        if (file_exists(MYBB_ADMIN_DIR . "styles/{$this->style}/{$this->active_module}.css")) {
            echo "	<link rel=\"stylesheet\" href=\"styles/{$this->style}/{$this->active_module}.css\" type=\"text/css\" />\n";
        }
        
        echo "	<script type=\"text/javascript\" src=\"../jscripts/prototype.js\"></script>\n";
        echo "	<script type=\"text/javascript\" src=\"../jscripts/general.js\"></script>\n";
        echo "	<script type=\"text/javascript\" src=\"../jscripts/effects.js\"></script>\n";
        echo "	<script type=\"text/javascript\" src=\"./jscripts/projectx_popup_menu.js\"></script>\n";
        echo "	<script type=\"text/javascript\" src=\"./jscripts/admincp.js\"></script>\n";
        echo "	<script type=\"text/javascript\" src=\"./jscripts/tabs.js\"></script>\n";
        echo "	<script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js\"></script>\n";
        echo "	<script type=\"text/javascript\" src=\"./jscripts/jquerytools.pack.js\"></script>\n";
        echo "	<script type=\"text/javascript\" src=\"./jscripts/projectx_common.js\"></script>\n";
        
        // Stop JS elements showing while page is loading (JS supported browsers only)
        echo "  <style type=\"text/css\">.popup_button { display: none; } </style>\n";
        echo "  <script type=\"text/javascript\">\n" . "//<![CDATA[\n" . "	document.write('<style type=\"text/css\">.popup_button { display: inline; }<\/style>');\n" . "//]]>\n" . "</script>\n";
        
        echo "	<script type=\"text/javascript\">
//<![CDATA[
var loading_text = '{$lang->loading_text}';
var cookieDomain = '{$mybb->settings['cookiedomain']}';
var cookiePath = '{$mybb->settings['cookiepath']}';
var cookiePrefix = '{$mybb->settings['cookieprefix']}';
var imagepath = '../images';
//]]>
</script>\n";
        echo $this->extra_header;
        echo "</head>\n";
        echo "<body>\n";
        echo "<div id=\"container\">\n";
        echo $this->_build_menu();
        echo "	<div id=\"welcome\"><span class=\"logged_in_as\">{$lang->logged_in_as} <a href=\"index.php?module=user-users&amp;action=edit&amp;uid={$mybb->user['uid']}\" class=\"username\">{$mybb->user['username']}</a></span> | <a href=\"{$mybb->settings['bburl']}\" target=\"_blank\" class=\"forum\">{$lang->view_board}</a> | <a href=\"index.php?action=logout&amp;my_post_key={$mybb->post_code}\" class=\"logout\">{$lang->logout}</a></div>\n";
        echo "	<div id=\"page\">\n";
        echo "		<div id=\"content\">\n";
        echo "			<div id=\"navigation\">\n";
        echo "			<ol id=\"breadcrumb\" class=\"ipsList_inline breadcrumb\">\n";
        echo $this->_generate_breadcrumb();
        echo "			</ol>\n";
        echo "			</div>\n";
        echo "           <div id=\"inner\">\n";
        echo "		<div id=\"left_menu\">\n";
        echo $this->submenu;
        echo $this->sidebar;
        echo "		</div>\n";
        if (isset($admin_session['data']['flash_message']) && $admin_session['data']['flash_message']) {
            $message = $admin_session['data']['flash_message']['message'];
            $type    = $admin_session['data']['flash_message']['type'];
            echo "<div id=\"flash_message\" class=\"{$type}\">\n";
            echo "{$message}\n";
            echo "</div>\n";
            update_admin_session('flash_message', '');
        }
        if ($this->show_post_verify_error == true) {
            $this->output_error($lang->invalid_post_verify_key);
        }
    }
    
    function output_footer($quit = true)
    {
        global $mybb, $maintimer, $db, $lang, $plugins;
        
        $plugins->run_hooks("admin_page_output_footer");
        
        $memory_usage = $lang->na;
        if (function_exists("memory_get_usage")) {
            $memory_usage = get_friendly_size(memory_get_peak_usage(true));
        }
        
        $totaltime  = $maintimer->stop();
        $querycount = $db->query_count;
        echo "			</div>\n";
        echo "		</div>\n";
        echo "	<br style=\"clear: both;\" />";
        echo "	<br style=\"clear: both;\" />";
        echo "	</div>\n";
        echo "<div id=\"footer\"><p class=\"generation\">" . $lang->sprintf($lang->generated_in, $totaltime, $querycount, $memory_usage) . "</p><p class=\"powered\">Powered By MyBB. &copy; " . COPY_YEAR . " MyBB Group. All Rights Reserved.</p><div class=\"footer\"><p></p><p>Designed on a Mac</p></div></div>\n";
        if ($mybb->debug_mode) {
            echo $db->explain;
        }
        echo "</div>\n";
        echo "</body>\n";
        echo "</html>\n";
        
        if ($quit != false) {
            exit;
        }
    }
    
    function _generate_breadcrumb()
    {
        if (!is_array($this->_breadcrumb_trail)) {
            return false;
        }
        $trail = "";
        foreach ($this->_breadcrumb_trail as $key => $crumb) {
            if ($this->_breadcrumb_trail[$key + 1]) {
                $trail .= "<li><a href=\"" . $crumb['url'] . "\"><span>" . $crumb['name'] . "</span></a></li>";
            } else {
                $trail .= "<li class=\"active\"><a href=\"" . $crumb['url'] . "\"><span>" . $crumb['name'] . "</span></a></li>";
            }
        }
        return $trail;
    }
    
    function show_login($message = "", $class = "success")
    {
        global $lang, $cp_style, $mybb;
        
        $copy_year = COPY_YEAR;
        
        $login_container_width = "";
        $login_label_width     = "";
        
        // If the language string for "Username" is too cramped then use this to define how much larger you want the gap to be (in px)
        if ($lang->login_field_width) {
            $login_label_width     = " style=\"width: " . (intval($lang->login_field_width) + 100) . "px;\"";
            $login_container_width = " style=\"width: " . (410 + (intval($lang->login_field_width))) . "px;\"";
        }
        
        print <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head profile="http://gmpg.org/xfn/1">
<title>{$lang->mybb_admin_login}</title>
<meta name="author" content="MyBB Group" />
<meta name="copyright" content="Copyright {$copy_year} MyBB Group." />
<link rel="stylesheet" href="./styles/{$cp_style}/login.css" type="text/css" />
<link href="http://fonts.googleapis.com/css?family=Open+Sans:300|Oswald" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../jscripts/prototype.js"></script>
<script type="text/javascript" src="../jscripts/general.js"></script>
<script type="text/javascript" src="./jscripts/admincp.js"></script>
<script type="text/javascript">
//<![CDATA[
	loading_text = '{$lang->loading_text}';
//]]>
</script>
</head>
<body>
<div id="container"{$login_container_width}>
	<div id="header">
		<div id="logo">
			<h1><a href="../" title="{$lang->return_to_forum}"></a></h1>
            <h2>{$lang->mybb_acp}</h2>
		</div>
	</div>
	<div id="content">
		<h2>{$lang->please_login}</h2>
EOF;
        if ($message) {
            echo "<p id=\"message\" class=\"{$class}\"><span class=\"text\">{$message}</span></p>";
        }
        // Make query string nice and pretty so that user can go to his/her preferred destination
        $query_string = '';
        if ($_SERVER['QUERY_STRING']) {
            $query_string = '?' . preg_replace('#adminsid=(.{32})#i', '', $_SERVER['QUERY_STRING']);
            $query_string = preg_replace('#my_post_key=(.{32})#i', '', $query_string);
            $query_string = str_replace('action=logout', '', $query_string);
            $query_string = preg_replace('#&+#', '&', $query_string);
            $query_string = str_replace('?&', '?', $query_string);
            $query_string = htmlspecialchars_uni($query_string);
        }
        switch ($mybb->settings['username_method']) {
            case 0:
                $lang_username = $lang->username;
                break;
            case 1:
                $lang_username = $lang->username1;
                break;
            case 2:
                $lang_username = $lang->username2;
                break;
            default:
                $lang_username = $lang->username;
                break;
        }
        
        // TODO: Better Fix?
        $_SERVER['PHP_SELF'] = htmlspecialchars_uni($_SERVER['PHP_SELF']);
        print <<<EOF
		<p>{$lang->enter_username_and_password}</p>
		<form method="post" action="{$_SERVER['PHP_SELF']}{$query_string}">
		<div class="form_container">

			<div class="label"{$login_label_width}><label for="username">{$lang_username}</label></div>

			<div class="field"><input type="text" name="username" id="username" class="text_input initial_focus" /></div>

			<div class="label"{$login_label_width}><label for="password">{$lang->password}</label>
            <span class="forgot_password">
				<a href="../member.php?action=lostpw">{$lang->lost_password}</a>
			</span></div>
			<div class="field"><input type="password" name="password" id="password" class="text_input" /></div>
		</div>
		<p class="submit">

			<input type="submit" value="{$lang->login}" />
			<input type="hidden" name="do" value="login" />
		</p>
		</form>
	</div>
    <div class="footer"><p>MyBB Admin Control Panel</p><p>Powered by Project<b>X</b> ACP Theme ¬©</div>
</div>
</body>
</html>
EOF;
        exit;
    }
    
    function _build_menu()
    {
        global $lang, $settings;
        if (!$lang->projectx) {
            $lang->load("projectx");
        }
        
        // custom header :)
        if ($settings['projectx_text_before'] OR $settings['projectx_text_after']) {
            $lang->projectx_before = $settings['projectx_text_before'];
            $lang->projectx_after  = $settings['projectx_text_after'];
        }
        if (!is_array($this->_menu)) {
            return false;
        }
        $build_menu = "<div id=\"menusup\">\n<div id=\"logo\">" . $lang->projectx_before . "<span>" . $lang->projectx_after . "</span></div>\n<ul>\n";
        ksort($this->_menu);
        foreach ($this->_menu as $items) {
            foreach ($items as $menu_item) {
                $menu_item['link'] = htmlspecialchars($menu_item['link']);
                if ($menu_item['id'] == $this->active_module) {
                    $sub_menu       = $menu_item['submenu'];
                    $sub_menu_title = $menu_item['title'];
                    $build_menu .= "<li class=\"active\"><a href=\"{$menu_item['link']}\">{$menu_item['title']}</a></li>\n";
                    
                } else {
                    $build_menu .= "<li><a href=\"{$menu_item['link']}\">{$menu_item['title']}</a></li>\n";
                }
            }
        }
        $build_menu .= "</ul>\n</div>";
        
        if ($sub_menu) {
            $this->_build_submenu($sub_menu_title, $sub_menu);
        }
        return $build_menu;
    }
    
    function output_tab_control($tabs = array(), $observe_onload = true, $id = "tabs")
    {
        global $plugins;
        $tabs = $plugins->run_hooks("admin_page_output_tab_control_start", $tabs);
        echo "<script type=\"text/javascript\">\n";
        if ($observe_onload) {
            echo "Event.observe(window,'load',function(){\n";
        }
        echo "	\$\$('#{$id}').each(function(tabs)\n";
        echo "	{\n";
        echo "		new Control.Tabs(tabs);\n";
        echo "	});\n";
        if ($observe_onload) {
            echo "});\n";
        }
        echo "</script>\n";
        echo "<div id=\"tabs-wrapper\"><ul class=\"tabs\" id=\"{$id}\">\n";
        $tab_count = count($tabs);
        $done      = 1;
        foreach ($tabs as $anchor => $title) {
            $class = "";
            if ($tab_count == $done) {
                $class .= " last";
            }
            if ($done == 1) {
                $class .= " first";
            }
            ++$done;
            echo "<li class=\"{$class}\"><a href=\"#tab_{$anchor}\">{$title}</a></li>\n";
        }
        echo "</ul></div>\n";
        $plugins->run_hooks("admin_page_output_tab_control_end", $tabs);
    }
    
}

class SidebarItem extends DefaultSidebarItem
{
    function add_menu_items($items, $active)
    {
        global $run_module;
        
        $this->_contents = "<ul class=\"menu\">";
        foreach ($items as $item) {
            if (!check_admin_permissions(array(
                "module" => $run_module,
                "action" => $item['id']
            ), false)) {
                continue;
            }
            
            $class = "";
            if ($item['id'] == $active) {
                $class = " class=\"active\"";
            }
            $item['link'] = htmlspecialchars($item['link']);
            $this->_contents .= "<li{$class}><a href=\"{$item['link']}\" title=\"{$item['title']}\"></a></li>\n";
        }
        $this->_contents .= "</ul>";
    }
    
    function get_markup()
    {
        $markup = "<div id=\"menu\">\n";
        $markup .= "<div class=\"title\">{$this->_title}</div>\n";
        if ($this->_contents) {
            $markup .= $this->_contents;
        }
        $markup .= "</div>";
        return $markup;
    }
}

class PopupMenu extends DefaultPopupMenu
{
    /**
     * @var string The title of the popup menu to be shown on the button.
     */
    private $_title;
    
    /**
     * @var string The ID of this popup menu. Must be unique.
     */
    private $_id;
    
    /**
     * @var string Built HTML for the items in the popup menu.
     */
    private $_items;
    
    /**
     * Initialise a new popup menu.
     *
     * @var string The ID of the popup menu.
     * @var string The title of the popup menu.
     */
    function __construct($id, $title = '')
    {
        $this->_id    = $id;
        $this->_title = $title;
    }
    
    /**
     * Add an item to the popup menu.
     *
     * @param string The title of this item.
     * @param string The page this item should link to.
     * @param string The onclick event handler if we have one.
     */
    function add_item($text, $link, $onclick = '')
    {
        if ($onclick) {
            $onclick = " onclick=\"{$onclick}\"";
        }
        $this->_items .= "<div class=\"popup_item_container\"><a href=\"{$link}\"{$onclick} class=\"popup_item\">{$text}</a></div>";
    }
    
    /**
     * Fetch the contents of the popup menu.
     *
     * @return string The popup menu.
     */
    function fetch()
    {
        $popup = "<div class=\"pjxPopup-wrap\"><div class=\"popup_menu\" id=\"{$this->_id}_popup\">\n{$this->_items}</div>\n";
        if ($this->_title) {
            $popup .= "<a href=\"javascript:;\" id=\"{$this->_id}\" class=\"popup_button\">{$this->_title}</a>\n";
        }
        $popup .= "<script type=\"text/javascript\">\n";
        $popup .= "new PopupMenu('{$this->_id}');\n";
        $popup .= "</script></div>\n";
        return $popup;
    }
    
    /**
     * Outputs a popup menu to the browser.
     */
    function output()
    {
        echo $this->fetch();
    }
}

class Table extends DefaultTable
{
}

class Form extends DefaultForm
{
    function generate_radio_button($name, $value = "", $label = "", $options = array())
    {
        global $lang;
        $input = "<input type=\"radio\" name=\"{$name}\" value=\"" . htmlspecialchars($value) . "\"";
        if (isset($options['class'])) {
            $input .= " class=\"radio_input " . $options['class'] . "\"";
        } else {
            $input .= " class=\"radio_input\"";
        }
        if (isset($options['id'])) {
            $input .= " id=\"" . $options['id'] . "\"";
        } else {
            $input .= " id=\"" . htmlspecialchars($value) . "_{$name}\"";
        }
        if (isset($options['checked']) && $options['checked'] != 0) {
            $input .= " checked=\"checked\"";
        }
        $input .= " />";
        
        $input .= "<label";
        if (isset($options['id'])) {
            $input .= " for=\"{$options['id']}\"";
        } else {
            $input .= " for=\"" . htmlspecialchars($value) . "_{$name}\"";
        }
        $input .= ">";
        if ($label != "") {
            if ($label == $lang->no || $label == $lang->yes || $label == $lang->on || $label == $lang->off) {
                $input .= "";
            } else {
                $input .= $label;
            }
        }
        $input .= "</label>";
        return $input;
    }
    
    function generate_submit_button($value, $options = array())
    {
        global $lang;
        if ($value == $lang->yes || $value == $lang->no) {
            $value = "";
        }
        $input = "<label ";
        if (isset($options['class'])) {
            $input .= " class=\"" . $options['class'] . "\"";
        }
        $input .= ">";
        $input .= "<input type=\"submit\" value=\"" . htmlspecialchars($value) . "\"";
        
        if (isset($options['class'])) {
            $input .= " class=\"submit_button " . $options['class'] . "\"";
        } else {
            $input .= " class=\"submit_button\"";
        }
        if (isset($options['id'])) {
            $input .= " id=\"" . $options['id'] . "\"";
        }
        if (isset($options['name'])) {
            $input .= " name=\"" . $options['name'] . "\"";
        }
        if ($options['disabled']) {
            $input .= " disabled=\"disabled\"";
        }
        if ($options['onclick']) {
            $input .= " onclick=\"" . str_replace('"', '\"', $options['onclick']) . "\"";
        }
        $input .= " /></label>";
        return $input;
    }
}

class FormContainer extends DefaultFormContainer
{
    private $_container;
    public $_title;
    
    /**
     * Initialise the new form container.
     *
     * @param string The title of the forum container
     * @param string An additional class to apply if we have one.
     */
    function __construct($title = '', $extra_class = '')
    {
        $this->_container  = new Table;
        $this->extra_class = $extra_class;
        $this->_title      = $title;
    }
    
    /**
     * Output a header row of the form container.
     *
     * @param string The header row label.
     * @param array TODO
     */
    function output_row_header($title, $extra = array())
    {
        $this->_container->construct_header($title, $extra);
    }
    
    /**
     * Output a row of the form container.
     *
     * @param string The title of the row.
     * @param string The description of the row/field.
     * @param string The HTML content to show in the row.
     * @param string The ID of the control this row should be a label for.
     * @param array Array of options for the row cell.
     * @param array Array of options for the row container.
     */
    function output_row($title, $description = "", $content = "", $label_for = "", $options = array(), $row_options = array())
    {
        global $plugins;
        $pluginargs = array(
            'title' => &$title,
            'description' => &$description,
            'content' => &$content,
            'label_for' => &$label_for,
            'options' => &$options,
            'row_options' => &$row_options,
            'this' => &$this
        );
        $plugins->run_hooks("admin_formcontainer_output_row", $pluginargs);
        if ($label_for != '') {
            $for = " for=\"{$label_for}\"";
        }
        
        if ($title) {
            $row = "<label{$for}>{$title}</label>";
        }
        
        if ($options['id']) {
            $options['id'] = " id=\"{$options['id']}\"";
        }
        $row .= "<div class=\"form_row\"{$options['id']}>{$content}</div>\n";
        
        if ($description != '') {
            $row .= "\n<div class=\"description\">{$description}</div>\n";
        }
        
        $this->_container->construct_cell($row, $options);
        
        if (!isset($options['skip_construct'])) {
            $this->_container->construct_row($row_options);
        }
    }
    
    /**
     * Output a row cell for a table based form row.
     *
     * @param string The data to show in the cell.
     * @param array Array of options for the cell (optional).
     */
    function output_cell($data, $options = array())
    {
        $this->_container->construct_cell($data, $options);
    }
    
    /**
     * Build a row for the table based form row.
     *
     * @param array Array of extra options for the cell (optional).
     */
    function construct_row($extra = array())
    {
        $this->_container->construct_row($extra);
    }
    
    /**
     * return the cells of a row for the table based form row.
     *
     * @param string The id of the row.
     * @param boolean Whether or not to return or echo the resultant contents.
     * @return string The output of the row cells (optional).
     */
    function output_row_cells($row_id, $return = false)
    {
        if (!$return) {
            echo $this->_container->output_row_cells($row_id, $return);
        } else {
            return $this->_container->output_row_cells($row_id, $return);
        }
    }
    
    /**
     * Count the number of rows in the form container. Useful for displaying a 'no rows' message.
     *
     * @return int The number of rows in the form container.
     */
    function num_rows()
    {
        return $this->_container->num_rows();
    }
    
    /**
     * Output the end of the form container row.
     *
     * @param boolean Whether or not to return or echo the resultant contents.
     * @return string The output of the form container (optional).
     */
    function end($return = false)
    {
        global $plugins;
        
        $hook = array(
            'return' => &$return,
            'this' => &$this
        );
        
        $plugins->run_hooks("admin_formcontainer_end", $hook);
        if ($return == true) {
            return $this->_container->output($this->_title, 1, "general form_container {$this->extra_class}", true);
        } else {
            echo $this->_container->output($this->_title, 1, "general form_container {$this->extra_class}", false);
        }
    }
}
?>