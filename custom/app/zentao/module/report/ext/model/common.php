<?php
/**feature-1077**/
/**feature-1245**/
/* 获取两个日期中间的工作日的个数 */
function getWorkDayCount($startDate, $endDate)
{
    $startDate = strtotime($startDate);
    $endDate = strtotime($endDate);

    $count = 0;
    for ($startDate; $startDate <=$endDate; $startDate += 86400) {
        $info = getdate($startDate);
        $date = date('Ymd', $startDate);
        if((in_array($info['weekday'],array('Sunday','Saturday')) || in_array($date, $this->lang->report->holidays)) && !in_array($date, $this->lang->report->weekendWorkDays)){
            continue;
        }
        $count ++;
    }

    return $count;
}

/* 获取连续的日期列表 */
function getDayList($startDate, $endDate)
{
    $startDate = strtotime($startDate);
    $endDate = strtotime($endDate);

    $dayList = array();
    $count = 0;
    for ($startDate; $startDate <=$endDate; $startDate += 86400) {
        array_push($dayList, date('Y-m-d', $startDate));
        $count ++;
    }

    return $dayList;
}

/* 获取连续的月份列表 */
function getMonthList($startDate, $endDate)
{
    $startDate = strtotime($startDate);
    $endDate = strtotime($endDate);

    $monthList = array();
    $count = 0;
    for ($startDate; $startDate <=$endDate; $startDate = strtotime('+1 month', $startDate)) {
        array_push($monthList, date('Y-m', $startDate));
        $count ++;
    }

    return $monthList;
}

public function getMonth($day)
{
    $month = '';
    $day23 = date('Y-m-24', strtotime($day));
    if(strtotime($day) <= strtotime($day23)) // 当月23号前(包括23号)
    {
        $month = date('Y-m', strtotime($day));
    }
    else // 当月23号后
    {
        $currentMonth = date('Y-m', strtotime($day));
        $month = date('Y-m', strtotime("$currentMonth +1 month"));
    }
    return $month;
}

/**
 * $monthNum 月份
 * $isBase 是否按照基线天数计算
 */
public function getMonthBeginEnd($monthNum, $isBase)
{
    $first = '';
    $month = '';
    if(empty($monthNum))
    {
        if(strtotime(date('Y-m-d')) <= strtotime(date('Y-m-24')))
        {
            $month = date('Y-m');
            $first = date('Y-m-01');
        }
        else
        {
            $currentMonth = date('Y-m');
            $month = date('Y-m', strtotime("$currentMonth +1 month"));
            $first = date('Y-m-01', strtotime("$currentMonth +1 month"));
        }
    }
    else
    {
        $month = date('Y-m', strtotime($monthNum . '01'));
        $first = date('Y-m-01', strtotime($monthNum . '01'));
    }
    
    $begin = date('Y-m-25', strtotime("$first -1 month"));
    $end = date('Y-m-24', strtotime($first));
    if($isBase == false)
    {
        if(strtotime($end) > strtotime(date("Y-m-d",strtotime('-1 day'))))
        {
            $end = date("Y-m-d", strtotime('-1 day'));
        }
    }
    
    return array($month, $begin, $end);
}
/* 
$timeType（非空）:日、月。
$timeNum（非空）:时间。
 */
public function getMonthNums($timeType, $timeNum) {
    $time = '';
    $monthNums = array();
    if($timeType == '月'){
        $monthNum = $timeNum;
        // 获取开始和结束日期
        list($month, $begin, $end) = $this->getMonthBeginEnd($monthNum, false);
        // 获取月份列表
        if(empty($monthNum))
        {
            $monthNum = date('Ym', strtotime($month));
        }
        array_push($monthNums, $monthNum);
        // 设置当前时间
        $time = $month;
    } else {
        if($timeNum == ''){
            $yearNum = date('Y');
        } else {
            $yearNum = $timeNum;
        }
        
        // 获取开始和结束日期
        $beginMonth = $yearNum . '-01';
        $endMonth = $yearNum . '-12';
        list($beginMonth, $begin, $midEnd) = $this->getMonthBeginEnd(date('Ym', strtotime($beginMonth)), false);
        list($endMonth, $midBegin, $end) = $this->getMonthBeginEnd(date('Ym', strtotime($endMonth)), false);
        // 获取月份列表
        $items = array('01', '02', '03', '04', '05', '06', 
                       '07', '08', '09', '10', '11', '12');
        foreach($items as $item){
            $monthNum = $yearNum . $item;
            array_push($monthNums, $monthNum);
        }
        // 设置当前时间
        $time = $yearNum;
    }
    
    return array($time, $monthNums, $begin, $end);
}

