<?php
/**feature-1077**/
/**feature-1245**/
/**
 * Get amibas. 
 * 
 * @access public
 * @return void
 */
public function getPersonTimeTop30Flot($userRootId, $monthNums, $begin, $end)
{
    $worklogs = $this->dao->query($this->getPersonTimeTop30Sql($userRootId, $monthNums, $begin, $end, 'total_time'));
    
    $PersonTop30Data['labels'] = '[';
    $PersonTop30Data['burnBar'] = '[';
    $PersonTop30Data['burnBar1'] = '[';
    foreach($worklogs as $worklog) 
    {
        $PersonTop30Data['labels'] .= '"' . $worklog->realname . '"' . ',';
        
        $PersonTop30Data['burnBar'] .= '"' . $this->setDefaultIfIntEmpty($worklog->total_time)  . '"' . ',';
        $PersonTop30Data['burnBar1'] .= '"' . $this->setDefaultIfIntEmpty($worklog->day_avg_time) . '"' . ',';
    }
    
    $PersonTop30Data['labels']   = rtrim($PersonTop30Data['labels'], ',');
    $PersonTop30Data['labels'] .= ']';
    
    $PersonTop30Data['burnBar'] = ltrim($PersonTop30Data['burnBar'], ',');
    $PersonTop30Data['burnBar'] .= ']';
    $PersonTop30Data['burnBar1'] = ltrim($PersonTop30Data['burnBar1'], ',');
    $PersonTop30Data['burnBar1'] .= ']';
    
    return $PersonTop30Data;
}

public function getPersonOutputTop30Flot($userRootId, $monthNums, $begin, $end)
{
    $worklogs = $this->dao->query($this->getPersonTimeTop30Sql($userRootId, $monthNums, $begin, $end, 'total_output'));
    
    $PersonTop30Data['labels'] = '[';
    $PersonTop30Data['burnBar'] = '[';
    foreach($worklogs as $worklog) 
    {
        $PersonTop30Data['labels'] .= '"' . $worklog->realname . '"' . ',';
        $PersonTop30Data['burnBar'] .= '"' . $this->setDefaultIfIntEmpty($worklog->total_output) . '"' . ',';
    }
    
    $PersonTop30Data['labels']   = rtrim($PersonTop30Data['labels'], ',');
    $PersonTop30Data['labels'] .= ']';
    
    $PersonTop30Data['burnBar'] = ltrim($PersonTop30Data['burnBar'], ',');
    $PersonTop30Data['burnBar'] .= ']';
    
    return $PersonTop30Data;
}

public function getPersonOutputEfficiencyTop30Flot($userRootId, $monthNums, $begin, $end)
{
    $worklogs = $this->dao->query($this->getPersonTimeTop30Sql($userRootId, $monthNums, $begin, $end, 'output_efficiency'));
    
    $PersonTop30Data['labels'] = '[';
    $PersonTop30Data['burnBar'] = '[';
    foreach($worklogs as $worklog) 
    {
        $PersonTop30Data['labels'] .= '"' . $worklog->realname . '"' . ',';
        $PersonTop30Data['burnBar'] .= '"' . $this->setDefaultIfIntEmpty($worklog->output_efficiency) . '"' . ',';
    }
    
    $PersonTop30Data['labels']   = rtrim($PersonTop30Data['labels'], ',');
    $PersonTop30Data['labels'] .= ']';
    
    $PersonTop30Data['burnBar'] = ltrim($PersonTop30Data['burnBar'], ',');
    $PersonTop30Data['burnBar'] .= ']';
    
    return $PersonTop30Data;
}

function getPersonTimeTop30Sql($userRootId, $monthNums, $begin, $end, $sortField)
{
    $andWhereWorkdate = " and w.work_date between '$begin' and '$end'";
    $workDayCount = $this->getWorkDayCount($begin, $end);
    
    $sql = str_replace('#{andWhereWorkdate}', $andWhereWorkdate, $this->config->report->PersonTimeTop30Sql);
    $sql = str_replace('#{workDayCount}', $workDayCount, $sql);
    $sql = str_replace('#{monthNums}', implode(',', $monthNums), $sql);
    $sql = str_replace('#{sortField}', $sortField, $sql);
    $sql = str_replace('#{userRootId}', $userRootId, $sql);
    return $sql;
}

public function getPersonAvgAmibaTimeTopFlot($userRootId, $begin, $end)
{
    $worklogs = $this->dao->query($this->getPersonAvgAmibaTimeTopSql($userRootId, $begin, $end));
    
    $PersonAvgAmibaTimeTopData['labels'] = '[';
    $PersonAvgAmibaTimeTopData['burnBar'] = '[';
    foreach($worklogs as $worklog) 
    {
        $PersonAvgAmibaTimeTopData['labels'] .= '"' . $worklog->amiba_name . '"' . ',';
        $PersonAvgAmibaTimeTopData['burnBar'] .= '"' . $this->setDefaultIfIntEmpty($worklog->amiba_person_time) . '"' . ',';
    }
    
    $PersonAvgAmibaTimeTopData['labels']   = rtrim($PersonAvgAmibaTimeTopData['labels'], ',');
    $PersonAvgAmibaTimeTopData['labels'] .= ']';
    
    $PersonAvgAmibaTimeTopData['burnBar'] = ltrim($PersonAvgAmibaTimeTopData['burnBar'], ',');
    $PersonAvgAmibaTimeTopData['burnBar'] .= ']';
    
    return $PersonAvgAmibaTimeTopData;
}

function getPersonAvgAmibaTimeTopSql($userRootId, $begin, $end)
{
    $andWhereWorkdate = " and w.work_date between '$begin' and '$end'";

    $sql1 = str_replace('#{andWhereWorkdate}', $andWhereWorkdate, $this->config->report->PersonAvgAmibaTimeTopSql);
    $sql1 = str_replace('#{userRootId}', $userRootId, $sql1);
    return $sql1;
}


public function setDefaultIfIntEmpty($value){
    if(empty($value)){
        return 0;
    } else {
        return $value;
    }
}
