<?php
    define('IS_IN_APP',1);
    define('DEBUG',1);

    include("inc.php");

    $mangas = getAllSeries(0,true);

    function countDIR($dir) {
    	$i = 0; 
    	if ($handle = opendir($dir)) {
        	while (($file = readdir($handle)) !== false){
         	   if (!in_array($file, array('.', '..')) && !is_dir($dir.$file)) 
         	       $i++;
        	}
    	}

    	return $i;
    }

    function makeDIR($name) {
    	if(!is_dir("/var/www/hosts/mangamags.com/tmpImgs/".$name)) {
	  		mkdir("/var/www/hosts/mangamags.com/tmpImgs/".$name);
	  		chmod('/var/www/hosts/mangamags.com/tmpImgs/'.$name, 0777);
		}
    }

    function addChapter($series,$chapter,$pages,$chapterName) {
    	global $dbInfo;

		$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);

		if(!mysqli_connect_errno()) {
			mysqli_query($db,"INSERT INTO mangaseries
				(id,chapter,pages,name,series,upped,userup) 
				VALUES 
				(NULL,".intval(abs($chapter)).",".intval(abs($pages)).",'".mysqli_real_escape_string($db,$chapterName)."','".$series."','".time()."',1)");
		}
		mysqli_close($db);
    }

    function newSeries($seriesInfo,$fullDIR) {
    	global $dbInfo;

    	$db = @mysqli_connect($dbInfo['host'],$dbInfo['username'],$dbInfo['password'],$dbInfo['db']);

		if(!mysqli_connect_errno()) {
			mysqli_query($db,"INSERT INTO mangalisting
				(id,name,directory,chapters,alternate,year,status,author,artist,direction,genre,description,rate,userup) 
				VALUES 
				(NULL,'".mysqli_real_escape_string($db,$seriesInfo['name'])."','".$fullDIR."',0,'".mysqli_real_escape_string($db,$seriesInfo['alternate'])."','".intval(abs($seriesInfo['year']))."','".mysqli_real_escape_string($db,$seriesInfo['status'])."','".mysqli_real_escape_string($db,$seriesInfo['author'])."','".mysqli_real_escape_string($db,$seriesInfo['artist'])."','".mysqli_real_escape_string($db,$seriesInfo['direction'])."','".mysqli_real_escape_string($db,$seriesInfo['genre'])."','".mysqli_real_escape_string($db,$seriesInfo['description'])."',0.00,1)");
		}

		mysqli_close($db);
    }

    function readimage($zip_file, $file_name) {
	    $z = new ZipArchive();
	    if ($z->open($zip_file) !== true) {
	        echo "File not found.";
	        return false;
	    }

	    $content = '';
	    $fp   = $z->getStream($file_name);
	    
	    if(!$fp) {
	        echo "Could not load image.";
	        return false;
	    }
	    
	    return $fp;
	}

	function readimageRAR($rar_file, $file_name) {
	    $r = new RarArchive();
	    if ($r->open($rar_file) !== true) {
	        echo "File not found.";
	        return false;
	    }

	    $content = '';
	    $fp   = $r->getStream($file_name);
	    
	    if(!$fp) {
	        echo "Could not load image.";
	        return false;
	    }
	    
	    return $fp;
	}

	function putCovImg($series,$file) {
		$seriesInfo = getSeriesInfo($series);
		file_put_contents('/var/www/hosts/mangamags.com/tmpImgs/'.$series.'/cover.jpg',file_get_contents($file));
    	chmod('/var/www/hosts/mangamags.com/tmpImgs/'.$series.'/cover.jpg', 0777);
	}

	function unrarManga($rarName,$directory,$chapter,$chapName) {
		$chapter = abs(intval($chapter));
		//$rarObj = rar_open($rarName);
		//$rarObj = RarArchive::open($rarName);
	    $validFiles = Array();

	    if($rarObj = RarArchive::open($rarName)) {
	    	$entries = $r->getEntries();
	    	foreach($entries as $rarEntry) {
	    		$rName = $rarEntry->getName();
	    		if(preg_match("/\.(?:jpg|gif|png)$/i",$rName,$matches)) {
	                array_push($validFiles,$rName);
	            }
	    	}
	    	$r->close();
	    } else {
	    	return '3';
	    }

	    /*
	    if(is_resource($rarObj)) {
	    	$entries = rar_list($rarObj);
	    	foreach($entries as $rarEntry) {
	            $rName = $rarEntry->getName();
	            if(preg_match("/\.(?:jpg|gif|png)$/i",$rName,$matches)) {
	                array_push($validFiles,$rName);
	            }
	        }
	        rar_close($rarObj);
	    } else {
	    	// Not a valid zip!
	    	return '3';
	    }
	    */

	    if(count($validFiles)==0) {
	        // No valid images!
	        return '2';
	    }

	    sort($validFiles);
	    array_flip($validFiles);
    	
	   	// extract pics
	    makeDIR($directory."/".$chapter);

	    $i = 0;
	    foreach($validFiles as $spot=>$file) {
			file_put_contents('/var/www/hosts/mangamags.com/tmpImgs/'.$directory.'/'.$chapter.'/'.($spot+1).'.jpg',readImage($zipName,$file));
        	chmod('/var/www/hosts/mangamags.com/tmpImgs/'.$directory.'/'.$chapter.'/'.($spot+1).'.jpg', 0777);

        	++$i;
	    }

	    addChapter($directory,$chapter,$i,$chapName);

	    return '0';
	}

    function unzipManga($zipName,$directory,$chapter,$chapName) {
    	$chapter = abs(intval($chapter));
	    $zipObj = zip_open($zipName);
	    $validFiles = Array();
	    if(is_resource($zipObj)) {
	        while($zipEntry = zip_read($zipObj)) {
	            $zName = zip_entry_name($zipEntry);
	            if(preg_match("/\.(?:jpg|gif|png)$/i",$zName,$matches)) {
	                array_push($validFiles,$zName);
	            }
	        }
	        zip_close($zipObj);
	    } else {
	    	// Not a valid zip!
	    	return '3';
	    }

	    if(count($validFiles)==0) {
	        // No valid images!
	        return '2';
	    }

	    sort($validFiles);
	    array_flip($validFiles);
    	
	   	// extract pics
	    makeDIR($directory."/".$chapter);

	    $i = 0;
	    foreach($validFiles as $spot=>$file) {
			file_put_contents('/var/www/hosts/mangamags.com/tmpImgs/'.$directory.'/'.$chapter.'/'.($spot+1).'.jpg',readImage($zipName,$file));
        	chmod('/var/www/hosts/mangamags.com/tmpImgs/'.$directory.'/'.$chapter.'/'.($spot+1).'.jpg', 0777);

        	++$i;
	    }

	    addChapter($directory,$chapter,$i,$chapName);

	    return '0';
	}

    if(isset($_GET['action'])) {
    	/* Instructions for upload
    		1. Get the zip/rar as an upload
			2. Open the zip/rar and find out if there are images in the main directory
			3. If no images exist in the main dir, and there is only ONE folder, recurse into the folder, go back to 2.
			4. If no images are found, return error
			4. If images are found, verify they are in proper format with regex:  ([0-9])+\.(?:jpg|png|gif)
			5. Discard images the regex does not match, unzip all the ones that do.
			6. Rename all of the images using regex matches to work with our format
		*/

    	if($_GET['action'] == "upload") {
    		if($_POST['upType'] == "new") {
	    		if(in_array(substr($_FILES['coverfile']['name'],-3),Array('jpg','png','gif'))) {
	    			$dir = preg_replace("/[^a-z0-9_\s-]/","",preg_replace("/[\s-]+/","-",preg_replace("/[\s_]/", "-",strtolower($_POST['seriesName']))));

	    			$seriesInfo = Array(
	    				'name'        => $_POST['seriesName'],
	    				'alternate'   => $_POST['alt'],
	    				'genre'       => $_POST['genres'],
	    				'author'      => $_POST['authors'],
	    				'artist'      => $_POST['artists'],
	    				'status'      => $_POST['status'],
	    				'year'        => $_POST['startyear'],
	    				'direction'   => $_POST['direction'],
	    				'description' => $_POST['description']
    				);

	    			newSeries($seriesInfo,$dir);
	    			makeDIR($dir);
	    			putCovImg($dir,$_FILES['coverfile']['tmp_name']);

	    			die("Added new series");
	    		}
    		} else if($_POST['upType'] == "exist") {
    			if(!doesSeriesExist($_POST['seriesSelector'])) die('4');
    			if(in_array(substr($_FILES['file']['name'],-3),Array('rar','cbr'))) {
	    			$seriesInfo = getSeriesInfo($_POST['seriesSelector']);
	    			$seriesDIR = $seriesInfo['directory'];
	    			$nextChapter = $seriesInfo['chapters'] + 1;

	    			$unrar = unrarManga($_FILES['file']['tmp_name'],$seriesDIR,$nextChapter,$_POST['chapterName']);

	    			die($unrar);
	    		} else if(in_array(substr($_FILES['file']['name'],-3),Array('zip','cbz'))) {
	    			$seriesInfo = getSeriesInfo($_POST['seriesSelector']);
	    			$seriesDIR = $seriesInfo['directory'];
	    			$nextChapter = $seriesInfo['chapters'] + 1;

	    			$unzipper = unzipManga($_FILES['file']['tmp_name'],$seriesDIR,$nextChapter,$_POST['chapterName']);

	    			die($unzipper);
	    		}
    		}
    		die();
	    } else if($_GET['action'] == "chapCount" && isset($_GET['series'])) {
	    	$seriesInfo = getSeriesInfo($_GET['series']);
	    	die($seriesInfo['chapters']);
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
        <title><?php echo($siteName); ?> - Upload</title>
        <meta property="og:title" content="<?php echo($siteName); ?> - Upload" />
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
        <?php if($isLoggedIn) { ?>
	        <script>
	        	function getValue(input,datalist) {
		            return jQuery(datalist+" option[value='"+jQuery(input).val()+"']").attr('id');
		        }
	        </script>
	        <script>
	        	$(document).ready(function(){
	        		$('#checkFalse').attr('checked','checked');

	        		$("#seriesSeletor").on('keyup',$.debounce(200,function() {
	        			if(getValue("#seriesSeletor","#seriesSelectorList") !== undefined) {
							$.ajax('upload.php?action=chapCount&series='+getValue("#seriesSeletor","#seriesSelectorList"))
							.success(function(resp) {
								$('#chapterCount').html(parseInt(resp) + 1);
							});
						} else {
							$('#chapterCount').html('Not A Series');
						}
					}));

					$('#submitButton').click(function() {
						$("#seriesSeletor").val(getValue("#seriesSeletor","#seriesSelectorList"));
					});

	        		$("#checkTrue").change(function(){
			        	if(this.checked) {
			        		$("#seriesInfo_exist").css('display','block');
			        		$("#seriesInfo").css('display','none');
			        	}
			        });
			        $("#checkFalse").change(function(){
			        	if(this.checked) {
			        		$("#seriesInfo_exist").css('display','none');
			        		$("#seriesInfo").css('display','block');
			        	}
			        });
	        	});
	    	</script>
		<?php } ?>
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
        	<?php if($isLoggedIn) { ?>
        		<div id="content" style="width:800px;">
	        		<div style="text-align:center;margin:0px auto 0px auto;"><span style="font-size:20px;">Upload Instructions</span></div>
	        		<br />
	        		<ul style="font-weight:bold;list-style-type:disc;margin-left:30px;">
	        			<li style="color:rgba(150,0,0,1);">You can only upload the next chapter of a series. <span style="font-weight:300;font-style:italic;">No skipping chapters.</span></li>
	        			<li>Series in .cbr and .cbz containers can be uploaded without changes.</li>
	    			</ul>

	    			<span style="font-weight:bold;text-decoration:underline;">How to upload:</span>
	    			<ol style="margin-left:30px;">
	    				<li>Zip or Rar, all images for one chapter named numerically. <br /><span style="font-weight:300;font-style:italic;">Anything that works in .cbz is acceptabe: 1.jpg, 2.jpg, 3.jpg, 01.jpg, 02.jpg, 03.jpg</span></li>
	    				<li>Make sure you do not have multiple chapters in the same zip file.  You may only upload one at a time.</li>
	    				<li>After picking a series, select your zipped chapter and press upload!</li>
					</ol>
	    		</div>
	        	<div id="content" style="width:800px;margin-bottom:10px;">
	        		<span style="font-size:20px;">Upload</span>
	        		<br />
	        		<br />
		        	<form action="<?php echo($fullSiteURL); ?>/upload.php?action=upload" method="POST" name="uploader" enctype="multipart/form-data">
		        	<div class="form_container">
		        		<input type="hidden" name="action" value="upload" />
		        		<input type="hidden" name="MAX_FILE_SIZE" value="367001600" />
			        	<div id="upType">
			        		<input type="radio" name="upType" value="new" id="checkFalse" />&nbsp;Upload New Series
			        		<br />
			        		<input type="radio" name="upType" value="exist" id="checkTrue" />&nbsp;Upload To Existing Series
		        		</div>
		        		<br />
		        		<div id="seriesInfo_exist" style="clear:both;font-weight:bold;display:none;">
		        			<div class="label"><label for="seriesSelector">Series Name:</label></div>
	                        <div class="field"><input name="seriesSelector" id="seriesSeletor" list="seriesSelectorList" autocomplete="off" class="text_input" /></div>

		        			<datalist id="seriesSelectorList">
				            	<?php
					            	for($i = 0;$i < count($mangas);++$i) {
					            		echo('<option id="'.$mangas[$i]['directory'].'" value="'.$mangas[$i]['name'].'" />'."\r\n");
					            	}
					            ?>
			            	</datalist>
			            	<br style="clear:both;" />
			            	<span style="float:left;">Chapter:</span><div id="chapterCount" style="float:left;margin-left:5px;"></div>
			            	<div class="clear"></div>

			            	<div class="label"><label for="chapterName">Chapter Name:</label></div>
	                        <div class="field"><input type="text" name="chapterName" id="chapterName" class="text_input" /></div>

			            	<div id="zipUp" style="clear:both;">
			        			<br />
			        			<div class="label"><label for="file" style="font-weight:bold;">Zip Upload:</label></div>
			        			<input type="file" name="file" id="file" style="width:100%;" />
			        		</div>
		        		</div>
		        		<div id="seriesInfo" style="clear:both;font-weight:bold;">
		     	  			<div class="label"><label for="seriesName">Series Name:</label></div>
	                        <div class="field"><input type="text" name="seriesName" id="seriesName" class="text_input" /></div>

		     	  			<div class="label"><label for="alt">Alternate Titles <span style="font-weight:200;font-style:italic;">(Seperated by commas)</span></label></div>
	                        <div class="field"><input type="text" name="alt" id="alt" class="text_input" /></div>
	                        
		     	  			<div class="label"><label for="genres">Genres <span style="font-weight:200;font-style:italic;">(Seperated by commas)</span></label></div>
	                        <div class="field"><input type="text" name="genres" id="genres" class="text_input" /></div>
	                        
		     	  			<div class="label"><label for="authors">Authors <span style="font-weight:200;font-style:italic;">(Seperated by commas)</span></label></div>
	                        <div class="field"><input type="text" name="authors" id="authors" class="text_input" /></div>
	                        
		     	  			<div class="label"><label for="artists">Artists <span style="font-weight:200;font-style:italic;">(Seperated by commas)</span></label></div>
	                        <div class="field"><input type="text" name="artists" id="artists" class="text_input" /></div>
	                        
		     	  			<div class="label"><label for="status">Status:</label></div>
		     	  			<div class="field"><select type="option" name="status" id="status" class="text_input"><option value="Complete">Complete</option><option value="Ongoing">Ongoing</option><option value="Unknown">Unknown</option></select></div>
	                        
		     	  			<div class="label"><label for="startyear">Starting Year:</label></div>
		     	  			<div class="field"><select name="startyear" id="startyear" class="text_input"><?php for($i = date('Y');$i >= 1950;--$i) echo('<option value="'.$i.'">'.$i.'</option>'); ?></select></div>

		     	  			<div class="label"><label for="direction">Read Direction:</label></div>
		     	  			<div class="field"><select type="option" name="direction" id="direction" class="text_input"><option value="Left to Right">Left to Right</option><option value="Right to Left">Right to Left</option><option value="Unknown">Unknown</option></select></div>
	                        
	                        <div class="label"><label for="description">Description:</label></div>
	                        <div class="field"><textarea name="description" style="width:100%;height:200px;"></textarea></div>

		        			<div id="zipUp" style="clear:both;">
			        			<br />
			        			<div class="label"><label for="coverfile" style="font-weight:bold;">Cover Page:</label></div>
			        			<input type="file" name="coverfile" id="coverfile" style="width:100%;" />
			        		</div>
		        		</div>
		        		<br />
		        		<p style="text-align:center;margin:0px;padding:0px;" class="submit"><button type="submit" id="submitButton" name="submit" value="Upload" style="width:100%;padding:5px;" />Submit</button></p>
	        		</div>
	    	    </div>
		            <!-- /form -->
        	<?php } else { ?>
        		<div id="content" style="width:800px;margin-top:200px;">
	        		<div style="text-align:center;margin:0px auto 0px auto;"><span style="font-size:25px;font-weight:bold;">Please <a href="<?php echo($fullSiteURL); ?>/login/" style="text-decoration:none;color:rgba(0,0,150,1);">login</a> to view this page</span></div>
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