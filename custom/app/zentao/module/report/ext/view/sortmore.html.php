<?php
/**feature-1077**/
/**feature-1245**/
include '../../../common/view/header.lite.html.php';
include '../../../common/view/chart.html.php';
include '../../../common/view/datepicker.html.php';
if($timeType == $lang->report->month){
    $dateNum = date('Ym',strtotime($time));
} else{
    $dateNum = date('Y',strtotime($time));
}
?>
<div id='titlebar'>
  <div class='heading'>
    <span><?php echo html::icon($lang->icons['report']);?></span>
    <?php 
    $title = $timeType . '报工-排名(' . date('Y年m月d日',strtotime($begin)) ;
    if($end != '')
    {
        $title .= '~' . date('Y年m月d日',strtotime($end));
    }
    $title .= ')';
    
    echo html::hidden('timeType', $timeType);
    echo html::hidden('sortField', $sortField);
    
    // echo $sortField;
    ?>
    <small class='text-muted'> <?php echo $title;?></small>
  </div>
</div>
<div class='worklogs'>

    <div class='main'>
        
        <form method='post'>
            <div class='row' style='margin-bottom:5px;'>
              <?php echo html::hidden('userRootId', $userRootId);?>
              <div class='col-sm-3'>
                <div class='input-group input-group-sm'>
                  <span class='input-group-addon'><?php echo $lang->report->amiba_name;?></span>
                  <?php echo html::select('amibaname', $amibaNames, $amibaName, "class='form-control chosen' onchange='changeParams(this)'");?>
                </div>
              </div>
              <div class='col-sm-3'>
                <div class='input-group input-group-sm'>
                  <span class='input-group-addon'><?php echo $lang->report->group_name;?></span>
                  <?php echo html::select('groupname', $groupNames, $groupName, "class='form-control chosen' onchange='changeParams(this)'");?>
                </div>
              </div>
              <div class='col-sm-3'>
                <div class='input-group input-group-sm'>
                  <span class='input-group-addon'><?php echo $lang->report->realname;?></span>
                  <?php echo html::select('username', $userNames, $userName, "class='form-control chosen' onchange='changeParams(this)'");?>
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
              <th><?php echo $lang->report->sort;?></th>
              <th><?php echo $lang->report->amiba_name;?></th>
              <th><?php echo $lang->report->group_name;?></th>
              <th><?php echo $lang->report->realname;?></th>
              
              <?php if($sortField == 'total_time'):?>
              <th style='color:red;'><?php echo $lang->report->reported . $lang->report->tasktime . '(↓)';?></th>
              <th style='color:red;' width='100'><?php echo $lang->report->day_avg_time . '(' . $workDayCount . '天)';?></th>
              <th style='color:red;' data-container='body' data-toggle='popover' data-placement='top' data-trigger='hover' data-content='<?php 
                echo $lang->report->extra . $lang->report->tasktime. ' = ' . $lang->report->reported . $lang->report->tasktime . ' - 标准工时';
                ?>'>
              <?php echo $lang->report->extra_time;?>
              </th>
              <?php else:?>
              <th><?php echo $lang->report->reported . $lang->report->tasktime;?></th>
              <th width='100'><?php echo $lang->report->day_avg_time . '(' . $workDayCount . '天)';?></th>
              <th data-container='body' data-toggle='popover' data-placement='top' data-trigger='hover' data-content='<?php 
                echo $lang->report->extra . $lang->report->tasktime. ' = ' . $lang->report->reported . $lang->report->tasktime . ' - 标准工时';
                ?>'>
              <?php echo $lang->report->extra_time;?>
              </th>
              <?php endif;?>
              
              <?php if($sortField == 'total_output'):?>
              <th style='color:red;'><?php echo $lang->report->real . $lang->report->output . '(↓)';?></th>
              <th style='color:red;'><?php echo $lang->report->day_avg . $lang->report->output;?></th>
              <?php else:?>
              <th><?php echo $lang->report->real . $lang->report->output;?></th>
              <th><?php echo $lang->report->day_avg . $lang->report->output;?></th>
              <?php endif;?>
              
              
              <?php if($sortField == 'output_efficiency'):?>
              <th style='color:red;' data-container='body' data-toggle='popover' data-placement='left' data-trigger='hover' data-content='<?php 
                echo $lang->report->output . $lang->report->efficiency. ' = ' . $lang->report->real . $lang->report->output . 
                    ' / ' . $lang->report->reported . $lang->report->tasktime;
                ?>'>
                <?php echo $lang->report->output . $lang->report->efficiency . '(↓)';?>
               </th>
              <?php else:?>
              <th data-container='body' data-toggle='popover' data-placement='left' data-trigger='hover' data-content='<?php 
                echo $lang->report->output . $lang->report->efficiency. ' = ' . $lang->report->real . $lang->report->output . 
                    ' / ' . $lang->report->reported . $lang->report->tasktime;
                ?>'>
              <?php echo $lang->report->output . $lang->report->efficiency;?>
              </th>
              <?php endif;?>
            </tr>
            </thead>
            <tbody>
            <?php foreach($monthWorkSortData as $user):?>
              <tr class="a-center">
                <td align='left'><?php echo $user->rankId;?></td>
                <td align='left'><?php echo $user->amiba_name;?></td>
                <td align='left'><?php echo $user->group_name;?></td>
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
                
                // 实报工时
                // echo "<div class='progress-pie' style='float:left; margin-right:10px;' title='". $progress. "%' data-value='" . $progress . "' data-animation='true' data-color='". $color . "'></div>";
                if($user->total_time > 0) {
                    if($timeType == $lang->report->month) {
                        $timetendencyLink = $this->createLink('report', 'timetendency', "userRootId=$userRootId&amibaName=amiba&isAmibaChanged=false&groupName=groupName&account=$user->account&orgType=person&timeType=月&endNum=$dateNum&beginNum=");
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
                // 日均工时
                if($user->day_avg_time > 0) {
                    if($timeType == $lang->report->month) {
                        $dayNum = $dateNum . '01';
                        $personTimetendencyLink = $this->createLink('report', 'timetendency', "userRootId=$userRootId&amibaName=amiba&isAmibaChanged=false&groupName=groupName&account=$user->account&orgType=person&timeType=日&endNum=$dayNum&beginNum=");
                        echo "<a href='$personTimetendencyLink' class='iframe'>" . $user->day_avg_time . "</a>";
                    } else {
                        echo $user->day_avg_time;
                    }
                } else {
                    echo '-';
                }
                ?>
                </td>
                <td align='left'><?php echo $user->extra_time;?></td>
                <td align='left'>
                <?php 
                // 实际输出
                $fontColor = $user->mod_merge_count > 0 ? 'orange' : '#03c';
                if($user->total_output > 0) {
                    if($timeType == $lang->report->month) {
                        $monthPerformanceLink = $this->createLink('report', 'monthperformance', "userRootId=$userRootId&amibaName=amiba&isAmibaChanged=false&groupName=groupName&account=$user->account&orgType=person&monthNum=$dateNum");
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
                <?php echo $user->day_avg_output;?>
                </td>
                <td align='left'>
                <?php 
                // 输出效率
                if($user->output_efficiency > 0) {
                    if($timeType == $lang->report->month) {
                        $monthPerformanceLink = $this->createLink('report', 'monthperformance', "userRootId=$userRootId&amibaName=amiba&isAmibaChanged=false&groupName=groupName&account=$user->account&orgType=person&monthNum=$dateNum");
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
        format: '<?php echo $timeType == '月' ? 'yyyy-mm' : 'yyyy' ;?>',
        startDate: new Date('<?php echo $timeType == '月' ? '2017-04' : '2017' ;?>')
    };
    $('input#time').fixedDate().datetimepicker(options);
}
</script>
<?php 
include '../../../common/view/footer.lite.html.php';
?>