<?php
/**feature-1509**/
include '../../common/view/header.html.php';
include '../../common/view/datepicker.html.php';
include '../../common/view/chart.html.php';
?>

<div id='titlebar'>
  <div class='heading'>
    <span class='prefix'><?php echo html::icon($lang->icons['report-file']);?></span>
    <?php 
    echo $zcj;
    ?>
    <strong> <?php echo $title;?></strong>
  </div>
</div>
<div class='side'>
  <?php include 'blockreportlist.html.php';?>
  <span class='btn btn-sm' id='saveAsImage' style='margin-top:5px;'>保存为图片</span>
  <?php echo $zcj;?>
</div>
<?php
$iconHtml = "<span class='tendency-icon' style='background-image:url(" . $defaultTheme . "images/ext/stat-feature-1509.png)'></span>";
?>

<div class='main' style='width:75%' id='daywork'>
<hr style='margin:1px 0px;'/>
  <form method='post'>
    <div class='row' style='margin-bottom:5px;padding-top:2px'>
      <div class='col-sm-3'>
        <div class='input-group input-group-sm'>
          <span class='input-group-addon'><?php echo '部门';?></span>
            <?php 
                echo html::select('userRootId', $userRootDict, $userRootId, "class='form-control chosen' onchange='changeParams(this)'");
            ?>
        </div>
      </div>
      <div class='col-sm-3'>
        <div style='margin-top:7px;'>
        <?php
            echo "<span class='table-cell'>共计" . $deptUserCount . "人" . "&nbsp;</span>";
            
            $endNum = date('Ymd',strtotime($day));
            $deptTimetendencyLink = $this->createLink('quantizedoutput', 'timetendency', "userRootId=$userRootId&amibaName=amibaName&isAmibaChanged=false&groupName=groupName&account=account&orgType=dept&timeType=日&endNum=$endNum&beginNum=");
            print(html::a($deptTimetendencyLink, $iconHtml, '', 'class="iframe"'));
          ?>
        </div>
      </div>
      <div class='col-sm-3'>
        <div style='margin-top:7px;'>
        <?php echo $lang->quantizedoutput->quantizedoutputed . $lang->quantizedoutput->tasktime . '(' . $deptAllTime . '小时)';?>
        </div>
      </div>
    </div>
    <hr style='margin-top:1px; margin-bottom:5px;'/>
  
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
          <?php echo html::select('username', $userNames, $userName, "class='form-control chosen' onchange='changeParams(this)'");?>
        </div>
      </div>
      <div class='col-sm-3'>
        <div class='input-group input-group-sm'>
          <span class='input-group-addon'><?php echo $lang->quantizedoutput->day;?></span>
          <div class='datepicker-wrapper datepicker-date'><?php echo html::input('day', $day, "class='form-control' onchange='changeParams(this)'");?></div>
        </div>
      </div>
    </div>
  </form>
  
  <table class='table table-condensed table-striped table-bordered tablesorter table-fixed active-disabled' id='dayreport'>
    <thead>
    <tr class='colhead'>
      <th width='160'><?php echo $lang->quantizedoutput->amiba_name;?></th>
      <th width='160'><?php echo $lang->quantizedoutput->group_name;?></th>
      <th width='160'><?php echo $lang->quantizedoutput->realname;?></th>
      <th width='160'><?php echo $lang->quantizedoutput->total_time . '（' . $lang->quantizedoutput->day . '）';?></th>
    </tr>
    </thead>
    <tbody>
    <?php $group_count = 0;?>
    <?php foreach($amibas as $amiba):?>
      <tr class="a-center">
        <?php $amiba_user_count = isset($amiba->groups) ? $amiba->usercount : 1;?>
        <?php $amiba_real_user_count = isset($amiba->groups) ? $amiba->realusercount : 0;?>
        <td align='left' rowspan="<?php echo $amiba_user_count;?>">
            <?php 
            echo "<span class='table-cell'>" . $amiba->amiba_name . '(' . $amiba->total_time . '小时/' . $amiba_real_user_count . '人)' . '&nbsp;</span>';
            
            $endNum = date('Ymd',strtotime($day));
            $amibaTimetendencyLink = $this->createLink('quantizedoutput', 'timetendency', "userRootId=$userRootId&amibaName=$amiba->amiba_name&isAmibaChanged=false&groupName=groupName&account=account&orgType=amiba&timeType=日&endNum=$endNum&beginNum=");
            print(html::a($amibaTimetendencyLink, $iconHtml, '', 'class="iframe"'));
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
                echo "<span class='table-cell'>" . $group->group_name . '(' . $group->total_time . '小时/' . $group_real_user_count . '人)' . "&nbsp;</span>";
                
                $endNum = date('Ymd',strtotime($day));
                $groupTimetendencyLink = $this->createLink('quantizedoutput', 'timetendency', "userRootId=$userRootId&amibaName=$amiba->amiba_name&isAmibaChanged=false&groupName=$group->group_name&account=account&orgType=group&timeType=日&endNum=$endNum&beginNum=");
                print(html::a($groupTimetendencyLink, $iconHtml, '', 'class="iframe"'));
                
                ?>
            </td>
            
                <?php if(isset($group->users)):?>
                <?php $userindex = 1;?>
                <?php foreach($group->users as $user):?>
                  <?php if($userindex != 1) echo "<tr class='a-center'>"?>
                    <td>
                        <?php 
                        echo "<span class='table-cell'>" . $user->realname . "&nbsp;</span>";
                        
                        $endNum = date('Ymd',strtotime($day));
                        $personTimetendencyLink = $this->createLink('quantizedoutput', 'timetendency', "userRootId=$userRootId&amibaName=amiba&isAmibaChanged=false&groupName=groupName&account=$user->account&orgType=person&timeType=日&endNum=$endNum&begin=");
                        print(html::a($personTimetendencyLink, $iconHtml, '', 'class="iframe"'));
                        ?>
                    </td>

                    <?php /* echo $user->account; */?>
                    <?php
                    echo "<td>";
                    
                    $bgclass = 'bg-green';
                    $progress = number_format($user->total_time / 24 * 100, 0);
                    $grayProgress = 100 - $progress;
                    if(empty($user->total_time))
                    {
                        $user->total_time = 0;
                        $bgclass = 'bg-red';
                    }
                    else
                    {
                        if($user->total_time < 8)
                        {
                            $bgclass = 'bg-red';
                        }
                        else
                        {
                            $bgclass = 'bg-green';
                        }
                    }
                    
                    $aSpan = "<span class='num . $bgclass'>$user->total_time</span>";
                    $dateNum = date('Ymd',strtotime($day));
                    $worklogsLink = $this->createLink('quantizedoutput', 'worklogs', 
                        "dimType=staff&amibaId=&groupId=&account=$user->account&dateNum=$dateNum&timeType=日");
                    $user->total_time > 0 ? print(html::a($worklogsLink, $aSpan, '', 'class="iframe"')) : print($aSpan);
                    $ap = "<p class='hitbar $bgclass' style='width:$progress%;'></p>";
                    $greenHtml = html::a($worklogsLink, $ap, '', 'class="iframe"');
                    
                    $dailyworkLink = $this->createLink('quantizedoutput', 'dailywork', "account=$user->account");
                    $agp = "<p class='hitbar bg-gray' style='width:$grayProgress%;'></p>";
                    $grayHtml = '';
                    if($this->app->user->account == $account)
                    {
                        $grayHtml = html::a($dailyworkLink, $agp, '', 'class="iframe"');
                    }
                    else
                    {
                        $grayHtml = $agp;
                    }
                    
                    echo "<div class='progress bg-gray'>" . $greenHtml . $grayHtml . "</div>";
                    echo "</td>";
                    ?> 
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
      </tr>
      <?php endif;?>
    <?php endforeach;?>
    </tbody>
  </table> 
  <form method='post'>
    <div class='row' style='margin-bottom:5px;'>
      <div class='col-sm-1'>
        
      </div>
    </div>
  </form>
</div>
<?php 
include '../../common/view/footer.html.php';
?>
