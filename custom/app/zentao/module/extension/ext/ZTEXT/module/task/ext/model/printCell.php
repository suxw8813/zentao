<?php
/**feature-859**/
/**
 * Print cell data.
 *
 * @param  object  $col
 * @param  object  $task
 * @param  array   $users
 * @param  string  $browseType
 * @access public
 * @return void
 */
public function printCell($col, $task, $users, $browseType, $branchGroups, $modulePairs = array())
{
    $taskLink = helper::createLink('task', 'view', "taskID=$task->id");
    $account  = $this->app->user->account;
    $id = $col->id;
    if($col->show)
    {
        $class = '';
        if($id == 'status') $class .= ' task-' . $task->status;
        if($id == 'name' or $id == 'story') $class .= ' text-left';
        if($id == 'deadline' and isset($task->delay)) $class .= ' delayed';
        if($id == 'assignedTo' && $task->assignedTo == $account) $class .= ' red';

        echo "<td class='" . $class . "'" . ($id=='name' ? " title='{$task->name}'":'') . ">";
        switch ($id)
        {
        case 'id':
            echo html::a($taskLink, sprintf('%03d', $task->id));
            break;
        case 'pri':
            echo "<span class='pri" . zget($this->lang->task->priList, $task->pri, $task->pri) . "'>";
            echo $task->pri == '0' ? '' : zget($this->lang->task->priList, $task->pri, $task->pri);
            echo "</span>";
            break;
        case 'name':
            if(isset($branchGroups[$task->product][$task->branch])) echo "<span class='label label-info label-badge'>" . $branchGroups[$task->product][$task->branch] . '</span> ';
            if($modulePairs and $task->module) echo "<span class='label label-info label-badge'>" . $modulePairs[$task->module] . '</span> ';
            echo html::a($taskLink, $task->name, null, "style='color: $task->color'");
            if($task->fromBug) echo html::a(helper::createLink('bug', 'view', "id=$task->fromBug"), "[BUG#$task->fromBug]", '_blank', "class='bug'");
            break;
        case 'type':
            echo $this->lang->task->typeList[$task->type];
            break;
        case 'status':
            $storyChanged = ($task->storyStatus == 'active' and $task->latestStoryVersion > $task->storyVersion);
            $storyChanged ? print("<span class='warning'>{$this->lang->story->changed}</span> ") : print($this->lang->task->statusList[$task->status]);
            break;
        case 'estimate':
            echo round($task->estimate, 1);
            break;
        case 'consumed':
            echo round($task->consumed, 1);
            break;
        case 'left':
            echo round($task->left, 1);
            break;
        case 'progess':
            echo "<div class='progress-pie' title='{$task->progess}%' data-value='{$task->progess}'></div>";
            break;
        case 'deadline':
            if(substr($task->deadline, 0, 4) > 0) echo substr($task->deadline, 5, 6);
            break;
        case 'openedBy':
            echo zget($users, $task->openedBy, $task->openedBy);
            break;
        case 'openedDate':
            echo substr($task->openedDate, 5, 11);
            break;
        case 'estStarted':
            echo $task->estStarted;
            break;
        case 'realStarted':
            echo $task->realStarted;
            break;
        case 'assignedTo':
            echo zget($users, $task->assignedTo, $task->assignedTo);
            break;
        case 'assignedDate':
            echo substr($task->assignedDate, 5, 11);
            break;
        case 'finishedBy':
            echo zget($users, $task->finishedBy, $task->finishedBy);
            break;
        case 'finishedDate':
            echo substr($task->finishedDate, 5, 11);
            break;
        case 'canceledBy':
            echo zget($users, $task->canceledBy, $task->canceledBy);
            break;
        case 'canceledDate':
            echo substr($task->canceledDate, 5, 11);
            break;
        case 'closedBy':
            echo zget($users, $task->closedBy, $task->closedBy);
            break;
        case 'closedDate':
            echo substr($task->closedDate, 5, 11);
            break;
        case 'closedReason':
            echo $this->lang->task->reasonList[$task->closedReason];
            break;
        case 'story':
            if($task->storyID)
            {
                if(!common::printLink('story', 'view', "storyid=$task->storyID", $task->storyTitle)) print $task->storyTitle;
            }
            break;
        case 'actions':
            common::printIcon('task', 'assignTo', "projectID=$task->project&taskID=$task->id", $task, 'list', '', '', 'iframe', true);
            common::printIcon('task', 'start',    "taskID=$task->id", $task, 'list', '', '', 'iframe', true);

            /**common::printIcon('task', 'recordEstimate', "taskID=$task->id", $task, 'list', 'time', '', 'iframe', true);**/
            common::printIcon('worklog', 'recordestimate','netType=inner','','','time','','','','','工时填报');
            if($browseType == 'needconfirm')
            {
                $this->lang->task->confirmStoryChange = $this->lang->confirm;
                common::printIcon('task', 'confirmStoryChange', "taskid=$task->id", '', 'list', '', 'hiddenwin');
            }
            common::printIcon('task', 'finish', "taskID=$task->id", $task, 'list', '', '', 'iframe', true);
            common::printIcon('task', 'close',    "taskID=$task->id", $task, 'list', '', '', 'iframe', true);
            common::printIcon('task', 'edit',"taskID=$task->id", '', 'list');
            break;
        }
        echo '</td>';
    }
}
