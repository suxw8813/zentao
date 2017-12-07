/**feature-1509**/
function changeParams(obj)
{
    var timeType = $('.heading').find('#timeType').val();
    var sortField = $('.heading').find('#sortField').val();
    var amibaId = '',timeNum  = '';
    
    amibaId = $('.main .row').find('#amibaId').val();
    var time  = $('.main .row').find('#time').val();
    
    if(time.indexOf('-') != -1) {
        var beginarray = time.split("-");
        for(i=0 ; i < beginarray.length ; i++) timeNum += beginarray[i]; 
    } else {
        timeNum = time;
    }

    link = createLink('quantizedoutput', 'prjsortmore', 'timeType=' + timeType 
        + '&sortField=' + sortField + '&amibaId=' + amibaId + '&timeNum=' + timeNum);
    location.href=link;
}

$(function()
{
    $('[data-toggle="popover"]').popover();
    initDatetimePicker();
})
