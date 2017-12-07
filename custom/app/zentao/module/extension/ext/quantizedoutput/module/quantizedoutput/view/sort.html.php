<?php
/**feature-1509**/
include '../../common/view/header.html.php';
include '../../common/view/datepicker.html.php';
include '../../common/view/chart.html.php';

$statPeriod = "统计周期:工作日" . $workDayCount . "天" . "(" . date('Y-m-d', strtotime($begin)) . "~" . date('Y-m-d', strtotime($end)) . ")";
if($timeType == $lang->quantizedoutput->month){
    $timeStr = date('Y年m月', strtotime($time));
    $timeNum = date('Ym',strtotime($time));
} else{
    $timeStr = date('Y年', strtotime($time));
    $timeNum = date('Y',strtotime($time));
}
?>

<div id='titlebar'>
  <div class='heading'>
    <span class='prefix'><?php echo html::icon($lang->icons['report-file']);?></span>
    <strong> 
    <?php 
    echo $title;
    echo html::hidden('timeType', $timeType);
    ?>
    </strong>
  </div>
</div>
<div class='side'>
  <?php include 'blockreportlist.html.php';?>
  <span class='btn btn-sm' id='saveAsImage' style='margin-top:5px;'>保存为图片</span>
  <?php 
  echo $zcj;
  ?>
</div>

<div class='main' id='sort'>
    <div class='row' style='margin-left:50px;'>
      <div class='col-sm-3'>
        <div class='input-group input-group-sm w-200px'>
          <span class='input-group-addon'><?php echo '部门';?></span>
            <?php 
                echo html::select('userRootId', $userRootDict, $userRootId, "class='w-100px form-control chosen' onchange='changeParams(this)'");
            ?>
        </div>
      </div>
      <div class='col-sm-3'>
        <div>
          <div class='input-group input-group-sm w-200px'>
              <span class='input-group-addon'><?php echo $timeType;?></span>
              <div class='datepicker-wrapper datepicker-date'><?php echo html::input('time', $time, "class='w-100px form-control' onchange='changeParams(this)'");?></div>
          </div>
      </div>
     </div>
    </div>
    <div class='clearfix'></div>
    
    <div class='row'>
    <div class='container text-center bd-0'>
      <h1 style="margin-top: 0px;" data-container='body' data-toggle='popover' data-placement='bottom' data-trigger='hover' data-content='<?php echo $statPeriod;?>'>
        <?php echo '人-' . $timeType . '有效输出-排名-Top30' . '（' . $timeStr . '）';?>
        <?php 
        $worklogsLink = $this->createLink('quantizedoutput', 'sortmore', "timeType=$timeType&sortField=total_output&userRootId=$userRootId&amibaName=&groupName=&userName=&timeNum=$timeNum");
        print(html::a($worklogsLink, $lang->quantizedoutput->more, '', 'class="iframe btn btn-primary btn-sm" data-width="65%"'));
        ?>
      </h1>
      <div class='canvas-wrapper'><div class='chart-canvas'><canvas id='PersonOutputTop30' width='800' height='100' data-bezier-curve='false' data-responsive='true'></canvas></div></div>
    </div>
    
    <div class='container text-center bd-0'>
      <h1 data-container='body' data-toggle='popover' data-placement='bottom' data-trigger='hover' data-content='<?php echo $statPeriod;?>'>
        <?php echo '人-' . $timeType . '输出效率-排名-Top30' . '（' . $timeStr . '）';?>
        <?php 
        $worklogsLink = $this->createLink('quantizedoutput', 'sortmore', "timeType=$timeType&sortField=output_efficiency&userRootId=$userRootId&amibaName=&groupName=&userName=&timeNum=$timeNum");
        print(html::a($worklogsLink, $lang->quantizedoutput->more, '', 'class="iframe btn btn-primary btn-sm" data-width="65%"'));
        ?>
      </h1>
      <div class='canvas-wrapper'><div class='chart-canvas'><canvas id='PersonOutputEfficiencyTop30' width='800' height='100' data-bezier-curve='false' data-responsive='true'></canvas></div></div>
    </div>
    
    <div class='container text-center bd-0'>
      <h1 data-container='body' data-toggle='popover' data-placement='bottom' data-trigger='hover' data-content='<?php echo $statPeriod;?>'>
        <?php echo '人-' . $timeType . '报工工时-排名-Top30' . '（' . $timeStr . '）';?>
        <?php 
        $worklogsLink = $this->createLink('quantizedoutput', 'sortmore', "timeType=$timeType&sortField=total_time&userRootId=$userRootId&amibaName=&groupName=&userName=&timeNum=$timeNum");
        print(html::a($worklogsLink, $lang->quantizedoutput->more, '', 'class="iframe btn btn-primary btn-sm" data-width="65%"'));
        ?>
      </h1>
      <div class='canvas-wrapper'><div class='chart-canvas'><canvas id='PersonTimeTop30' width='800' height='100' data-bezier-curve='false' data-responsive='true'></canvas></div></div>
    </div>
    
    <div class='container text-center bd-0'>
      <h1 data-container='body' data-toggle='popover' data-placement='bottom' data-trigger='hover' data-content='<?php echo $statPeriod;?>'>
        <?php echo '一级组织人均-' . $timeType . '报工工时-排名' . '（' . $timeStr . '）';?>
      </h1>
      <div class='canvas-wrapper'><div class='chart-canvas'><canvas id='PersonAvgAmibaTimeTop' width='800' height='100' data-bezier-curve='false' data-responsive='true'></canvas></div></div>
    </div>
