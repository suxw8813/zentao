/**feature-1053**/

function setDuplicate(resolution,bugconfirmed)
{   
		if(resolution == 'duplicate')
		{
			$('#duplicateBugBox').show();
		}
		else
		{
			$('#duplicateBugBox').hide();
		}
	
	if(bugconfirmed==0){
		$('#submitbuttonBox').hide();
		$('#confirmedNoteBox').show();
	}
}

