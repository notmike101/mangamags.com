<?php
    define('IS_IN_APP',1);

    include("inc.php");

    $mangas = getAllSeries(0,true);

    if(isset($_GET['action']) && $_GET['action'] == "send") {
        if(isset($_POST['email']) && isset($_POST['name']) && isset($_POST['series']) && isset($_POST['proof'])) { 
            $to = "admin@example.com";
            $subject = "Takedown Request";
            $message = "
IP: ".$_SERVER['REMOTE_ADDR']."
Name/Organization: ".$_POST['name']."
Email: ".$_POST['email']."

Series: ".$_POST['series']."
Proof of Ownership: ".$_POST['proof']."
";
            $headers .= 'From: MangaMags Takedown Request <takedownrequest@example.com>';
            
            if(mail($to,$subject,$message,$headers)) {
                die('0');
            } else {
                die('1');
            }
        } else {
            die('2');
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
        <title><?php echo($siteName); ?> - Takedown Request</title>
        <meta property="og:title" content="<?php echo($siteName); ?> - Takedown Request" />
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

        <script>
            function getValue(input,datalist) {
                return jQuery(datalist+" option[value='"+jQuery(input).val()+"']").attr('id')
            }
        </script>
        <script>
            $(document).ready(function(){
                var sentEmail = false;

                $('#buttonsubmit').click(function() {
                    $('#message').hide();
                    $('#name').attr("disabled","disabled");
                    $('#email').attr("disabled","disabled");
                    $("#proof").attr("disabled","disabled");
                    $("#seriesSelector").attr("disabled","disabled");

                    if(sentEmail == true) {
                        $('#message').addClass('error');
                        $('#text_response').html("Please don't spam.  Your request was already sent..");

                        $('#name').removeAttr("disabled");
                        $('#email').removeAttr("disabled");
                        $("#proof").removeAttr("disabled");
                        $("#seriesSelector").removeAttr("disabled");
                    } else if($('#name').val() == '' || $('#email').val() == '' || getValue('#seriesSelector','#seriesSelectorList') == '' || $('#proof').val() == '') {
                        $('#message').addClass('error');
                        $('#text_response').html("Please fill in all fields with valid information to send a request.");

                        $('#name').removeAttr("disabled");
                        $('#email').removeAttr("disabled");
                        $("#proof").removeAttr("disabled");
                        $("#seriesSelector").removeAttr("disabled");
                    } else {
                        $.ajax({
                            url: '<?php echo($fullSiteURL); ?>/request.php?action=send',
                            type: 'POST',
                            data: {
                                name: $('#name').val(),
                                email: $('#email').val(),
                                proof: $('#proof').val(),
                                series: getValue("#seriesSelector","#seriesSelectorList"),
                            }
                        }).success(function(resp) {
                            if(resp == '0') {
                                $('#message').addClass('success');
                                $('#text_response').html("Your request has been successfully sent and will be reviewed shortly.");
                                $('#buttonsubmit').hide();
                                sentEmail = true;
                            } else {
                                $('#message').addClass('error');
                                $('#text_response').html("Your message failed to send.  Please try again.");

                                $('#name').removeAttr("disabled");
                                $('#email').removeAttr("disabled");
                                $("#proof").removeAttr("disabled");
                                $("#seriesSelector").removeAttr("disabled");
                            }
                        });
                    }
                    $('#message').show("slow");
                })
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
            <div id="content" style="margin-bottom:10px;">
                <h2>Takedown Request</h2>
                <p id="message" style="display:none;"><span class="text" id="text_response"></span></p>

                <!-- form method="post" id="" -->
                    <div class="form_container">
                        <div class="label"><label for="fname">Name/Organization:</label></div>
                        <div class="field"><input type="text" name="name" id="name" class="text_input initial_focus" /></div>

                        <div class="label"><label for="email">Email:</label></div>
                        <div class="field"><input type="email" name="email" id="email" class="text_input" /></div>
                        
                        <div class="label"><label for="seriesSelector">Series:</label></div>
                        <div class="field"><input id="seriesSelector" list="seriesSelectorList" name="seriesSelector" class="text_input" autocomplete="off" /></div>
                        <datalist id="seriesSelectorList">
                            <?php
                                for($i = 0;$i < count($mangas);++$i) {
                                    echo('<option id="'.$mangas[$i]['directory'].'" value="'.$mangas[$i]['name'].'" />');
                                }
                            ?>
                        </datalist>

                        <div class="label"><label for="proof">Proof of Ownership:</label></div>
                        <div class="field"><textarea class="text_input" id="proof" style="width:100%;resize:none;height:400px;"></textarea></div>

                        <p style="text-align:center;margin:0px;padding:0px;" class="submit"><button type="submit" id="buttonsubmit" value="Submit" style="width:80%;margin:0px auto 0px auto;" />Submit</button></p>
                    </div>
                <!-- /form -->
            </div>
        </section>
        <footer>
            <p class="content">
                <?php include("./resources/footer.php"); ?>
            </p>
        </footer>
    </body>
</html>