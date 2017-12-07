<?php
/**feature-1245**/
include '../../../common/view/header.lite.html.php';
include '../../../common/view/chart.html.php';
?>

<div id='titlebar'>
  <div class='heading'>
    <span><?php echo html::icon($lang->icons['report']);?></span>
    <?php 
    $title = $mergeInfo->merge_id;
    ?>
    <small class='text-muted'> <?php echo $title;?></small>
  </div>
</div>
<div class='worklogs'>
  <form class='form-condensed' target='hiddenwin'>
    <table class='table table-condensed table-striped table-bordered tablesorter table-fixed active-disabled'>
      <tbody>
        <tr>
            <td align='right'>所属阿米巴：</td>
            <td><?php echo $mergeInfo->amiba_name;?></td>
            
            <td align='right'>所属组：</td>
            <td><?php echo $mergeInfo->group_name;?></td>
            
            <td align='right'><?php echo $lang->report->commitPerson;?></td>
            <td><?php echo $users[$mergeInfo->committer];?></td>
            
        </tr>
        <tr>
            <td align='right'><?php echo $lang->report->belongProject;?></td>
            <td><?php echo $mergeInfo->project_name;?></td>
            
            <td align='right'><?php echo $lang->report->sourceBranch;?></td>
            <td><?php echo $mergeInfo->source_branch;?></td>
            
            <td align='right'><?php echo $lang->report->targetBranch;?></td>
            <td><?php echo $mergeInfo->target_branch;?></td>
            
        </tr>
        <tr>
            <td align='right'><?php echo $lang->report->prStatus;?></td>
            <td><?php echo $mergeInfo->state;?></td>
            
            <td align='right'><?php echo $lang->report->requestDate;?></td>
            <td><?php echo $mergeInfo->pr_time;?></td>
            
            <td align='right'><?php echo $lang->report->auditDate;?></td>
            <td><?php echo $mergeInfo->check_time;?></td>
            
        </tr>
        <tr>
            <td align='right'>任务编号：</td>
            <td>
            <?php 
            echo html::a($this->createLink('task', 'view', "taskID=$mergeInfo->task_id"), $mergeInfo->task_id, '', 'target="blank"');
            ?>
            </td>
            
            <td align='right'>GitLab项目：</td>
            <td>
            <?php
            echo "<a href='" . $mergeInfo->web_url . "' target='_blank'>GitLab项目</a>";
            ?>
            </td>
            
            <td align='right'>合并地址：</td>
            <td>
            <?php
            echo "<a href='" . $mergeInfo->pr_url . "' target='_blank'>合并地址</a>";
            ?>
            </td>
        </tr>
        <tr>
            <td align='right'><?php echo $lang->report->auditPerson;?></td>
            <td><?php echo $users[$mergeInfo->auditor];?></td>
            
            <td align='right'>备注：</td>
            <td><?php echo $mergeInfo->pr_desc;?></td>
            
            <td align='right'>mergeId:</td>
            <td data-container='body' data-toggle='popover' data-placement='top' data-trigger='hover' data-content='<?php 
                    echo 'mergeId：' . $mergeInfo->merge_id;
                    ?>' style="overflow:hidden;text-overflow:ellipsis;">
            <?php echo $mergeInfo->merge_id;?>
            </td>
        </tr>
        <tr>
            <td align='right'>计算过程：</td>
            <td data-container='body' data-toggle='popover' data-placement='right' data-trigger='hover' data-content='<?php 
                    echo $lang->report->calculateProcess . '：' . $mergeInfo->calc_process;
                    echo '<br/>';
                    echo $lang->report->mark . '：' . $mergeInfo->pr_desc;
                    ?>' style="overflow:hidden;text-overflow:ellipsis;">
            <?php echo $mergeInfo->calc_process;?>
            </td>
            
            <td align='right'><?php echo $lang->report->calculateResult;?></td>
            <td><?php echo $mergeInfo->calc_result;?></td>
            
            <td align='right'></td>
            <td></td>
        </tr>
      </tbody>
    </table>
  </form>
  
  <form class='form-condensed' method='post' target='hiddenwin' id='dataform'>
      <table class='table table-form table-fixed table-bordered' id='mergeDetailInfoTable' style='margin-bottom:30px;'>
        <thead>
          <tr>
            <th style='text-align:center;vertical-align: middle;' class='w-90px' rowspan='2'><?php echo '文件类型';?></th> 
            <th style='text-align:center;' class='w-150px' colspan='2'><?php echo '文件个数';?></th>
            <th style='text-align:center;' class='w-150px' colspan='2'><?php echo '增加行数';?></th>
            <th style='text-align:center;' class='w-150px' colspan='2'><?php echo '删除行数';?></th>
            <th style='text-align:center;vertical-align: middle;' rowspan='2'><?php echo '备注';?></th>
          </tr>
          <tr>
            <th style='text-align:center;' class='w-120px'><?php echo '原值';?></th>
            <th style='text-align:center;' class='w-120px'><?php echo '修正值';?></th>
            
            <th style='text-align:center;' class='w-150px'><?php echo '原值';?></th>
            <th style='text-align:center;' class='w-150px'><?php echo '修正值';?></th>
            
            <th style='text-align:center;' class='w-150px'><?php echo '原值';?></th>
            <th style='text-align:center;' class='w-150px'><?php echo '修正值';?></th>
          </tr>
        </thead>
        <?php
        echo html::hidden('userRootId', $userRootId);
        echo html::hidden('amibaName', $mergeInfo->amiba_name);
        echo html::hidden('groupName', $mergeInfo->group_name);
        echo html::hidden('account', $mergeInfo->account);    
        echo html::hidden('orgType', $orgType);
        echo html::hidden('monthNum', $monthNum); 
        ?>
        <?php foreach($mergeInfo->mergeDetailInfo as $detail):?>
        <?php
            // 是否具有编辑权限
            $readonly = 'readonly';
            if(common::hasPriv('report', 'saveModMerge')){
                $readonly = '';
            }
            
            // 修改历史信息
            $modHistory = '';
            if(!empty($detail->time_stamp_new)){
                $modHistory = '上次修正日期：' ;
                $modHistory .= $detail->time_stamp_new;
                
                $modHistory .= '<br/>';
                $modHistory .= '历史修正人：';
                $accounts = explode(',', $detail->modifier_new);
                foreach($accounts as $account){
                    if(empty($account) || $account == '') {
                        continue;
                    }
                    
                    $modHistory .= $users[$account] . ', ';
                }
                
                $modHistory = rtrim($modHistory, ', ') . '.';
            }
            
            // 是否可修改文件个数
            $specialFileTypes = "jpg,png";
            $canModFileCount = strpos($specialFileTypes, strtolower($detail->file_type)) > 0;
        ?>
        <tr class='text-center'>
          <td>
              <?php 
              
              // 计算详情Id
              echo html::hidden("id[$detail->id]", $detail->id);
              echo html::hidden("id_new[$detail->id]", $detail->id_new);
              
              // mergeId
              echo html::hidden("related_pr_cuid[$detail->id]", $detail->related_pr_cuid);
              echo html::hidden("related_pr_cuid_new[$detail->id]", zget($detail, 'related_pr_cuid_new', $detail->related_pr_cuid));
              
              // 文件类型
              echo html::hidden("file_type[$detail->id]", $detail->file_type);
              echo html::hidden("file_type_new[$detail->id]", zget($detail, 'file_type_new', $detail->file_type));
              
              // 历史修正人
              echo html::hidden("modifier_new[$detail->id]", $detail->modifier_new);
              
              // 显示文件类型
              echo $detail->file_type;
              ?>
          </td>
          
          <td>
              <?php 
              // 文件个数-原值
              echo html::input("file_count[$detail->id]",  $detail->file_count, 
              "class='form-control' readonly autocomplete='off' style='width:100%;float:left;'");
              // <div style="margin-top:5px;float:right;">个</div>
              ?>
          </td>
          <td>
              <?php 
              // 文件个数-修正值
              // 是否可修改文件个数。
              if($canModFileCount){
                  $fontColor = $detail->file_count == zget($detail, 'file_count_new', $detail->file_count)
                        ? 'black' : 'orange';
                        
                  echo html::input("file_count_new[$detail->id]",  zget($detail, 'file_count_new', $detail->file_count), 
                  "class='form-control' $readonly autocomplete='off' style='width:100%;float:left;color:$fontColor;' onkeyup='changePerformanceFontColor(\"file_count\", $detail->id)' data-container='body' data-toggle='popover' data-placement='right' data-trigger='hover' data-content='$modHistory'");
              } else {
                  echo html::hidden("file_count_new[$detail->id]", zget($detail, 'file_count_new', $detail->file_count));
              }
              // <div style="margin-top:5px;float:right;">个</div>
              ?>
          </td>
          
          <td>
              <?php 
              // 新增行-原值
              if($detail->line_add != 0){
                  echo html::input("line_add[$detail->id]",  $detail->line_add, 
                  "class='form-control' readonly autocomplete='off' style='width:100%;float:left;'");
              } else {
                  echo html::hidden("line_add[$detail->id]", $detail->line_add);
              }
              // <div style="margin-top:5px;float:right;">行</div>
              ?>
          </td>
          
          <td>
              <?php 
              // 新增行-修正值
              if($detail->line_add != 0){
                  // 增加的行数编辑框
                  $fontColor = $detail->line_add == zget($detail, 'line_add_new', $detail->line_add)
                        ? 'black' : 'orange';
                  echo html::input("line_add_new[$detail->id]",  zget($detail, 'line_add_new', $detail->line_add), 
                  "class='form-control' $readonly autocomplete='off' style='width:100%;float:left;color:$fontColor;' onkeyup='changePerformanceFontColor(\"line_add\", $detail->id)' data-container='body' data-toggle='popover' data-placement='right' data-trigger='hover' data-content='$modHistory'");
              } else {
                  echo html::hidden("line_add_new[$detail->id]", zget($detail, 'line_add_new', $detail->line_add));
              }
              // <div style="margin-top:5px;float:right;">行</div>
              ?>
          </td>
          
          <td>
              <?php 
              // 删除行-原值
              if($detail->line_del != 0){
                  echo html::input("line_del[$detail->id]", $detail->line_del, 
                  "class='form-control' readonly autocomplete='off' style='width:100%;float:left;'");
              } else {
                  echo html::hidden("line_del[$detail->id]", $detail->line_del);
              }
              // <div style="margin-top:5px;float:right;">行</div>
              ?>
          </td>
          
          <td>
              <?php 
              // 删除行-修正值
              if($detail->line_del != 0){
                  // 删除的行数编辑框
                  $fontColor = $detail->line_del == zget($detail, 'line_del_new', $detail->line_del)
                        ? 'black' : 'orange';
                  echo html::input("line_del_new[$detail->id]", zget($detail, 'line_del_new', $detail->line_del), 
                  "class='form-control' $readonly autocomplete='off' style='width:100%;float:left;color:$fontColor;' onkeyup='changePerformanceFontColor(\"line_del\", $detail->id)' data-container='body' data-toggle='popover' data-placement='right' data-trigger='hover' data-content='$modHistory'");
              } else {
                  echo html::hidden("line_del_new[$detail->id]", zget($detail, 'line_del_new', $detail->line_del));
              }
             
              // <div style="margin-top:5px;float:right;">行</div>
              ?>
          </td>
          
          <td>
                <div class="required required-wrapper"></div>
                <?php 
                echo html::input("remark_new[$detail->id]", $detail->remark_new,
                "class='form-control disabled-ie-placeholder' $readonly autocomplete='off' placeholder='如果该行的分数被修改，则备注为必填项...'  data-container='body' data-toggle='popover' data-placement='top' data-trigger='hover' data-content='$detail->remark_new' style='overflow:hidden;text-overflow:ellipsis;'");
                 // style='float: left;width: 98%;'
                // <span style="color: red; font-size: 19px; ">*</span>
                
                ?>
                
          </td>
        </tr>
        <?php endforeach;?>
        <tr>
            <td colspan='8' class='text-center'>
            <?php 
            if(common::hasPriv('report', 'saveModMerge')){
                echo html::submitButton();
            }
            
            echo  html::backButton();
            ?>
            </td>
        </tr>
      </table>
    </form>
</div>
<?php 
include '../../../common/view/footer.lite.html.php';
?>


