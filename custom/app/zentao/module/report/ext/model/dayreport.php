<?php
/**feature-1077**/
/**feature-1245**/
/**
 * Get amibas. 
 * 
 * @access public
 * @return void
 */
public function getDayAmibas($userRootId, $workdate)
{
    $dayAmibasSql = str_replace('#{workdate}', $workdate, $this->config->report->DayAmibasSql);
    $dayAmibasSql = str_replace('#{userRootId}', $userRootId, $dayAmibasSql);
    $worklogs = $this->dao->query($dayAmibasSql);
    
    $deptUserCount = 0;
    $deptAllTime = 0;
    $amibas = array();
    foreach ($worklogs as $worklog)
    {
        if (!isset($amibas[$worklog->amiba_name]))
        {
            $amibas[$worklog->amiba_name] = new stdclass();
            $amibas[$worklog->amiba_name]->usercount = 0;
            $amibas[$worklog->amiba_name]->realusercount = 0;
            $amibas[$worklog->amiba_name]->total_time = 0;
            $amibas[$worklog->amiba_name]->amiba_name = $worklog->amiba_name;
        }
        
        if (!isset($amibas[$worklog->amiba_name]->groups[$worklog->group_name]))
        {
            $amibas[$worklog->amiba_name]->groups[$worklog->group_name] = new stdclass();
            $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->usercount = 0;
            $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->realusercount = 0;
            $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->total_time = 0;
            $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->group_name = $worklog->group_name;
        }
        
        $amibas[$worklog->amiba_name]->usercount++;
        $amibas[$worklog->amiba_name]->realusercount++;
        $amibas[$worklog->amiba_name]->total_time += $worklog->total_time;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->usercount++;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->realusercount++;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->total_time += $worklog->total_time;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->users[$worklog->account] = new stdclass();
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->users[$worklog->account]->realname = $worklog->realname;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->users[$worklog->account]->total_time = $worklog->total_time;
        $amibas[$worklog->amiba_name]->groups[$worklog->group_name]->users[$worklog->account]->account = $worklog->account;
        
        
        $deptAllTime += $worklog->total_time;
        $deptUserCount += 1;
    }
    
    return array($deptUserCount, $deptAllTime, $amibas);
}