</div>
<script>
function initDatetimePicker() {
    // 初始化日历控件
    var options = 
    {
        language: config.clientLang,
        weekStart: 1,
        todayBtn:  0,
        autoclose: 1,
        todayHighlight: 1,
        forceParse: 0,
        showMeridian: 1,
        startView: <?php echo $timeType == '月' ? 3 : 4 ;?>,
        minView: <?php echo $timeType == '月' ? 3 : 4 ;?>,
        format: '<?php echo $timeType == '月' ? 'yyyy-mm' : 'yyyy' ;?>'/* ,
        startDate: new Date('<?php echo $timeType == '月' ? '2017-04' : '2017' ;?>') */
    };
    $('input#time').fixedDate().datetimepicker(options);
}

function initBurnChar()
{
    /* 人-报工工时-排名-Top30 */
    var ctx = document.getElementById("PersonTimeTop30");
    var data1 =
    {
        labels: <?php echo $PersonTimeTop30Data['labels']?>,
        datasets: [
        {
            label: "<?php echo '' . $timeType . '报工工时';?>",
            color: "#CCC",
            showTooltips: true,
            data: <?php echo $PersonTimeTop30Data['burnBar']?>
        },
        {
            label: "<?php '天平均工时';?>",
            color: "red",
            showTooltips: true,
            data: <?php echo $PersonTimeTop30Data['burnBar1']?>
        }]
    };
    var burnChart = $("#PersonTimeTop30").barChart(data1,
    {
        animation: !($.zui.browser && $.zui.browser.ie === 8),
        pointDotStrokeWidth: 0,
        pointDotRadius: 1,
        datasetFill: false,
        datasetStroke: true,
        scaleShowBeyondLine: false,
        multiTooltipTemplate: "<%= value %>h"
    });
    
    /* 人-有效输出-排名-Top30 */
    var ctx = document.getElementById("PersonOutputTop30");
    var personOutputTop30Data =
    {
        labels: <?php echo $PersonOutputTop30Data['labels']?>,
        datasets: [
        {
            label: "<?php echo '' . $timeType . '有效输出';?>",
            color: "#CCC",
            showTooltips: true,
            data: <?php echo $PersonOutputTop30Data['burnBar']?>
        }]
    };
    var burnChart = $("#PersonOutputTop30").barChart(personOutputTop30Data,
    {
        animation: !($.zui.browser && $.zui.browser.ie === 8),
        pointDotStrokeWidth: 0,
        pointDotRadius: 1,
        datasetFill: false,
        datasetStroke: true,
        scaleShowBeyondLine: false,
        multiTooltipTemplate: "<%= value %>h"
    });
    
    /* 人-输出效率-排名-Top30 */
    var ctx = document.getElementById("PersonOutputEfficiencyTop30");
    var personOutputEfficiencyTop30Data =
    {
        labels: <?php echo $PersonOutputEfficiencyTop30Data['labels']?>,
        datasets: [
        {
            label: "<?php echo '' . $timeType . '有效输出';?>",
            color: "#CCC",
            showTooltips: true,
            data: <?php echo $PersonOutputEfficiencyTop30Data['burnBar']?>
        }]
    };
    var burnChart = $("#PersonOutputEfficiencyTop30").barChart(personOutputEfficiencyTop30Data,
    {
        animation: !($.zui.browser && $.zui.browser.ie === 8),
        pointDotStrokeWidth: 0,
        pointDotRadius: 1,
        datasetFill: false,
        datasetStroke: true,
        scaleShowBeyondLine: false,
        multiTooltipTemplate: "<%= value %>h"
    });
    
    /* 一级组织人均-报工工时-排名 */
    var data3 =
    {
        labels: <?php echo $PersonAvgAmibaTimeTopData['labels']?>,
        datasets: [
        {
            label: "<?php echo zcj;?>",
            color: "#CCC",
            showTooltips: false,
            data: <?php echo $PersonAvgAmibaTimeTopData['burnBar']?>
        }]
    };
    var burnChart = $("#PersonAvgAmibaTimeTop").barChart(data3,
    {
        animation: !($.zui.browser && $.zui.browser.ie === 8),
        pointDotStrokeWidth: 0,
        pointDotRadius: 1,
        datasetFill: false,
        datasetStroke: true,
        scaleShowBeyondLine: false,
        multiTooltipTemplate: "<%= value %>h"
    });
}
</script>
<?php include '../../common/view/footer.html.php';?>
