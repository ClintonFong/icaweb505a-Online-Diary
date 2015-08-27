<?php

require_once    'class.basicDB.inc.php';

class c_loginController extends c_basicDB
{

    public $idUser          = '';
    public $firstname       = '';
    public $lastname        = '';
    public $email           = '';


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
    // registerNewUser
    //
    // Description: register new user
	//---------------------------------------------------------------------------------------------
	function registerNewUser(   $firstname,
                                $lastname,
                                $email,
                                $password,
                                &$idUser )
	{
       // echo "In registerNewMember";

        assert( isset( $this->db) );

        $isRegisterSuccessful = FALSE;

        if( !$this->db->connect_errno )
        {
            $bUniqueName    = FALSE;
            $deptName       = $this->scrubInput( $email );
    	    $stmtQuery      = "SELECT count(*) as num_users FROM icaweb505a_users WHERE email='{$email}'";

     	    if( $resultQuery = $this->db->query( $stmtQuery ) )
            {
		        $row = $resultQuery->fetch_array( MYSQL_ASSOC );
                $bUniqueName = ( $row['num_users'] == 0 );
                $resultQuery->close();
            }

            // proceed if unique email
            if( $bUniqueName )
            {
		        $stmtQuery      = "INSERT INTO icaweb505a_users (firstname, lastname, email, password, isLoggedIn) VALUES ";
                $stmtQuery     .= "(?, ?, ?, ?, '" . LOGGED_IN . "' ) ";


                if( $stmt = $this->db->prepare( $stmtQuery ) )
                {
                    $firstname      = $this->scrubInput( $firstname );
                    $lastname       = $this->scrubInput( $lastname );
                    $sha256Password = hash('sha256', $password);
                    
                    $stmt->bind_param( "ssss", $firstname, $lastname, $email, $sha256Password );

		            $bSuccess = $stmt->execute();

                    if( $bSuccess && ($stmt->affected_rows > 0) )
                    { 
                        $isRegisterSuccessful = TRUE; 
                        $this->idUser = $stmt->insert_id;
                    }
                }
            }
        }    

        return $isRegisterSuccessful;

	} // registerNewUser

    
    
	//---------------------------------------------------------------------------------------------
    // isLoginValid
    //
    // Description: Validates login details
	//---------------------------------------------------------------------------------------------
	function isLoginValid( $email,
                           $password )
	{
        assert( isset( $this->db) );

        $isLoginSuccessful = FALSE;

        if( $this->db )
        {
            $stmtQuery  = "SELECT idUser, firstname, lastname, password, email";
            $stmtQuery .= " FROM icaweb505a_users";
            $stmtQuery .= " WHERE email=?";

            if ($stmt = $this->db->prepare( $stmtQuery ) )
            {
                $email = $this->scrubInput( $email );
                $stmt->bind_param( "s", $email );

		        if( $bSuccess = $stmt->execute())
                {
                    $stmt->bind_result( $db_idUser, $db_firstname, $db_lastname, $db_password, $db_email );

		            if( $stmt->fetch() ) 
		            {
                        $sha256Password =  hash('sha256', $password);

			            if( $db_password == $sha256Password )
                        {
                            $isLoginSuccessful = TRUE;

                            $this->idUser       = $db_idUser;
                            $this->firstname    = $db_firstname;
                            $this->lastname     = $db_lastname;
                            $this->email        = $db_email;

                        }
		            } 
                }
	            $stmt->close(); 	// Free resultset 
            
            } // if ($stmt = $this->db->prepare( $stmtQuery ) )
            
        } // if( $this->db )
        
    	return $isLoginSuccessful;

	} // isLoginValid


    //---------------------------------------------------------------------------------------------
    // flagLoggedIn
    //
    // Description: Flags the database that this user has successfully logged in 
    //---------------------------------------------------------------------------------------------
	function flagLoggedIn( $email )
	{
//        echo "In flagLoggedIn";
        assert( isset( $this->db) );
        $bSuccess   = FALSE;
        
		$stmtQuery  = "UPDATE icaweb505a_users SET isLoggedIn='" . LOGGED_IN . "' WHERE email=?";

        if( $stmt = $this->db->prepare( $stmtQuery ) )
        {
            $userID = $this->scrubInput( $email );
            $stmt->bind_param("s", $email );
            $bSuccess = $stmt->execute();
   	        $stmt->close(); 	// Free resultset 
        }
		return $bSuccess;
    
	} // flagLoggedIn

