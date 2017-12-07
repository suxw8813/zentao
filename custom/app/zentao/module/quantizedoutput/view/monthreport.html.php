<?php
/**feature-1509**/
include '../../common/view/header.html.php';
include '../../common/view/datepicker.html.php';
include '../../common/view/chart.html.php';

$statPeriod = "统计周期:工作日" . $workDayCount . "天" . "(" . date('Y-m-d', strtotime($begin)) . "~" . date('Y-m-d', strtotime($end)) . ")";
?>

<div id='titlebar'>
  <div class='heading'>
    <span class='prefix'><?php echo html::icon($lang->icons['report-file']);?></span>
    <strong> <?php echo $title;?></strong>
  </div>
</div>
<div class='side'>
  <?php include 'blockreportlist.html.php';?>
  <span class='btn btn-sm' id='saveAsImage' style='margin-top:5px;'>保存为图片</span>
  <?php 
    echo $zcj;
  
    // 测试RestRequest
    /* $app->loadClass('restrequest', true);
    $request = new restrequest('https://182.18.57.7:6443/api/v4/projects/17/merge_requests?private_token=znM7bN5W6RSwuKmmei1H', 'GET');  
    $request->execute();  
    echo '<pre>' . $request->getResponseBody() . '</pre>';  */
  ?>
  
  <?php
  
      // 导出按钮
      $monthNum = date('Ym', strtotime($month));
      $monthReportExportLink = $this->createLink('quantizedoutput', 'monthreportexport', "userRootId=$userRootId&monthNum=$monthNum");
      print(html::a($monthReportExportLink, '导出', '', 'class="iframe btn btn-sm"'));
   ?>
   
  <?php if(common::hasPriv('quantizedoutput', 'recalperformance')):?>
  <form class='form-condensed' method='post' target='hiddenwin'>
      <?php 
      // 以下隐藏域用于 重新计算完成后能够刷新当前页面
      echo html::hidden('fresh_userRootId', $userRootId);
      echo html::hidden('fresh_amibaName', $amibaName);
      echo html::hidden('fresh_groupName', $groupName);
      echo html::hidden('fresh_account', $account);
      echo html::hidden('fresh_monthNum', $monthNum);
      
      echo html::hidden('startNum', date('Ymd', strtotime($begin)) );
      echo html::hidden('endNum',  date('Ymd', strtotime($end)));
      echo html::submitButton('重新计算', "style='width:87px;' onclick=recalPerformanceTip()");
      ?>
  </form>
  <?php endif;?>
  
  <div class='panel panel-body' style='padding: 10px 6px;margin-top:5px;'>
    <div class='text proversion'>
      <strong class='text-danger small text-latin'>有效输出统计-截止今天早上0点。</strong></span>
    </div>
  </div>
  
