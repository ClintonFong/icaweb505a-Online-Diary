<?php
    //
    // contactus.php
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

    $isLoggedIn         = true;
   	$idUser             = (isset($_SESSION['icaweb505a-user-id']))? $_SESSION['icaweb505a-user-id'] : "-1";	
    $objLoginController = new c_loginController();
    if( !$objLoginController->isUserLoggedIn( $idUser ) )
    {
        $isLoggedIn     = false;
        //header( "Location: login.php" ); // redirect to login page
    }

    $currentPage    = "contactus.php";
    
?>

<!DOCTYPE html>

<html>

<head>
    <meta charset='utf-8' />
    <meta name='description' content='Online Diary - My Account' />
    <meta name='keywords' content='Online Diary' />
    <meta name='author' content='Clinton Fong' />

    <title>Online Diary</title>

    <link rel='stylesheet' type='text/css' href='css/main.css' />
    <link rel='stylesheet' type='text/css' href='css/contactUs.css' />

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

                <!-- Member Register -->
                <div id='cntMyAccount'>

                    <div id="errorBox">
                        <div id="errorMessages"></div>
                    </div>

                    <form name='frmContactUs' action='' target='_self' method ='post'>
					
                        <input name='actionTaken' type='hidden' value='none' />
                        <input type="hidden" name="idUser"  value="<?php echo $idUser; ?>" />

                        <div id='cntContactUsWrapper'>

                            <fieldset id='fldsetOurContact'>
                                <legend id='legendOurContact'>Our Contact</legend>
                                <div id='cntOurContact'>
                                    <label class='required' >Email:</label>                        
                                    <input name='ourEmail' id='ourEmail' type ='text' value ='cf.evocca.test.s1@gmail.com' disabled="disabled" /><br />
                                </div>
                            </fieldset>

                            <fieldset id='fldsetContactUs'>
                                <legend id='legendContactUs'>Contact Us</legend>
                                <div id='cntContactUs'>
                                    <label class='required'>First Name:</label>                        
                                    <input name='firstname' id='firstname' type ='text' class='isValidNormalCharKey' value='<?php echo $objLoginController->firstname; ?>' required /><br />
                                    <label class='required'>Last Name:</label>                        
                                    <input name='lastname' id='lastname' type ='text' class='isValidNormalCharKey' value='<?php echo $objLoginController->lastname; ?>' required /><br />
                                    <label class='required' >Email:</label>                        
                                    <input name='email' id='email' type ='text' value ='<?php echo $objLoginController->email; ?>' required /><br />
                                    <br />
                                    <label class='required' >Message:</label>                        
                                    <textarea name='message' id='message' required></textarea><br />

                                    <input name='btnContactUsSubmit' id='btnContactUsSubmit' type ='button' value='Submit &#9658;' />
                                    <div id='ajaxContactUsMessageResponse'></div>
                                </div>
                            </fieldset>

                        </div>

                       
                    </form>



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
    <script src="js/contactUs.js"></script> 

</body>
</html>

