//---------------------------------------------------------------------------------------------
// JQuery functions for login.php
//---------------------------------------------------------------------------------------------

jQuery(function($)
{

    var errorHighlightColor = '#FFF8E2';
    var resetHighlightColor = '#ffffff';

    if( $('#registerAttempt').val() == '1' )
    {
        $('#cntSigninBox').css( 'display', 'none' );
        $('#cntRegisterBox').css( 'display', 'block' );
    }


	//---------------------------------------------------------------------------------------------
	$('#btnSignin').click( function( event ) 
    {
        //alert('btnSignin');

        var bOk = true;
        var strErrorMessage = '';

        $('#signinSigninEmail').css('background-color', resetHighlightColor );
        $('#signinPassword').css('background-color',   resetHighlightColor );

        if ( document.forms['frmSignin'].signinEmail.value == '' )
        {
            bOk = false;
            strErrorMessage = '*Sign-in Email is required';
            $('#signinEmail').css('background-color', errorHighlightColor );

        }
        if ( document.forms['frmSignin'].password.value == '' )
        {
            bOk = false;
            strErrorMessage += '\n*Password is required';
            $('#signinPassword').css('background-color', errorHighlightColor );
        }

        if (!bOk)
        {
            alert (strErrorMessage);
        }
        else 
        {
            document.forms['frmSignin'].submit();
        }
	
	}); // $('#btnSignin').click

	//---------------------------------------------------------------------------------------------
	$('#btnRegister').click( function( event ) 
    {
       // alert('btnRegister');

        var bOk = true;
        var strErrorMessage = '';

        // reset all the background colors
        $('#registerFirstname').css('background-color',         resetHighlightColor );
        $('#registerLastname').css('background-color',          resetHighlightColor );
        $('#registerSigninEmail').css('background-color',       resetHighlightColor );
        $('#registerPassword').css('background-color',          resetHighlightColor );
        $('#registerConfirmPassword').css('background-color',   resetHighlightColor );


        // check values and mark the ones that needs fixing
        //
        if ( document.forms['frmRegister'].firstname.value == '' )
        {
            bOk = false;
            strErrorMessage = '*First name is required\n';
            $('#registerFirstname').css('background-color', errorHighlightColor );
        }

        if ( document.forms['frmRegister'].lastname.value == '' )
        {
            bOk = false;
            strErrorMessage += '*Last name is required\n';
            $('#registerLastname').css('background-color', errorHighlightColor );
        }

        if ( document.forms['frmRegister'].signinEmail.value == '' )
        {
            bOk = false;
            strErrorMessage += '*Signin Email is required\n';
            $('#registerSigninEmail').css('background-color', errorHighlightColor );
        }

        if ( document.forms['frmRegister'].password.value == '' )
        {
            bOk = false;
            strErrorMessage += '*Password is required\n';
            $('#registerPassword').css('background-color', errorHighlightColor );
        }

        if ( document.forms['frmRegister'].password.value != document.forms['frmRegister'].confirmPassword.value )
        {
            bOk = false;
            strErrorMessage += '*Passwords do not match';
            $('#registerConfirmPassword').css('background-color', errorHighlightColor );
        }

        if (!bOk)
        {
            alert (strErrorMessage);
        }
        else 
        {
            document.forms['frmRegister'].submit();
        }
	
	}); // $('#btnRegister').click

	//---------------------------------------------------------------------------------------------
	$('#aRegister').click( function( event ) 
    {
        $('#cntSigninBox').css('display', 'none' );
        $('#cntRegisterBox').css('display', 'block');

    }); // $('#aRegister').click

	//---------------------------------------------------------------------------------------------
	$('#aSignin').click( function( event ) 
    {
        $('#cntSigninBox').css('display', 'block');
        $('#cntRegisterBox').css('display', 'none');

    }); // $('#aRegister').click

    
}); // $(document).ready(function()


// end JQuery functions
//---------------------------------------------------------------------------------------------

/*
function doForgotPassword()
{
    //alert('doForgetPassword');

    var email = prompt('Please enter your Sign-In Email to Reset your Password');

    if( isValidEmail( email ) )
    {
        document.forms['frmForgotPassword'].email.value = email;
        document.forms['frmForgotPassword'].submit();
    }

} // doForgotPassword

*/