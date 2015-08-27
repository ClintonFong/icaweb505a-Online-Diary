<?php
session_start();

require_once    '../include/lib/class.basicDB.inc.php';

//---------------------------------------------------------------------------------------------
class structDiaryDoc
{
    public $idUser      = -1;
    public $date        = "";
    public $text        = "";
    public $attachments = array();
}


//---------------------------------------------------------------------------------------------
class structAttachment
{
    public $filename;
    public $filetype;
    
} // structAttachment

//---------------------------------------------------------------------------------------------
class c_ajaxDiaryController extends c_basicDB
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
    // Documents
    //---------------------------------------------------------------------------------------------
    
    //---------------------------------------------------------------------------------------------
    // getDocumentsForDay
    //
    // Description: gets the documents pertaining to the users day - includes diary text and attachments
	//---------------------------------------------------------------------------------------------
	function getDocumentsForDay( $diaryEntry,
                                 &$diaryDoc )
	{
        assert( isset( $this->db) );

        $isSuccessful = false;

        $diaryDoc = new structDiaryDoc();
        $diaryDoc->idUser = $this->scrubInput( $diaryEntry->idUser );
        $diaryDoc->date   = $this->scrubInput( $diaryEntry->date );

        if( $this->db )
        {
            $stmtQuery  = "SELECT text FROM icaweb505a_diary WHERE userID=? AND date=?";
        
            if ($stmt = $this->db->prepare( $stmtQuery ) )
            {
                $stmt->bind_param( "is", $diaryEntry->idUser, $diaryEntry->date );

		        if( $bSuccess = $stmt->execute())
                {
                    $isSuccessful     = true;
                    $stmt->bind_result( $db_text );

		            if( $stmt->fetch() ) 
		            {
                        $diaryDoc->text   = $db_text;
		            } 
                }
	            $stmt->close(); 	// Free resultset 
            }
            
            // get the attachments
            if( $isSuccessful ) { $isSuccessful = $this->getAttachmentsForDay( $diaryDoc ); }
        }    

        return $isSuccessful;

	} // getDocumentsForDay

    //---------------------------------------------------------------------------------------------
    // getAttachmentsForDay
    //
    // Description: gets the attachments pertaining the users day. called from getDocumentsForDay
	//---------------------------------------------------------------------------------------------
	function getAttachmentsForDay( $diaryDoc )
	{
        assert( isset( $this->db) );
        assert( isset( $diaryDoc) );

        $isSuccessful = false;
     
        if( $this->db )
        {
            $stmtQuery  = "SELECT filename, filetype";
            $stmtQuery .= " FROM icaweb505a_attachments JOIN icaweb505a_diary ON icaweb505a_attachments.diaryID = icaweb505a_diary.idDiary";
            $stmtQuery .= " WHERE icaweb505a_diary.userID=?";
            $stmtQuery .= " AND icaweb505a_diary.date=?";
        
            if ($stmt = $this->db->prepare( $stmtQuery ) )
            {
                $stmt->bind_param( "is", $diaryDoc->idUser, $diaryDoc->date );

		        if( $bSuccess = $stmt->execute())
                {
                    $isSuccessful   = true;                
                    $stmt->bind_result( $db_filename, $db_filetype );

                    while( $stmt->fetch() ) 
		            {
                        $attachment                 = new structAttachment();

                        $attachment->filename       = $db_filename;
                        $attachment->filetype       = $db_filetype;
                        
                        $diaryDoc->attachments[]    = $attachment;
		            } 
                }
	            $stmt->close(); 	// Free resultset 
            }
        }    

        return $isSuccessful;
        
    } // getAttachmentsForDay    

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

        $isSuccessful           = false;
        $isDoInsert             = true;
        $diaryEntry->idDiary    = -1;
    
        if( $this->db )
        {
            // check if document is in database
            //
    	    $stmtQuery  = "SELECT idDiary";
            $stmtQuery .= " FROM icaweb505a_diary";
            $stmtQuery .= " WHERE userID=? AND date=?";

            $diaryEntry->idUser = $this->scrubInput( $diaryEntry->idUser );
            $diaryEntry->date   = $this->scrubInput( $diaryEntry->date );

            if ($stmt = $this->db->prepare( $stmtQuery ) )
            {
                $stmt->bind_param( "is", $diaryEntry->idUser, $diaryEntry->date );

		        if( $isSuccessful = $stmt->execute())
                {
                    $stmt->bind_result( $db_idDiary );
		            if( $stmt->fetch() ) 
		            {
                        $isDoInsert             = false;
                        $diaryEntry->idDiary    = $db_idDiary;
		            } 
                }
	            $stmt->close(); 	// Free resultset 
            }
                
            // do insert or update accordingly
            //
            if( $isSuccessful && $isDoInsert )  { $isSuccessful = $this->insertNewEntry( $diaryEntry );        }
            elseif( $isSuccessful )             { $isSuccessful = $this->updateExistingEntry( $diaryEntry);    }
                            
        }    

        return $isSuccessful;

	} // saveDocumentsForDay

	//---------------------------------------------------------------------------------------------
    // insertNewEntry
	//---------------------------------------------------------------------------------------------
    function insertNewEntry( $diaryEntry )
    {
        assert( isset( $this->db) );
        assert( isset( $diaryEntry->date ) && ( $diaryEntry->date != '' ) );

        $isSuccessful   = false;
        $isDoInsert     = false;

        if( $this->db )
        {
            $diaryEntry->idUser = $this->scrubInput( $diaryEntry->idUser );
            $diaryEntry->date   = $this->scrubInput( $diaryEntry->date );
            $diaryEntry->text   = $this->scrubInput( $diaryEntry->text );

    	    $stmtQuery  = "INSERT INTO icaweb505a_diary ( userID, date, text ) VALUES (?, ?, ?)";
            
            if ($stmt = $this->db->prepare( $stmtQuery ) )
            {
                $stmt->bind_param( "iss", $diaryEntry->idUser, $diaryEntry->date, $diaryEntry->text );

		        if( $bSuccess = $stmt->execute())
                {
                    $isSuccessful = ( $stmt->affected_rows > 0 );
                }
	            $stmt->close(); 	// Free resultset 
            }
        }    

        return $isSuccessful;
        
    } // insertNewEntryAttachments

	//---------------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------
    function updateExistingEntry( $diaryEntry )
    {
        assert( isset( $this->db) );
        assert( isset( $diaryEntry->date ) && ( $diaryEntry->date != '' ) );

        /* ---------------------
         * update existing entry 
         * --------------------- */

        $isSuccessful   = false;

        if( $this->db )
        {
            $diaryEntry->idDiary    = $this->scrubInput( $diaryEntry->idDiary );
            $diaryEntry->text       = $this->scrubInput( $diaryEntry->text );

    	    $stmtQuery  = "UPDATE icaweb505a_diary set text=?";
            $stmtQuery .= " WHERE idDiary=?";
                
            if ($stmt = $this->db->prepare( $stmtQuery ) )
            {
                $stmt->bind_param( "si", $diaryEntry->text, $diaryEntry->idDiary );

		        if( $bSuccess = $stmt->execute())
                {
                    $isSuccessful = ( $stmt->affected_rows > 0 );                  
                }
	            $stmt->close(); 	// Free resultset 
            }
                
        }    

        return $isSuccessful;

    } // updateExistingEntry
    
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
    	    if( $objAjaxDiaryController->getDocumentsForDay( $diaryEntry, $diaryDoc ) )
            {
                $strResponseStatus  = "Success";
                $strResponseMessage = "";
                $strResponseData    = json_encode( $diaryDoc );
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

