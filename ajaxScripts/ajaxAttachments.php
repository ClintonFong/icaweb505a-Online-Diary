<?php

session_start();

require_once    '../include/lib/class.basicDB.inc.php';


class structAttachment
{
    public $fileName    = "";
    public $fileType    = "";
    public $fileExt     = "";
}

//---------------------------------------------------------------------------------------------
class c_ajaxAttachmentsController extends c_basicDB
{
    public $Attachments         = array();
  
    public $idUser              = "";
    public $date                = "";
    
    
	//---------------------------------------------------------------------------------------------
	// constructors 
	//---------------------------------------------------------------------------------------------
	function __construct()
	{
		parent::__construct();

        $path = "../" . ATTACHMENTS_DIR ;
		$this->createPath( $path );
		
	} // __construct

	//---------------------------------------------------------------------------------------------
	// destructors
	//---------------------------------------------------------------------------------------------
	function __destruct()
	{
		parent::__destruct();	
		
	} // __destruct

   
    //---------------------------------------------------------------------------------------------
    // Attachments
    //---------------------------------------------------------------------------------------------

    //------------------------------------------------------------------------------------------------------
    /** 
     * recursively create a long directory path
     * taken and modified from : http://stackoverflow.com/questions/2303372/create-a-folder-if-it-doesnt-already-exist
     */
    function createPath( $path ) 
    {
        if( is_dir($path) ) { return true; }
        $prev_path  = substr( $path, 0, strrpos($path, '/', -2) + 1 );
        $return     = $this->createPath( $prev_path );
        return ( $return && is_writable($prev_path) ) ? mkdir( $path ) : false;
    
    } // createPath

    
    //------------------------------------------------------------------------------------------------------
    function checkUploadFileForErrors(  $file, 
                                        &$strResponseMessage )
    {
	    $bIsOk = TRUE;
	
	    if (!isset( $file ))
	    {
		    $bIsOk = FALSE;
		    $strResponseMessage .= "The uploaded file error...please try again";
	    }
	    elseif( $file['error'] == UPLOAD_ERR_INI_SIZE )
	    {
		    $bIsOk= FALSE;
		    $strResponseMessage .= "The uploaded file  exceeds the upload_max_filesize directive in php.ini";
	    }
	    elseif( $file['error'] == UPLOAD_ERR_FORM_SIZE )
	    {
		    $bIsOk= FALSE;
		    $strResponseMessage .= "The uploaded file exceeds the maximum suggested file size of 1 megabyte.";
	    }
	    elseif( $file['error'] == UPLOAD_ERR_PARTIAL )
	    {
		    $bIsOk= FALSE;
		    $strResponseMessage .= "The uploaded file was partially uploaded";
	    }
	    elseif( $file['error'] == UPLOAD_ERR_NO_FILE )
	    {
		    $bIsOk= FALSE;
		    $strResponseMessage .= "No File was uploaded";
	    }
	    elseif( $file['error'] == UPLOAD_ERR_NO_TMP_DIR )
	    {
		    $bIsOk= FALSE;
		    $strResponseMessage .= "Missing temporary folder.";
	    }
	
	    return $bIsOk;
			
    } // checkUploadFileForErrors

  
    //------------------------------------------------------------------------------------------------------
    function moveUploadFile(    $file, 
                                &$strResponseMessage )
    {
        $bSuccess   = false;
        
        $fileName   = $file['name'];
        $fileSize   = $file['size'];
        $fileTmp    = $file['tmp_name'];
        $fileType   = $file['type'];   

        $formats    = array( "mp4", "mp3", "jpg", "jpeg", "png", "gif", "bmp", "doc", "docx", "pdf", "txt" );
        $fileExt    = strtolower( pathinfo( $fileName, PATHINFO_EXTENSION ) );

        if( in_array( $fileExt, $formats ) )
        {
            $attachment = new structAttachment();
            
            // create file name   
            $attachment->fileName   = $this->idUser . "_" . time() . "_". strtolower($fileName); 
            $attachment->fileType   = $this->getFileType( $attachment->fileName );
            $attachment->fileExt    = $fileExt;
            $fileDestination        = "../" . ATTACHMENTS_DIR . $attachment->fileName;

            // move the file and change permissions
            if( move_uploaded_file( $fileTmp, $fileDestination ) )
            {
                chmod( $fileDestination, 0755 );
            }           
            $bSuccess               = true;
            $this->Attachments[]    = $attachment;
        }
        else
        {
            $strResponseMessage .= "File {$fileName} is not an accepted file";
        }
 
        return $bSuccess;
        
    } // moveUploadFile

