<?php
/**feature-1077**/
/**feature-1245**/
public function getScoreDetail($scoreType, $userRootId, $amibaName, $groupName, $account, $orgType, $monthNum)
{
    $outputDetailSql = $this->getScoreDetailSql($scoreType, $userRootId, $amibaName, $groupName, $account, $orgType, $monthNum, $begin, $end);
    $outputDetailList = $this->dao->query($outputDetailSql);
    
    
    if($scoreType == 'DevTask')
    {
        $tasks = array();
    
        foreach($outputDetailList as $d)
        {
            if(!isset($tasks[$d->task_id]))
            {
                $tasks[$d->task_id] = array();
            }
            
            if(!isset($tasks[$d->task_id][$d->git_id]))
            {
                $tasks[$d->task_id][$d->git_id] = new stdclass();
                $tasks[$d->task_id][$d->git_id]->project_name = $d->project_name;
                $tasks[$d->task_id][$d->git_id]->source_branch = $d->source_branch;
                $tasks[$d->task_id][$d->git_id]->target_branch = $d->target_branch;
                $tasks[$d->task_id][$d->git_id]->state = $d->state;
                $tasks[$d->task_id][$d->git_id]->pr_time = $d->pr_time;
                $tasks[$d->task_id][$d->git_id]->check_time = $d->check_time;
                $tasks[$d->task_id][$d->git_id]->committer = $d->committer;
                $tasks[$d->task_id][$d->git_id]->auditor = $d->auditor;
                $tasks[$d->task_id][$d->git_id]->month_wp_task = $d->month_wp_task;
                $tasks[$d->task_id][$d->git_id]->web_url = $d->web_url;
                $tasks[$d->task_id][$d->git_id]->pr_url = $d->pr_url;
                $tasks[$d->task_id][$d->git_id]->git_id = $d->git_id;
                $tasks[$d->task_id][$d->git_id]->task_id = $d->task_id;
                $tasks[$d->task_id][$d->git_id]->pr_desc = $d->pr_desc;
                $tasks[$d->task_id][$d->git_id]->merge_id = $d->merge_id;
                $tasks[$d->task_id][$d->git_id]->mod_merge_count = $d->mod_merge_count;
                $tasks[$d->task_id][$d->git_id]->calc_process = $d->calc_process;
                $tasks[$d->task_id][$d->git_id]->calc_result = $d->calc_result;
            }
        }
        return $tasks;
    }
    else
    {
        return $outputDetailList;
    }
}

public function getScoreDetailSql($scoreType, $userRootId, $amibaName, $groupName, $account, $orgType, $monthNum, $begin, $end)
{
    if($orgType == 'dept')
    {
        $andWhereAmibaGroupAccount = "";
    }
    else if($orgType == 'amiba')
    {
        $andWhereAmibaGroupAccount = "and a.amiba_name = '$amibaName'";
    }
    else if($orgType == 'group')
    {
        $andWhereAmibaGroupAccount = "and a.group_name = '$groupName'";
    } 
    else
    {
        $andWhereAmibaGroupAccount = "and a.account = '$account'";
    }
    
    $sqlKey = $scoreType . 'DetailSql';
    $bugTypes = array('InsideBugReqResponse', 'ProvinceBugReqResponse', 'TestBug', 'InsideBugTestResponse', 'ProvinceBugTestResponse');
    if(in_array($scoreType, $bugTypes)) {
        $sqlKey = 'BugDetailSql';
    }
    
    $outputDetailSql = str_replace('#{monthNum}', $monthNum, $this->config->report->$sqlKey);
    $outputDetailSql = str_replace('#{begin}', $begin, $outputDetailSql);
    $outputDetailSql = str_replace('#{end}', $end, $outputDetailSql);
    $outputDetailSql = str_replace('#{andWhereAmibaGroupAccount}', $andWhereAmibaGroupAccount, $outputDetailSql);
    $outputDetailSql = str_replace('#{bugType}', $scoreType, $outputDetailSql);
    $outputDetailSql = str_replace('#{userRootId}', $userRootId, $outputDetailSql);
    
    return $outputDetailSql;
}