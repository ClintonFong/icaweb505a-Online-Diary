
//---------------------------------------------------------------------------------------------
// JQuery functions for overall application
//---------------------------------------------------------------------------------------------

var months          = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

$(document).ready( function() 
{

    var dateCurrent = $('#calendar').datepicker('getDate');
    var diaryDate   = $.datepicker.formatDate('dd MM yy', dateCurrent );

    diaryEntryDate   = $.datepicker.formatDate('yy-mm-dd', dateCurrent );
    doAjaxDisplayDiary( diaryDate );

    //---------------------------------------------------------------------------------------------
    // saveDiaryEntry
    //---------------------------------------------------------------------------------------------
	$('#saveEntry').click(function() 
    {
        doAjaxSaveDiaryEntry();
	});

    //---------------------------------------------------------------------------------------------
    // addAttachment
    //---------------------------------------------------------------------------------------------
	$('#addAttachment').click(function() 
    {
        $('#cntFileUpload').css( "display", "block" );
	});

    //---------------------------------------------------------------------------------------------
    // removeAttachment
    //---------------------------------------------------------------------------------------------
	$('#removeAttachment').click(function() 
    {
        //doAjaxRemoveAttachment();
	});

    //---------------------------------------------------------------------------------------------
    // btnUploadFile
    //---------------------------------------------------------------------------------------------
	$('#btnUploadFile').click(function() 
    {

        var filename        = $('#fileUpload').val();
        var extension       = filename.split('.').pop().toLowerCase();
        var ValidExtensions = [ "mp4", "mp3", "gif", "jpg", "jpeg", "png", "pdf", "doc", "docx", "txt" ];
        var idx = ValidExtensions.indexOf( extension );

        if( idx != -1 )
        {
            doAjaxSaveAddAttachment();
        }
        else
        {
            alert( "NOT Recognised File Format for Upload" );
        }
        
	});
    
    //---------------------------------------------------------------------------------------------
    // btnCloseUpload
    //---------------------------------------------------------------------------------------------
	$('#btnCloseUpload').click(function() 
    {
        $('#cntFileUpload').css( "display", "none" );
	});

    //---------------------------------------------------------------------------------------------
    // showing the appropriate media viewer/player
    //---------------------------------------------------------------------------------------------
	$('#cntDiaryAttachments').on("click", ".attachmentName", function() 
    {
		
		// Getting the variable's value from a link 
		var attachmentObject = $(this).attr('href');

		//Fade in the Popup and add close button
		$(attachmentObject).fadeIn(300);

        // get the available display area and adjust display div accordingly
        var maxDisplayHeight = screen.availHeight * 0.75;
        var maxDisplayWidth  = screen.availWidth * 0.75;
        $(attachmentObject).find("video").css( "width", maxDisplayWidth );
		$(attachmentObject).css(
        { 
            'max-height'    : maxDisplayHeight,
            'max-width'     : maxDisplayWidth
		});
		
		//Set the center alignment padding + border

		var popMargTop = ( Math.min( $(attachmentObject).height(), maxDisplayHeight ) + 24 ) / 2; 
		var popMargLeft = ( Math.min( $(attachmentObject).width(), maxDisplayWidth ) + 24 ) / 2; 
		

		$(attachmentObject).css(
        { 
			'margin-top'    : -popMargTop,
			'margin-left'   : -popMargLeft
		});
	
        
		// Add the mask to body
		$('body').append("<div id='mask'></div>");
		$('#mask').fadeIn(300);
		
		return false;

	}); // $('#cntDiaryAttachments').on("click", ".attachmentName", function() 
	
    //---------------------------------------------------------------------------------------------
  	$('#cntDiaryAttachments').on("click", "a.close", function() // When clicking on the button close (the cross at the top right corner)
    { 
	    $('.attachmentObjectPopup').fadeOut(300 , function() 
        {
		    $('#mask').remove();  
	    }); 
	    return false;

    }); // $('#cntDiaryAttachments').on("click", "a.close", function() 


    //---------------------------------------------------------------------------------------------
  	$('#cntDiaryAttachments').on("click", ".deleteAttachment", function( event ) // delete Attachment
    { 
        var parentElement = $(this).parent();
        var anchorElement = parentElement.children('a');
        var filename = anchorElement.html();

        if( confirm("Confirm DELETE Attachment " + filename + "?" ) )
        {
            doAjaxRemoveAttachment( filename, parentElement );
        }

    }); // $('#cntDiaryAttachments').on("click", ".deleteAttachment", function() 


}); // $(document).ready( function() 


//---------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------
$('#calendar').datepicker({
    inline:             true,
    firstDay:           1,
    showOtherMonths:    true,
    changeMonth:        true,
    changeYear:         true,
    autosize:           true,
    dayNamesMin:        ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    onSelect:           function( dateText, inst )
                        {
                            var diaryDate = inst.currentDay + ' ' + months[inst.currentMonth] + ' ' + inst.currentYear;

                            diaryEntryDate = inst.currentYear + '-' + ("0" + (inst.currentMonth+1)).slice(-2) + '-' + ("0" + inst.currentDay).slice(-2);
                            doAjaxDisplayDiary( diaryDate );
                        }
});


