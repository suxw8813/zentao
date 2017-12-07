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
    echo html::hidden('orgType', $orgType);
    
    echo html::hidden('amibaName', $amibaName);
    echo html::hidden('groupName', $groupName);
    echo html::hidden('account', $account);    
    ?>
    <small class='text-muted'> <?php echo $title;?></small>
  </div>
</div>
<div class='worklogs' style='min-height:250px;'>
  
    <div class='main' style='margin:auto;'>
       
        <table class='table active-disabled table-condensed table-bordered table-hover table-striped tablesorter' id='groupTable'>
          <thead>
              <tr>
               <th class='text-center w-90px'><?php echo $lang->quantizedoutput->taskId;?></th>
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
          <tbody>
              <?php foreach($tasks as $task):?>
              <tr class='text-center'>
                   <td align='left'><?php echo $task->task_id;?></td>
                   <td align='left'><?php echo $task->project_name;?></td>
                   <td align='left'><?php echo $task->source_branch;?></td>
                   <td align='left'><?php echo $task->target_branch;?></td>
                   <td align='left'><?php echo $task->state;?></td>
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
                           // $fontColor = $task->mod_merge_count > 0 ? 'orange' : '#03c';
                           // $encodeMergeId = str_replace('-', '___', $task->merge_id);
                           // $modLink = $this->createLink('quantizedoutput', 'modmerge', "dimType=staff&userRootId=$userRootId&monthNum=$monthNum&encodeMergeId=$encodeMergeId");
                           // echo "<a href='$modLink' class='iframe' style='color:$fontColor'>" . $task->calc_result . "</a>";
                           echo $task->calc_result;
                       ?>
                   </td>
                   <td align='left'>
                   <?php echo "<a href='" . $task->pr_url . "' target='_blank'>链接请求</a>"?>
                   </td>
              </tr>
              <?php endforeach;?>
          </tbody>
        </table> 
    </div>
</div>
<?php 
echo $zcj;
include '../../common/view/footer.lite.html.php';
?>
