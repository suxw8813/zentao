/**feature-1077**/
$(function()
{
     $('[data-toggle="popover"]').popover();
});

function changePeriod(dateNum){
    var account = $('.heading').find('#account').val();
    var timeType = $('.heading').find('#timeType').val();
    
    link = createLink('report', 'worklogs', 'account=' + account + '&dateNum=' + dateNum + '&timeType=' + timeType);
    location.href=link;
}