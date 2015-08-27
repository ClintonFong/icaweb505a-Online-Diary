<?php
session_start();

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('log_errors_max_length', 0);
ini_set('error_log', './error_log.txt');

require_once    '../include/lib/class.basicDB.inc.php';


//---------------------------------------------------------------------------------------------
class c_ajaxMyAccountController extends c_basicDB
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
    // updateUserDetails
    //
    // Description: updates details pertaining to the user
	//---------------------------------------------------------------------------------------------
	function updateUserDetails( $updateEntry,
                                &$strResponseMessage )
	{
       // echo "In registerNewMember";

        assert( isset( $this->db) );
    
        $isSuccessful       = false;
        $strResponseMessage = "Update Failed";

        if( $this->db )
        {
		    $stmtQuery      = "UPDATE icaweb505a_users SET firstname=?, lastname=?, email=? WHERE idUser=?";

            if( $stmt = $this->db->prepare( $stmtQuery ) )
            {
                $updateEntry->idUser    = $this->scrubInput( $updateEntry->idUser );
                $updateEntry->firstname = $this->scrubInput( $updateEntry->firstname );
                $updateEntry->lastname  = $this->scrubInput( $updateEntry->lastname );
                $updateEntry->email     = $this->scrubInput( $updateEntry->email );
                
                $stmt->bind_param('ssss',   $updateEntry->firstname,
                                            $updateEntry->lastname,
                                            $updateEntry->email,
                                            $updateEntry->idUser);


                if( $bSuccess = $stmt->execute() )
                { 
                    if( $stmt->affected_rows > 0 )  
                    {
                        $isSuccessful = true; 
                        $strResponseMessage = "Updated Successfully";
                    }
                    else
                    {
                        $strResponseMessage = "Nothing updated - details are the same";
                    }
                }
                $stmt->close();
            }
	    }

        return $isSuccessful;
        
        
	} // updateUserDetails


    //---------------------------------------------------------------------------------------------
    // updatePasswordDetails
    //
    // Description: updates details pertaining to the user
	//---------------------------------------------------------------------------------------------
	function updatePasswordDetails( $updateEntry,
                                    &$strResponseMessage )
	{
       // echo "In registerNewMember";
        assert( isset( $this->db) );

        $isSuccessful = false;
        $strResponseMessage = "Update Failed";

        if( $this->db )
        {
            $updateEntry->idUser    = $this->scrubInput( $updateEntry->idUser );
            $sha256OldPassword      = hash('sha256', $updateEntry->oldPassword );
            
            $existingPassword       = $this->getExistingPassword( $updateEntry );
                
            if( $sha256OldPassword != $existingPassword )   { $strResponseMessage = "Update Failed - Old Password incorrect";               }
            else                                            { $isSuccessful = $this->updatePassword( $updateEntry, $strResponseMessage );   } 
            
        } // if( $this->db ) 
 
        return $isSuccessful;

	} // updatePasswordDetails
   
	//---------------------------------------------------------------------------------------------
    function getExistingPassword( $updateEntry )
    {
        assert( isset( $this->db) );

        $password       = "";

        if( $this->db )
        {
            $stmtQuery  = "SELECT password FROM icaweb505a_users WHERE idUser=?";
        
            if ($stmt = $this->db->prepare( $stmtQuery ) )
            {
                $stmt->bind_param( "i", $updateEntry->idUser );

		        if( $stmt->execute())
                {
                    $stmt->bind_result( $db_password );

		            if( $stmt->fetch() ) 
		            {
                        $password = $db_password;
		            } 
                }
	            $stmt->close(); 	// Free resultset 
            }
        }    
        return $password;
        
    } // getExistingPassword()

	//---------------------------------------------------------------------------------------------
    function updatePassword( $updateEntry,
                             &$strResponseMessage )
    {
        assert( isset( $this->db) );

        $isSuccessful   = false;

        if( $this->db )
        {
		    $stmtQuery  = "UPDATE icaweb505a_users SET password=?";
            $stmtQuery .= " WHERE idUser=?";

            if( $stmt = $this->db->prepare( $stmtQuery ) )
            {
                $sha256NewPassword = hash('sha256', $updateEntry->newPassword );
                             
                $stmt->bind_param('si', $sha256NewPassword, $updateEntry->idUser );

		        if( $bSuccess = $stmt->execute() )
                {
                    if( $stmt->affected_rows == 0) { $strResponseMessage = "Old Password is the Same as New Password"; }
                    else
                    { 
                        $isSuccessful = true; 
                        $strResponseMessage = "Updated Successfully";
                    }
                }
                $stmt->close();
            }
        }                
        return $isSuccessful;
        
    } // updatePassword
    
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
    

} // c_ajaxMyAccountController


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
    $updateEntry = json_decode(stripslashes($_POST['data']));
    
    $objAjaxMyAccountController = new c_ajaxMyAccountController();

    switch ( $updateEntry->action )
    {
        case "update-user-details"   :   
           
    	    if( $objAjaxMyAccountController->updateUserDetails( $updateEntry, $strResponseMessage ) ) { $strResponseStatus  = "Success"; }
            else                                                                                      { $strResponseStatus  = "Failure"; }
            break;
        
	    case "update-user-password" :	
        
            if( $objAjaxMyAccountController->updatePasswordDetails( $updateEntry, $strResponseMessage ) )   { $strResponseStatus  = "Success";  }
            else                                                                                            { $strResponseStatus  = "Failure";  }
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

