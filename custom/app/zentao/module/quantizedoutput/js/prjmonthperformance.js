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
    $('input#month').fixedDate().datetimepicker(options);
    
    $('[data-toggle="popover"]').popover();
});

function changeParams(obj)
{
    var amibaId = '',groupId = '',account  = '',monthNum  = '';
    
    if (obj.name == 'amibaId') {
        amibaId = $('.main .row').find('#amibaId').val();
    }
    else if(obj.name == 'groupId') {
        amibaId = $('.main .row').find('#amibaId').val();
        groupId = $('.main .row').find('#groupId').val();
    }
    else if(obj.name == 'account' || obj.name == 'month') {
        amibaId = $('.main .row').find('#amibaId').val();
        groupId = $('.main .row').find('#groupId').val();
        account  = $('.main .row').find('#account').val();
    }
    var month  = $('.main .row').find('#month').val();
    
    if(month.indexOf('-') != -1) {
        var beginarray = month.split("-");
        for(i=0 ; i < beginarray.length ; i++) monthNum += beginarray[i]; 
    }
    
    link = createLink('quantizedoutput', 'prjmonthperformance', 'amibaId=' + amibaId 
        + '&groupId=' + groupId +'&account=' + account 
        +'&monthNum=' + monthNum);
    // alert(link);
    location.href=link;
};