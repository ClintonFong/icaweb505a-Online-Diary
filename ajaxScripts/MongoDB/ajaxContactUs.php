<?php
session_start();

require_once    '../include/lib/class.basicDB.inc.php';


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
	//---------------------------------------------------------------------------------------------
	function submitContactUsForm(   $contactUsEntry,
                                    &$strResponseMessage )
	{
       // echo "In submitContactUsForm";

        assert( isset( $this->db) );

        $isSuccessful       = false;
        $strResponseMessage = "Submission Failed";
        
        if( $this->db )
        {
            $contactUs  = $this->db->selectCollection( "contactUs" );
            

            $entry = array( "iduser"    => $contactUsEntry->idUser,
                            "firstname" => $contactUsEntry->firstname,
                            "lastname"  => $contactUsEntry->lastname,
                            "email"     => $contactUsEntry->email,
                            "message"   => $contactUsEntry->message );

            $idEntry = $contactUs->insert( $entry );    
            $isSuccessful = true;                
            $strResponseMessage = "Updated Successfully";
        }    

        return $isSuccessful;

	} // submitContactUsForm


    
    
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

