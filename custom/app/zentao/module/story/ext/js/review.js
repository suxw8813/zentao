/**feature-880**/
function switchShow(result)
{
    if(result == 'reject')
    {
        $('#rejectedReasonBox').show();
        $('#preVersionBox').hide();
        $('#assignedTo').val('closed');
        $('#assignedTo').trigger("chosen:updated");
// add by yangjinlian at 20161227
		$('#passNoteBox').hide();
//===============================		
    }
// add by yangjinlian at 20161227	
	else if((result == 'pass'))
    {
        $('#passNoteBox').show();
		$('#rejectedReasonBox').hide();
    }
//===============================		
    else if(result == 'revert')
    {
        $('#preVersionBox').show();
        $('#rejectedReasonBox').hide();
        $('#duplicateStoryBox').hide();
        $('#childStoriesBox').hide();
        $('#assignedTo').val(assignedTo);
        $('#assignedTo').trigger("chosen:updated");
    }
    else
    {
        $('#preVersionBox').hide();
        $('#rejectedReasonBox').hide();
        $('#duplicateStoryBox').hide();
        $('#childStoriesBox').hide();
        $('#rejectedReasonBox').hide();
        $('#assignedTo').val(assignedTo);
        $('#assignedTo').trigger("chosen:updated");
    }
}

function setStory(reason)
{
    if(reason == 'duplicate')
    {
        $('#duplicateStoryBox').show();
        $('#childStoriesBox').hide();
    }
    else if(reason == 'subdivided')
    {
        $('#duplicateStoryBox').hide();
        $('#childStoriesBox').show();
    }
    else
    {
        $('#duplicateStoryBox').hide();
        $('#childStoriesBox').hide();
    }
}
