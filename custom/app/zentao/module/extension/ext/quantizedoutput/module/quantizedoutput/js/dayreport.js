/**feature-1509**/
function changeParams(obj)
{
    var userRootId='',amibaname = '',groupname = '',username  = '',dayNum  = '';
    
    if(obj.name == 'userRootId'){
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
    else if(obj.name == 'username' || obj.name == 'day') {
        userRootId = $('.main .row').find('#userRootId').val();
        amibaname = $('.main .row').find('#amibaname').val();
        groupname = $('.main .row').find('#groupname').val();
        username  = $('.main .row').find('#username').val();
    }
    var day  = $('.main .row').find('#day').val();
 
    if(day.indexOf('-') != -1) {
        var beginarray = day.split("-");
        for(i=0 ; i < beginarray.length ; i++) dayNum += beginarray[i]; 
    }

    link = createLink('quantizedoutput', 'dayreport', 'userRootId=' + userRootId + '&amibaname=' + amibaname + '&groupname=' + groupname + '&username=' + username + '&dayNum=' + dayNum);
    location.href=link;
}

$(function()
{
    var options = 
    {
        language: config.clientLang,
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0,
        showMeridian: 1,
        minView: 2,
        format: 'yyyy-mm-dd'/* ,
        startDate: new Date("2017-04-01") */
    };
    $('input#day').fixedDate().datetimepicker(options);
    
    $('#saveAsImage').click(function(){
        html2canvas( $('div#daywork')[0], {
            onrendered: function(canvas) {
                var imgData = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
                var filename = 'daywork-' + (new Date()).getTime() + '.png';
                var save_link = document.createElementNS('http://www.w3.org/1999/xhtml', 'a');
                save_link.href = imgData;
                save_link.download = filename;
                save_link.click();
                // var event = document.createEvent('MouseEvents');
                // event.initMouseEvent('click', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
            }
        });
    });
    setTimeout(function(){fixedTheadOfList('#dayreport')}, 100);
});


