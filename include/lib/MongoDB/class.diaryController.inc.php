<?php

require_once    'class.basicDB.inc.php';

class c_diaryController extends c_basicDB
{
    public $idEntry = '';
    public $date    = '';
    public $text    = '';
    public $videos  = array();
    public $audios  = array();


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
    // getDocumentsForDate
    //
    // Description: gets the document pertaining 
	//---------------------------------------------------------------------------------------------
	function getDocumentsForDate( $date  )
	{
       // echo "In registerNewMember";

        assert( isset( $this->db) );

        $isSuccessful = FALSE;

        if( $this->db )
        {
            $diary      = $this->db->selectCollection( "diary" );
            
            $filter     = array("date" => $date );
            $docsExist  = $date->findOne( $filter );

            if( count( $docsExist ) == 0 )            
            {
                $docs = array(  "text"      => $text );
                $id = $diary->insert( $docs );    
                
                // store attributes
                //
                $this->date     = $date;
                $this->text     = $text;
                $isSuccessful;                
                
            }
        }    

        return $isSuccessful;

	} // getDocumentsForDate

    
    
	//---------------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------
    // debugging tools
	//---------------------------------------------------------------------------------------------
    function __displayAttributes()
    {
        echo "<br>
            idDiaryEntry= {$this->idDiaryEntry}<br>
            date = {$this->date}<br>
            text = {$this->text}<br>
            videos = {$this->videos}<br>
            audio = {$this->audios}<br>
            <br>
            ";

    } // __displayAttributes
    
} // class c_diaryController

?>