    //---------------------------------------------------------------------------------------------
    // flagLoggedOut
    //
    // Description: Flags the database that this user has successfully logged out
    //              $idUser is a mongoId object 
	//---------------------------------------------------------------------------------------------
	function flagLoggedOut( $idUser )
	{
        assert( isset( $this->db) );
        
        //echo "In flagLoggedOut";
        //assert( is_object( $idUser ) );
        
        $bSuccess   = FALSE;
		$stmtQuery  = "UPDATE icaweb505a_users SET isLoggedIn='" . LOGGED_OUT . "' WHERE idUser=?";

        if( $stmt = $this->db->prepare( $stmtQuery ) )
        {
            $idUser = $this->scrubInput( $idUser );
            $stmt->bind_param("i", $idUser );
            $bSuccess = $stmt->execute();
	        $stmt->close(); 	// Free resultset 
        }
		return $bSuccess;

	} // flagLoggedOut

   	//---------------------------------------------------------------------------------------------
    // isUserLoggedIn
    //
    // Description: returns true if the user is logged in, otherwise false
	//---------------------------------------------------------------------------------------------
	function isUserLoggedIn( $idUser )
	{
        assert( isset( $this->db) );

        $bUserLoggedIn = FALSE;

		$stmtQuery  = "SELECT idUser, firstname, lastname, email, isLoggedIn";
        $stmtQuery .= " FROM icaweb505a_users WHERE idUser=?";

        if( $stmt = $this->db->prepare( $stmtQuery ) )
        {
            $idUser = $this->scrubInput( $idUser );
            $stmt->bind_param("i", $idUser );

		    if( $bSuccess = $stmt->execute())
            {
                $stmt->bind_result( $db_idUser, $db_firstname, $db_lastname, $db_email, $db_isLoggedIn );

		        if ( $stmt->fetch() ) 
		        {
			        if ( $db_isLoggedIn == LOGGED_IN )
                    {
                        $bUserLoggedIn      = TRUE;

                        $this->idUser       = $db_idUser;
                        $this->firstname    = $db_firstname;
                        $this->lastname     = $db_lastname;
                        $this->email        = $db_email;
                    }
		        } 
            }
	        $stmt->close(); 	// Free resultset 
        }
    	return $bUserLoggedIn;

	} // isUserLoggedIn

    //---------------------------------------------------------------------------------------------
    // getUserDetails
    //
    // Description: gets the users details with a given $idUser
	//---------------------------------------------------------------------------------------------
	function getUserDetails( $idUser )
	{

        assert( isset( $this->db) );

        $isSuccessful = false;

		$stmtQuery  = "SELECT idUser, firstname, lastname, email";
        $stmtQuery .= " FROM icaweb505a_users WHERE idUser=?";

        if( $stmt = $this->db->prepare( $stmtQuery ) )
        {
            $idUser = $this->scrubInput( $idUser );
            $stmt->bind_param("i", $idUser );

		    if( $bSuccess = $stmt->execute())
            {
                $stmt->bind_result( $db_idUser, $db_firstname, $db_lastname, $db_email );

		        if ( $stmt->fetch() ) 
		        {
                    $this->idUser       = $db_idUser;
                    $this->firstname    = $db_firstname;
                    $this->lastname     = $db_lastname;
                    $this->email        = $db_email;
		        } 
            }
	        $stmt->close(); 	// Free resultset 
        }

        return $isSuccessful;

	} // getUserDetails
    


	//---------------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------
    // debugging tools
	//---------------------------------------------------------------------------------------------
    function __displayAttributes()
    {
        echo "<br>
            idUser = {$this->userID}<br>
            firstname = {$this->firstname}<br>
            lastname = {$this->lastname}<br>
            email = {$this->email}<br>
            isLoggedIn = {$this->isLoggedIn}<br>
            <br>
            ";

    } // __displayAttributes
    
} // class c_loginController

?>
