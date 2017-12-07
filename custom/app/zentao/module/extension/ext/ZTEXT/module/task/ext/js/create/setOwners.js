/**feature-1488**/
function setOwners(result)
{
    if(result == 'affair')
    {
        $('#assignedTo').attr('multiple', 'multiple');
        $('#assignedTo').chosen('destroy');
        $('#assignedTo').chosen(defaultChosenOptions);
        $('#selectAllUser').removeClass('hidden');
		$('#contractsum').addClass('hidden');		
    }
    else if($('#assignedTo').attr('multiple') == 'multiple')
    {
        $('#assignedTo').removeAttr('multiple');
        $('#assignedTo').chosen('destroy');
        $('#assignedTo').chosen(defaultChosenOptions);
        $('#selectAllUser').addClass('hidden');
		$('#contractsum').addClass('hidden');	
    }
	else if(result == 'workloadsplit')
	{
		$('#contractsum').removeClass('hidden');
		$('#contractsumvalue').val('0');
	}
	else if(result == 'bid')
	{
		$('#contractsum').removeClass('hidden');
		$('#contractsumvalue').val('0');
	}
	else
	{
		$('#contractsum').addClass('hidden');
	}
}

function checkSum(result,min,max)
{
	
	if(isNaN(result)){
		$('#contractsumvalue').val(min)
	}
	else if(result < min)	{
		$('#contractsumvalue').val(min);
	}else if(result >max)
	{
		$('#contractsumvalue').val(max);
	}else
	{
		$('#contractsumvalue').val(parseFloat(result))
	}
}