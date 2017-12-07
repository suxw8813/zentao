/**feature-1509**/
$(function()
{
    // 初始化日历控件
    var options = 
    {
        language: config.clientLang,
        weekStart: 1,
        todayBtn:  0,
        autoclose: 1,
        todayHighlight: 1,
        startView: 3,
        forceParse: 0,
        showMeridian: 1,
        minView: 3,
        format: 'yyyy-mm'/* ,
        startDate: new Date("2017-04") */
    };
    $('input#begin').fixedDate().datetimepicker(options);
    $('input#end').fixedDate().datetimepicker(options);
    
    initBurnChar();
});

function changeParams(obj)
{
    var orgType = $('.heading').find('#orgType').val();
    var timeType = $('.heading').find('#timeType').val();
    
    var userRootId = $('.main .row').find('#userRootId').val();
    var amibaName = $('.main .row').find('#amibaName').val();
    var groupName = $('.main .row').find('#groupName').val();
    var account = $('.main .row').find('#account').val();
    var begin = $('.main .row').find('#begin').val();
    var end = $('.main .row').find('#end').val();
 
    var beginNum = '', endNum = '';
    if(begin.indexOf('-') != -1)
    {
        var beginarray = begin.split("-");
        for(i=0 ; i < beginarray.length ; i++) beginNum += beginarray[i]; 
    }
    if(end.indexOf('-') != -1)
    {
        var endarray = end.split("-");
        for(i=0 ; i < endarray.length ; i++) endNum += endarray[i]; 
    }    
    
    var isAmibaChanged = 'false';
    if (obj.name == 'amibaName')
    {
        isAmibaChanged = 'true';
    }
    
    link = createLink('quantizedoutput', 'timetendency', 'userRootId=' + userRootId + '&amibaName=' + amibaName +'&isAmibaChanged=' + isAmibaChanged + '&groupName=' + groupName +'&account=' + account +'&orgType=' + orgType +'&timeType=' + timeType + '&endNum=' + endNum + '&beginNum=' + beginNum);
    // alert(link);
    location.href=link;
};