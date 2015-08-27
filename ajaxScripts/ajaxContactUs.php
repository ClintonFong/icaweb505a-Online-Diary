<?php
session_start();

require_once    '../include/lib/class.basicDB.inc.php';
require_once    '../PHPMailer_5.2.4/class.phpmailer.php';

//---------------------------------------------------------------------------------------------
class c_ajaxContactUsController extends c_basicDB
{
    
	//---------------------------------------------------------------------------------------------
	// constructors 
	//---------------------------------------------------------------------------------------------
	function __construct()
	{
		parent::__construct();
        
	} // __construct

	//---------------------------------------------------------------------------------------------
	// destructors
	//---------------------------------------------------------------------------------------------
	function __destruct()
	{
		parent::__destruct();	
		
	} // __destruct

    
    //---------------------------------------------------------------------------------------------
    // submitContactUsForm
    //
    // Description: inserts contact us form details into our database for us to review
    //              and sends an email to notify us of the message
	//---------------------------------------------------------------------------------------------
	function submitContactUsForm(   $contactUsEntry,
                                    &$strResponseMessage )
    {
        $isSuccessful       = false;
        $isSuccessful       = $this->insertDBContactUsMessage( $contactUsEntry );
        $isSuccessful       = ( $isSuccessful )? $this->sendEmailNotifier( $contactUsEntry ) : false;
        $strResponseMessage = ( $isSuccessful )? "Message Successfully Sent" : "Submission Failed";
            
        return $isSuccessful;
            
    } // submitContactUsForm
    
    //---------------------------------------------------------------------------------------------
    // insertDBContactUsMessage
    //
    // Description: inserts contact us form details into our database for us to review
	//---------------------------------------------------------------------------------------------
	function insertDBContactUsMessage( $contactUsEntry )
	{
       // echo "In submitContactUsForm";
        assert( isset( $this->db) );
        assert( isset( $contactUsEntry ) && ( $contactUsEntry != '' ) );

        $isSuccessful   = false;
        
        if( $this->db )
        {
            $contactUsEntry->idUser     = $this->scrubInput( $contactUsEntry->idUser );
            $contactUsEntry->firstname  = $this->scrubInput( $contactUsEntry->firstname );
            $contactUsEntry->lastname   = $this->scrubInput( $contactUsEntry->lastname );
            $contactUsEntry->email      = $this->scrubInput( $contactUsEntry->email );
            $contactUsEntry->message    = $this->scrubInput( $contactUsEntry->message );

    	    $stmtQuery  = "INSERT INTO icaweb505a_contactus ( userID, firstname, lastname, email, message ) VALUES (?, ?, ?, ?, ?)";
            
            if( $stmt = $this->db->prepare( $stmtQuery ) )
            {
                $stmt->bind_param( "issss", $contactUsEntry->idUser, 
                                            $contactUsEntry->firstname, 
                                            $contactUsEntry->lastname, 
                                            $contactUsEntry->email, 
                                            $contactUsEntry->message );

		        if( $stmt->execute())
                {
                    $isSuccessful = ( $stmt->affected_rows > 0 );
                }
	            $stmt->close(); 	// Free resultset 
            }
        }    


        return $isSuccessful;

	} // insertDBContactUsMessage


    // --------------------------------------------------------------------------------------------------------------
    // sendEmailNotifier
    // --------------------------------------------------------------------------------------------------------------
    function sendEmailNotifier( $contactUsEntry )
    {
        assert( isset( $contactUsEntry ) && ( $contactUsEntry != "") );
    
        $isSuccessful = false;


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
        $mail->AddAddress( CONTACT_US_EMAIL, "Contact Us" );
        //$mail->AddCC("info@clintonfong.com", "Clinton Fong");

        $mail->Subject  = "Contact Us Message from Online Diaries Website";
        $msg            = "Firstname: {$contactUsEntry->firstname} <br />";
        $msg           .= "Lastname: {$contactUsEntry->lastname} <br />";
	    $msg           .= "Email: {$contactUsEntry->email}<br />";
	    $msg           .= "Message: {$contactUsEntry->message}<br>";

        $mail->Body    = $msg;

	    // Mail it
        $isSuccessful = $mail->Send(); 

/*                
        if( !$isSuccessful )
        {
            //$strResult         .= " Mailer error: {$mail->ErrorInfo}";
        }
*/
        return $isSuccessful;

    } // sendEmailNotifier

    
    
	//---------------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------
    // debugging tools
	//---------------------------------------------------------------------------------------------
    function __displayAttributes()
    {
        echo "<br>
            <br>
            ";

    } // __displayAttributes
    

} // c_ajaxContactUsController


//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
// Link to the outside world - the view/controller that called this ajax controller
//-------------------------------------------------------------------------------------

$strResponseStatus  = "";
$strResponseMessage = "No package data";
$strResponseData    = "";

$jsonDiaryEntryPackage = "";

if (isset($_POST['data'])) 
{  
    $contactUsEntry = json_decode(stripslashes($_POST['data']));
    
    $objAjaxContactUsController = new c_ajaxContactUsController();

    switch ( $contactUsEntry->action )
    {
        case "contact-us-submission"   :   
           
    	    if( $objAjaxContactUsController->submitContactUsForm( $contactUsEntry, $strResponseMessage ) )  { $strResponseStatus  = "Success"; }
            else                                                                                            { $strResponseStatus  = "Failure"; }
            break;
        
        default:
            $strResponseMessage = "Request Undefined";


    } // switch

}

$strResponse  = "<status>{$strResponseStatus}</status>";
$strResponse .= "<message>{$strResponseMessage}</message>";
$strResponse .= "<data><![CDATA[{$strResponseData}]]></data>";
$strPackage   = "<package>{$strResponse}</package>";
echo $strPackage;

?>

