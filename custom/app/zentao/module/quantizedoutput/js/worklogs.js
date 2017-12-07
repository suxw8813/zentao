/**feature-1509**/
$(function()
{
     $('[data-toggle="popover"]').popover();
});

function changePeriod(dateNum){
    var dimType = $('.heading').find('#dimType').val();
    var amibaId = $('.heading').find('#amibaId').val();
    var groupId = $('.heading').find('#groupId').val();
    var account = $('.heading').find('#account').val();
    var timeType = $('.heading').find('#timeType').val();
    
    link = createLink('quantizedoutput', 'worklogs', 
        'dimType=' + dimType + '&amibaId=' + amibaId + '&groupId=' + groupId + 
        '&account=' + account + '&dateNum=' + dateNum + '&timeType=' + timeType);
    // alert(link);
    location.href=link;
}