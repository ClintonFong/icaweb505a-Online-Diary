<?php
    //
    // index.php
    //
    // by Clinton Fong
    //
/*
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('log_errors_max_length', 0);
    ini_set('error_log', './error_log.txt');
*/
    session_start();

    //----------------------------------------------
    // check if logged in, otherwise throw them out.
    //----------------------------------------------
	require_once 	'include/lib/class.loginController.inc.php';
    

   	$idUser             = (isset($_SESSION['icaweb505a-user-id']))? $_SESSION['icaweb505a-user-id'] : "-1";	
    $objLoginController = new c_loginController();
    if( !$objLoginController->isUserLoggedIn( $idUser ) )
    {
        header( "Location: login.php" ); // redirect to login page
    }
    
    $isLoggedIn = true;
    $currentPage    = "index.php";

    
    
    
?>

<!DOCTYPE html>

<html>

<head>
    <meta charset='utf-8' />
    <meta name='description' content='Online Diary' />
    <meta name='keywords' content='Online Diary' />
    <meta name='author' content='Clinton Fong' />

    <title>Online Diary</title>

    <link rel='stylesheet' type='text/css' href='css/main.css' />
    <link rel='stylesheet' type='text/css' href='css/calendar.css' />

</head>

<body>

    <!-- header -->
    <?php include "include/header.php"; ?>
    <!-- end header -->

    <div id="mainWrapper">

        <!-- Main Content -->
		<div id="main-content">

            <div class="ajaxLoader"></div>

            <div id="cntCalendarWrapper">
                <!-- calendar part -->
                <div id="calendar"></div>

                <!-- diary part -->
                <div id="diary">
                    <!-- title -->
                    <div id="diaryTitle">
                        <h1></h1>
                        <a id="saveEntry" class="diaryButtons">Save</a>
                    </div>
                    <div id="diaryText"><textarea></textarea></div>

                    <!-- Attachments -->
                    <div id="diaryAttachmentsTitle" class="diaryAttachments">
                        <span>Attachments</span>
                        <div id="cntAddAttachment">
                            <a id="addAttachment" class="diaryButtons">Add</a>
                            <!-- <a id="removeAttachment" class="diaryButtons">Remove</a> -->
                        </div>
                    </div>

                    <div id="cntFileUpload">
                        <form id='frmFileUpload' method="post">
                            <input type="hidden" name="action"  value="upload-attachment-file" />
                            <input type="hidden" name="idUser"  value="<?php echo $idUser; ?>" />
                            <input type="hidden" name="date"    value="" />

                            <input type="file" id="fileUpload" name="fileUpload"/>
                            <div id="cntUploadButtons">
                                <input type="button" id="btnUploadFile" class="diaryButtons" value="Upload"/>
                                <input type="button" id="btnCloseUpload" class="diaryButtons" value="Close"/>
                            </div>
                        </form>
                    </div>
                    <!-- --------------------------------------- -->
                    <div id="cntDiaryAttachments">
                    <!--
                        <div class="attachment">
                            <span class="attachmentIcon"><img src="images/audio_icon2.png" /></span>
                            <a href="#attachmentObject1" class="attachmentName">All Time High-Rita Coolidge.mp3</a>
                            <div class="attachmentObjectPopup" id="attachmentObject1">
                                <a href='#' class='close'><img src='images/close_pop.png' class='btnClose' title='Close Window' alt='Close' /></a>
                                <audio controls>
                                    <source src="diary_attachments/All Time High-Rita Coolidge.mp3" type="audio/mpeg" />
                                    Your browser does not support the audio element
                                </audio>
                            </div>
                        </div>
                        <div class="attachment">
                            <span class="attachmentIcon"><img src="images/video_icon2.png" /></span>
                            <a href="#attachmentObject2" class="attachmentName">Broken Hearted Melody -- Sarah Vaughan _in HD_.mp4</a>
                            <div class="attachmentObjectPopup" id="attachmentObject2">
                                <a href='#' class='close'><img src='images/close_pop.png' class='btnClose' title='Close Window' alt='Close' /></a>
                                <video width="400" controls>
                                    <source src="diary_attachments/Broken Hearted Melody -- Sarah Vaughan _in HD_.mp4" type="video/mp4">
                                    Your browser does not support HTML5 video.
                                </video>
                            </div>
                        </div>
                        <div class="attachment">
                            <span class="attachmentIcon"><img src="images/picture_icon2.png" /></span>
                            <a href="#attachmentObject3" class="attachmentName">IMG00052-20110318-1521.jpg</a>
                            <div id="attachmentObject3" class="attachmentObjectPopup">
                                <a href='#' class='close'><img src='images/close_pop.png' class='btnClose' title='Close Window' alt='Close' /></a>
                                <img src="diary_attachments/IMG00052-20110318-1521.jpg" />
                            </div>
                        </div>

                    -->
                    </div>
                    <!-- ----------------------------------------- -->
                </div>

            </div>

        </div>
        <!-- end Main Content -->
		
    </div>

    <!-- footer -->
    <?php include "include/footer.php"; ?>
    <!-- end footer -->


    <script src="js/lib/jquery-1.11.1.min.js"></script>
    <script src="js/lib/jquery-ui.min.js"></script> 
    <script src="js/lib/jquery.form.js"></script>
    <script src="js/basictools.js"></script> 
    <script src="js/main.js"></script> 
    <script src="js/index.js"></script> 

</body>
</html>