/**
 * Create json data of single charts
 * @param  array $sets
 * @param  array $dateList
 * @return string the json string
 */
public function createSingleJSONRight($sets, $monthList)
{
    $data = '[';
    foreach($monthList as $month)
    {
        $isFound = 'false';
        foreach($sets as $key => $set)
        {
            if(strtotime($month) == strtotime($key))
            {
                $data .= "{x:'" . date('Ymd', strtotime($month)) . "',y:" . $set->value . "},";
                $isFound = 'true';
                continue;
            }
        }
        if($isFound == 'false')
        {
            $data .= "{x:'" . date('Ymd', strtotime($month)) . "',y:0},";
        }
    }
    $data = rtrim($data, ',');
    $data .= ']';
    return $data;
}

/* 获取综合监控的用户名 */
/* public function getUserNames()
{
    $userList = $this->dao->query("select u.realname, u.account
from 
    zt_user u, 
    zt_dept dept
where 
    u.deleted='0'
    and u.dept = dept.id
    and dept.path like '%,1,%'
    and dept.name != '已离职'
    and u.realname != '已关闭'");
    
    $result = array();
    foreach($userList as $user)
    {
        $result[$user->account] = $user->realname;
    }
    
    return $result;
} */

public function getAmibaGroupPerson($userRootId)
{
    $amibaGroupPersonSql = str_replace('#{userRootId}', $userRootId, $this->config->report->AmibaGroupPersonSql);
    $accounts = $this->dao->query($amibaGroupPersonSql);
    $amibas = array();
    $amibaNames = array();
    $accountDict = array();
    foreach ($accounts as $account)
    {
        if (!isset($amibas[$account->amiba_name]))
        {
            $amibas[$account->amiba_name] = new stdclass();
            $amibas[$account->amiba_name]->usercount = 0;
            $amibas[$account->amiba_name]->realusercount = 0;
            $amibas[$account->amiba_name]->amiba_name = $account->amiba_name;
        }
        
        if (!isset($amibas[$account->amiba_name]->groups[$account->group_name]))
        {
            $amibas[$account->amiba_name]->groups[$account->group_name] = new stdclass();
            $amibas[$account->amiba_name]->groups[$account->group_name]->usercount = 0;
            $amibas[$account->amiba_name]->groups[$account->group_name]->realusercount = 0;
            $amibas[$account->amiba_name]->groups[$account->group_name]->group_name = $account->group_name;
        }
        
        $amibas[$account->amiba_name]->usercount++;
        $amibas[$account->amiba_name]->realusercount++;
        $amibas[$account->amiba_name]->groups[$account->group_name]->usercount++;
        $amibas[$account->amiba_name]->groups[$account->group_name]->realusercount++;
        $amibas[$account->amiba_name]->groups[$account->group_name]->users[$account->account] = new stdclass();
        $amibas[$account->amiba_name]->groups[$account->group_name]->users[$account->account]->account = $account->account;
        $amibas[$account->amiba_name]->groups[$account->group_name]->users[$account->account]->account = $account->realname;
        
        if(!isset($amibas[$account->amiba_name]->groupDict[$account->group_name]))
        {
            $amibas[$account->amiba_name]->groupDict[$account->group_name] = $account->group_name;
        }
        if(!isset($amibas[$account->amiba_name]->groups[$account->group_name]->users[$account->account]))
        {
            $amibas[$account->amiba_name]->groups[$account->group_name]->accountDict[$account->account] = $account->realname;
        }
        if(!isset($amibaNames[$account->amiba_name]))
        {
            $amibaNames[$account->amiba_name] = $account->amiba_name;
        }
        if(!isset($accountDict[$account->account]))
        {
            $accountDict[$account->account] = $account->realname;
        }
    }
    return array($amibaNames, $accountDict, $amibas);
}

public function getDeptInfo(){
    $dept = $this->dao->query($this->config->report->deptInfoSql)->fetch();
    return $dept;
}

public function getUserRootDict(){
    $userRootDict = array();
    $items = $this->dao->query($this->config->report->userRootDictSql);
    foreach($items as $item){
        $userRootDict[$item->id] = $item->name;
    }
    return $userRootDict;
}

public function getMinUserRoot($userRootDict){
    $minId = PHP_INT_MAX;
    $minName = '';
    foreach($userRootDict as $id => $rootName){
        if($id < $minId){
            $minId = $id;
        }
    }
    
    return array($minId, $minName);
}

public function getUserRoot($userRootDict, $userRootId){
    
    foreach($userRootDict as $curId => $rootName){
        if($userRootId == $curId){
            return array($userRootId, $rootName);
        }
    }
    
    return array($userRootId, '');
}