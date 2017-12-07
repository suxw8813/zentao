<?php
/**feature-1077**/
/**feature-1245**/
/**
 * Get amibas. 
 * 
 * @access public
 * @return void
 */
public function getMonthAmibas($userRootId, $monthNum, $begin, $end)
{
    $worklogs = $this->getMonthReportData($userRootId, $monthNum, $begin, $end);
    
    $deptUserCount = 0;
    $amibas = array();
    $deptAllTime = 0;
    $deptAllOutput = 0;
    $deptHasOutputUserCount = 0;
    foreach ($worklogs as $worklog)
    {
        if (!isset($amibas[$worklog->amiba_name]))
        {
            $amibas[$worklog->amiba_name] = new stdclass();
            $amibas[$worklog->amiba_name]->usercount = 0;
            $amibas[$worklog->amiba_name]->realusercount = 0;
            $amibas[$worklog->amiba_name]->total_time = 0;
            $amibas[$worklog->amiba_name]->total_output = 0;
            $amibas[$worklog->amiba_name]->mod_merge_count = 0;
            $amibas[$worklog->amiba_name]->amiba_name = $worklog->amiba_name;
        }
        
        if (!isset($amibas[$worklog->amiba_name]->groups[$worklog->group_name]))
        {
            $amibas[$worklog->amiba_name]->groups[$worklog->group_name] = new stdclass();
            $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->usercount = 0;
            $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->realusercount = 0;
            $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->total_time = 0;
            $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->total_output = 0;
            $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->mod_merge_count = 0;
            $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->group_name = $worklog->group_name;
        }
        
        $amibas[$worklog->amiba_name]->usercount++;
        $amibas[$worklog->amiba_name]->realusercount++;
        $amibas[$worklog->amiba_name]->total_time += $worklog->total_time;
        $amibas[$worklog->amiba_name]->total_output += $worklog->total_output;
        $amibas[$worklog->amiba_name]->mod_merge_count += $worklog->mod_merge_count;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->usercount++;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->realusercount++;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->total_time += $worklog->total_time;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->total_output += $worklog->total_output;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->mod_merge_count += $worklog->mod_merge_count;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->users[$worklog->account] = new stdclass();
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->users[$worklog->account]->realname = $worklog->realname;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->users[$worklog->account]->total_time = $worklog->total_time;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->users[$worklog->account]->total_output = $worklog->total_output;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->users[$worklog->account]->mod_merge_count = $worklog->mod_merge_count;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->users[$worklog->account]->account = $worklog->account;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->users[$worklog->account]->day_avg_time = $worklog->day_avg_time;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->users[$worklog->account]->extra_time = $worklog->extra_time;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->users[$worklog->account]->day_avg_output = $worklog->day_avg_output;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->users[$worklog->account]->extra_output = $worklog->extra_output;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->users[$worklog->account]->output_efficiency = $worklog->output_efficiency;
        
        $deptAllTime += $worklog->total_time;
        $deptAllOutput += $worklog->total_output;
        
        $deptUserCount += 1;
        if($worklog->total_output > 0){
            $deptHasOutputUserCount += 1;
        }
    }
    $deptAvgOutput = 0 ;
    if($deptHasOutputUserCount != 0){
        $deptAvgOutput = round($deptAllOutput / $deptHasOutputUserCount);
    }
    
    return array($deptUserCount, $deptAllTime, $deptAvgOutput, $amibas);
}

public function getMonthReportData($userRootId, $monthNum, $begin, $end)
{
    return $this->dao->query($this->getMonthReportSql($userRootId, $monthNum, $begin, $end));
}

function getMonthReportSql($userRootId, $monthNum, $begin, $end)
{
    $andWhereWorkdate = "and w.work_date between '" . $begin . "' and '" . $end . "'";
    $workDayCount = $this->getWorkDayCount($begin, $end);
    
    $monthReportSql = str_replace('#{andWhereWorkdate}', $andWhereWorkdate, $this->config->report->MonthAmibasSql);
    $monthReportSql = str_replace('#{workDayCount}', $workDayCount, $monthReportSql);
    $monthReportSql = str_replace('#{standardOutput}', $this->config->report->standardOutput, $monthReportSql);
    $monthReportSql = str_replace('#{monthNum}', $monthNum, $monthReportSql);
    $monthReportSql = str_replace('#{userRootId}', $userRootId, $monthReportSql);
    
    return $monthReportSql;
}