</div>
<?php
$iconHtml = "<span class='tendency-icon' style='background-image:url(" . $defaultTheme . "images/ext/stat-feature-1509.png)'></span>";
?>
<div class='main' id='monthwork'>
 <hr style='margin:1px 0px;'/>
  <form method='post'>
    <div class='row' style='margin-bottom:5px;padding-top:2px'>
      <div class='col-sm-2'>
        <div class='input-group input-group-sm'>
          <span class='input-group-addon'><?php echo '部门';?></span>
            <?php 
                echo html::select('userRootId', $userRootDict, $userRootId, "class='form-control chosen' onchange='changeParams(this)'");
            ?>
        </div>
      </div>
      <div class='col-sm-2'>
        <div style='margin-top:7px;'>
        <?php
            echo "<span class='table-cell'>共计" . $deptUserCount . "人" . "&nbsp;</span>";
            
            $deptTimetendencyLink = $this->createLink('quantizedoutput', 'timetendency', "userRootId=$userRootId&amibaName=amibaName&isAmibaChanged=false&groupName=groupName&account=account&orgType=dept&timeType=月&endNum=$monthNum&beginNum=");
            print(html::a($deptTimetendencyLink, $iconHtml, '', 'class="iframe"'));
          ?>
        </div>
      </div>
      <div class='col-sm-2'>
        <div style='margin-top:7px;' data-container='body' data-toggle='popover' data-placement='top' data-trigger='hover' data-content='<?php 
            echo $lang->quantizedoutput->standard . $lang->quantizedoutput->tasktime. ' = ' . '人数 * 天数 * 8';
            ?>'>
            <?php 
          $deptStandardTime = $deptUserCount * $workDayCount * 8;
          echo $lang->quantizedoutput->standard . $lang->quantizedoutput->tasktime . '(' . $deptStandardTime . ')';
          ?>
        </div>
      </div>
      <div class='col-sm-2'>
        <div style='margin-top:7px;' >
            <?php echo $lang->quantizedoutput->quantizedoutputed . $lang->quantizedoutput->tasktime . '(' . $deptAllTime . ')';?>
        </div>
      </div>
      <div class='col-sm-2'>
        <div style='margin-top:7px;' >
            <?php 
                $dayavgtime = round($deptAllTime / $workDayCount, 0);
                echo $lang->quantizedoutput->day_avg . $lang->quantizedoutput->tasktime . '(' . $dayavgtime . ')';
              ?>
        </div>
      </div>
      <div class='col-sm-2'>
        <div style='margin-top:7px;'  data-container='body' data-toggle='popover' data-placement='top' data-trigger='hover' data-content='<?php 
            echo $lang->quantizedoutput->extra . $lang->quantizedoutput->tasktime. ' = ' . $lang->quantizedoutput->quantizedoutputed . $lang->quantizedoutput->tasktime . ' - 标准工时';
            ?>'>
            <?php 
                $extrtime = (round(($deptAllTime - 8 * $workDayCount * $deptUserCount), 0) - 0);
                echo $lang->quantizedoutput->extra . $lang->quantizedoutput->tasktime . '(' . $extrtime . ')';
              ?>
        </div>
      </div>
    </div>
  </form>
  <hr style='margin-top:1px; margin-bottom:5px;'/>

  <form method='post'>
    <div class='row' style='margin-bottom:5px;'>
      <div class='col-sm-3'>
        <div class='input-group input-group-sm'>
          <span class='input-group-addon'><?php echo $lang->quantizedoutput->amiba_name;?></span>
          <?php echo html::select('amibaname', $amibaNames, $amibaName, "class='form-control chosen' onchange='changeParams(this)'");?>
        </div>
      </div>
      <div class='col-sm-3'>
        <div class='input-group input-group-sm'>
          <span class='input-group-addon'><?php echo $lang->quantizedoutput->group_name;?></span>
          <?php echo html::select('groupname', $groupNames, $groupName, "class='form-control chosen' onchange='changeParams(this)'");?>
        </div>
      </div>
      <div class='col-sm-3'>
        <div class='input-group input-group-sm'>
          <span class='input-group-addon'><?php echo $lang->quantizedoutput->realname;?></span>
          <?php echo html::select('account', $userNames, $account, "class='form-control chosen' onchange='changeParams(this)'");?>
        </div>
      </div>
      <div class='col-sm-3'>
        <div class='input-group input-group-sm'  data-container='body' data-toggle='popover' data-placement='left' data-trigger='hover' data-content='<?php echo $statPeriod;?>'>
          <span class='input-group-addon'><?php echo $lang->quantizedoutput->month;?></span>
          <div class='datepicker-wrapper datepicker-date'><?php echo html::input('month', $month, "class='w-100px form-control' onchange='changeParams(this)'");?></div>
        </div>
      </div>
    </div>
  </form>
  
  <table class='table table-condensed table-striped table-bordered tablesorter active-disabled' id='monthreport'>
    <thead>
        <tr>
          <th width='220' colspan='4'><?php echo $lang->quantizedoutput->amiba_name;?></th>
          <th width='210' colspan='4'><?php echo $lang->quantizedoutput->group_name;?></th>
          <th width='460' colspan='8'><?php echo $lang->quantizedoutput->person;?></th>
        </tr>
        
        <tr>
          <th width='70'><?php echo $lang->quantizedoutput->name;?></th>
          <th width='50'><?php echo $lang->quantizedoutput->quantizedoutputed . $lang->quantizedoutput->tasktime;?></th>
          <th width='50'><?php echo $lang->quantizedoutput->output . $lang->quantizedoutput->efficiency;?></th>
          <th width='50'><?php echo $lang->quantizedoutput->person_avg . $lang->quantizedoutput->output;?></th>
          
          
          <th width='60'><?php echo $lang->quantizedoutput->name;?></th>
          <th width='50'><?php echo $lang->quantizedoutput->quantizedoutputed . $lang->quantizedoutput->tasktime;?></th>
          <th width='50'><?php echo $lang->quantizedoutput->output . $lang->quantizedoutput->efficiency;?></th>
          <th width='50'><?php echo $lang->quantizedoutput->person_avg . $lang->quantizedoutput->output;?></th>
          
          
          <th width='60'><?php echo $lang->quantizedoutput->name;?></th>
          <th width='50'><?php echo $lang->quantizedoutput->quantizedoutputed . $lang->quantizedoutput->tasktime;?></th>
          <th width='50'><?php echo $lang->quantizedoutput->day_avg . $lang->quantizedoutput->tasktime;?></th>
          <th width='50' data-container='body' data-toggle='popover' data-placement='top' data-trigger='hover' data-content='<?php 
            echo $lang->quantizedoutput->extra . $lang->quantizedoutput->tasktime. ' = ' . $lang->quantizedoutput->quantizedoutputed . $lang->quantizedoutput->tasktime . ' - 标准工时';
            ?>'>
          <?php echo $lang->quantizedoutput->extra . $lang->quantizedoutput->tasktime;?>
          </th>
          
          <th width='50' data-container='body' data-toggle='popover' data-placement='top' data-trigger='hover' data-content='<?php 
            echo '平均输出 = 总有效输出 / 有效输出不为0的人数';
            ?>'>
              <?php 
              echo '平均输出';
              ?>
          </th>
          <th width='50' style='color:red;'><?php echo $lang->quantizedoutput->real . $lang->quantizedoutput->output;?></th>
          <th width='50'><?php echo $lang->quantizedoutput->day_avg . $lang->quantizedoutput->output;?></th>
          <th width='50' style='color:red;' data-container='body' data-toggle='popover' data-placement='left' data-trigger='hover' data-content='<?php 
            echo $lang->quantizedoutput->output . $lang->quantizedoutput->efficiency. ' = ' . $lang->quantizedoutput->real . $lang->quantizedoutput->output . 
                ' / ' . $lang->quantizedoutput->quantizedoutputed . $lang->quantizedoutput->tasktime;
            ?>'>
          <?php echo $lang->quantizedoutput->output . $lang->quantizedoutput->efficiency;?>
          </th>
        </tr>
    </thead>
    <tbody>
    <?php $group_count = 0;?>
    <?php foreach($amibas as $amiba):?>
      <tr class="a-center">
        <?php $amiba_user_count = isset($amiba->groups) ? $amiba->usercount : 1;?>
        <?php $amiba_real_user_count = isset($amiba->groups) ? $amiba->realusercount : 1;?>
        <td align='left' rowspan="<?php echo $amiba_user_count;?>">
        <?php 
            echo $amiba->amiba_name . $amiba_real_user_count . '人';
        ?>
        </td>
        <td align='left' rowspan="<?php echo $amiba_user_count;?>">
        <?php 
            // 一级组织-总工时
            $timetendencyLink = $this->createLink('quantizedoutput', 'timetendency', "userRootId=$userRootId&amibaName=$amiba->amiba_name&isAmibaChanged=false&groupName=groupName&account=account&orgType=amiba&timeType=月&endNum=$monthNum&beginNum=");
            
            echo "<a href='$timetendencyLink' class='iframe'>" . $amiba->total_time . "</a>";
        ?>
        </td>
        <td align='left' rowspan="<?php echo $amiba_user_count;?>" data-container='body' data-toggle='popover' data-placement='right' data-trigger='hover' data-content='<?php 
        if(!empty($amiba->total_output) && !empty($amiba->total_time))
        {
            $amibaEfficiency = number_format($amiba->total_output / $amiba->total_time, 2);
            echo $lang->quantizedoutput->output . $lang->quantizedoutput->efficiency . '(' . $amibaEfficiency . ')' . '=' . $lang->quantizedoutput->total . $lang->quantizedoutput->output . '(' . $amiba->total_output . ')/' . $lang->quantizedoutput->total . $lang->quantizedoutput->tasktime . '(' . $amiba->total_time . ')' ;
        }
        ?>'>
        <?php
            $fontColor = $amiba->mod_merge_count > 0 ? 'orange' : '#03c';
            // 一级组织-输出效率
            if(!empty($amiba->total_output) && !empty($amiba->total_time))
            {
                $monthPerformanceLink = $this->createLink('quantizedoutput', 'monthperformance', 
                "userRootId=$userRootId&amibaName=$amiba->amiba_name&isAmibaChanged=false&groupName=groupName&account=account&orgType=amiba&monthNum=$monthNum");
            
                echo "<a href='$monthPerformanceLink' class='iframe' data-width='80%' style='color:$fontColor'>" . $amibaEfficiency . "</a>";
            }
            else
            {
                echo '-';
            }
        ?>
        </td>
        <td align='left' rowspan="<?php echo $amiba_user_count;?>">
        <?php
            // 一级组织-人均输出
            if(!empty($amiba->total_output))
            {
                $personAvgOutput = number_format($amiba->total_output / $amiba_real_user_count, 1);
                $monthPerformanceLink = $this->createLink('quantizedoutput', 'monthperformance', 
                "userRootId=$userRootId&amibaName=$amiba->amiba_name&isAmibaChanged=false&groupName=groupName&account=account&orgType=amiba&monthNum=$monthNum");
            
                echo "<a href='$monthPerformanceLink' class='iframe' data-width='80%' style='color:$fontColor'>" . $personAvgOutput . "</a>";
            }
            else
            {
                echo '-';
            }
        ?>
        </td>
        
        <?php if(isset($amiba->groups)):?>
        <?php $id = 1;?>
        <?php foreach($amiba->groups as $group):?>
            <?php if($id != 1) echo "<tr class='a-center'>"?>
            <?php $group_count ++;?>
            <?php $group_user_count = isset($group->users) ? $group->usercount : 1;?>
            <?php $group_real_user_count = isset($group->users) ? $group->realusercount : 1;?>
            <td align='left' style='<?php echo $group_count % 2 === 1 ?  'background-color:#f2f2f2' : 'background-color:white';?>' rowspan="<?php echo $group_user_count;?>">
            <?php 
                echo $group->group_name . $group_real_user_count . '人';
            ?>
            </td>
            <td align='left' style='<?php echo $group_count % 2 === 1 ?  'background-color:#f2f2f2' : 'background-color:white';?>' rowspan="<?php echo $group_user_count;?>">
            <?php 
                // 组-总工时
                $timetendencyLink = $this->createLink('quantizedoutput', 'timetendency', "userRootId=$userRootId&amibaName=$amiba->amiba_name&isAmibaChanged=false&groupName=$group->group_name&account=account&orgType=group&timeType=月&endNum=$monthNum&beginNum=");
                
                echo "<a href='$timetendencyLink' class='iframe'>" . $group->total_time . "</a>";
            ?>
            </td>
            <td align='left' style='<?php echo $group_count % 2 === 1 ?  'background-color:#f2f2f2' : 'background-color:white';?>' rowspan="<?php echo $group_user_count;?>" data-container='body' data-toggle='popover' data-placement='left' data-trigger='hover' data-content='<?php 
            if(!empty($group->total_output) && !empty($group->total_time))
            {
                $groupEfficiency = number_format($group->total_output / $group->total_time, 2);
                echo $lang->quantizedoutput->output . $lang->quantizedoutput->efficiency . '(' . $amibaEfficiency . ')' . '=' . $lang->quantizedoutput->total . $lang->quantizedoutput->output . '(' . $group->total_output . ')/' . $lang->quantizedoutput->total . $lang->quantizedoutput->tasktime . '(' . $group->total_time . ')' ;
            }
            ?>'>
            <?php 
                $fontColor = $group->mod_merge_count > 0 ? 'orange' : '#03c';
                // 组-输出效率
                if(!empty($group->total_output) && !empty($group->total_time))
                {
                    $monthPerformanceLink = $this->createLink('quantizedoutput', 'monthperformance', 
                    "userRootId=$userRootId&amibaName=$amiba->amiba_name&isAmibaChanged=false&groupName=$group->group_name&account=account&orgType=group&monthNum=$monthNum");
                
                    echo "<a href='$monthPerformanceLink' class='iframe' data-width='80%' style='color:$fontColor'>" . $groupEfficiency . "</a>";
                }
                else
                {
                    echo '-';
                }
            ?>
            </td>
            <td align='left' style='<?php echo $group_count % 2 === 1 ?  'background-color:#f2f2f2' : 'background-color:white';?>' rowspan="<?php echo $group_user_count;?>">
            <?php 
                // 组-人均输出
                if(!empty($group->total_output))
                {
                    $personAvgOutput = number_format($group->total_output / $group_real_user_count, 1);
                    $monthPerformanceLink = $this->createLink('quantizedoutput', 'monthperformance', 
                    "userRootId=$userRootId&amibaName=$amiba->amiba_name&isAmibaChanged=false&groupName=$group->group_name&account=account&orgType=group&monthNum=$monthNum");
                
                    echo "<a href='$monthPerformanceLink' class='iframe' data-width='80%' style='color:$fontColor'>" . $personAvgOutput . "</a>";
                }
                else
                {
                    echo '-';
                }
            ?>
            </td>
            
            
            <?php if(isset($group->users)):?>
            <?php $userindex = 1;?>
            <?php foreach($group->users as $user):?>
              <?php if($userindex != 1) echo "<tr class='a-center'>"?>
                <td>
                <?php 
                    echo $user->realname;
                ?>
                </td>
                <td>
                <?php 
                    // 个人-总报工
                    if(!empty($user->total_time))
                    {
                        $timetendencyLink = $this->createLink('quantizedoutput', 'timetendency', "userRootId=$userRootId&amibaName=amiba&isAmibaChanged=false&groupName=groupName&account=$user->account&orgType=person&timeType=月&endNum=$monthNum&beginNum=");
                        
                        echo "<a href='$timetendencyLink' class='iframe'>" . $user->total_time . "</a>";
                    }
                    else
                    {
                        echo '-';
                    }
                ?>
                </td>

                <td>
                    <?php 
                    // 个人-日均报工
                    if(!empty($user->day_avg_time))
                    {
                        $dayNum = date('Ym01', strtotime($month));
                        $personTimetendencyLink = $this->createLink('quantizedoutput', 'timetendency', "userRootId=$userRootId&amibaName=amiba&isAmibaChanged=false&groupName=groupName&account=$user->account&orgType=person&timeType=日&endNum=$dayNum&beginNum=");
                        echo "<a href='$personTimetendencyLink' class='iframe'>" . $user->day_avg_time . "</a>";
                    }
                    else
                    {
                        echo '-';
                    }
                    ?>
                </td>
                
                <td>
                    <?php 
                    // 个人-额外报工
                    if(!empty($user->extra_time))
                    {
                        echo $user->extra_time;
                    }
                    else
                    {
                        echo '-';
                    }
                    ?>
                </td>
                <td>
                <?php 
                    // 个人-平均输出
                    echo $deptAvgOutput;;
                ?>
                </td>
                <td>
                <?php 
                    $fontColor = $user->mod_merge_count > 0 ? 'orange' : '#03c';
                    // 个人-总输出
                    if(!empty($user->total_output))
                    {
                        $monthPerformanceLink = $this->createLink('quantizedoutput', 'monthperformance', 
                        "userRootId=$userRootId&amibaName=amiba&isAmibaChanged=false&groupName=groupName&account=$user->account&orgType=person&monthNum=$monthNum");
                        
                        echo "<a href='$monthPerformanceLink' class='iframe' data-width='80%' style='color:$fontColor'>" . $user->total_output . "</a>";
                    }
                    else
                    {
                        echo '-';
                    }
                ?>
                </td>
                <td>
                    <?php 
                    // 个人-日均输出
                    if(empty($user->day_avg_output) || $user->day_avg_output == 0)
                    {
                        echo '-';
                    }
                    else
                    {
                        echo $user->day_avg_output . "";
                    }
                    ?>
                </td>
                <td align='left' data-container='body' data-toggle='popover' data-placement='left' data-trigger='hover' data-content='<?php 
                if(!empty($user->total_output) && !empty($user->total_time))
                {
                    echo $lang->quantizedoutput->output . $lang->quantizedoutput->efficiency . '(' . $user->output_efficiency . ')' . '=' . $lang->quantizedoutput->total . $lang->quantizedoutput->output . '(' . $user->total_output . ')/' . $lang->quantizedoutput->total . $lang->quantizedoutput->tasktime . '(' . $user->total_time . ')' ;
                }
                ?>'>
                <?php
                    // 个人-输出效率
                    if(!empty($user->total_output) && !empty($user->total_time))
                    {
                        $monthPerformanceLink = $this->createLink('quantizedoutput', 'monthperformance', 
                        "userRootId=$userRootId&amibaName=amiba&isAmibaChanged=false&groupName=groupName&account=$user->account&orgType=person&monthNum=$monthNum");
                        
                        echo "<a href='$monthPerformanceLink' class='iframe' data-width='80%' style='color:$fontColor'>" . $user->output_efficiency . "</a>";
                    }
                    else
                    {
                        echo '-';
                    }
                ?>
                </td>    
              <?php if($userindex != 1) echo "</tr>"?>
              <?php $userindex ++;?>
            <?php endforeach;?>
            <?php else:?>
              <td></td>
            <?php endif;?>
            
          <?php if($id != 1) echo "</tr>"?>
          <?php $id ++;?>
        <?php endforeach;?>
        <?php else:?>
          <td></td>
        <?php endif;?>
      </tr>
    <?php endforeach;?>
    </tbody>
  </table> 
</div>
<script>
function recalPerformanceTip(){
    var msg = 
        "<?php
            // 获取服务地址
            $performanceServiceUrl = $config->quantizedoutput->performanceServiceUrls[$config->worklog->depcode];
            $performanceServiceUrl = str_replace('#{startNum}', date('Ymd', strtotime($begin)), $performanceServiceUrl);
            $performanceServiceUrl = str_replace('#{endNum}', date('Ymd', strtotime($end)), $performanceServiceUrl);
            // 开始计算前提示
            echo date("Y年m月d日", strtotime($begin)) . "-" . date("Y年m月d日", strtotime($end)) 
            . "的有效输出将重新计算。 计算过程大约需要十几秒钟，请您耐心等待。" . '服务地址是：' . $performanceServiceUrl . '。';
        ?>";
    alert(msg);
}
</script>
<?php include '../../common/view/footer.html.php';?>

