/**feature-1509**/
function changeParams(obj)
{
    var userRootId = '',amibaname = '',groupname = '',account  = '',monthNum  = '';
    
    if (obj.name == 'userRootId') {
        userRootId = $('.main .row').find('#userRootId').val();
    }
    else if (obj.name == 'amibaname') {
        userRootId = $('.main .row').find('#userRootId').val();
        amibaname = $('.main .row').find('#amibaname').val();
    }
    else if(obj.name == 'groupname') {
        userRootId = $('.main .row').find('#userRootId').val();
        amibaname = $('.main .row').find('#amibaname').val();
        groupname = $('.main .row').find('#groupname').val();
    }
    else if(obj.name == 'account' || obj.name == 'month') {
        userRootId = $('.main .row').find('#userRootId').val();
        amibaname = $('.main .row').find('#amibaname').val();
        groupname = $('.main .row').find('#groupname').val();
        account  = $('.main .row').find('#account').val();
        
    }
    var month  = $('.main .row').find('#month').val();
    
    if(month.indexOf('-') != -1) {
        var beginarray = month.split("-");
        for(i=0 ; i < beginarray.length ; i++) monthNum += beginarray[i]; 
    }

    link = createLink('quantizedoutput', 'monthreport', 'userRootId=' + userRootId + '&amibaname=' + amibaname + '&groupname=' + groupname + '&account=' + account + '&monthNum=' + monthNum);
    // alert(link);
    location.href=link;
}

$(function()
{
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
    
    $('#saveAsImage').click(function(){
        html2canvas($('div#monthwork')[0], {
            onrendered: function(canvas) {
                var imgData = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
                var filename = 'monthwork-' + (new Date()).getTime() + '.png';
                var save_link = document.createElementNS('http://www.w3.org/1999/xhtml', 'a');
                save_link.href = imgData;
                save_link.download = filename;
                save_link.click();
            }
        });
    });
    
    $('[data-toggle="popover"]').popover();
    setTimeout(function(){fixedTheadOfList('#monthreport')}, 100);
})
