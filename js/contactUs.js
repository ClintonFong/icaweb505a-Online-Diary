
//---------------------------------------------------------------------------------------------
// JQuery functions for myAccount.php
//---------------------------------------------------------------------------------------------

var errorHighlightColor = '#FeFe00';
var resetHighlightColor = '#ffffff';


$(document).ready( function() 
{

	//---------------------------------------------------------------------------------------------
	$('#btnContactUsSubmit').click( function( event ) 
    {
        //alert("#btnAccountUpdate");
        var bOk = true;
        var strErrorMessage = '';

        // reset all the background colors
        $('#firstname').css('background-color',   resetHighlightColor );
        $('#lastname').css('background-color',    resetHighlightColor );
        $('#email').css('background-color',       resetHighlightColor );
        $('#message').css('background-color',     resetHighlightColor );


        // check values and mark the ones that needs fixing
        //
        if ( document.forms['frmContactUs'].firstname.value == '' )
        {
            bOk = false;
            strErrorMessage = '*First name is required<br>';
            $('#firstname').css('background-color', errorHighlightColor );
        }

        if ( document.forms['frmContactUs'].lastname.value == '' )
        {
            bOk = false;
            strErrorMessage += '*Last name is required<br>';
            $('#lastname').css('background-color', errorHighlightColor );
        }

        if ( document.forms['frmContactUs'].email.value == '' )
        {
            bOk = false;
            strErrorMessage += '*Email is required<br>';
            $('#email').css('background-color', errorHighlightColor );
        }

        if ( !isValidEmail( document.forms['frmContactUs'].email.value ) )
        {
            bOk = false;
            strErrorMessage += '*Email is NOT in Correct format<br>';
            $('#email').css('background-color', errorHighlightColor );
        }

        if ( document.forms['frmContactUs'].message.value == '' )
        {
            bOk = false;
            strErrorMessage += '*Message is required<br>';
            $('#message').css('background-color', errorHighlightColor );
        }


        if (!bOk)
        {
            $('#errorMessages').html(strErrorMessage);
            $('#errorBox').css( "display", "block" );
        }
        else 
        {
            doAjaxContactUs(); // make the ajax call to update the account
        }

	}); // $('#btnContactUsSubmit').click


    

}); // $(document).ready( function() 



//---------------------------------------------------------------------------------------------
// AJAX function calls
//---------------------------------------------------------------------------------------------

$.ajaxSetup(
{
    cache: false
});


//---------------------------------------------------------------------------------------------
function doAjaxContactUs()
{
//	alert('doAjaxUpdateAccount');
	
    $(".ajaxLoader").css( "display", "block" );
    $('#errorBox').css( "display", "none" );
	$('#ajaxContactUsMessageResponse').html( 'Submitting form...please wait' );

    
    //alert(dataSend );
    var submitPackage = {   "action"    : "contact-us-submission",
                            "idUser"    : document.forms['frmContactUs'].idUser.value,
                            "firstname" : document.forms['frmContactUs'].firstname.value,
                            "lastname"  : document.forms['frmContactUs'].lastname.value,
                            "email"     : document.forms['frmContactUs'].email.value,
                            "message"   : document.forms['frmContactUs'].message.value
                        };

    $.ajax({
            url         :   "ajaxScripts/ajaxContactUs.php",
            type        :   "POST",
            data        :   { data : JSON.stringify( submitPackage ) },
            dataType    :   "json",
            complete    :   function( data )
                            {
                                if( stripStatusFromAjaxData( data.responseText ) == "Success" ) { $('#ajaxContactUsMessageResponse').css( "color", "#74BF43" ); }
                                else                                                            { $('#ajaxContactUsMessageResponse').css( "color", "#BF1E23" ); }

                                $('#ajaxContactUsMessageResponse').html( stripMessageFromAjaxData( data.responseText ) );
                                $(".ajaxLoader").css( "display", "none") ;
                            }
		   });

} // doAjaxContactUs


