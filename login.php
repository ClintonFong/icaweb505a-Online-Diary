<?php
    //
    // login.php
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
	require_once 	'include/lib/class.loginController.inc.php';
    require_once    'PHPMailer_5.2.4/class.phpmailer.php';


   	$actionTaken        = (isset($_POST['actionTaken']))? $_POST['actionTaken'] : '';
    $signinAttempt      = 0;
    $registerAttempt    = 0;
    $signinEmail        = '';

    $objLoginController = new c_loginController();
    
    if( $actionTaken == 'header-signup' )
    {
        $signinAttempt = 1;
    }   
    elseif ($actionTaken == 'validate-member-login')
    {

   	    $signinEmail    = (isset($_POST['signinEmail']))? $_POST['signinEmail'] : '';
   	    $password       = (isset($_POST['password']))? $_POST['password'] : '';

        if( $objLoginController->isLoginValid( $signinEmail, $password ) )
        {
            $objLoginController->flagLoggedIn( $signinEmail );
            $_SESSION['icaweb505a-user-id'] = $objLoginController->idUser; 
            header( "Location: index.php" ); // redirect to logged-in page
        }
        else
        {
            $signinAttempt = 1;
        }

    }
    elseif ( $actionTaken == 'register' )
    {
   	    $firstname      = (isset($_POST['firstname']))? $_POST['firstname'] : '';
   	    $lastname       = (isset($_POST['lastname']))? $_POST['lastname'] : '';
   	    $signinEmail    = (isset($_POST['signinEmail']))? $_POST['signinEmail'] : '';
   	    $password       = (isset($_POST['password']))? $_POST['password'] : '';


        $idUser = '-1';
        if( $objLoginController->registerNewUser( $firstname, $lastname, $signinEmail, $password, $idUser ) )
        {
            $objLoginController->flagLoggedIn( $signinEmail );
            $_SESSION['icaweb505a-user-id'] = $objLoginController->idUser; 
            header( "Location: index.php" ); // redirect to logged-in page
        }
        else
        {
            $registerAttempt = 1;
        }
    }
    else //if( $actionTaken == 'header-signout' )
    {
       	$userID = (isset($_SESSION['icaweb505a-user-id']))? $_SESSION['icaweb505a-user-id'] : "-1";	
        if( $userID != '-1' )
        {
            // reset all session variables and flag database as user logged out
            // return to statelessness
            //
            $_SESSION['icaweb505a-user-id'] = '-1';   // remove the member ID
            $_SESSION = array();                        // clear the variables
            session_destroy();                          // destroy the session itself
            $objLoginController->flagLoggedOut( $userID );
            setcookie('PHPSESSID', '', time()-3600, '/', '', 0, 0);
        }

    } // if
    
    $currentPage    = "login.php";
    
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
    <link rel='stylesheet' type='text/css' href='css/login.css' />
    <link rel='stylesheet' type='text/css' href='css/popupForgotPassword.css' />


</head>

<body>

    <!-- header -->
    <?php include "include/header.php"; ?>
    <!-- end header -->
        
    <div id="mainWrapper">

        <!-- Main Content -->
		<div id="main-content">

    		<div id='main-panel'>

                <!-- Member Sign-In -->
                <div id='cntSigninBox'>
                    <form name='frmSignin' action='login.php' target='_self' method ='post'>

                        <input type='hidden' name='actionTaken' value='validate-member-login' />
                        <input type='hidden' name='signinAttempt' value='<?php echo $signinAttempt; ?>' id='signinAttempt' />

                        <div id='cntRegisterHref'>
                            <a id='aRegister' href ='#' class='toggleRegister'>*Register</a>
                        </div>
                            
                        <fieldset id='fldsetSignin'>
                            <legend id='legendSignin'>Diary Sign in</legend>
                            <div id='cntSigninDetails'>
                                <label>Sign-in Email:</label>                        
                                <input name='signinEmail' id='signinSigninEmail' type ='text' value='<?php echo $signinEmail; ?>' /><br />
                                <label>Password:</label>                        
                                <input name='password' id='signinPassword' type ='password' value='' /><br />
                                <input id='btnSignin' class='btn' type='button' value='Sign In' />
                                <a id='aForgotPassword' href='#forgotPasswordBox'>Forgot your Password?</a> 
                                <!-- <a id='aForgotPassword' href='javascript:doForgotPassword();'>Forgot your Password?</a> -->
                                <?php
                                    if ($signinAttempt == 1) 
                                    {
                                        echo "<br><label id='lblErrMsgSignin' class='required important errMsg'>Login unsuccessful</label>";
                                    }
                                ?>
                            </div>
                        </fieldset>


                    </form>
                </div>
                <!-- Member /Sign-In -->

                <!-- Member Register -->
                <div id='cntRegisterBox'>
                    <form name='frmRegister' action='login.php' target='_self' method ='post'>

                        <input type='hidden' name='actionTaken' value='register' />
                        <input type='hidden' name='registerAttempt' value='<?php echo $registerAttempt; ?>' id='registerAttempt' />

                        <div id='cntSigninHref'>
                            <a id='aSignin' href ='#' class='toggleRegister' >*Sign-In</a>
                        </div>

                        <fieldset id='fldsetRegister'>
                            <legend id='legendRegister'>Register for Diary</legend>
                            <div id='cntRegisterDetails'>
                                <label class='required'>First Name:</label>                        
                                <input name='firstname' id='registerFirstname' type ='text' value='<?php echo $firstname; ?>' /><br />
                                <label class='required'>Last Name:</label>                        
                                <input name='lastname' id='registerLastname' type ='text' value='<?php echo $lastname; ?>' /><br />
                                <label class='required' >Sign-in Email:</label>                        
                                <input name='signinEmail' id='registerSigninEmail' type='text' value='<?php echo $signinEmail; ?>' /><br />
                                <label class='required' >Password:</label>                        
                                <input name='password' id='registerPassword' type ='password' value='' /><br />
                                <label class='required'>Confirm Password:</label>                        
                                <input name='confirmPassword' id='registerConfirmPassword' type='password' value='' /><br />
                                <input id='btnRegister' class='btn' type ='button' value='Register' />

                                <?php
                                    if ($registerAttempt == 1) 
                                    {
                                        echo "<br><label id='lblErrMsgRegister' class='required important'>Register unsuccessful:: Sign-in email already taken</label>";
                                    }
                                ?>

                            </div>


                        </fieldset>

                    </form>
                </div>
                <!-- /Member Register -->

                <!-- Forgot Email -->
                <div id='forgotPasswordBox' class='forgotPasswordPopup'>
                    <a href='#' class='close'><img src='images/close_pop.png' class='btnClose' title='Close Window' alt='Close' /></a>
                    <form name='frmForgotPassword' action='#' method='post' class='signin'>

                        <fieldset class='textbox'>
            	            <label>
                                <span>Please enter your Sign-in Email address<br> and your new password will be emailed to you.</span>
                                <input id='forgotPasswordSigninEmail' name='signinEmail' value='' type='text' autocomplete='on' placeholder='Sign-in Email'>
                            </label>

                            <button id='btnSend' class='button' type='button'>Send</button><br>
                            <div id='ajaxForgotPasswordMessageResponse'></div>
                        </fieldset>

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
    <script src="js/basictools.js"></script> 
    <script src="js/main.js"></script>
    <script src="js/login.js"></script> 
    <script src="js/popupForgotPassword.js"></script>

</body>
</html>

