<?php
    define('IS_IN_APP',1);
    define('DEBUG',1);

    include("inc.php");
    require_once $forumPath."/inc/datahandlers/user.php";

    include("./resources/recaptchalib.php");
    $publickey = '6LcrMe0SAAAAAEgTkrngo-gMPMH-SN7YLB5TewNm';
    $privatekey = '6LcrMe0SAAAAAO_NGHb8REwGVKBZqV5PtBp0PZRV';
    
    $login = null;

    if(isset($MyBBI->mybb->input['action']) && $MyBBI->mybb->input['action'] == "logout") {
        my_unsetcookie("mybbuser");
        my_unsetcookie("sid");
        if($mybb->user['uid']) {
            $time = TIME_NOW;
            // Run this after the shutdown query from session system
            $db->shutdown_query("UPDATE ".TABLE_PREFIX."users SET lastvisit='{$time}', lastactive='{$time}' WHERE uid='{$mybb->user['uid']}'");
            $db->delete_query("sessions", "sid='".$session->sid."'");
        }
        header("Location: ".$fullSiteURL);
    } else {
        if($isLoggedIn) header("Location: ".$fullSiteURL);

        if(isset($MyBBI->mybb->input['action']) && $MyBBI->mybb->input['action']=="register" && isset($MyBBI->mybb->input['user']) && isset($MyBBI->mybb->input['pass']) && isset($MyBBI->mybb->input['email'])) {
            $resp = recaptcha_check_answer ($privatekey,
                                 $_SERVER["REMOTE_ADDR"],
                                 $_POST["recaptcha_challenge_field"],
                                 $_POST["recaptcha_response_field"]);
            if (!$resp->is_valid) {
               die ("The reCAPTCHA wasn't entered correctly. Go back and try it again.  (reCAPTCHA said: " . $resp->error . ")");
            } else {
                $usergroup = 5;
                $userhandler = new UserDataHandler("insert");

                // Set the data for the new user.
                $user = array(
                    "username"       => $MyBBI->mybb->input['user'],
                    "password"       => $MyBBI->mybb->input['pass'],
                    "password2"      => $MyBBI->mybb->input['pass'],
                    "email"          => $MyBBI->mybb->input['email'],
                    "email2"         => $MyBBI->mybb->input['email'],
                    "usergroup"      => 2,
                    "referrer"       => '',
                    "profile_fields" => '',
                    "regip"          => $MyBBI->session->ipaddress,
                    "longregip"      => my_ip2long($MyBBI->session->ipaddress),
                    "avatar"         => "./images/avatars/default_avatar.jpg"
                );
                $userhandler->set_data($user);

                $errors = "";

                if(!$userhandler->validate_user()) {
                    $errors = $userhandler->get_friendly_errors();
                    echo($errors);
                } else {
                    $user_info = $userhandler->insert_user();

                    $login = $MyBBI->login($MyBBI->mybb->input['user'],$MyBBI->mybb->input['pass']);

                    //my_setcookie("mybbuser", $user_info['uid']."_".$user_info['loginkey'], null, true);
                    header("Location: ".$fullSiteURL);
                }
            }
        } else {
            if(isset($MyBBI->mybb->input['user']) && isset($MyBBI->mybb->input['pass'])) {
                if(isset($MyBBI->mybb->input['captcha_hash'])) {
                    $login = $MyBBI->login($MyBBI->mybb->input['user'],$MyBBI->mybb->input['pass'],$MyBBI->mybb->input['captcha_hash'],$MyBBI->mybb->input['captcha']);
                    if(is_array($login)) {
                        $captcha_hash = $login['imagehash'];
                    }
                } else {
                    $login = $MyBBI->login($MyBBI->mybb->input['user'],$MyBBI->mybb->input['pass']);
                    if(is_array($login)) {
                        $captcha_hash = $login['imagehash'];
                    }
                }

                if($login === true) {
                    header("Location: ".$fullSiteURL);
                }
            }
        }
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <base href="<?php echo($fullSiteURL); ?>" />

        <meta content="utf-8" http-equiv="encoding">
        <meta content="text/html;charset=utf-8" http-equiv="Content-Type">

        <link rel="Shortcut Icon" href="favicon.ico" type="image/x-icon" />

        <meta name="keywords" content="" />
        <meta name="description" content="" />
        <title><?php echo($siteName); ?> - Login</title>
        <meta property="og:title" content="<?php echo($siteName); ?> - Register" />
        <meta property="og:site_name" content="Read Manga Here!" />
        <meta property="og:image" content="pageimage" />
        
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="<?php echo($fullSiteURL); ?>/resources/css/reset.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo($fullSiteURL); ?>/resources/css/theme.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo($fullSiteURL); ?>/resources/css/login.css" />

        <!-- Fonts -->
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,300italic,400italic,700,700italic,900,900italic" />
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lobster" />
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Fugaz+One" />
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Rum+Raisin" ?>

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
            <?php if(isset($MyBBI->mybb->input['action']) && $MyBBI->mybb->input['action']=="register") { ?>
                <br />
                <br />
                <div id="content">
                    <h2>Registration</h2>
                    <form method="post" action="">
                        <div class="form_container">
                            <div class="label"><label for="username">Username:</label></div>
                            <div class="field"><input type="text" name="user" id="username" class="text_input initial_focus" /></div>
                            <div class="label"><label for="password">Password:</label></div>
                            <div class="field"><input type="password" name="pass" id="password" class="text_input" /></div>
                            <div class="label"><label for="email">Email:</label></div>
                            <div class="field"><input type="email" name="email" id="email" class="text_input"></div>
                            <div class="field" style="width: 350px;margin:0px auto 0px auto;">
                            <?php
                                echo(recaptcha_get_html($publickey));
                            ?>
                            </div>
                        </div>
                        <p class="submit">
                            <input type="submit" value="<?php echo($mybb->settings['disableregs'] ? 'Registrations Disabled' : 'Register'); ?>" <?php echo($mybb->settings['disableregs'] ? 'disabled' : NULL); ?> />
                        </p>
                        <br />
                    </form>
                </div>
            <?php } else { ?>
                <br />
                <br />
                <br />
                <div id="content">
                    <h2>Login</h2>
                    <?php if($login !== true && isset($MyBBI->mybb->input['user']) && isset($MyBBI->mybb->input['pass'])) { ?><p id="message" class="error"><span class="text">Hey dumbass, you got your username or password wrong.  *facepalm*</span></p><?php } ?>
                    <form method="post" action="">
                        <div class="form_container">
                            <div class="label"><label for="username">Username/Email:</label></div>
                            <div class="field"><input type="text" name="user" id="username" class="text_input initial_focus" /></div>
                            <div class="label"><label for="password">Password:</label>
                            <span class="forgot_password">
                                <a href="<?php echo($fullSiteURL.'/'.$forumPath.'/'); ?>member.php?action=lostpw">Forgot your password?</a>
                            </span></div>
                            <div class="field"><input type="password" name="pass" id="password" class="text_input" /></div>
                        </div>
                        <p class="submit">
                            <input type="submit" value="Login" style="float:left;" /><input type="submit" style="float:right;" value="Register" onclick="document.location = './login.php?action=register';return false;" />
                            <div class="clear"></div>
                        </p>
                    </form>
                </div>
            <?php } ?>
        </section>
        <footer>
            <p class="content">
                <?php include("./resources/footer.php"); ?>
            </p>
        </footer>
    </body>
</html>