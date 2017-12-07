<?php
/**feature-1077**/
/**feature-1245**/
/**
 * Get amibas. 
 * 
 * @access public
 * @return void
 */
public function getTimeTendencyData($userRootId, $amibas, $amibaName, $groupName, $account, $orgType, $timeType, $begin, $end)
{
    $userCount = 0;
    if($orgType == 'dept')
    {
        foreach($amibas as $amiba)
        {
            foreach($amiba->groups as $group)
            {
                foreach($group->users as $user)
                {
                    $userCount ++;
                }
            }
        }
    }
    else if($orgType == 'amiba')
    {
        foreach($amibas as $amiba)
        {
            if($amiba->amiba_name != $amibaName) continue;
            foreach($amiba->groups as $group)
            {
                foreach($group->users as $user)
                {
                    $userCount ++;
                }
            }
        }
    }
    else if($orgType == 'group')
    {
        foreach($amibas as $amiba)
        {
            if($amiba->amiba_name != $amibaName) continue;
            foreach($amiba->groups as $group)
            {
                if($group->group_name != $groupName) continue;
                foreach($group->users as $user)
                {
                    $userCount ++;
                }
            }
        }
    }
    else if($orgType == 'person')
    {
        $userCount = 1;
    }
     
    if($timeType == '日')
    {
        return $this->getDayTimeTendencyData($userRootId, $userCount, $amibaName, $groupName, $account, $orgType, $begin, $end);
    }
    else
    {
        return $this->getMonthTimeTendencyData($userRootId, $userCount, $amibaName, $groupName, $account, $orgType, $begin, $end);
    }
 }
 
public function getDayTimeTendencyData($userRootId, $userCount, $amibaName, $groupName, $account, $orgType, $begin, $end)
{
    $timeSql = $this->getDayTimeTendencyDataSql($userRootId, $amibaName, $groupName, $account, $orgType, $begin, $end);
    $worklogs = $this->dao->query($timeSql);
    $dayList = $this->getDayList($begin, $end);
    
    $chartData['labels'] = '[';
    foreach($dayList as $day)
    {
        $chartData['labels'] .= '"' . date('n月j日', strtotime($day)) . '"' . ',';
    }
    $chartData['labels']   = rtrim($chartData['labels'], ',');
    $chartData['labels'] .= ']';

    $sets = array();
    foreach($worklogs as $worklog)
    {
        $sets[$worklog->work_date] = new stdclass();
        $sets[$worklog->work_date]->value = $worklog->value;
    }
    
    $baseSet = array();
    foreach($dayList as $i => $day)
    {
        $baseSet[$day] = new stdclass();
        if($this->getWorkDayCount($day, $day))
        {
            $baseSet[$day]->value = 8 * $userCount;
        }
        else
        {
            $baseSet[$day]->value = 0;
        }
    }
    $chartData['baseLine'] = $this->createSingleJSONRight($baseSet, $dayList);

    $chartData['burnLine'] = $this->createSingleJSONRight($sets, $dayList);
    return $chartData;
}

public function getDayTimeTendencyDataSql($userRootId, $amibaName, $groupName, $account, $orgType, $begin, $end)
{
    $andWhereWorkdate = " and w.work_date between '" . $begin . "' and '" . $end . "'";
    
    $andWhereAmibaGroupAccount = "" ;
    if($orgType == 'dept')
    {
        $andWhereAmibaGroupAccount = "" ;
    }
    else if($orgType == 'amiba')
    {
        $andWhereAmibaGroupAccount = "and a.dept_name1 = '" .$amibaName . "'" ;
    }
    else if($orgType == 'group')
    {
        $andWhereAmibaGroupAccount = "and a.dept_name1 = '" .$amibaName . "' and a.dept_name2 = '" . $groupName . "'" ;
    }
    else if($orgType == 'person')
    {
        $andWhereAmibaGroupAccount = "and b.account = '" .$account . "'" ;
    }
    
    $sql= str_replace('#{andWhereWorkdate}', $andWhereWorkdate, $this->config->report->DayTimeTendencyDataSql);
    $sql = str_replace('#{andWhereAmibaGroupAccount}', $andWhereAmibaGroupAccount, $sql);
    $sql = str_replace('#{userRootId}', $userRootId, $sql);
    return $sql;
}

public function getMonthTimeTendencyData($userRootId, $userCount, $amibaName, $groupName, $account, $orgType, $beginMonth, $endMonth)
{
    $monthList = $this->getMonthList($beginMonth, $endMonth);
    $chartData['labels'] = '[';
    foreach($monthList as $month)
    {
        $chartData['labels'] .= '"' . date('y年m月', strtotime($month)) . '"' . ',';
    }
    $chartData['labels']   = rtrim($chartData['labels'], ',');
    $chartData['labels'] .= ']';

    $baseSet = array();
    foreach($monthList as $i => $month)
    {
        list($month, $monthFirstDay, $monthLastDay) = $this->getMonthBeginEnd(date('Ym', strtotime($month)), true); 
        $workDayCount = $this->getWorkDayCount($monthFirstDay, $monthLastDay);
        
        $baseSet[$month] = new stdclass();
        $baseSet[$month]->value = $workDayCount * 8 * $userCount;
    }
    $chartData['baseLine'] = $this->createSingleJSONRight($baseSet, $monthList);

    
    $timeSql = $this->getMonthTimeTendencyDataSql($userRootId, $amibaName, $groupName, $account, $orgType, $beginMonth, $endMonth);
    $worklogs = $this->dao->query($timeSql);
    $sets = array();
    $chartData['zcj'] = array();
    foreach($worklogs as $worklog)
    {
        $month = $this->getMonth($worklog->work_date);
        $chartData['zcj'][date('Y-m-d', strtotime($worklog->work_date))]= $month;
        
        if(!isset($sets[$month]) && !empty($sets[$month]))
        {
            $sets[$month] = new stdclass();
            $sets[$month]->value = 0;
        }
        $sets[$month]->value += $worklog->value;
    }
    
    $chartData['burnLine'] = $this->createSingleJSONRight($sets, $monthList);
    return $chartData;
}

public function getMonthTimeTendencyDataSql($userRootId, $amibaName, $groupName, $account, $orgType, $beginMonth, $endMonth)
{
    list($beginMonth, $begin, $midEnd) = $this->getMonthBeginEnd(date('Ym', strtotime($beginMonth)), false);
    list($endMonth, $midBegin, $end) = $this->getMonthBeginEnd(date('Ym', strtotime($endMonth)), false);
    $andWhereWorkdate = " and w.work_date between '$begin' and '$end'" ;
    
    $andWhereAmibaGroupAccount = "" ;
    if($orgType == 'dept')
    {
        $andWhereAmibaGroupAccount = "" ;
    }
    else if($orgType == 'amiba')
    {
        $andWhereAmibaGroupAccount = "and a.dept_name1 = '" .$amibaName . "'" ;
    }
    else if($orgType == 'group')
    {
        $andWhereAmibaGroupAccount = "and a.dept_name1 = '" .$amibaName . "' and a.dept_name2 = '" . $groupName . "'";
    }
    else if($orgType == 'person')
    {
        $andWhereAmibaGroupAccount = "and b.account = '" .$account . "'" ;
    }
    
    $sql= str_replace('#{andWhereWorkdate}', $andWhereWorkdate, $this->config->report->MonthTimeTendencyDataSql);
    $sql = str_replace('#{andWhereAmibaGroupAccount}', $andWhereAmibaGroupAccount, $sql);
    $sql = str_replace('#{userRootId}', $userRootId, $sql);
    return $sql;
}