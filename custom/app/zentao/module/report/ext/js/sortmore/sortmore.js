/**feature-1077**/
/**feature-1245**/
function changeParams(obj)
{
    var timeType = $('.heading').find('#timeType').val();
    var sortField = $('.heading').find('#sortField').val();
    var userRootId='',amibaname = '',groupname = '',username  = '',timeNum  = '';
    
    if (obj.name == 'userRootId') {
        userRootId = $('.main .row').find('#userRootId').val();
    }
    if (obj.name == 'amibaname') {
        userRootId = $('.main .row').find('#userRootId').val();
        amibaname = $('.main .row').find('#amibaname').val();
    }
    else if(obj.name == 'groupname') {
        userRootId = $('.main .row').find('#userRootId').val();
        amibaname = $('.main .row').find('#amibaname').val();
        groupname = $('.main .row').find('#groupname').val();
    }
    else if(obj.name == 'username' || obj.name == 'time') {
        userRootId = $('.main .row').find('#userRootId').val();
        amibaname = $('.main .row').find('#amibaname').val();
        groupname = $('.main .row').find('#groupname').val();
        username  = $('.main .row').find('#username').val();
    }
    var time  = $('.main .row').find('#time').val();
    
    if(time.indexOf('-') != -1) {
        var beginarray = time.split("-");
        for(i=0 ; i < beginarray.length ; i++) timeNum += beginarray[i]; 
    } else {
        timeNum = time;
    }

    link = createLink('report', 'sortmore', 'timeType=' + timeType + '&sortField=' + sortField + '&userRootId=' + userRootId + '&amibaname=' + amibaname + '&groupname=' + groupname + '&username=' + username + '&timeNum=' + timeNum);
    location.href=link;
}

$(function()
{
    $('[data-toggle="popover"]').popover();
    initDatetimePicker();
})
