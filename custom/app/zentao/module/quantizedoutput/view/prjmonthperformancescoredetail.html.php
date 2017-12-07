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
    // echo "<span class='tendency-icon' style='background-image:url(" . $defaultTheme . "images/ext/stat-feature-1509.png)'></span>";
    echo html::hidden('scoreType', $scoreType);
    
    echo html::hidden('amibaId', $amibaId);
    echo html::hidden('groupId', $groupId);
    echo html::hidden('account', $account);   
    ?>
    <small class='text-muted'> <?php echo $title;?></small>
  </div>
</div>
<div class='worklogs' style='min-height:250px;'>
  
    <div class='main' style='margin:auto;'>
        <form method='post'>
          <div class='row' style='margin-bottom:5px;'>
              <div class='col-sm-3'>
                  <div class='input-group input-group-sm'>
                    <span class='input-group-addon'><?php echo $lang->projectCommon;?></span>
                    <?php echo html::select('amibaId', $amibaNameDict, $amibaId, "class='form-control chosen' onchange='changeParams(this)'");?>
                  </div>
              </div>
              <div class='col-sm-3'>
                  <div class='input-group input-group-sm'>
                    <span class='input-group-addon'><?php echo $lang->productCommon;?></span>
                    <?php echo html::select('groupId', $groupNameDict, $groupId, "class='form-control chosen' onchange='changeParams(this)'");?>
                  </div>
              </div>
              <div class='col-sm-3'>
                  <div class='input-group input-group-sm'>
                    <span class='input-group-addon'><?php echo $lang->quantizedoutput->realname;?></span>
                    <?php echo html::select('account', $userNameDict, $account, "class='form-control chosen' onchange='changeParams(this)'");?>
                  </div>
              </div>                
              <div class='col-sm-3'>
                  <div class='input-group input-group-sm'>
                    <span class='input-group-addon'><?php echo $lang->quantizedoutput->month;?></span>
                    <div class='datepicker-wrapper datepicker-date'><?php echo html::input('month', $month, "class='w-100px form-control' onchange='changeParams(this)'");?></div>
                  </div>
              </div>
            </div>
          </div>
        </form>
         
        <?php if($scoreType == 'DevTask'):?>
        <?php $groupBy = $lang->quantizedoutput->taskId; ?>
        <table class='table active-disabled table-condensed table-bordered table-hover table-striped tablesorter' id='groupTable'>
           <thead>
               <tr>
                   <th class='w-100px text-left'>
                     <?php echo html::a('###', "<i class='icon-caret-down'></i> " . $groupBy, '', "class='expandAll' data-action='expand'")?>
                     <?php echo html::a('###', "<i class='icon-caret-right'></i> " . $groupBy, '', "class='collapseAll hidden' data-action='collapse'")?>
                   </th>
                   <th class='text-center w-90px'><?php echo $lang->quantizedoutput->belongProject;?></th>
                   <th class='text-center w-90px'><?php echo $lang->quantizedoutput->sourceBranch;?></th>
                   <th class='text-center w-90px'><?php echo $lang->quantizedoutput->targetBranch;?></th>
                   <th class='text-center w-90px'><?php echo $lang->quantizedoutput->prStatus;?></th>
                   <th class='text-center w-90px'><?php echo $lang->quantizedoutput->requestDate;?></th>
                   <th class='text-center w-90px'><?php echo $lang->quantizedoutput->auditDate;?></th>
                   <th class='text-center w-90px'><?php echo $lang->quantizedoutput->commitPerson;?></th>
                   <th class='text-center w-90px'><?php echo $lang->quantizedoutput->auditPerson;?></th>
                   <th class='text-center w-90px'><?php echo $lang->quantizedoutput->calculateResult;?></th>
                   <th class='text-center w-180px'><?php echo $lang->quantizedoutput->operation;?></th>
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
                   echo $lang->quantizedoutput->calculateProcess . '：' . $task->calc_process;
                   echo '<br/>';
                   echo $lang->quantizedoutput->mark . '：' . $task->pr_desc;
                   ?>'>
                       <?php 
                           $fontColor = $task->mod_merge_count > 0 ? 'orange' : '#03c';
                           $encodeMergeId = str_replace('-', '___', $task->merge_id);
                           $modLink = $this->createLink('quantizedoutput', 'modmerge', "dimType=prj&userRootId=$userRootId&monthNum=$monthNum&encodeMergeId=$encodeMergeId");
                           echo "<a href='$modLink' class='iframe' style='color:$fontColor'>" . $task->calc_result . "</a>";
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
                     <?php
                     if(preg_match("/^\d*$/",$groupName)) 
                     {
                        $task_id = $groupName;
                        echo html::a($this->createLink('task', 'view', "task_id=$task_id"), '查看任务');
                     }
                     ?>
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
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->belongProject   ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->sourceBranch    ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->targetBranch    ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->prStatus        ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->requestDate     ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->auditDate       ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->commitPerson    ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->auditPerson     ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->taskId          ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->calculateProcess;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->calculateResult ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->mark            ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->operation       ;?></th>
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
                  if(!empty($task->task_id)) {
                      if(strpos($task->task_id, 'NR') === 0) {
                          echo $task->task_id;
                      } else {
                          echo html::a($this->createLink('task', 'view', "task_id=$task->task_id"), $task->task_id);
                      }
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
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->bug_id      ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->bug_title   ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->bug_type    ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->bug_source  ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->openedDate  ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->openedBy    ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->resolvedDate;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->calc_process;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->calc_result ;?></th>
              </tr>
          </thead>
          <tbody>
              <?php foreach($tasks as $task):?>
              <tr class='text-center'>
                  <td align='left'>
                  <?php 
                  if(!empty($task->bug_id)) {
                      if(strpos($task->bug_id, 'QU') === 0) {
                          echo $task->bug_id;
                      } else {
                          echo html::a($this->createLink('bug', 'view', "bugID=$task->bug_id"), $task->bug_id);
                      }
                  }
                  ?>
                  </td>
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
       
        <?php elseif($scoreType == 'ReviewPass'):?>
        <table class='table active-disabled table-condensed table-bordered table-hover table-striped tablesorter' id='groupTable'>
           <thead>
               <tr>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->product_name;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->story_id;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->story_name;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->source;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->openedby;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->openeddate;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->stage;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->status;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->reviewedby;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->revieweddate;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->passnote;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->calc_process;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->calc_result;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->mark;?></th>
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
        
        <?php elseif($scoreType == 'ITReq'):?>
        <table class='table active-disabled table-condensed table-bordered table-hover table-striped tablesorter' id='groupTable'>
           <thead>
               <tr>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->product_name;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->story_id;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->story_name;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->close_time;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->status;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->calc_process;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->calc_result;?></th>
                 <th class='text-center w-90px'><?php echo $lang->quantizedoutput->mark;?></th>
               </tr>
           </thead>
           <tbody>
               <?php foreach($tasks as $task):?>
               <tr class='text-center'>
                   <td align='left'><?php echo $task->product_name;?></td>
                   <td align='left'>
                   <?php 
                   /* if(!empty($task->story_id))
                   {
                       echo html::a($this->createLink('story', 'view', "storyID=$task->story_id"), $task->story_id);
                   } */
                   echo $task->story_id; 
                   ?>
                   </td>
                   <td align='left'><?php echo $task->story_name;?></td>
                   <td align='left'><?php echo $task->close_time;?></td>
                   <td align='left'><?php echo $task->status;?></td>
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
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->story_id    ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->story_name  ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->stage       ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->passnote    ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->task_id     ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->task_name   ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->committer   ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->check_time  ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->calc_process;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->calc_result ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->mark        ;?></th>
              </tr>
          </thead>
          <tbody>
              <?php foreach($tasks as $task):?>
              <tr class='text-center'>
                  <td align='left'>
                  <?php
                  if(!empty($task->story_id)) {
                      if(strpos($task->story_id, 'NR') === 0) {
                          echo $task->story_id;
                      } else {
                          echo html::a($this->createLink('story', 'view', "storyID=$task->story_id"), $task->story_id);
                      }
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
                  <td align='left'><?php echo $task->calc_process;?></td>
                  <td align='left'><?php echo $task->calc_result ;?></td>
                  <td align='left'><?php echo $task->mark        ;?></td>
              </tr>
              <?php endforeach;?>
          </tbody>
        </table> 
         
        <?php elseif($scoreType == 'WorkSplit'):?>
        <table class='table active-disabled table-condensed table-bordered table-hover table-striped tablesorter' id='groupTable'>
          <thead>
              <tr>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->story_id    ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->story_name  ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->stage       ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->task_id     ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->task_name   ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->calc_process;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->calc_result ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->mark        ;?></th>
              </tr>
          </thead>
          <tbody>
              <?php foreach($tasks as $task):?>
              <tr class='text-center'>
                  <td align='left'>
                  <?php
                  if(!empty($task->story_id)) {
                      if(strpos($task->story_id, 'NR') === 0) {
                          echo $task->story_id;
                      } else {
                          echo html::a($this->createLink('story', 'view', "storyID=$task->story_id"), $task->story_id);
                      }
                  }
                  ?>
                  </td>
                  <td align='left'><?php echo $task->story_name  ;?></td>
                  <td align='left'><?php echo $task->stage       ;?></td>
                  <td align='left'>
                  <?php 
                  if(!empty($task->task_id))
                  {
                      echo html::a($this->createLink('task', 'view', "task_id=$task->task_id"), $task->task_id);
                  }
                  ?>
                  </td>
                  <td align='left'><?php echo $task->task_name   ;?></td>
                  <td align='left'><?php echo $task->calc_process;?></td>
                  <td align='left'><?php echo $task->calc_result ;?></td>
                  <td align='left'><?php echo $task->mark        ;?></td>
              </tr>
              <?php endforeach;?>
          </tbody>
        </table> 
        
        <?php elseif($scoreType == 'ReqSatisfy'):?>
        <span>ReqSatisfy</span>
        
        <?php elseif(in_array($scoreType, $config->quantizedoutput->bugTypes)):?>
        <table class='table active-disabled table-condensed table-bordered table-hover table-striped tablesorter' id='groupTable'>
          <thead>
              <tr>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->story_id;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->story_name;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->stage;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->bug_id;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->bug_title;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->status;?></th>
                <th class='text-center w-90px'><?php echo '得分人';?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->resolvedDate;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->calc_process;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->calc_result;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->mark;?></th>
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
                  <td align='left'><?php echo $users[$task->account];?></td>
                  <td align='left'><?php echo $task->resolvedDate;?></td>
                  <td align='left'><?php echo $task->calc_process;?></td>
                  <td align='left'><?php echo $task->calc_result;?></td>
                  <td align='left'><?php echo $task->mark;?></td>
              </tr>
              <?php endforeach;?>
          </tbody>
        </table> 
          
        <?php elseif(in_array($scoreType, $config->quantizedoutput->caseTypes)):?>
        <table class='table active-disabled table-condensed table-bordered table-hover table-striped tablesorter' id='groupTable'>
          <thead>
              <tr>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->story_id     ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->story_name   ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->stage        ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->case_id      ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->case_title   ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->case_type    ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->case_status  ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->lastrunner   ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->lastrundate  ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->lastrunresult;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->calc_process ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->calc_result  ;?></th>
                <th class='text-center w-90px'><?php echo $lang->quantizedoutput->mark         ;?></th>
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
echo $zcj;
include '../../common/view/footer.lite.html.php';
?>
