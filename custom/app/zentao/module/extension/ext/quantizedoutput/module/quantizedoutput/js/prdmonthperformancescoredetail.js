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
    
    $('[data-toggle="popover"]').popover({html:true});
    
    
    
    setTimeout(function(){fixedTheadOfList('#groupTable')}, 100);
    $(document).on('click', '.expandAll', function()
    {
        $('.expandAll').addClass('hidden');
        $('.collapseAll').removeClass('hidden');
        $('table#groupTable').find('tbody').each(function()
        {
            $(this).find('tr').addClass('hidden');
            $(this).find('tr.group-collapse').removeClass('hidden');
        })
    });
    $(document).on('click', '.collapseAll', function()
    {
        $('.collapseAll').addClass('hidden');
        $('.expandAll').removeClass('hidden');
        $('table#groupTable').find('tbody').each(function()
        {
            $(this).find('tr').removeClass('hidden');
            $(this).find('tr.group-collapse').addClass('hidden');
        })
    });
    $('.expandGroup').closest('.groupby').click(function()
    {
        $tbody = $(this).closest('tbody');
        $tbody.find('tr').addClass('hidden');
        $tbody.find('tr.group-collapse').removeClass('hidden');
    });
    $('.collapseGroup').click(function()
    {
        $tbody = $(this).closest('tbody');
        $tbody.find('tr').removeClass('hidden');
        $tbody.find('tr.group-collapse').addClass('hidden');
    });
});

function changeParams(obj)
{
    var scoreType = $('.heading').find('#scoreType').val();
    
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
    
    // alert('dd');
    link = createLink('quantizedoutput', 'prdmonthperformancescoredetail', 
        'scoreType=' + scoreType + '&amibaId=' + amibaId + '&groupId=' + groupId 
        + '&account=' + account +'&monthNum=' + monthNum);
    // alert(link);
    location.href=link;
};