//---------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------
// AJAX function calls
//---------------------------------------------------------------------------------------------

$.ajaxSetup(
{
    cache: false
});

var diaryEntryDate  = ''; // this variable will be updated in calender.js 


//---------------------------------------------------------------------------------------------
function doAjaxDisplayDiary( diaryDate )
{
    $('#diaryTitle h1').html( diaryDate );
    $('#diaryText textarea').val("");

    var diaryEntryPackage = {   "action"    : "get-diary-entry",
                                "date"      : diaryEntryDate,
                                "idUser"    : document.forms['frmFileUpload'].idUser.value };

    $.ajax({
        url         :   "ajaxScripts/ajaxDiary.php",
        type        :   "POST",
        data        :   { data : JSON.stringify( diaryEntryPackage ) },
        dataType    :   "json",
        complete    :   function( data )
                        {
                            if( stripStatusFromAjaxData( data.responseText ) == 'Success' )
                            {
                                var diaryEntry      = JSON.parse( stripDataFromAjaxData( data.responseText ) );
                                var diaryText       = "";
                                var htmlAttachments = "";


                                if( diaryEntry != null )
                                {
                                    numAttachments  = 0;
                                    diaryText       = diaryEntry.text;

                                    for( attachment in diaryEntry.attachments )
                                    {
                                        numAttachments++; 
                                        switch( diaryEntry.attachments[attachment].filetype )
                                        {
                                            case "image" : htmlAttachments += writeImageHTML( diaryEntry.attachments[attachment].filename ); break;
                                            case "audio" : htmlAttachments += writeAudioHTML( diaryEntry.attachments[attachment].filename ); break;
                                            case "video" : htmlAttachments += writeVideoHTML( diaryEntry.attachments[attachment].filename ); break;
                                            default      : htmlAttachments += writeDefaultHTML( diaryEntry.attachments[attachment].filename  );
                                        }
                                    }
                                }
                                $('#diaryText textarea').val( diaryText );
                                $('#cntDiaryAttachments').html( htmlAttachments );
                            }
                            else
                            {
                                alert("Trouble Getting Info");

                                // do need to display each unsuccessful step.  $('#ajaxForgotPasswordMessageResponse').html( stripDataFromAjaxData(data) );
					            //$('#ajaxForgotPasswordMessageResponse').html( 'Problems encountered resetting your password. <br>Please contact us to resolve this issue.' );
                            }        
            
                        } // function
                    
    });

} // doAjaxDisplayDiary

//---------------------------------------------------------------------------------------------
function doAjaxSaveDiaryEntry()
{
//	alert('doAjaxForgotPassword');
    $(".ajaxLoader").css("display", "block");
	
    var diaryEntryPackage = {   "action"    : "save-diary-entry",
                                "date"      : diaryEntryDate,
                                "idUser"    : document.forms['frmFileUpload'].idUser.value,
                                "text"      : $('#diaryText textarea').val() };

    $.ajax({
        url         :   "ajaxScripts/ajaxDiary.php",
        type        :   "POST",
        data        :   { data : JSON.stringify( diaryEntryPackage ) },
        dataType    :   "json",
        complete    :   function( data )
                        {
                            if( stripStatusFromAjaxData( data.responseText ) == 'Success' )
                            {
                                //alert("Save Successfully");
                            }
                            else
                            {
                                alert("Trouble Saving");
                            }
                            $(".ajaxLoader").css("display", "none");

                        }
                    
    });

} // doAjaxSaveDiaryEntry

// --------------------------------------------------------------------------
// --------------------------------------------------------------------------
var numAttachments = 0;

function doAjaxSaveAddAttachment()
{
    $(".ajaxLoader").css("display", "block");

    var url = "ajaxScripts/ajaxAttachments.php";

    document.forms['frmFileUpload'].date.value = diaryEntryDate;

    //alert(dataSend );
    $('#frmFileUpload').ajaxSubmit({
        url         :   url,
        success     :   function( data, status ) 
                        {
                            if( stripStatusFromAjaxData( data ) != 'Success' )  { ajaxReturnSaveAddAttachmentError( data );     }
                            else                                                { ajaxReturnSaveAddAttachmentSuccess( data );   }
                            $(".ajaxLoader").css("display", "none");
                        }
    });

} // doAjaxUploadChallengePhotos

//---------------------------------------------------------------------------------------------
// AJAX Return Helpers
//---------------------------------------------------------------------------------------------

function ajaxReturnSaveAddAttachmentError(  data )
{
    alert("Trouble adding attachment. Please contact us.");
} // ajaxReturnSaveAddAttachmentError

