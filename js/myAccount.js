
//---------------------------------------------------------------------------------------------
// JQuery functions for myAccount.php
//---------------------------------------------------------------------------------------------

var errorHighlightColor = '#FeFe00';
var resetHighlightColor = '#ffffff';


$(document).ready( function() 
{

	//---------------------------------------------------------------------------------------------
	$('#btnAccountUpdate').click( function( event ) 
    {
        //alert("#btnAccountUpdate");
        var bOk = true;
        var strErrorMessage = '';

        // reset all the background colors
        $('#firstname').css('background-color',         resetHighlightColor );
        $('#lastname').css('background-color',          resetHighlightColor );
        $('#signinEmail').css('background-color',       resetHighlightColor );


        // check values and mark the ones that needs fixing
        //
        if ( document.forms['frmUpdateUser'].firstname.value == '' )
        {
            bOk = false;
            strErrorMessage = '*First name is required<br>';
            $('#firstname').css('background-color', errorHighlightColor );
        }

        if ( document.forms['frmUpdateUser'].lastname.value == '' )
        {
            bOk = false;
            strErrorMessage += '*Last name is required<br>';
            $('#lastname').css('background-color', errorHighlightColor );
        }

        if ( document.forms['frmUpdateUser'].signinEmail.value == '' )
        {
            bOk = false;
            strErrorMessage += '*Sign-in email is required<br>';
            $('#signinEmail').css('background-color', errorHighlightColor );
        }

        if ( !isValidEmail( document.forms['frmUpdateUser'].signinEmail.value ) )
        {
            bOk = false;
            strErrorMessage += '*Sign-in email is NOT in Correct format<br>';
            $('#signinEmail').css('background-color', errorHighlightColor );
        }

        if (!bOk)
        {
            $('#errorMessages').html(strErrorMessage);
            $('#errorBox').css( "display", "block" );
        }
        else 
        {
            doAjaxUpdateAccount(); // make the ajax call to update the account
        }
	
	

	}); // $('#btnAccountUpdate').click

	//---------------------------------------------------------------------------------------------
	$('#btnPasswordUpdate').click( function( event ) 
    {
        //alert("#btnPasswordUpdate");
        var bOk = true;
        var strErrorMessage = '';

        // reset all the background colors
        $('#oldPassword').css('background-color',       resetHighlightColor );
        $('#newPassword').css('background-color',       resetHighlightColor );
        $('#confirmPassword').css('background-color',   resetHighlightColor );


        // check values and mark the ones that needs fixing
        //
        if ( document.forms['frmUpdateUser'].oldPassword.value == '' )
        {
            bOk = false;
            strErrorMessage += '*Old Password is required<br>';
            $('#oldPassword').css('background-color', errorHighlightColor );
        }

        if ( document.forms['frmUpdateUser'].newPassword.value == '' )
        {
            bOk = false;
            strErrorMessage += '*New Password is required<br>';
            $('#newPassword').css('background-color', errorHighlightColor );
        }

        if ( !isPasswordSecureEnough( document.forms['frmUpdateUser'].newPassword.value ) )
        {
            bOk = false;
            strErrorMessage += '*Password is Not Secure Enough - requires at least 7 characters, with at least 1 number and a letter<br>';
            $('#newPassword').css('background-color', errorHighlightColor );
        }


        if ( document.forms['frmUpdateUser'].newPassword.value != document.forms['frmUpdateUser'].confirmPassword.value )
        {
            bOk = false;
            strErrorMessage += '*Passwords do not match';
            $('#confirmPassword').css('background-color', errorHighlightColor );
        }

        if (!bOk)
        {
            $('#errorMessages').html(strErrorMessage);
            $('#errorBox').css( "display", "block" );
        }
        else 
        {
            doAjaxUpdatePassword(); // make the ajax call to update the password
        }
	
	}); // $('#btnPasswordUpdate').click

    

}); // $(document).ready( function() 



//---------------------------------------------------------------------------------------------
// AJAX function calls
//---------------------------------------------------------------------------------------------

$.ajaxSetup(
{
    cache: false
});


//---------------------------------------------------------------------------------------------
function doAjaxUpdateAccount()
{
//	alert('doAjaxUpdateAccount');
	
    $(".ajaxLoader").css("display", "block");
    $('#errorBox').css( "display", "none" );
	$('#ajaxUpdateAccountMessageResponse').html( 'Updating account...please wait' );
	$('#ajaxUpdatePasswordMessageResponse').html( '' );

    
    //alert(dataSend );
    var updatePackage = {   "action"    : "update-user-details",
                            "idUser"    : document.forms['frmUpdateUser'].idUser.value,
                            "firstname" : document.forms['frmUpdateUser'].firstname.value,
                            "lastname"  : document.forms['frmUpdateUser'].lastname.value,
                            "email"     : document.forms['frmUpdateUser'].signinEmail.value
                        };

    $.ajax({
            url         :   "ajaxScripts/ajaxMyAccount.php",
            type        :   "POST",
            data        :   { data : JSON.stringify( updatePackage ) },
            dataType    :   "json",
            complete    :   function( data )
                            {
                                if( stripStatusFromAjaxData( data.responseText ) == "Success" ) { $('#ajaxUpdateAccountMessageResponse').css( "color", "#74BF43" ); }
                                else                                                            { $('#ajaxUpdateAccountMessageResponse').css( "color", "#F50000" ); }

                                $('#ajaxUpdateAccountMessageResponse').html( stripMessageFromAjaxData( data.responseText ) );
                                $(".ajaxLoader").css("display", "none");
                            }

		   });

} // doAjaxUpdateAccount


//---------------------------------------------------------------------------------------------
function doAjaxUpdatePassword()
{
	//alert('doAjaxUpdatePassword');
    $(".ajaxLoader").css("display", "block");
    $('#errorBox').css( "display", "none" );
	$('#ajaxUpdatePasswordMessageResponse').html( 'Updating password...please wait' );
	$('#ajaxUpdateAccountMessageResponse').html( '' );
    
    var updatePackage = {   "action"        : "update-user-password",
                            "idUser"        : document.forms['frmUpdateUser'].idUser.value,
                            "oldPassword"   : document.forms['frmUpdateUser'].oldPassword.value,
                            "newPassword"   : document.forms['frmUpdateUser'].newPassword.value
                        };

    $.ajax({
            url         :   "ajaxScripts/ajaxMyAccount.php",
            type        :   "POST",
            data        :   { data : JSON.stringify( updatePackage ) },
            dataType    :   "json",
            complete    :   function( data )
                            {
                                if( stripStatusFromAjaxData( data.responseText ) == "Success" ) { $('#ajaxUpdatePasswordMessageResponse').css( "color", "#74BF43" ); }
                                else                                                            { $('#ajaxUpdatePasswordMessageResponse').css( "color", "#F50000" ); }

                                $('#ajaxUpdatePasswordMessageResponse').html( stripMessageFromAjaxData( data.responseText ) );
                                $(".ajaxLoader").css("display", "none");
                            }
		   });

} // doAjaxUpdatePassword




