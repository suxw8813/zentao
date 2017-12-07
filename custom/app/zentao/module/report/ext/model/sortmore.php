<?php
/**feature-1077**/
/**feature-1245**/
/**
 * Get amibas. 
 * 
 * @access public
 * @return void
 */
public function getMonthWorkSort($userRootId, $sortField, $monthNums, $begin, $end)
{
    $monthWorks = $this->dao->query($this->getMonthWorkSortSql($userRootId, $sortField,$monthNums, $begin, $end));
    $lastMonthWorks = array();
    $index = 1;
    foreach ($monthWorks as $w)
    {
        $lastMonthWorks[$index] = new stdclass();
        $lastMonthWorks[$index]->rankId = $index;
        $lastMonthWorks[$index]->amiba_name = $w->amiba_name;
        $lastMonthWorks[$index]->group_name = $w->group_name;
        $lastMonthWorks[$index]->realname = $w->realname;
        $lastMonthWorks[$index]->account = $w->account;
        $lastMonthWorks[$index]->total_time = $w->total_time;
        $lastMonthWorks[$index]->day_avg_time = $w->day_avg_time;
        $lastMonthWorks[$index]->extra_time = $w->extra_time;
        $lastMonthWorks[$index]->total_output = $w->total_output;
        $lastMonthWorks[$index]->day_avg_output = $w->day_avg_output;
        $lastMonthWorks[$index]->output_efficiency = $w->output_efficiency;
        $lastMonthWorks[$index]->mod_merge_count = $w->mod_merge_count;
        $index++;
    }
    
    return $lastMonthWorks;
}

function getMonthWorkSortSql($userRootId, $sortField,$monthNums, $begin, $end)
{
    $andWhereWorkdate = "and w.work_date between '$begin' and '$end'";
    
    $workDayCount = $this->getWorkDayCount($begin, $end);
    
    $sql = str_replace('#{andWhereWorkdate}', $andWhereWorkdate, $this->config->report->MonthWorkSortSql);
    $sql = str_replace('#{workDayCount}', $workDayCount, $sql);
    $sql = str_replace('#{monthNums}', implode(',', $monthNums), $sql);
    $sql = str_replace('#{sortField}', $sortField, $sql);
    $sql = str_replace('#{userRootId}', $userRootId, $sql);
    return $sql;
}