	//---------------------------------------------------------------------------------------------
    // getFileType
    //---------------------------------------------------------------------------------------------
    function getFileType( $fileName )
    {
        $formats    = array( "mp4", "mp3", "jpg", "jpeg", "png", "gif", "bmp", "doc", "docx" );
        $fileExt    = strtolower( pathinfo( $fileName, PATHINFO_EXTENSION ) );

        switch( $fileExt )
        {
            case "mp4"  : 
                return "video"; 
                break;
               
            case "mp3"  :
                return "audio";
                break;
                
            case "jpg"  :
            case "jpeg" :
            case "png"  :
            case "gif"  :
            case "bmp"  :
                return "image";
                break;

            case "doc"  :
            case "docx" :
            case "pdf"  :
            case "txt"  :
                return "document";
                break;
        }    

        return "";
        
    } // getFileType

	//---------------------------------------------------------------------------------------------
    // getAttachmentFiles
    //
    // Description: - won't be needed
	//---------------------------------------------------------------------------------------------
	function getAttachmentFiles()
	{
        assert( isset( $this->db ) );
        
        $isSuccess = false;
    	return $isSuccess;

	} // getAttachmentFile
    
    //---------------------------------------------------------------------------------------------
    // insertAttachmentFile
    //
    // Description: insert an attachment file into the database
    //         pre: a call to checkUploadFileForErrors & moveUploadFile with array 
    //              $this->photoFilenames[] populated with filenames of the uploaded files
	//---------------------------------------------------------------------------------------------
	function insertAttachmentFile()
	{
        
        assert( isset( $this->db) );
        assert( isset( $this->date ) && ( $this->date != '' ) );

        $isSuccessful       = false;
        $isCreateDiaryEntry = true;
        $idDiary            = -1;
    
        if( $this->db )
        {
            // check if document is in database
            //
    	    $stmtQuery  = "SELECT idDiary";
            $stmtQuery .= " FROM icaweb505a_diary";
            $stmtQuery .= " WHERE userID=? AND date=?";

            $this->idUser = $this->scrubInput( $this->idUser );
            $this->date   = $this->scrubInput( $this->date );

            if ($stmt = $this->db->prepare( $stmtQuery ) )
            {
                $stmt->bind_param( "is", $this->idUser, $this->date );

		        if( $isSuccessful = $stmt->execute())
                {
                    $stmt->bind_result( $db_idDiary );
		            if( $stmt->fetch() ) 
		            {
                        $isCreateDiaryEntry = false;
                        $idDiary            = $db_idDiary;
		            } 
                }
	            $stmt->close(); 	// Free resultset 
            }
                
            // do insert attachment
            //
            if( $isSuccessful && $isCreateDiaryEntry )  { $isSuccessful = $this->insertNewDiaryEntry( $idDiary ); } // create entry in diary
            if( $isSuccessful )                         { $isSuccessful = $this->insertNewAttachment( $idDiary ); }
                            
        }    
        
        return $isSuccessful;
   
	} // insertAttachmentFile
    
	//---------------------------------------------------------------------------------------------
    // insertNewDiaryEntry - need to have a diary entry first before adding any attachments
	//---------------------------------------------------------------------------------------------
    function insertNewDiaryEntry( &$idDiary )
    {
        assert( isset( $this->db) );
        assert( isset( $this->date ) && ( $this->date != '' ) );

        $isSuccessful   = false;
        $isDoInsert     = false;

        if( $this->db )
        {
            $this->idUser   = $this->scrubInput( $this->idUser );
            $this->date     = $this->scrubInput( $this->date );

    	    $stmtQuery  = "INSERT INTO icaweb505a_diary ( userID, date, text ) VALUES (?, ?, '')";
            
            if ($stmt = $this->db->prepare( $stmtQuery ) )
            {
                $stmt->bind_param( "is", $this->idUser, $this->date );

		        if( $bSuccess = $stmt->execute())
                {
                    $isSuccessful   = ( $stmt->affected_rows > 0 );
                    $idDiary        = $stmt->insert_id;
                }
	            $stmt->close(); 	// Free resultset 
            }
        }    

        return $isSuccessful;
        
    } // insertNewDiaryEntry
  
