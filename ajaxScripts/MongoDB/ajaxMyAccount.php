<?php
session_start();

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
            $users      = $this->db->selectCollection( "users" );
            
            $filter     = array( "_id" => new MongoId( $updateEntry->idUser ) );
            $user       = $users->findOne( $filter );

            if( count( $user ) > 0 )            
            {

                $update = array( '$set' => array(   "firstname" => $updateEntry->firstname,
                                                    "lastname"  => $updateEntry->lastname,
                                                    "email"     => $updateEntry->email  ) );

                $wResult = $users->update( $filter, $update );    

                if( $wResult['nModified'] == 1 )
                {
                    $isSuccessful = true;                
                    $strResponseMessage = "Updated Successfully";
                }
                else
                {
                    $strResponseMessage = "Nothing updated - details are the same";
                }
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
            $users      = $this->db->selectCollection( "users" );
            
            $filter     = array( "_id" => new MongoId( $updateEntry->idUser) );
            $user       = $users->findOne( $filter );

            if( count( $user ) > 0 )            
            {
                if( hash('sha256', $updateEntry->oldPassword ) == $user['password'] )
                {
                    $update = array( '$set' => array(   "password" => hash('sha256', $updateEntry->newPassword )  ) );
                    $wResult = $users->update( $filter, $update );    
                    
                    if( $wResult['nModified'] == 1 )
                    {
                        $isSuccessful = true;                
                        $strResponseMessage = "Updated Successfully";
                    }
                    else
                    {
                        $strResponseMessage = "Nothing updated - Password is the Same as Old one";
                    }                       
                }                
                else
                {
                    $strResponseMessage = "Update Failed - Old Password incorrect";
                }
            }
            else
            {
                $strResponseMessage = "Update Failed - User does not exist";
            }
                
        }    

        return $isSuccessful;

	} // updatePasswordDetails
    
   
    
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

