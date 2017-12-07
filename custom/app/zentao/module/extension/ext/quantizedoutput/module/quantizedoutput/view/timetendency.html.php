<?php
/**feature-1509**/
include '../../common/view/header.lite.html.php';
include '../../common/view/chart.html.php';
include '../../common/view/datepicker.html.php';
include '../../common/view/chosen.html.php';
?>
<div id='titlebar'>
  <div class='heading'>
    <?php 
    echo "<span class='tendency-icon' style='background-image:url(" . $defaultTheme . "images/ext/stat-feature-1509.png)'></span>";
    echo html::hidden('orgType', $orgType);
    echo html::hidden('timeType', $timeType);
    
    echo html::hidden('amibaName', $amibaName);
    echo html::hidden('groupName', $groupName);
    echo html::hidden('account', $timeType);    
    ?>
    <small class='text-muted'> <?php echo $title;?></small>
  </div>
</div>
<div class='worklogs'>
  
    <div class='main' style='margin:auto;'>
        <form method='post' class="container">
            <div class='row' style='margin-bottom:5px;'>
                <?php echo html::hidden('userRootId', $userRootId);?>
                <?php if($orgType == 'amiba' || $orgType == 'group'):?>
                <div class='col-sm-3'>
                    <div class='input-group input-group-sm'>
                      <span class='input-group-addon'><?php echo $lang->quantizedoutput->amiba_name;?></span>
                      <?php 
                      echo html::select('amibaName', $amibaNames, $amibaName, "class='form-control chosen' onchange='changeParams(this)'");
                      ?>
                    </div>
                </div>
                <?php endif?>
                <?php if($orgType == 'group'):?>
                <div class='col-sm-3'>
                    <div class='input-group input-group-sm'>
                      <span class='input-group-addon'><?php echo $lang->quantizedoutput->group_name;?></span>
                      <?php echo html::select('groupName', $groupNames, $groupName, "class='form-control chosen' onchange='changeParams(this)'");?>
                    </div>
                </div>                 
                <?php endif?>
                <?php if($orgType == 'person'):?>
                <div class='col-sm-3'>
                    <div class='input-group input-group-sm'>
                      <span class='input-group-addon'><?php echo $lang->quantizedoutput->realname;?></span>
                      <?php echo html::select('account', $userNames, $account, "class='form-control chosen' onchange='changeParams(this)'");?>
                    </div>
                </div>                
                <?php endif?>
                <div class='col-sm-5'>
                    <div class='input-group input-group-sm'>
                      <?php $formdateclass = '';
                      if($timeType == '日')
                      {
                          $formdateclass = 'form-date';
                      }
                      else
                      {
                          $formdateclass = '';
                      }    
                      ?>
                      <span class='input-group-addon'><?php echo $lang->quantizedoutput->dateSect;?></span>
                      <div class='datepicker-wrapper datepicker-date'><?php echo html::input('begin', $begin, "class='w-100px form-control $formdateclass' onchange='changeParams(this)'");?></div>
                      <span class='input-group-addon fix-border'><?php echo $lang->quantizedoutput->to;?></span>
                      <div class='datepicker-wrapper datepicker-date'><?php echo html::input('end', $end, "class='form-control $formdateclass' onchange='changeParams(this)'");?></div>
                    </div>
                </div>
              </div>
            </div>
          </form>
        
         <div class='container text-center bd-0'>
          <div class='canvas-wrapper'><div class='chart-canvas'><canvas id='burnChart' width='800' height='200' data-bezier-curve='false' data-responsive='true'></canvas></div></div>
          <h1><?php echo $projectName . ' ' . $this->lang->project->burn;?></h1>
        </div>
        
    </div>
</div>

<script>
function initBurnChar()
{
    var ctx = document.getElementById("burnChart");
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo $chartData['labels']?>,
            datasets: [{
                label: '工作小时数',
                data: <?php echo $chartData['burnLine']?>,
                fill:false,
                backgroundColor: ['blue'],
                borderColor: ['blue'],
                borderWidth: 1,
                borderJoinStyle:'miter',
                
                pointBorderColor: "blue",
                pointBackgroundColor: "blue",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "blue",
                pointHoverBorderColor: "blue",
                pointHoverBorderWidth: 2,
                pointRadius: 2,
                pointHitRadius: 10,
                // pointStyle:'triangle',
            },{
                label: '标准小时数',
                data: <?php echo $chartData['baseLine']?>,
                fill:false,
                backgroundColor: ['gray'],
                borderColor: ['gray'],
                borderWidth: 1,
                borderDash:[2,2],
                borderJoinStyle:'miter',
                
                pointBorderColor: "gray",
                pointBackgroundColor: "gray",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "gray",
                pointHoverBorderColor: "gray",
                pointHoverBorderWidth: 2,
                pointRadius: 2,
                pointHitRadius: 10,
                // pointStyle:'triangle',
            },]
        },
        options: {
            responsive: true,
            hover: {
                mode: 'index'
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:false,
                        min:0,
                        // max:24
                    }
                }]
            },
            legend: {
                display: false
            },
            tooltips: {
                mode: 'index',
                intersect: true
            },
            // events:["click"],
            onClick: function(e){
                if(e.type != 'click') return;
                
                var self = this;
                if(self.active == 'undefine' || self.active == null || self.active.length == 0) return;
                if(self.active[0]._datasetIndex != 0) return;
                if(self.active[0]._xScale.ticks == null || self.active[0]._xScale.ticks.length <= 0) return;
                
                var labelIndex = this.active[0]._index;
                var datasetIndex = this.active[0]._datasetIndex;
                var value = this.chart.config.data.datasets[datasetIndex].data[labelIndex].y;
                if (value <= 0) return;
                
                var orgType = $('.heading').find('#orgType').val();
                if(orgType != 'person') return;
                
                
                var amibaName = $('.main .row').find('#amibaName').val();
                var groupName = $('.main .row').find('#groupName').val();
                var account = $('.main .row').find('#account').val();
                var timeType = $('.heading').find('#timeType').val();
                
                var dateNum = this.chart.config.data.datasets[datasetIndex].data[labelIndex].x;
                if(timeType == '月')
                {
                    dateNum = dateNum.substr(0, 6);
                }
                
                var link = createLink('quantizedoutput', 'worklogs', 
                    'dimType=' + 'staff' + '&amibaId=' + 
                    '&groupId=' + '&account=' + account + 
                    '&dateNum=' + dateNum + '&timeType=' + timeType);
                // alert(link);
                location.href=link;
            }
        }
    });
    
};
</script>
<?php 
echo $zcj;
include '../../common/view/footer.lite.html.php';
?>
