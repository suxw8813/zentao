<?php
/**feature-1077**/
/**feature-1245**/
include '../../../common/view/header.lite.html.php';
include '../../../common/view/chart.html.php';
include '../../../common/view/datepicker.html.php';
include '../../../common/view/chosen.html.php';
?>
<div id='titlebar'>
  <div class='heading'>
    <?php 
    // echo "<span class='tendency-icon' style='background-image:url(" . $defaultTheme . "images/ext/stat-feature-1077.png)'></span>";
    echo html::hidden('scoreType', $scoreType);
    echo html::hidden('orgType', $orgType);
    
    echo html::hidden('amibaName', $amibaName);
    echo html::hidden('groupName', $groupName);
    echo html::hidden('account', $account);    
    echo $zcj;
    ?>
    <small class='text-muted'> <?php echo $title;?></small>
  </div>
</div>
<div class='worklogs' style='min-height:250px;'>
  
    <div class='main' style='margin:auto;'>
        <form method='post'>
            <div class='row' style='margin-bottom:5px;'>
                <?php     echo html::hidden('userRootId', $userRootId);?>
                <?php if($orgType == 'amiba' || $orgType == 'group'):?>
                <div class='col-sm-3'>
                    <div class='input-group input-group-sm'>
                      <span class='input-group-addon'><?php echo $lang->report->amiba_name;?></span>
                      <?php echo html::select('amibaName', $amibaNames, $amibaName, "class='form-control chosen' onchange='changeParams(this)'");?>
                    </div>
                </div>
                <?php endif?>
                <?php if($orgType == 'group'):?>
                <div class='col-sm-3'>
                    <div class='input-group input-group-sm'>
                      <span class='input-group-addon'><?php echo $lang->report->group_name;?></span>
                      <?php echo html::select('groupName', $groupNames, $groupName, "class='form-control chosen' onchange='changeParams(this)'");?>
                    </div>
                </div>
                <?php endif?>
                <?php if($orgType == 'person'):?>
                <div class='col-sm-3'>
                    <div class='input-group input-group-sm'>
                      <span class='input-group-addon'><?php echo $lang->report->realname;?></span>
                      <?php echo html::select('account', $userNames, $account, "class='form-control chosen' onchange='changeParams(this)'");?>
                    </div>
                </div>                
                <?php endif?>
                <div class='col-sm-3'>
                    <div class='input-group input-group-sm'>
                      <span class='input-group-addon'><?php echo $lang->report->month;?></span>
                      <div class='datepicker-wrapper datepicker-date'><?php echo html::input('month', $month, "class='w-100px form-control' onchange='changeParams(this)'");?></div>
                    </div>
                </div>
              </div>
            </div>
          </form>
         <?php 
         $bugTypes = array('InsideBugReqResponse', 'ProvinceBugReqResponse', 'TestBug', 'InsideBugTestResponse', 'ProvinceBugTestResponse');
         ?>
         
        <?php if($scoreType == 'DevTask'):?>
        <?php 
        $groupBy = $lang->report->taskId;
        ?>
         <table class='table active-disabled table-condensed table-bordered table-hover table-striped tablesorter' id='groupTable'>
            <thead>
                <tr>
                    <th class='w-100px text-left'>
                      <?php echo html::a('###', "<i class='icon-caret-down'></i> " . $groupBy, '', "class='expandAll' data-action='expand'")?>
                      <?php echo html::a('###', "<i class='icon-caret-right'></i> " . $groupBy, '', "class='collapseAll hidden' data-action='collapse'")?>
                    </th>
                    <th class='text-center w-90px'><?php echo $lang->report->belongProject;?></th>
                    <th class='text-center w-90px'><?php echo $lang->report->sourceBranch;?></th>
                    <th class='text-center w-90px'><?php echo $lang->report->targetBranch;?></th>
                    <th class='text-center w-90px'><?php echo $lang->report->prStatus;?></th>
                    <th class='text-center w-90px'><?php echo $lang->report->requestDate;?></th>
                    <th class='text-center w-90px'><?php echo $lang->report->auditDate;?></th>
                    <th class='text-center w-90px'><?php echo $lang->report->commitPerson;?></th>
                    <th class='text-center w-90px'><?php echo $lang->report->auditPerson;?></th>
                    <th class='text-center w-90px'><?php echo $lang->report->calculateResult;?></th>
                    <th class='text-center w-180px'><?php echo $lang->report->operation;?></th>
                </tr>
            </thead>
            
            <?php
            if($groupBy == 'finishedBy') unset($tasks['']);
            if($groupBy == 'closedBy') unset($tasks['']);
            ?>
            <?php foreach($tasks as $groupKey => $groupTasks):?>
            <?php $i = 0;?>
            <?php
            $groupWait     = 0;
            $groupDone     = 0;
            $groupDoing    = 0;
            $groupClosed   = 0;  
            $groupEstimate = 0.0;
            $groupConsumed = 0.0;
            $groupLeft     = 0.0;

            $groupName = $groupKey;
            if($groupBy == 'story') $groupName = empty($groupName) ? $this->lang->task->noStory : zget($groupByList, $groupKey);
            if($groupBy == 'assignedTo' and $groupName == '') $groupName = $this->lang->task->noAssigned;
            ?>
            <tbody>
                <?php
                $groupSum = count($groupTasks);
                foreach($groupTasks as $task)
                {
                  $groupEstimate  += $task->estimate;
                  $groupConsumed  += $task->consumed;
                  $groupLeft      += ($task->status == 'cancel' ? 0 : $task->left);

                  if($task->status == 'wait')   $groupWait++;
                  if($task->status == 'doing')  $groupDoing++;
                  if($task->status == 'done')   $groupDone++;
                  if($task->status == 'closed') $groupClosed++;
                }
                ?>
                <?php foreach($groupTasks as $task):?>
                <?php
                if(isset($currentFilter) and $currentFilter != 'all')
                {
                  if($groupBy == 'story'      and $currentFilter == 'linked' and empty($task->story)) continue;
                  if($groupBy == 'pri'        and $currentFilter == 'noset'  and !empty($task->pri)) continue;
                  if($groupBy == 'assignedTo' and $currentFilter == 'undone' and $task->status != 'wait' and $task->status != 'doing') continue;
                }
                ?>
                <?php $assignedToClass = $task->assignedTo == $app->user->account ? "style='color:red'" : '';?>
                <?php $taskLink        = $this->createLink('task','view',"taskID=$task->id"); ?>
                <tr class='text-center'>
                  <?php if($i == 0):?>
                  <td rowspan='<?php echo count($groupTasks) + 1?>' class='groupby text-left'>
                    <?php echo html::a('###', "<i class='icon-caret-down'></i> " . $groupName, '', "class='expandGroup' data-action='expand' title='$groupName'");?>
                    <div class='groupSummary text' style='white-space:normal'>
                    <?php if($groupBy == 'assignedTo' and isset($members[$task->assignedTo])) printf($lang->project->memberHours, $users[$task->assignedTo], $members[$task->assignedTo]->totalHours);?>
                    <?php printf($lang->project->groupSummaryAB, $groupSum, $groupWait, $groupDoing, $groupEstimate, $groupConsumed, $groupLeft);?>
                    </div>
                  </td>
                  <?php endif;?>
                    <td align='left'><?php echo $task->project_name;?></td>
                    <td align='left'><?php echo $task->source_branch;?></td>
                    <td align='left'><?php echo $task->target_branch;?></td>
                    <td align='left'>
                        <?php echo $task->state;?>
                    </td>
                    <td align='left'><?php echo $task->pr_time;?></td>
                    <td align='left'><?php echo $task->check_time;?></td>
                    <td align='left'><?php echo $users[$task->committer];?></td>
                    <td align='left'><?php echo $users[$task->auditor];?></td>
                    <td align='left' data-container='body' data-toggle='popover' data-placement='left' data-trigger='hover' data-content='<?php 
                    echo $lang->report->calculateProcess . '：' . $task->calc_process;
                    echo '<br/>';
                    echo $lang->report->mark . '：' . $task->pr_desc;
                    ?>'>
                        <?php 
                        if(empty($task->calc_result)){
                            echo "0";
                        } else {
                            $fontColor = $task->mod_merge_count > 0 ? 'orange' : '#03c';
                            $encodeMergeId = str_replace('-', '___', $task->merge_id);
                            $modLink = $this->createLink('report', 'modmerge', "userRootId=$userRootId&orgType=$orgType&monthNum=$monthNum&encodeMergeId=$encodeMergeId");
                            print(html::a($modLink, $task->calc_result, '', "class='iframe' style='color:$fontColor'"));
                        }
                        ?>
                    </td>
                    <td align='left'>
                    <?php echo "<a href='" . $task->pr_url . "' target='_blank'>链接请求</a>"?>
                    </td>
                </tr>
                <?php $i++;?>
                <?php endforeach;?>
                
                <?php if($i != 0):?>
                <tr class='actie-disabled group-collapse hidden text-center group-title'>
                  <td class='text-left'>
                    <?php echo html::a('###', "<i class='icon-caret-right'></i> " . $groupName, '', "class='collapseGroup' data-action='collapse' title='$groupName'");?>
                  </td>
                  <td colspan='10' class='text-left'>
                    <span class='groupdivider' style='margin-left:10px;'>
                      <span class='text'>
                        <?php if($groupBy == 'assignedTo' and isset($members[$task->assignedTo])) printf($lang->project->memberHours, $users[$task->assignedTo], $members[$task->assignedTo]->totalHours);?>
                        <?php printf($lang->project->groupSummary, $groupSum, $groupWait, $groupDoing, $groupEstimate, $groupConsumed, $groupLeft);?>
                      </span>
                    </span>
                  </td>
                </tr>
                <?php endif;?>
            </tbody>
            <?php endforeach;?>
          </table> 
          <script language='Javascript'>$('#<?php echo $browseType;?>Tab').addClass('active');</script>
        <?php elseif($scoreType == 'DevBeRejectedAndLeaderCheck'):?>
        <table class='table active-disabled table-condensed table-bordered table-hover table-striped tablesorter' id='groupTable'>
            <thead>
                <tr>
                  <th class='text-center w-90px'><?php echo $lang->report->belongProject   ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->sourceBranch    ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->targetBranch    ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->prStatus        ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->requestDate     ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->auditDate       ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->commitPerson    ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->auditPerson     ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->taskId          ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->calculateProcess;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->calculateResult ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->mark            ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->operation       ;?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($tasks as $task):?>
                <tr class='text-center'>
                    <td align='left'><?php echo $task->project_name   ;?></td>
                    <td align='left'><?php echo $task->source_branch    ;?></td>
                    <td align='left'><?php echo $task->target_branch    ;?></td>
                    <td align='left'><?php echo $task->state        ;?></td>
                    <td align='left'><?php echo $task->pr_time     ;?></td>
                    <td align='left'><?php echo $task->check_time       ;?></td>
                    <td align='left'><?php echo $users[$task->committer]    ;?></td>
                    <td align='left'><?php echo $task->auditor     ;?></td>
                    <td align='left'>
                    <?php 
                    if(!empty($task->task_id))
                    {
                        echo html::a($this->createLink('task', 'view', "task_id=$task->task_id"), $task->task_id);
                    } 
                    ?>
                    </td>
                    <td align='left'><?php echo $task->calc_process;?></td>
                    <td align='left'><?php echo $task->calc_result ;?></td>
                    <td align='left'><?php echo $task->mark            ;?></td>
                    <td align='left'><?php echo "<a href='" . $task->pr_url . "' target='_blank'>链接请求</a>"?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
          </table> 
        <?php elseif($scoreType == 'DevBug'):?>
        <table class='table active-disabled table-condensed table-bordered table-hover table-striped tablesorter' id='groupTable'>
            <thead>
                <tr>
                  <th class='text-center w-90px'><?php echo $lang->report->bug_id      ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->bug_title   ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->bug_type    ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->bug_source  ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->openedDate  ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->openedBy    ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->resolvedDate;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->calc_process;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->calc_result ;?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($tasks as $task):?>
                <tr class='text-center'>
                    <td align='left'><?php echo html::a($this->createLink('bug', 'view', "bugID=$task->bug_id"), $task->bug_id);?></td>
                    <td align='left'><?php echo $task->bug_title   ;?></td>
                    <td align='left'><?php echo $task->bug_type    ;?></td>
                    <td align='left'><?php echo $task->bug_source  ;?></td>
                    <td align='left'><?php echo $task->openedDate  ;?></td>
                    <td align='left'><?php echo $task->openedBy    ;?></td>
                    <td align='left'><?php echo $task->resolvedDate;?></td>
                    <td align='left'><?php echo $task->calc_process;?></td>
                    <td align='left'><?php echo $task->calc_result ;?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
          </table> 
        <?php elseif($scoreType == 'OncePass'):?>
         <table class='table active-disabled table-condensed table-bordered table-hover table-striped tablesorter' id='groupTable'>
            <thead>
                <tr>
                  <th class='text-center w-90px'><?php echo $lang->report->product_name;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->story_id;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->story_name;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->source;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->openedby;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->openeddate;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->stage;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->status;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->reviewedby;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->revieweddate;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->passnote;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->calc_process;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->calc_result;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->mark;?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($tasks as $task):?>
                <tr class='text-center'>
                    <td align='left'><?php echo $task->product_name;?></td>
                    <td align='left'>
                    <?php 
                    if(!empty($task->story_id))
                    {
                        echo html::a($this->createLink('story', 'view', "storyID=$task->story_id"), $task->story_id);
                    }
                    ?>
                    </td>
                    <td align='left'><?php echo $task->story_name;?></td>
                    <td align='left'><?php echo $task->source;?></td>
                    <td align='left'><?php echo $task->openedby;?></td>
                    <td align='left'><?php echo $task->openeddate;?></td>
                    <td align='left'><?php echo $task->stage;?></td>
                    <td align='left'><?php echo $task->status;?></td>
                    <td align='left'><?php echo $task->reviewedby;?></td>
                    <td align='left'><?php echo $task->revieweddate;?></td>
                    <td align='left'><?php echo $task->passnote;?></td>
                    <td align='left'><?php echo $task->calc_process;?></td>
                    <td align='left'><?php echo $task->calc_result;?></td>
                    <td align='left'><?php echo $task->mark;?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
          </table> 
          <?php elseif($scoreType == 'ReqDev'):?>
          <table class='table active-disabled table-condensed table-bordered table-hover table-striped tablesorter' id='groupTable'>
            <thead>
                <tr>
                  <th class='text-center w-90px'><?php echo $lang->report->story_id    ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->story_name  ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->stage       ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->passnote    ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->task_id     ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->task_name   ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->committer   ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->check_time  ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->task_output ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->calc_process;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->calc_result ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->mark        ;?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($tasks as $task):?>
                <tr class='text-center'>
                    <td align='left'>
                    <?php
                    if(!empty($task->story_id))
                    {
                        echo html::a($this->createLink('story', 'view', "storyID=$task->story_id"), $task->story_id);
                    }
                    ?>
                    </td>
                    <td align='left'><?php echo $task->story_name  ;?></td>
                    <td align='left'><?php echo $task->stage       ;?></td>
                    <td align='left'><?php echo $task->passnote    ;?></td>
                    <td align='left'>
                    <?php 
                    if(!empty($task->task_id))
                    {
                        echo html::a($this->createLink('task', 'view', "task_id=$task->task_id"), $task->task_id);
                    }
                    ?>
                    </td>
                    <td align='left'><?php echo $task->task_name   ;?></td>
                    <td align='left'><?php echo $task->committer   ;?></td>
                    <td align='left'><?php echo $task->check_time  ;?></td>
                    <td align='left'><?php echo $task->task_output ;?></td>
                    <td align='left'><?php echo $task->calc_process;?></td>
                    <td align='left'><?php echo $task->calc_result ;?></td>
                    <td align='left'><?php echo $task->mark        ;?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
          </table> 
          <?php elseif($scoreType == 'ReqSatisfy'):?>
          <span>ReqSatisfy</span>
          <?php elseif(in_array($scoreType, $bugTypes)):?>
          <table class='table active-disabled table-condensed table-bordered table-hover table-striped tablesorter' id='groupTable'>
            <thead>
                <tr>
                  <th class='text-center w-90px'><?php echo $lang->report->story_id;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->story_name;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->stage;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->bug_id;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->bug_title;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->status;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->resolvedBy;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->resolvedDate;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->calc_process;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->calc_result;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->mark;?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($tasks as $task):?>
                <tr class='text-center'>
                    <td align='left'>
                    <?php 
                    if(!empty($task->story_id))
                    {
                        echo html::a($this->createLink('story', 'view', "storyID=$task->story_id"), $task->story_id);
                    }
                    ?>
                    </td>
                    <td align='left'><?php echo $task->story_name;?></td>
                    <td align='left'><?php echo $task->stage;?></td>
                    <td align='left'>
                    <?php 
                    if(!empty($task->bug_id))
                    {
                        echo html::a($this->createLink('bug', 'view', "bug_id=$task->bug_id"), $task->bug_id);
                    }
                    ?>
                    </td>
                    <td align='left'><?php echo $task->bug_title;?></td>
                    <td align='left'><?php echo $task->status;?></td>
                    <td align='left'><?php echo $task->resolvedBy;?></td>
                    <td align='left'><?php echo $task->resolvedDate;?></td>
                    <td align='left'><?php echo $task->calc_process;?></td>
                    <td align='left'><?php echo $task->calc_result;?></td>
                    <td align='left'><?php echo $task->mark;?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
          </table> 
          <?php elseif($scoreType == 'TestCase'):?>
          <table class='table active-disabled table-condensed table-bordered table-hover table-striped tablesorter' id='groupTable'>
            <thead>
                <tr>
                  <th class='text-center w-90px'><?php echo $lang->report->story_id     ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->story_name   ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->stage        ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->case_id      ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->case_title   ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->case_type    ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->case_status  ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->lastrunner   ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->lastrundate  ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->lastrunresult;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->calc_process ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->calc_result  ;?></th>
                  <th class='text-center w-90px'><?php echo $lang->report->mark         ;?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($tasks as $task):?>
                <tr class='text-center'>
                    <td align='left'>
                    <?php 
                    if(!empty($task->story_id))
                    {
                        echo html::a($this->createLink('story', 'view', "storyID=$task->story_id"), $task->story_id);
                    }
                    ?>
                    </td>
                    <td align='left'><?php echo $task->story_name   ;?></td>
                    <td align='left'><?php echo $task->stage        ;?></td>
                    <td align='left'>
                    <?php 
                    if(!empty($task->case_id))
                    {
                        echo html::a($this->createLink('testcase', 'view', "case_id=$task->case_id"), $task->case_id);
                    }
                    ?>
                    </td>
                    <td align='left'><?php echo $task->case_title   ;?></td>
                    <td align='left'><?php echo $task->case_type    ;?></td>
                    <td align='left'><?php echo $task->case_status  ;?></td>
                    <td align='left'><?php echo $task->lastrunner   ;?></td>
                    <td align='left'><?php echo $task->lastrundate  ;?></td>
                    <td align='left'><?php echo $task->lastrunresult;?></td>
                    <td align='left'><?php echo $task->calc_process ;?></td>
                    <td align='left'><?php echo $task->calc_result  ;?></td>
                    <td align='left'><?php echo $task->mark         ;?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
          </table> 
          <?php else:?>
          <span>郑长江</span>
          <?php endif;?>
          
          <script language='Javascript'>$('#<?php echo $browseType;?>Tab').addClass('active');</script>
    </div>
</div>
<?php 
include '../../../common/view/footer.lite.html.php';
?>
