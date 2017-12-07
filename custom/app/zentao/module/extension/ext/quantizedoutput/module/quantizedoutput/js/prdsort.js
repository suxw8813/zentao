/**feature-1509**/
$(function()
{
    initDatetimePicker();
    initBurnChar();
    
    $('#saveAsImage').click(function(){
        html2canvas($('div#sort'), {
            onrendered: function(canvas) {
                var imgData = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
                var filename = 'sort-' + (new Date()).getTime() + '.png';
                var save_link = document.createElementNS('http://www.w3.org/1999/xhtml', 'a');
                save_link.href = imgData;
                save_link.download = filename;
                save_link.click();
            }
        });
    });
    
    $('[data-toggle="popover"]').popover();
})

function changeParams(obj)
{
    var amibaId = $('.row').find('#amibaId').val();
    var timeType = $('.heading').find('#timeType').val();
    var timeNum = '';
    var time = $('.row').find('#time').val();
 
    if(time.indexOf('-') != -1){
        var beginarray = time.split("-");
        for(i=0 ; i < beginarray.length ; i++) timeNum += beginarray[i]; 
    } else {
        timeNum = time;
    }

    link = createLink('quantizedoutput', 'prdsort', 'amibaId=' + amibaId + '&timeType=' + timeType + '&timeNum=' + timeNum);
    location.href=link;
}