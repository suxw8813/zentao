<?php
/**feature-1077**/
/**
 * Get amibas. 
 * 
 * @access public
 * @return void
 */
public function getWorklogs($account, $workdate, $lastday = '')
{
    $andWhereAccountWorkdate = '';
    if(!isset($lastday) || $lastday == '')
    {
        $andWhereAccountWorkdate = "and w.account = '" . $account . "' and w.work_date = '" . $workdate . "'";
    }
    else
    {
        $andWhereAccountWorkdate = "and w.account = '" . $account . "' and w.work_date between '" . $workdate . "' and '" . $lastday . "'";
    }
    $sql = str_replace('#{andWhereAccountWorkdate}', $andWhereAccountWorkdate, $this->config->report->WorklogsSql);
    $worklogs = $this->dao->query($sql);
    
    $total_time = 0;
    $lastWorklogs = array();
    foreach($worklogs as $w)
    {
        array_push($lastWorklogs, $w);
        $total_time += $w->work_time;
    }
    
    return array($total_time, $lastWorklogs);
}