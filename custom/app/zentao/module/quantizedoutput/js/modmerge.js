/**feature-1509**/

$(function(){
     $('[data-toggle="popover"]').popover({html:true});
     setTimeout(function(){fixedTheadOfList('#mergeDetailInfoTable')}, 100);
});

function changePerformanceFontColor(oldFieldName, id){
    var oldSelect = "#" + oldFieldName + "\\[" + id + "\\]";
    var oldValue = $('#mergeDetailInfoTable').find(oldSelect).val();
    
    var newSelect = "#" + oldFieldName + "_new\\[" + id + "\\]";
    var newValue = $('#mergeDetailInfoTable').find(newSelect).val();
    
    var fontColor = oldValue === newValue ? 'black' : 'orange'
    $('#mergeDetailInfoTable').find(newSelect).css("color", fontColor);
}