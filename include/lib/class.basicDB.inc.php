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
		$this->db = new mysqli (DB_SERVER, USER_NAME, PASSWORD, DATABASE);
		
        if( $this->db->connect_errno )
        {
            echo "Connection to database failed";
            exit();
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
            $this->db->close();
		}
	
	} // closeDB


	//---------------------------------------------------------------------------------------------
	// getDBConnection
	//---------------------------------------------------------------------------------------------
	function getDBConnection()
	{
	    return $this->db;

	} // getDBConnection

    
	//---------------------------------------------------------------------------------------------
    // srubInput 
    //
    // Description: scrubs down input value elimaate possible sql injection
	//---------------------------------------------------------------------------------------------
    function scrubInput($value)
    {
        
        //if( get_magic_quotes_gpc() )    { $value = stripslashes($value); }                                           // Stripslashes


        $value = $this->db->real_escape_string( $value );

        //if (!is_numeric($value)) { $value = "'" . $value . "'";  } // Quote if not a number

        return $value;

    } // scrubInput
    

} // class c_BasicDB

?>