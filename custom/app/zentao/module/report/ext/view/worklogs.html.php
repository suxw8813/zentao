<?php
/**feature-1077**/
/**feature-1245**/
include '../../../common/view/header.lite.html.php';
include '../../../common/view/chart.html.php';

$prevDateNum = '';
$nextDateNum = '';
if($timeType == '日')
{
    $prevDateNum = date('Ymd', strtotime("$begin -1 day"));
    $nextDateNum = date('Ymd', strtotime("$begin +1 day"));
}
else
{
    $prevDateNum = date('Ym', strtotime("$month -1 month"));
    $nextDateNum = date('Ym', strtotime("$month +1 month"));
}
?>

<div id='titlebar'>
  <div class='heading'>
    <span><?php echo html::icon($lang->icons['report']);?></span>
    <?php 
    $title = '';
    if($timeType == '日')
    {
        $title = $users[$account] . '-' . date('Y年m月d日', strtotime($begin)) . '-报工';
        
    }
    else
    {
        $title = $users[$account] . '-' . date('Y年m月', strtotime($month)) . '-报工(' . date('m月d日',strtotime($begin));
        $title .= '~' . date('m月d日',strtotime($end)) .  ')';
    }
    ?>
    <small class='text-muted'> <?php echo $title;?></small>
    <span class='btn btn-sm' style='margin-left:10px;' id='prev' onclick="changePeriod(<?php echo $prevDateNum;?>)">
        <?php 
        if($timeType == '日')
        {
            echo '上一天';
        }
        else
        {
            echo '上一月';
        }
        ?>
    </span>
    <span class='btn btn-sm' id='next' onclick='changePeriod(<?php echo $nextDateNum;?>)'>
        <?php 
        if($timeType == '日')
        {
            echo '下一天';
        }
        else
        {
            echo '下一月';
        }
        ?>
    </span>

    <?php 
        echo html::hidden('account', $account);
        echo html::hidden('timeType', $timeType);
    ?>
  </div>
</div>
<style>
.task_type.story {
    background-image: url(<?php echo $defaultTheme . "images/ext/blue-label-feature-1077.png"?>);
}
.task_type.bug {
    background-image: url(<?php echo $defaultTheme . "images/ext/red-label-feature-1077.png"?>);
}
.task_type.task {
    background-image: url(<?php echo $defaultTheme . "images/ext/yellow-label-feature-1077.png"?>);
}
</style>
<div class='worklogs'>
  <form class='form-condensed' target='hiddenwin'>
    <table class='table table-condensed table-striped table-bordered tablesorter table-fixed active-disabled'>
      <thead>
        <tr class='text-center'>
          <th class='w-20px'><?php echo $lang->report->worklog->task_type_name;?></th>
          <th class='w-20px'><?php echo $lang->report->worklog->task_id;?></th>
          <th class='w-40px'><?php echo $lang->report->worklog->time_sect . '(共' . $total_time . '小时)';?></th>
          <th class='w-40px'><?php echo $lang->report->worklog->work_content;?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($worklogs as $worklog):?>
        <tr class='text-center'>
          <td>
            <?php
            $taskclass = '';
            switch($worklog->task_type)
            {
                case '1' :
                    $taskclass = 'task_type story';
                    break;
                case '2' :
                    $taskclass = 'task_type task';
                    break;
                case '3' :
                    $taskclass = 'task_type bug';
                    break;
                default:
                    $taskclass = '';
            }
            
            echo "<span class='". $taskclass . "'>" . $worklog->task_type_name . "</span>";
            ?>
          </td>
          
          <?php if($worklog->task_type_name == $lang->report->zentao->story):?>
          <td><?php echo "<p>" . html::a($this->createLink('story', 'view', "taskID=$worklog->task_id"), $worklog->task_id) . "</p>";?></td>
          <?php elseif ($worklog->task_type_name == $lang->report->zentao->task):?>
          <td><?php echo "<p>" . html::a($this->createLink('task', 'view', "taskID=$worklog->task_id"), $worklog->task_id) . "</p>";?></td>
          <?php elseif ($worklog->task_type_name == $lang->report->zentao->bug):?>
          <td><?php echo "<p>" . html::a($this->createLink('bug', 'view', "taskID=$worklog->task_id"), $worklog->task_id) . "</p>";?></td>
          <?php else:?>
          <td><?php echo $worklog->task_id;?></td>
          <?php endif;?>
          
          <td><?php echo $worklog->time_sect;?></td>
          <td data-container='body' data-toggle='popover' data-placement='left' data-trigger='hover' data-content="<?php echo $worklog->work_content;?>" style='overflow:hidden;text-overflow:ellipsis;'>
          <?php echo $worklog->work_content;?>
          </td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  </form>
</div>
<?php 
include '../../../common/view/footer.lite.html.php';
?>
