<?php
session_start();

require_once    '../include/lib/class.basicDB.inc.php';


//---------------------------------------------------------------------------------------------
class c_ajaxDiaryController extends c_basicDB
{
    public $diaryDoc    = "";
    
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
    // Documents
    //---------------------------------------------------------------------------------------------
    
    //---------------------------------------------------------------------------------------------
    // getDocumentsForDay
    //
    // Description: gets the document pertaining 
	//---------------------------------------------------------------------------------------------
	function getDocumentsForDay( $date )
	{

        assert( isset( $this->db) );

        $isSuccessful = false;

        if( $this->db )
        {
            $diary          = $this->db->selectCollection( "diary" );
            $filter         = array("date" => $date );
            $this->diaryDoc = $diary->findOne( $filter );
            $isSuccessful   = true;                
        }    

        return $isSuccessful;

	} // getDocumentsForDay


    //---------------------------------------------------------------------------------------------
    // saveDocumentsForDay
    //
    // Description: Saves the document entry for a given day 
    //              Assumes the member variables are updated before hand
	//---------------------------------------------------------------------------------------------
	function saveDocumentsForDay( $diaryEntry )
	{
        assert( isset( $this->db) );
        assert( isset( $diaryEntry->date ) && ( $diaryEntry->date != '' ) );

        $isSuccessful = FALSE;

        if( $this->db )
        {
            $diary      = $this->db->selectCollection( "diary" );

            $filter     = array("date" => $diaryEntry->date );
            $docExist   = $diary->findOne( $filter );

            if( count( $docExist ) == 0 )            
            {
                /* ----------------
                 * insert new entry 
                 * ---------------- */
                $entry = array( "date"          => $diaryEntry->date,
                                "text"          => $diaryEntry->text,
                                "attachments"   => array() );
                $idEntry = $diary->insert( $entry );    
            }
            else
            {
                /* ---------------------
                 * update existing entry 
                 * --------------------- */
                $filter     = array( "date"     => $diaryEntry->date );         
                $update     = array( '$set'     => array( "text" => $diaryEntry->text ) );
                $wResult    = $diary->update( $filter, $update );           
            }
            $isSuccessful = true;                
        }    

        return $isSuccessful;

	} // saveDocumentsForDay

   
    
	//---------------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------
    // debugging tools
	//---------------------------------------------------------------------------------------------
    function __displayAttributes()
    {
        echo "<br>
            idEntry= {$this->idEntry}<br>
            date = {$this->date}<br>
            text = {$this->text}<br>
            videos = {$this->videos}<br>
            audio = {$this->audios}<br>
            <br>
            ";

    } // __displayAttributes
    

    

} // c_ajaxDiaryController


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
    $diaryEntry = json_decode(stripslashes($_POST['data']));
    
    $objAjaxDiaryController = new c_ajaxDiaryController();

    switch ( $diaryEntry->action )
    {
        case "get-diary-entry"   :   // gets diary entry
    	    if( $objAjaxDiaryController->getDocumentsForDay( $diaryEntry->date ) )
            {
                $strResponseStatus  = "Success";
                $strResponseMessage = "";
                $strResponseData    = json_encode( $objAjaxDiaryController->diaryDoc );
            }
            else
            {
                $strResponseStatus  = "Failure";
                $strResponseMessage = "";
            }
            break;
        
	    case "save-diary-entry" :	// saves the diary entry
        
            if( $objAjaxDiaryController->saveDocumentsForDay( $diaryEntry ) )
            {
                $strResponseStatus  = "Success";
                $strResponseMessage = "Saved Successfully";
            }
            else
            {
                $strResponseStatus  = "Failure";
                $strResponseMessage = "Unable to save document";
            }
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

