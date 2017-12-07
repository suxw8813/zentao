<?php
/**feature-1509**/
include '../../common/view/header.lite.html.php';
include '../../common/view/chart.html.php';
include '../../common/view/datepicker.html.php';
if($timeType == $lang->quantizedoutput->month){
    $dateNum = date('Ym',strtotime($time));
} else{
    $dateNum = date('Y',strtotime($time));
}
?>
<div id='titlebar'>
  <div class='heading'>
    <span><?php echo html::icon($lang->icons['quantizedoutput']);?></span>
    <?php 
    $title = $timeType . '报工-排名(' . date('Y年m月d日',strtotime($begin)) ;
    if($end != '')
    {
        $title .= '~' . date('Y年m月d日',strtotime($end));
    }
    $title .= ')';
    
    echo html::hidden('timeType', $timeType);
    echo html::hidden('sortField', $sortField);
    
    ?>
    <small class='text-muted'> <?php echo $title;?></small>
  </div>
</div>
<div class='worklogs'>

    <div class='main'>
        
        <form method='post'>
            <div class='row' style='margin-bottom:5px;'>
              <div class='col-sm-5'>
                <div class='input-group input-group-sm'>
                  <span class='input-group-addon'><?php echo $lang->projectCommon;?></span>
                  <?php echo html::select('amibaId', $amibaNameDict, $amibaId, "class='form-control chosen' onchange='changeParams(this)'");?>
                </div>
              </div>
              <div class='col-sm-3'>
                <div class='input-group input-group-sm'>
                  <span class='input-group-addon'><?php echo $timeType;?></span>
                  <div class='datepicker-wrapper datepicker-date'><?php echo html::input('time', $time, "class='w-100px form-control' onchange='changeParams(this)'");?></div>
                </div>
              </div>
            </div>
          </form>
        
         <table class='table table-condensed table-striped table-bordered tablesorter table-fixed active-disabled' id='daywork'>
            <thead>
            <tr class='colhead'>
              <th><?php echo $lang->quantizedoutput->sort;?></th>
              <th width='200'><?php echo $lang->projectCommon;?></th>
              <th><?php echo $lang->quantizedoutput->realname;?></th>
              
              <?php if($sortField == 'total_time'):?>
              <th style='color:red;'><?php echo $lang->quantizedoutput->quantizedoutputed . $lang->quantizedoutput->tasktime . '(↓)';?></th>
              <?php else:?>
              <th><?php echo $lang->quantizedoutput->quantizedoutputed . $lang->quantizedoutput->tasktime;?></th>
              <?php endif;?>
              
              <?php if($sortField == 'total_output'):?>
              <th style='color:red;'><?php echo $lang->quantizedoutput->real . $lang->quantizedoutput->output . '(↓)';?></th>
              <?php else:?>
              <th><?php echo $lang->quantizedoutput->real . $lang->quantizedoutput->output;?></th>
              <?php endif;?>
              
              
              <?php if($sortField == 'output_efficiency'):?>
              <th style='color:red;' data-container='body' data-toggle='popover' data-placement='left' data-trigger='hover' data-content='<?php 
                echo $lang->quantizedoutput->output . $lang->quantizedoutput->efficiency. ' = ' . $lang->quantizedoutput->real . $lang->quantizedoutput->output . 
                    ' / ' . $lang->quantizedoutput->quantizedoutputed . $lang->quantizedoutput->tasktime;
                ?>'>
                <?php echo $lang->quantizedoutput->output . $lang->quantizedoutput->efficiency . '(↓)';?>
               </th>
              <?php else:?>
              <th data-container='body' data-toggle='popover' data-placement='left' data-trigger='hover' data-content='<?php 
                echo $lang->quantizedoutput->output . $lang->quantizedoutput->efficiency. ' = ' . $lang->quantizedoutput->real . $lang->quantizedoutput->output . 
                    ' / ' . $lang->quantizedoutput->quantizedoutputed . $lang->quantizedoutput->tasktime;
                ?>'>
              <?php echo $lang->quantizedoutput->output . $lang->quantizedoutput->efficiency;?>
              </th>
              <?php endif;?>
            </tr>
            </thead>
            <tbody>
            <?php foreach($monthWorkSortData as $user):?>
              <tr class="a-center">
                <td align='left'><?php echo $user->rankId;?></td>
                <td align='left'><?php echo $user->amiba_name;?></td>
                <td align='left'><?php echo $user->realname;?></td>
                <?php
                $cellBackgroundColor = 'white';
                $color = '#4CAF50';
                $progress = number_format($user->total_time / 240 * 100, 0);
                if ($user->total_time == 0)
                {
                    $cellBackgroundColor = 'red';
                    $color = 'red';
                }
                else if($user->total_time < 8)
                {
                    $cellBackgroundColor = 'yellow';
                    $color = 'yellow';
                }
                
                $cellBackgroundColor = 'white';
                echo "<td class='linkbox' style='background-color:" . $cellBackgroundColor . ";'>";
                
                // 个人-实报工时
                // echo "<div class='progress-pie' style='float:left; margin-right:10px;' title='". $progress. "%' data-value='" . $progress . "' data-animation='true' data-color='". $color . "'></div>";
                if($user->total_time > 0) {
                    if($timeType == $lang->quantizedoutput->month) {
                        $timetendencyLink = $this->createLink('quantizedoutput', 'worklogs', 
                            "dimType=prj&amibaId=$user->amiba_id&groupId=&account=$user->account&dateNum=$monthNum&timeType=月");
                        echo "<a href='$timetendencyLink' class='iframe'>" . $user->total_time . "</a>";
                    } else {
                        echo $user->total_time . '时';
                    }
                } else {
                    echo '-';
                }
                echo "</td>";
                ?> 
                <td align='left'>
                <?php 
                // 个人-实际输出
                $fontColor = $user->mod_merge_count > 0 ? 'orange' : '#03c';
                if($user->total_output > 0) {
                    if($timeType == $lang->quantizedoutput->month) {
                        $monthPerformanceLink = $this->createLink('quantizedoutput', 'prjmonthperformance', 
                            "amibaId=$user->amiba_id&&groupId=&account=$user->account&monthNum=$dateNum&tag=skipGroupId");
                        echo "<a href='$monthPerformanceLink' class='iframe' data-width='80%' style='color:$fontColor'>" . $user->total_output . "</a>";
                    } else {
                        echo $user->total_output;
                    }
                }
                else {
                    echo '-';
                }
                ?>
                </td>
                <td align='left'>
                <?php 
                // 个人-输出效率
                if($user->output_efficiency > 0) {
                    if($timeType == $lang->quantizedoutput->month) {
                        $monthPerformanceLink = $this->createLink('quantizedoutput', 'prjmonthperformance', 
                            "amibaId=$user->amiba_id&&groupId=&account=$user->account&monthNum=$dateNum&tag=skipGroupId");
                        echo "<a href='$monthPerformanceLink' class='iframe' data-width='80%' style='color:$fontColor'>" . $user->output_efficiency . "</a>";
                    } else {
                        echo $user->output_efficiency;
                    }
                }
                else {
                    echo '-';
                }
                ?>
                </td>
              </tr>
            <?php endforeach;?>
            </tbody>
        </table> 
        
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
</script>
<?php 
echo $zcj;
include '../../common/view/footer.lite.html.php';
?>