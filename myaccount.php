<?php
    //
    // myaccount.php
    //
    // by Clinton Fong
    //

    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('log_errors_max_length', 0);
    ini_set('error_log', './error_log.txt');

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
    
    $isLoggedIn     = true;
    $currentPage    = "myaccount.php";
    
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
    <link rel='stylesheet' type='text/css' href='css/myAccount.css' />

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

                    <form name='frmUpdateUser' action='' target='_self' method ='post'>
					
                        <input name='actionTaken' type='hidden' value='none' />
                        <input type="hidden" name="idUser"  value="<?php echo $idUser; ?>" />

                        <div id='cntUpdateMember'>
                            <fieldset id='fldsetAccountUpdate'>
                                <legend id='legendAccountUpdate'>Account Update</legend>
                                <div id='cntAccountUpdateDetails'>
                                    <label class='required'>First Name:</label>                        
                                    <input name='firstname' id='firstname' type ='text' class='isValidNormalCharKey' value='<?php echo $objLoginController->firstname; ?>' required /><br />
                                    <label class='required'>Last Name:</label>                        
                                    <input name='lastname' id='lastname' type ='text' class='isValidNormalCharKey' value='<?php echo $objLoginController->lastname; ?>' required /><br />
                                    <label class='required' >Sign-in Email:</label>                        
                                    <input name='signinEmail' id='signinEmail' type ='text' value ='<?php echo $objLoginController->email; ?>' required /><br />

                                    <input name='btnAccountUpdate' id='btnAccountUpdate' type ='button' value='Update &#9658;' /><br />
                                    <div id='ajaxUpdateAccountMessageResponse'></div>
                                </div>
                            </fieldset>

                            <fieldset id='fldsetPasswordUpdate'>
                                <legend id='legendPasswordUpdate'>Password Update</legend>
                                <div id='cntPasswordUpdateDetails'>
                                    <label class='required' >Old Password:</label>                        
                                    <input name='oldPassword' id='oldPassword' type ='password' value='' required /><br />
                                    <br>
                                    <label class='required' >New Password:</label>                        
                                    <input name='newPassword' id='newPassword' type ='password' value='' required /><br />
                                    <label class='required'>Confirm New Password:</label>                        
                                    <input name='confirmPassword' id='confirmPassword' type ='password' value='' required /><br />

                                    <input name='btnPasswordUpdate' id='btnPasswordUpdate' type ='button' value='Update &#9658;' /><br />
                                    <div id='ajaxUpdatePasswordMessageResponse'></div>
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
    <script src="js/myAccount.js"></script> 

</body>
</html>

