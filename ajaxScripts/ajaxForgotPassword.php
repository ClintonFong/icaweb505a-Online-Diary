<?php
//header("Content-type: text/xml");

require_once    '../include/lib/common.inc.php';
require_once    '../PHPMailer_5.2.4/class.phpmailer.php';

// decide what action to take depending on the client request
$strResponseStatus  = "Failure";
$strResponseMessage = "Request Undefined";
$strResponseData    = "";

$actionTaken        = ( isset( $_REQUEST['action'] ) )? $_REQUEST['action'] : "";
$email              = ( isset( $_REQUEST['email'] ) )? $_REQUEST['email'] : "";

$strNewPassword     = "";


switch( $actionTaken )
{
	case "forgot-password" :	// handles the forgot password request
        $isSuccessful = resetPasswordDB( $email, $strNewPassword, $strResponseMessage );
        $isSuccessful = ( $isSuccessful )? sendEmailPasswordChanged( $email, $strNewPassword, $strResponseMessage ) : false;

        $strResponseStatus = ( $isSuccessful )? "Success" : "Failure";
        break;

	default:
		$strResponseMessage = "Unknown request";		
		
} // switch


$strResponse  = "<status>{$strResponseStatus}</status>";
$strResponse .= "<message><![CDATA[{$strResponseMessage}]]></message>";
$strResponse .= "<data><![CDATA[{$strResponseData}]]></data>";
$strPackage   = "<package>{$strResponse}</package>";
echo $strPackage;

// --------------------------------------------------------------------------------------------------------------
// getDBConnection
// --------------------------------------------------------------------------------------------------------------
function getDBConnection(&$strResponseMessage)
{
    $dbConnection = new mysqli (DB_SERVER, USER_NAME, PASSWORD, DATABASE);

    if( $dbConnection->connect_errno )
    {
        $strResponseMessage = "Connection to database failed";
        trigger_error("Connection to database failed " . $dbConnection->connect_errno );
    }
    return $dbConnection;

} // getDBConnection

// --------------------------------------------------------------------------------------------------------------
// --------------------------------------------------------------------------------------------------------------
// resetPasswordDB
// --------------------------------------------------------------------------------------------------------------
function resetPasswordDB( $email,
                          &$strNewPassword,
                          &$strResponseMessage )
{
    assert( isset( $email) );
    
    $isSuccessful       = false;
    $strResponseMessage = "Reset password unsuccessful";
    
	$dbConnection = getDBConnection( $strResponseMessage ); 

    if( !$dbConnection->connect_errno )
    {
        $strNewPassword = generateTemporaryPassword();
        $sha256Password =  hash('sha256', $strNewPassword);

		$stmtQuery      = "UPDATE icaweb505a_users SET password='{$sha256Password}' WHERE email=?";

        if( $stmt = $dbConnection->prepare( $stmtQuery ) )
        {
            $email = scrubInput( $email, $dbConnection );
            $stmt->bind_param('s', $email );

		    $bSuccess = $stmt->execute();

            if( $bSuccess && ($stmt->affected_rows > 0) )
            { 
                $isSuccessful = true; 
                $strResponseMessage = "Password has been reset to a temporary password.";
            }
            $stmt->close();
        }
        $dbConnection->close();
	}

    return $isSuccessful;

} // resetPasswordDB