function ajaxReturnSaveAddAttachmentSuccess( data )
{
    numAttachments++; 
    var jsonDataString  = stripDataFromAjaxData( data ); 
    var objAttachment   = JSON.parse( jsonDataString );

    var htmlAttachment  = "";
    switch( objAttachment.fileType )
    {
        case "image" : htmlAttachment = writeImageHTML( objAttachment.fileName ); break;
        case "audio" : htmlAttachment = writeAudioHTML( objAttachment.fileName ); break;
        case "video" : htmlAttachment = writeVideoHTML( objAttachment.fileName ); break;
        default      : htmlAttachment = writeDefaultHTML( objAttachment.fileName );

    }

    if( htmlAttachment != "" ) { $('#cntDiaryAttachments').prepend( htmlAttachment ); }

} // ajaxReturnSaveAddAttachmentSuccess

//---------------------------------------------------------------------------------------------
function writeImageHTML( fileName )
{
    return  "<div id='#attachment" + numAttachments + "' class='attachment'> \
                <span class='attachmentIcon'><img src='images/picture_icon2.png' alt='Image' /></span> \
                <a href='#attachmentObject" + numAttachments + "' class='attachmentName'>" + fileName + "</a> \
                <div id='attachmentObject" + numAttachments + "' class='attachmentObjectPopup'> \
                    <a href='#' class='close'><img src='images/close_pop.png' class='btnClose' title='Close Window' alt='Close' /></a> \
                    <img src='" + ATTACHMENTS_DIR + fileName + "' /> \
                </div> \
                <div class='deleteAttachment'> \
                    <a class='tooltip'> \
                        <img src='images/trash_can.png' alt='delete' /> \
                        <span>Delete</span> \
                    </a> \
                </div> \
            </div> \
            ";

} // writeImageHTML

function writeAudioHTML( fileName  )
{
    return  "<div id='#attachment" + numAttachments + "' class='attachment'> \
                <span class='attachmentIcon'><img src='images/audio_icon2.png' alt='Audio' /></span> \
                <a href='#attachmentObject" + numAttachments + "' class='attachmentName'>" + fileName + "</a> \
                <div class='attachmentObjectPopup' id='attachmentObject" + numAttachments + "'> \
                    <a href='#' class='close'><img src='images/close_pop.png' class='btnClose' title='Close Window' alt='Close' /></a> \
                    <audio controls> \
                        <source src='" + ATTACHMENTS_DIR + fileName + "' type='audio/mpeg' /> \
                        Your browser does not support the audio element \
                    </audio> \
                </div> \
                <div class='deleteAttachment'> \
                    <a class='tooltip'> \
                        <img src='images/trash_can.png' alt='delete' /> \
                        <span>Delete</span> \
                    </a> \
                </div> \
            </div> \
            ";

} // writeImageHTML

function writeVideoHTML( fileName )
{
    return  "<div id='#attachment" + numAttachments + "' class='attachment'> \
                <span class='attachmentIcon'><img src='images/video_icon2.png' alt='Video' /></span> \
                <a href='#attachmentObject" + numAttachments + "' class='attachmentName'>" + fileName + "</a> \
                <div class='attachmentObjectPopup' id='attachmentObject" + numAttachments + "'> \
                    <a href='#' class='close'><img src='images/close_pop.png' class='btnClose' title='Close Window' alt='Close' /></a> \
                    <video width='400' controls> \
                        <source src='" + ATTACHMENTS_DIR + fileName + "' type='video/mp4'> \
                        Your browser does not support HTML5 video. \
                    </video> \
                </div> \
                <div class='deleteAttachment'> \
                    <a class='tooltip'> \
                        <img src='images/trash_can.png' alt='delete' /> \
                        <span>Delete</span> \
                    </a> \
                </div> \
            </div> \
            ";

} // writeVideoHTML

function writeDefaultHTML( fileName )
{
    return  "<div id='#attachment" + numAttachments + "' class='attachment'> \
                <span class='attachmentIcon'><img src='images/document_unknown.png' /></span> \
                <a href='" + ATTACHMENTS_DIR + fileName + "' class='defaultAttachmentName'>" + fileName + "</a> \
                <div class='deleteAttachment'> \
                    <a class='tooltip'> \
                        <img src='images/trash_can.png' alt='delete' /> \
                        <span>Delete</span> \
                    </a> \
                </div> \
            </div> \
            ";

} // writeDefaultHTML

//---------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------
function doAjaxRemoveAttachment( filename,
                                 elementToRemove )
{
    $(".ajaxLoader").css("display", "block");

    var diaryEntryPackage = {   "action"    : "delete-attachment-file",
                                "date"      : diaryEntryDate,
                                "filename"  : filename };

    $.ajax({
        url         :   "ajaxScripts/ajaxAttachments.php",
        type        :   "POST",
        data        :   { data : JSON.stringify( diaryEntryPackage ) },
        dataType    :   "json",
        complete    :   function( data )
                        {
                            if( stripStatusFromAjaxData( data.responseText ) != 'Success' ) 
                            {
                                alert("Trouble adding attachment. Please contact us.");
                            }
                            else                                                            
                            { 
                                elementToRemove.remove(); // elementToRemove is a jQuery element
                            }
                            $(".ajaxLoader").css("display", "none");
                        }
                    
    });

} // doAjaxRemoveAttachment

