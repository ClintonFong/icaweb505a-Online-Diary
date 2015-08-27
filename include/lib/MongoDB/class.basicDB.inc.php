<?php

require_once 'common.inc.php';

class c_basicDB
{
    private static $conn;
    protected static $db;		// db connection
    
    public $errorMessage = '';          // errorMessage if any would be saved here

	//---------------------------------------------------------------------------------------------
	// constructors 
	//---------------------------------------------------------------------------------------------
	function __construct( $dbConnection = '' )
	{
        if(isset($dbConnection) && ($dbConnection != '' ))  { $this->db = $dbConnection;  }
        else                                                { $this->connectDB();                   }
			
	} // __construct
	
	
	//---------------------------------------------------------------------------------------------
	
	//---------------------------------------------------------------------------------------------
	function __destruct()
	{

		$this->closeDB();
		
	} // __destruct

	//---------------------------------------------------------------------------------------------
	// connectDB
	//---------------------------------------------------------------------------------------------
	function connectDB()
	{
        
//      echo "In connectDB()";
        if( !isset($this->db) || ($this->db == ''))
        {
		    $this->conn = new MongoClient();
            $this->db   = $this->conn->selectDB( "icaweb505a" ); 
        }
		return $this->db;
				
	} // connectDB

	//---------------------------------------------------------------------------------------------
	// closeDB
	//---------------------------------------------------------------------------------------------
	function closeDB()
	{
		if ( isset($this->db)  )
		{ 
            //$this->db->close();
		}
	
	} // closeDB


	//---------------------------------------------------------------------------------------------
	// getDBConnection
	//---------------------------------------------------------------------------------------------
	function getDBConnection()
	{
	    return $this->db;

	} // getDBConnection


} // class c_BasicDB

?>