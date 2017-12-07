/**feature-1053**/
function switchShow(source)
{
    if(source == '1')
    {
        $('#backtrackingBox').show();
        $('#rdresponserBox').show();
		$('#testresponserBox').show();
		$('#reqresponserBox').show();
    }
    else
    {

        $('#backtrackingBox').hide();
        $('#rdresponserBox').hide();
		$('#testresponserBox').hide();
		$('#reqresponserBox').hide();
	}

}