	//---------------------------------------------------------------------------------------------
    // insertNewAttachment 
	//---------------------------------------------------------------------------------------------
    function insertNewAttachment( $idDiary )
    {
        assert( isset( $this->db) );
        assert( $idDiary > 0 );

        $isSuccessful   = false;
        $isDoInsert     = false;

        if( $this->db )
        {
            $idDiary    = $this->scrubInput( $idDiary );
            $filename   = $this->scrubInput( $this->Attachments[0]->fileName );
            $filetype   = $this->scrubInput( $this->Attachments[0]->fileType );

    	    $stmtQuery  = "INSERT INTO icaweb505a_attachments ( diaryID, filename, filetype ) VALUES (?, ?, ?)";
            
            if ($stmt = $this->db->prepare( $stmtQuery ) )
            {
                $stmt->bind_param( "iss", $idDiary, $filename, $filetype );

		        if( $bSuccess = $stmt->execute())
                {
                    $isSuccessful = ( $stmt->affected_rows > 0 );
                }
	            $stmt->close(); 	// Free resultset 
            }
        }    

        return $isSuccessful;                
        
    } // insertNewAttachment
    
    //---------------------------------------------------------------------------------------------
    // deleteAttachmentFile
    //
    // Description: deletes an attachment file from the database
	//---------------------------------------------------------------------------------------------
	function deleteAttachmentFile( $filename )
	{
        assert( isset( $this->db) );

        $isSuccessful = FALSE;

        if( $this->db )
        {

            $filetype   = $this->scrubInput( $this->Attachments[0]->fileType );

    	    $stmtQuery  = "DELETE FROM icaweb505a_attachments WHERE filename=?";
            
            if ($stmt = $this->db->prepare( $stmtQuery ) )
            {
                $stmt->bind_param( "s", $filename );

		        if( $bSuccess = $stmt->execute())
                {
                    $isSuccessful = ( $stmt->affected_rows == 1 );
                }
	            $stmt->close(); 	// Free resultset 
            }
            
/*            
            $diary      = $this->db->selectCollection( "diary" );
         
            $filter     = array( "date" => $this->date );
            $update     = array( '$pull' => array( "attachments" => array( 'filename' => $filename ) ) );
            $wResult    = $diary->update( $filter, $update );           
*/            
            
            //$retFields  = array( "attachments" );
            //$doc        = $diary->findOne( $filter, $retFields );
            

            $isSuccessful = true;                
        }    

        return $isSuccessful;
        
	} // deleteAttachmentFile

    
    //---------------------------------------------------------------------------------------------
    // unlinkAttachmentFile
    //
    // Description: unlinks a photo from the file system and deletes it from the database
	//---------------------------------------------------------------------------------------------
	function unlinkAttachmentFile( $id )
	{
        // if whichPhoto = 0 or empty - delete all photos
        
        $isSuccess = false;
        
        if( $photo = $this->getChallengePhoto( $idChallengePhoto ) )
        {
            // delete the photo file from the filesystem
            if( ( $whichPhoto == 0 ) || ( $whichPhoto == 1 ) )
            {
                $filePath = "../" . ATTACHMENTS_DIR . $this->attachmentFilename; 
                $isSuccess =  unlink( $filePath ); 
            }
            
            // delete the attachment from the database
            if( $isSuccess ) 
            { 
                if( $whichPhoto == 0 )  { $isSuccess = $this->deleteChallengePhotoDB( $idChallengePhoto );              }
                else                    { $isSuccess = $this->removeChallengePhotoDB( $idChallengePhoto, $whichPhoto ); }
            }
        }
        
        return $isSuccess;
        
    } // unlinkAttachmentFile
    
   
    
} // class c_ajaxAttachmentsController extends c_basicDB


//---------------------------------------------------------------------------------------------

$strResponseStatus  = "Request Undefined";
$strResponseMessage = "";
$strResponseData    = "";

$action                     = "";
$filename                   = "";
$objAttachmentsController   = new c_ajaxAttachmentsController();

