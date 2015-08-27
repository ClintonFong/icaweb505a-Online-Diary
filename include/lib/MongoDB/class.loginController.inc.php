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

        if( $this->db )
        {
            $users      = $this->db->selectCollection( "users" );
            
            $filter     = array("email" => $email );
            $userExist  = $users->findOne( $filter );

            if( count( $userExist ) == 0 )            
            {
                $sha256Password = hash('sha256', $password);
                $user = array(
                            "firstname"     => $firstname,
                            "lastname"      => $lastname,
                            "email"         => $email,
                            "password"      => $sha256Password,
                            "isLoggedIn"    => false
                        );
                $users->insert( $user );    
                
                
                // store attributes
                //
                $this->firstname    = $firstname;
                $this->lastname     = $lastname;
                $this->email        = $email;
                $this->password     = $sha256Password;
                $this->idUser       = $idUser           =  $user['_id'];
                $isRegisterSuccessful = true;                
                
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
            $users  = $this->db->selectCollection( "users" );
            
            $filter = array("email" => $email );
            $user   = $users->findOne( $filter );

            if( count( $user ) > 0 )            
            {
                $db_password = $user['password'];
                $sha256Password =  hash('sha256', $password);
			    if( $db_password == $sha256Password )
                {
                    $isLoginSuccessful = TRUE;
                    $this->idUser = $user['_id'];
                }
            }
        }
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
        
        if( $this->db )
        {
            $users      = $this->db->selectCollection( "users" );
            $filter     = array( "email"  => $email );         
            $update     = array( '$set' => array( "isLoggedIn" => true ) );
            $bSuccess   = $users->update( $filter, $update );           
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
        //echo "In flagLoggedOut";
        assert( is_object( $idUser ) );
        assert( isset( $this->db) );
        $bSuccess   = FALSE;
        
        if( $this->db )
        {
            $users      = $this->db->selectCollection( "users" );
            $filter     = array( "_id"  => $idUser );         
            $update     = array( '$set' => array( "isLoggedIn" => false ) );
            $bSuccess   = $users->update( $filter, $update );           
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

        if( $this->db )
        {
            $users  = $this->db->selectCollection( "users" );
            $filter = array( "_id"  => $idUser );         
            $user   = $users->findOne( $filter );           
            
            if( $user['isLoggedIn'] )
            {
                $bUserLoggedIn      = TRUE;
                $this->idUser       = $user['_id'];
                $this->firstname    = $user['firstname'];
                $this->lastname     = $user['lastname'];
                $this->email        = $user['email'];
            }
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

        if( $this->db )
        {
            $users  = $this->db->selectCollection( "users" );
            $filter = array( "_id" => $idUser );
            $user   = $users->findOne( $filter );
            
            $this->idUser       = $user['_id'];
            $this->firstname    = $user['firstname'];
            $this->lastname     = $user['lastname'];
            $this->email        = $user['email'];
            
            $isSuccessful   = true;                
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