// --------------------------------------------------------------------------------------------------------------
// sendEmailPasswordChanged
// --------------------------------------------------------------------------------------------------------------
function sendEmailPasswordChanged( $email,
                                   $strNewPassword,
                                   &$strResponseMessage )
{
    assert( isset( $email) );

    $newResponseMessage = "Sending reset email notification for new password unsuccessful.";
    
    $isSuccessful = false;
    $usersName    = getUsersNameDB  ( $email, $isSuccessful );

    if( $isSuccessful )
    {
        $mail             = new PHPMailer();

        //$mail->IsSMTP();                                            // telling the class to use SMTP
        $mail->SMTPDebug    = 0;                                    // enables SMTP debug information (for testing)
        //$mail->SMTPAuth     = TRUE;                                 // enable SMTP authentication
        //$mail->SMTPSecure   = "ssl";                                // sets the prefix to the server
        //$mail->Host         = "smtp.gmail.com";                     // sets GMAIL as the SMTP server
        //$mail->Port         = 465;                                  // set the SMTP port for the GMAIL server
        $mail->IsHTML(TRUE);

        // gmail account to use to send the email
        //$mail->Username     = "fongclinton.mail.gateway@gmail.com"; 
        //$mail->Password     = "Password001";  

        //$mail->SetFrom("fongclinton.mail.gateway@gmail.com");
        //$mail->AddAddress("Sharon.Carrasco@evocca.com.au", "Sharon Carrasco");
        $mail->AddAddress( $email, $usersName );
        //$mail->AddCC("info@clintonfong.com", "Clinton Fong");

        $mail->Subject  = "Online Diary - Password reset";
        $msg            = "Dear {$usersName},<br><br>";
	    $msg           .= "As requested, your new Temporary Password for Sign-in at Online Diary website is <span style='color:#78655F'>{$strNewPassword}</span><br>";
	    $msg           .= "Please change this password as soon as possible when you next Sign-in.<br><br>";
        $msg           .= "Your friendly support team at Online Diary";

        $mail->Body    = $msg;

	    // Mail it
        if( $mail->Send() )
        {
            $isSuccessful       = true;     
            $newResponseMessage = "Email with new password has been sent to you.";
        }
        else
        {
            //$strResult         .= " Mailer error: {$mail->ErrorInfo}";
        }
    }
		
    $strResponseMessage .= "<br>{$newResponseMessage}";

    return $isSuccessful;

} // sendEmailPasswordChanged

// --------------------------------------------------------------------------------------------------------------
// getUsersNameDB
// --------------------------------------------------------------------------------------------------------------
function getUsersNameDB(    $email, 
                            &$isSuccessful )
{
    assert( isset( $email) );

    $strName        = "";
    $isSuccessful   = FALSE;
	$dbConnection   = getDBConnection( $strResponseMessage ); 

    if( !$dbConnection->connect_errno )
    {
		$stmtQuery  = "SELECT firstname, lastname FROM icaweb505a_users WHERE email=?";

		if( $stmt = $dbConnection->prepare( $stmtQuery ) )
        {
            $email = scrubInput( $email, $dbConnection );
            $stmt->bind_param('s', $email);

            if( $stmt->execute() )
            {
                $stmt->bind_result( $db_firstname, $db_lastname );
		        if( $stmt->fetch() ) 
		        {
                    $strName = $db_firstname . " " . $db_lastname;
                    $isSuccessful = true; 
		        } 
            }
		    $stmt->close(); 	// Free resultset 
        }
        $dbConnection->close();
	}

    return $strName;

} // getUsersNameDB

// --------------------------------------------------------------------------------------------------------------
// generateTemporaryPassword
// --------------------------------------------------------------------------------------------------------------
function generateTemporaryPassword() 
{
    $alphabet       = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $alphaLength    = strlen( $alphabet ) - 1;
    $newPassword    = "";

    for ($i = 0; $i < TEMPORARY_PASSWORD_LENGTH; $i++) 
    {
        $n = rand(0, $alphaLength);
        $newPassword .= $alphabet[$n];
    }
    return $newPassword;

} // generateTemporaryPassword

//---------------------------------------------------------------------------------------------
// srubInput 
//
// Description: scrubs down input value elimaate possible sql injection
//---------------------------------------------------------------------------------------------
function scrubInput($value, $dbConnection)
{
        
    //if( get_magic_quotes_gpc() )    { $value = stripslashes($value); }                                           // Stripslashes


    $value = $dbConnection->real_escape_string( $value );

    //if (!is_numeric($value)) { $value = "'" . $value . "'";  } // Quote if not a number

    return $value;

} // scrubInput



?>