if( isset($_POST['data'])) 
{  
    $dataPackage                        = json_decode(stripslashes($_POST['data']));
    $action                             = $dataPackage->action;
    $filename                           = $dataPackage->filename; 
    $objAttachmentsController->date     = $dataPackage->date;    
}
else
{    
    $action                             = (isset($_POST['action']))?    $_POST['action']            : '';
    $objAttachmentsController->idUser   = (isset($_POST['idUser']))?    $_POST['idUser']            : '';
    $objAttachmentsController->date     = (isset($_POST['date']))?      $_POST['date']              : '';
}


switch( $action )
{
    case "upload-attachment-file": 
        $strResponseStatus  = "Success"; 
        
        if( count( $_FILES ) > 0 )
        {        
           
            // process the files
            //
            foreach( $_FILES as $file )
            {   
                if( !$objAttachmentsController->checkUploadFileForErrors( $file, $strResponseMessage ) ) 
                {
                    $strResponseStatus   = "Failure"; 
                }
                else if( !$objAttachmentsController->moveUploadFile( $file, $strResponseMessage ) )
                {
                    $strResponseStatus   = "Failure"; 
                }
                
            } // foreach

            // update the database
            //
            if( $strResponseStatus == "Success"  )
            {
                if( $objAttachmentsController->insertAttachmentFile( ) )
                {
                    $strResponseData    = json_encode( $objAttachmentsController->Attachments[0] ); 
                    
                }
                else
                {
                    $strResponseStatus   = "Failure"; 
                    $strResponseMessage .= "Database insert failed.";
                }
            }
        }
        else
        {
            $strResponseStatus  = "Failure"; 
            $strResponseMessage = "No File";
        }
        break; // case upload-photos
/*
    case "get-attachment-file":
        $strResponseStatus   = "Failure"; 
        $objAttachmentsController  = new c_ajaxAttachmentsController( $objChallengeStruct );
        if( $objAttachmentsController->getAttachmentFile() )
        {
            $strResponseStatus   = "Success"; 
        }
        break;

  
    case "update-photos": 
        $strResponseStatus  = "Success"; 
        
        if( count( $_FILES ) > 0 )
        {        
            $objThumbnailCreator           = new c_createThumbnailImage();
            $objChallengePhotosController  = new c_ajaxChallengePhotosController( $objChallengeStruct );

            // process the files
            //
            foreach( $_FILES as $tagName => $file )
            {   
                if( !$objChallengePhotosController->checkUploadPhotoForErrors( $file, $strResponseMessage ) ) 
                {
                    $strResponseStatus   = "Failure"; 
                    $strResponseMessage .= "<br>";
                }
                else if( !$objChallengePhotosController->moveUploadPhotoFile( $tagName, $file, $strResponseMessage ) )
                {
                    $strResponseStatus   = "Failure"; 
                    $strResponseMessage .= "<br>";
                }
                else if( !$objChallengePhotosController->createPhotoFileThumbnail( $tagName, $objThumbnailCreator, $strResponseMessage ) )
                {
                    $strResponseStatus   = "Failure"; 
                    $strResponseMessage .= "<br>";
                }
                
            } // foreach

            // update the database
            //
            if( $strResponseStatus == "Success"  )
            {
                if( !$objChallengePhotosController->updateChallengePhoto( $idChallengePhoto ))
                {
                    $strResponseStatus   = "Failure"; 
                    $strResponseMessage .= "Database update failed.";
                }
            }
        }
        else
        {
            $strResponseStatus  = "Failure"; 
            $strResponseMessage = "No File";
        }
        break; // case update-photos
*/        
    case "delete-attachment-file":
        
        
        if( $objAttachmentsController->deleteAttachmentFile( $filename ) )
        {
            $strResponseStatus  = "Success"; 
        }
        else
        {
            $strResponseStatus  = "Failure"; 
            $strResponseMessage = "Deletion of Attachment failed";
        }
        break;

} // switch



$strResponse  = "<status>{$strResponseStatus}</status>";
$strResponse .= "<message>{$strResponseMessage}</message>";
$strResponse .= "<data><![CDATA[{$strResponseData}]]></data>";
$strPackage   = "<package>{$strResponse}</package>";
echo $strPackage;

?>
