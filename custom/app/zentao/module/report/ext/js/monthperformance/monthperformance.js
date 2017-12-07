/**feature-1077**/
/**feature-1245**/
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
        format: 'yyyy-mm',
        startDate: new Date("2017-04")
    };
    $('input#month').fixedDate().datetimepicker(options);
    
    $('[data-toggle="popover"]').popover();
});

function changeParams(obj)
{
    var orgType = $('.heading').find('#orgType').val();
    
    var userRootId = $('.main .row').find('#userRootId').val();
    var amibaName = $('.main .row').find('#amibaName').val();
    var groupName = $('.main .row').find('#groupName').val();
    var account = $('.main .row').find('#account').val();
    var month = $('.main .row').find('#month').val();
 
    var monthNum = '';
    if(month.indexOf('-') != -1)
    {
        var beginarray = month.split("-");
        for(i=0 ; i < beginarray.length ; i++) monthNum += beginarray[i]; 
    }
    
    var isAmibaChanged = 'false';
    if (obj.name == 'amibaName')
    {
        isAmibaChanged = 'true';
    }
    
    link = createLink('report', 'monthperformance', 'userRootId=' + userRootId + '&amibaName=' + amibaName +'&isAmibaChanged=' + isAmibaChanged + '&groupName=' + groupName +'&account=' + account +'&orgType=' + orgType +'&monthNum=' + monthNum);
    // alert(link);
    location.href=link;
};