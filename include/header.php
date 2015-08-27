<?php



    //-------------------------------------------------------------------------------------------
    //-------------------------------------------------------------------------------------------
    function displayHeaderIconsWhenLoggedIn( $currentPage )
    {
        //echo "In displayHeaderIconsWhenLoggedIn";
        
        $MenuItemsForCurrentPage = array(   "index.php"     => array( "myAccount", "contactUs", "logOut" ),
                                            "contactus.php" => array( "diary", "myAccount", "logOut" ),
                                            "myaccount.php" => array( "diary", "contactUs", "logOut" ) );
                        
        if( isset( $currentPage ) )
        {
            // -------------------------------------------------------------
            if( in_array( "diary", $MenuItemsForCurrentPage[$currentPage] ))
            {
                // display diary icon button
                echo "
                    <a id='btnHeaderMyDiary' href='index.php' class='tooltip'>
                        <img id='my-diary' src='images/calendar_icon.png' alt='Calendar' />
                        <span>My Diary</span>
                    </a>
                    ";
            }

            // -------------------------------------------------------------
            if( in_array( "myAccount", $MenuItemsForCurrentPage[$currentPage] ))
            {
                // display myaccount icon button
                echo "
                    <a id='btnHeaderMyAccount' href='myaccount.php' class='tooltip'>
                        <img id='my-account' src='images/my_account.png' alt='My Account' />
                        <span>My Account</span>
                    </a>
                    ";
            }
                            
            // -------------------------------------------------------------
            if( in_array( "contactUs", $MenuItemsForCurrentPage[$currentPage] ))
            {
                echo "
                    <a id='btnHeaderContactUs' href='contactus.php' class='tooltip'>
                        <img id='contact-us' src='images/contact_us.png' alt='Contact Us' />
                        <span>Contact Us</span>
                    </a>
                    ";
            }
                            
        } // if ( isset( $currentPage ) )
                        
        // -------------------------------------------------------------
        echo "
            <a id='btnHeaderSignOut' href='login.php' class='tooltip'>
                <img id='sign-out' src='images/exit.png' alt='Exit' />
                <span>Sign Out</span>
            </a>
            ";
    
    
    } // displayHeaderIconsWhenLoggedIn

    //-------------------------------------------------------------------------------------------
    //-------------------------------------------------------------------------------------------
    function displayHeaderIconsWhenNotLoggedIn( $currentPage )
    {
//        echo "In displayHeaderIconsWhenNotLoggedIn";
        
        $MenuItemsForCurrentPage = array(   "login.php"     => array( "contactUs" ),
                                            "contactus.php" => array( "logIn" ) );


        if( isset( $currentPage ) )
        {        
            if( in_array( "contactUs", $MenuItemsForCurrentPage[$currentPage] ))
            {
                echo "
                    <a id='btnHeaderContactUs' href='contactus.php' class='tooltip'>
                        <img id='contact-us' src='images/contact_us.png' />
                        <span>Contact Us</span>
                    </a>
                    ";
            }
        
            if( in_array( "logIn", $MenuItemsForCurrentPage[$currentPage] ))
            {
                echo "
                    <a id='btnHeaderContactUs' href='login.php' class='tooltip'>
                        <img id='contact-us' src='images/login.png' />
                        <span>Sign In</span>
                    </a>
                    ";
            }
        }
        
    } // displayHeaderIconsWhenNotLoggedIn

?>



<div id='cntHeader'>

    <div id='cntHeaderInner'>

        <div id='cntLogo'>Online Diary</div>

        <div id='cntHeaderMenu'>

            <form name='frmHeaderMenu' action='login.php' target='_self' method ='post'>

                <input type='hidden' name='actionTaken' value='' />

                <?php
                    if( isset( $isLoggedIn ) && $isLoggedIn )   
                    { 
                        displayHeaderIconsWhenLoggedIn( $currentPage );      
                        $welcomeName = "Welcome, {$objLoginController->firstname} {$objLoginController->lastname}";                
                    }
                    else                                        
                    { 
                        displayHeaderIconsWhenNotLoggedIn( $currentPage );   
                        $welcomeName = "";
                    } 
                ?>

            </form>                
        </div>
        <div id='cntUserName'><?php echo $welcomeName ?></div>
    </div>
</div>

