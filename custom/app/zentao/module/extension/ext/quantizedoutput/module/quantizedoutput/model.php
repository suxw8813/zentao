<?php
/**feature-1509**/
/**
 * The model file of report module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     report
 * @version     $Id: model.php 4726 2013-05-03 05:51:27Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
class quantizedoutputModel extends model
{
    /* ============================== modmerge start ================================== */
    /**
     * Get amibas. 
     * 
     * @access public
     * @return void
     */
    public function getMergeInfo($dimType, $userRootId, $monthNum, $mergeId){
        $sqlKey = $dimType . 'MergeInfoSql';
        $mergeInfoSql = str_replace('#{mergeId}', $mergeId, $this->config->quantizedoutput->$sqlKey);
        $mergeInfoSql = str_replace('#{monthNum}', $monthNum, $mergeInfoSql);
        $mergeInfoSql = str_replace('#{userRootId}', $userRootId, $mergeInfoSql);
        $mergeInfo = $this->dao->query($mergeInfoSql)->fetch();
        
        $mergeDetailInfoSql = str_replace('#{mergeId}', $mergeId, $this->config->quantizedoutput->mergeDetailInfoSql);
        $mergeInfo->mergeDetailInfo = $this->dao->query($mergeDetailInfoSql);
        
        // die(js::alert($mergeDetailInfoSql   ));
        return $mergeInfo;
    }

    /**
     * 更新有效输出修正信息
     */
    public function updateMergeDetailInfo(){
        $data = fixer::input('post')->get();
        if($this->validateInputError($data)){
            exit;
        }
        
        foreach($data->file_type as $id => $file_type){
            // 如果修正记录已经存在，删除该记录。
            if($data->id_new[$id] != ''){
                $this->deleteModRecord($data->related_pr_cuid_new[$id], $data->file_type_new[$id]);
            }
            
            $neededMod = false;
            // 如果不可统计行数，修正文件个数；否则，修正代码行数。
            
            $canModFileCount = strpos($this->config->quantizedoutput->specialFileTypes, strtolower($file_type)) > 0;
            if($canModFileCount){
                // 如果文件个数不相同，修正文件个数
                if(!$this->compareFieldValue($id, 'file_count')){
                    $neededMod = true;
                }
            } else {
                if(!$this->compareFieldValue($id, 'file_size') || !$this->compareFieldValue($id, 'line_add') || !$this->compareFieldValue($id, 'line_del')){
                    $neededMod = true;
                }
            }
            
            if($neededMod){
                $record = array();
                $record['department']       = $this->config->worklog->depcode;
                $record['related_pr_cuid']  = $data->related_pr_cuid_new[$id];
                $record['file_size']        = $data->file_size_new[$id];
                $record['line_add']         = $data->line_add_new[$id];
                $record['line_del']         = $data->line_del_new[$id];
                $record['line_modify']      = $data->line_modify_new[$id];
                $record['file_type']        = $data->file_type_new[$id];
                $record['file_count']       = $data->file_count_new[$id];
                $record['remark']           = $data->remark_new[$id];
                $record['time_stamp']       = date('Y-m-d H:i');
                
                // 修改历史修正人
                $lastIndex = strrpos($data->modifier_new[$id], ',');
                if($lastIndex > 0){
                    $lastAccount = substr($data->modifier_new[$id], $lastIndex + 1);
                    // die(js::alert( $this->app->user->account . '0011' . $lastAccount));
                    
                    if($lastAccount == $this->app->user->account){
                        $record['modifier'] = $data->modifier_new[$id];
                    } else {
                        $record['modifier'] = $data->modifier_new[$id] . ',' . $this->app->user->account;
                    }
                } else {
                    $record['modifier'] = $this->app->user->account;
                }
                
                $this->insertModRecord($record);
            }
        }
    }

    public function validateInputError($data){
        $hasEmptyMark = false;
        foreach($data->file_type as $id => $file_type){
            $hasMod = !$this->compareFieldValue($id, 'file_count') ||
                      !$this->compareFieldValue($id, 'file_size') ||
                      !$this->compareFieldValue($id, 'line_add') ||
                      !$this->compareFieldValue($id, 'line_del');
            if($hasMod && empty($data->remark_new[$id])){
                $hasEmptyMark = true;
                break;
            }
        }
        if($hasEmptyMark){
            print(js::alert('修改分数后，请填写备注！'));
        }
        
        return $hasEmptyMark;
    }

    public function compareFieldValue($id, $fieldName){
        $data = fixer::input('post')->get();
        
        $fieldValues = $data->$fieldName;
        
        $fieldNameNew = $fieldName . '_new';
        $fieldValuesNew = $data->$fieldNameNew;
        if($fieldValues[$id] == $fieldValuesNew[$id]){
            return true;
        } else {
            return false;
        }
    }

    public function insertModRecord($record){
       $sql = $this->config->quantizedoutput->insertModRecordSql;
       foreach($record as $fieldName => $fieldValue){
           $sql = str_replace('#{' . $fieldName . '}', $fieldValue, $sql);
       }
       
       $this->dao->exec($sql);
    }

    public function deleteModRecord($related_pr_cuid_new, $file_type_new){
        $sql = str_replace('#{related_pr_cuid}', $related_pr_cuid_new, $this->config->quantizedoutput->deleteModRecordSql);
        $sql = str_replace('#{file_type}', $file_type_new, $sql);
        
        $this->dao->exec($sql);
    }
    /* ============================== modmerge end ================================== */

    /* ============================== dayreport start ================================== */
        /**
     * Get amibas. 
     * 
     * @access public
     * @return void
     */
    public function getDayAmibas($userRootId, $workdate)
    {
        $dayAmibasSql = $this->getDayAmibasSql($userRootId, $workdate);
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
    
    public function getDayAmibasSql($userRootId, $workdate)
    {
        $dayAmibasSql = str_replace('#{workdate}', $workdate, $this->config->quantizedoutput->DayAmibasSql);
        $dayAmibasSql = str_replace('#{userRootId}', $userRootId, $dayAmibasSql);
        return $dayAmibasSql;
    }
    /* ============================== dayreport end ================================== */

    /* ============================== common start ================================== */
    
    function insertSort($array, $sortField){ //从小到大排列
        //先默认$array[0]，已经有序，是有序表
        for($i = 1; $i < count($array);$i++){
            $insertVal = $array[$i]; //$insertVal是准备插入的数
            $insertIndex = $i - 1; //有序表中准备比较的数的下标
            while($insertIndex >= 0 && $insertVal->$sortField > $array[$insertIndex]->$sortField){
                $array[$insertIndex + 1] = $array[$insertIndex]; //将数组往后挪
                $insertIndex--; //将下标往前挪，准备与前一个进行比较
            }
            if($insertIndex + 1 !== $i){
                $array[$insertIndex + 1] = $insertVal;
            }
        }
        return $array;
    }
    
    /* 获取两个日期中间的工作日的个数 */
    function getWorkDayCount($startDate, $endDate)
    {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        $count = 0;
        for ($startDate; $startDate <=$endDate; $startDate += 86400) {
            $info = getdate($startDate);
            $date = date('Ymd', $startDate);
            if((in_array($info['weekday'],array('Sunday','Saturday')) || in_array($date, $this->lang->quantizedoutput->holidays)) && !in_array($date, $this->lang->quantizedoutput->weekendWorkDays)){
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
        $amibaGroupPersonSql = str_replace('#{userRootId}', $userRootId, $this->config->quantizedoutput->AmibaGroupPersonSql);
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

    public function getPrjAmibaGroupPerson($monthNums)
    {
        $sql = $this->config->quantizedoutput->PrjAmibaGroupPersonSql;
        $sql = str_replace('#{monthNums}', implode(',', $monthNums), $sql);
        $accounts = $this->dao->query($sql);
        $amibas = array();
        $accountDict = array();
        foreach ($accounts as $account)
        {
            if (!isset($amibas[$account->amiba_id]))
            {
                $amibas[$account->amiba_id] = new stdclass();
                $amibas[$account->amiba_id]->usercount = 0;
                $amibas[$account->amiba_id]->realusercount = 0;
                $amibas[$account->amiba_id]->amiba_id = $account->amiba_id;
                $amibas[$account->amiba_id]->amiba_name = $account->amiba_name;
            }
            
            if (!isset($amibas[$account->amiba_id]->groups[$account->group_id]))
            {
                $amibas[$account->amiba_id]->groups[$account->group_id] = new stdclass();
                $amibas[$account->amiba_id]->groups[$account->group_id]->usercount = 0;
                $amibas[$account->amiba_id]->groups[$account->group_id]->realusercount = 0;
                $amibas[$account->amiba_id]->groups[$account->group_id]->group_id = $account->group_id;
                $amibas[$account->amiba_id]->groups[$account->group_id]->group_name = $account->group_name;
            }
            
            $amibas[$account->amiba_id]->usercount++;
            $amibas[$account->amiba_id]->realusercount++;
            $amibas[$account->amiba_id]->groups[$account->group_id]->usercount++;
            $amibas[$account->amiba_id]->groups[$account->group_id]->realusercount++;
            $amibas[$account->amiba_id]->groups[$account->group_id]->users[$account->account] = new stdclass();
            $amibas[$account->amiba_id]->groups[$account->group_id]->users[$account->account]->account = $account->account;
            
        }
        return $amibas;
    }

    public function getPrdAmibaGroupPerson($monthNums)
    {
        $sql = $this->config->quantizedoutput->PrdAmibaGroupPersonSql;
        $sql = str_replace('#{monthNums}', implode(',', $monthNums), $sql);
        $accounts = $this->dao->query($sql);
        $amibas = array();
        $accountDict = array();
        foreach ($accounts as $account)
        {
            if (!isset($amibas[$account->amiba_id]))
            {
                $amibas[$account->amiba_id] = new stdclass();
                $amibas[$account->amiba_id]->usercount = 0;
                $amibas[$account->amiba_id]->realusercount = 0;
                $amibas[$account->amiba_id]->amiba_id = $account->amiba_id;
                $amibas[$account->amiba_id]->amiba_name = $account->amiba_name;
            }
            
            if (!isset($amibas[$account->amiba_id]->groups[$account->group_id]))
            {
                $amibas[$account->amiba_id]->groups[$account->group_id] = new stdclass();
                $amibas[$account->amiba_id]->groups[$account->group_id]->usercount = 0;
                $amibas[$account->amiba_id]->groups[$account->group_id]->realusercount = 0;
                $amibas[$account->amiba_id]->groups[$account->group_id]->group_id = $account->group_id;
                $amibas[$account->amiba_id]->groups[$account->group_id]->group_name = $account->group_name;
            }
            
            $amibas[$account->amiba_id]->usercount++;
            $amibas[$account->amiba_id]->realusercount++;
            $amibas[$account->amiba_id]->groups[$account->group_id]->usercount++;
            $amibas[$account->amiba_id]->groups[$account->group_id]->realusercount++;
            $amibas[$account->amiba_id]->groups[$account->group_id]->users[$account->account] = new stdclass();
            $amibas[$account->amiba_id]->groups[$account->group_id]->users[$account->account]->account = $account->account;
            
        }
        return $amibas;
    }

    public function getDeptInfo(){
        $dept = $this->dao->query($this->config->quantizedoutput->deptInfoSql)->fetch();
        return $dept;
    }

    public function getUserRootDict(){
        $userRootDict = array();
        $items = $this->dao->query($this->config->quantizedoutput->userRootDictSql);
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
    /* ============================== common end ================================== */

    /* ============================== worklogs start ================================== */
    public function getWorklogs($dimType, $amibaId, $groupId, $account, $begin = '', $end = '')
    {
        $sql = $this->getWorklogsSql($dimType, $amibaId, $groupId, $account, $begin, $end);
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
    
    public function getWorklogsSql($dimType, $amibaId, $groupId, $account, $begin = '', $end = '')
    {
        $sqlKey = $dimType == 'staff' ? 'staff' . 'WorklogsSql' : 'prjprd' . 'WorklogsSql';
        
        $andWhereAmibaGroupAccount = '';
        if($dimType == 'staff')
        {
            
        }
        else if($dimType == 'prj')
        {
            if(!empty($amibaId))
            {
                $andWhereAmibaGroupAccount .=  "and pp.project = '" . $amibaId . "'" ;
            }
            if(!empty($groupId))
            {
                $andWhereAmibaGroupAccount .=  "and pp.product = '" . $groupId . "'" ;
            }
        }
        else /* if($dimType == 'prd') */
        {
            if(!empty($amibaId))
            {
                $andWhereAmibaGroupAccount .=  "and pp.product = '" . $amibaId . "'" ;
            }
            if(!empty($groupId))
            {
                $andWhereAmibaGroupAccount .=  "and pp.project = '" . $groupId . "'" ;
            }
        }
        if(!empty($account))
        {
            $andWhereAmibaGroupAccount .=  "and w.account = '" . $account . "'" ;
        }
        
        $sql = str_replace('#{andWhereAmibaGroupAccount}', $andWhereAmibaGroupAccount, 
            $this->config->quantizedoutput->$sqlKey);
        
        $andWhereWorkdate = '';
        if(!isset($end) || empty($end))
        {
            $andWhereWorkdate = " and w.work_date = '" . $begin . "'";
        }
        else
        {
            $andWhereWorkdate = " and w.work_date between '" . $begin . "' and '" . $end . "'";
        }
        $sql = str_replace('#{andWhereWorkdate}', $andWhereWorkdate, $sql);
        return $sql;
    }
    /* ============================== worklogs end ================================== */
        
    /* ============================== timetendency start ================================== */
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
            $andWhereAmibaGroupAccount = "and a.amiba_name = '" .$amibaName . "'" ;
        }
        else if($orgType == 'group')
        {
            $andWhereAmibaGroupAccount = "and a.amiba_name = '" .$amibaName . "' and a.group_name = '" . $groupName . "'" ;
        }
        else if($orgType == 'person')
        {
            $andWhereAmibaGroupAccount = "and b.account = '" .$account . "'" ;
        }
        
        $sql= str_replace('#{andWhereWorkdate}', $andWhereWorkdate, $this->config->quantizedoutput->DayTimeTendencyDataSql);
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
            $andWhereAmibaGroupAccount = "and a.amiba_name = '" .$amibaName . "'" ;
        }
        else if($orgType == 'group')
        {
            $andWhereAmibaGroupAccount = "and a.amiba_name = '" .$amibaName . "' and a.group_name = '" . $groupName . "'";
        }
        else if($orgType == 'person')
        {
            $andWhereAmibaGroupAccount = "and b.account = '" .$account . "'" ;
        }
        
        $sql= str_replace('#{andWhereWorkdate}', $andWhereWorkdate, $this->config->quantizedoutput->MonthTimeTendencyDataSql);
        $sql = str_replace('#{andWhereAmibaGroupAccount}', $andWhereAmibaGroupAccount, $sql);
        $sql = str_replace('#{userRootId}', $userRootId, $sql);
        return $sql;
    }
    /* ============================== timetendency end ================================== */

    /* ============================== sortmore start ================================== */
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
        
        $sql = str_replace('#{andWhereWorkdate}', $andWhereWorkdate, $this->config->quantizedoutput->MonthWorkSortSql);
        $sql = str_replace('#{workDayCount}', $workDayCount, $sql);
        $sql = str_replace('#{monthNums}', implode(',', $monthNums), $sql);
        $sql = str_replace('#{sortField}', $sortField, $sql);
        $sql = str_replace('#{userRootId}', $userRootId, $sql);
        return $sql;
    }

    /* ============================== sortmore end ================================== */

    /* ============================== prjsortmore start ================================== */
    public function getPrjMonthWorkSort($amibaId, $sortField, $monthNums, $begin, $end)
    {
        $monthWorks = $this->dao->query($this->getPrjMonthWorkSortSql($amibaId, $sortField, $monthNums, $begin, $end));
        $lastMonthWorks = array();
        $index = 1;
        foreach ($monthWorks as $w)
        {
            $lastMonthWorks[$index] = new stdclass();
            $lastMonthWorks[$index]->rankId = $index;
            $lastMonthWorks[$index]->amiba_id = $w->amiba_id;
            $lastMonthWorks[$index]->amiba_name = $w->amiba_name;
            $lastMonthWorks[$index]->group_id = $w->group_id;
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

    function getPrjMonthWorkSortSql($amibaId, $sortField, $monthNums, $begin, $end)
    {
        $andWhereWorkdate = "and w.work_date between '$begin' and '$end'";
        
        $workDayCount = $this->getWorkDayCount($begin, $end);
        
        $sql = str_replace('#{andWhereWorkdate}', $andWhereWorkdate, $this->config->quantizedoutput->PrjMonthWorkSortSql);
        $sql = str_replace('#{workDayCount}', $workDayCount, $sql);
        $sql = str_replace('#{monthNums}', implode(',', $monthNums), $sql);
        $sql = str_replace('#{sortField}', $sortField, $sql);
        $sql = str_replace('#{amibaId}', $amibaId, $sql);
        return $sql;
    }

    /* ============================== prjsortmore end ================================== */

    /* ============================== prdsortmore start ================================== */
    public function getPrdMonthWorkSort($amibaId, $sortField, $monthNums, $begin, $end)
    {
        $monthWorks = $this->dao->query($this->getPrdMonthWorkSortSql($amibaId, $sortField, $monthNums, $begin, $end));
        $lastMonthWorks = array();
        $index = 1;
        foreach ($monthWorks as $w)
        {
            $lastMonthWorks[$index] = new stdclass();
            $lastMonthWorks[$index]->rankId = $index;
            $lastMonthWorks[$index]->amiba_id = $w->amiba_id;
            $lastMonthWorks[$index]->amiba_name = $w->amiba_name;
            $lastMonthWorks[$index]->group_id = $w->group_id;
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

    function getPrdMonthWorkSortSql($amibaId, $sortField, $monthNums, $begin, $end)
    {
        $andWhereWorkdate = "and w.work_date between '$begin' and '$end'";
        
        $workDayCount = $this->getWorkDayCount($begin, $end);
        
        $sql = str_replace('#{andWhereWorkdate}', $andWhereWorkdate, $this->config->quantizedoutput->PrdMonthWorkSortSql);
        $sql = str_replace('#{workDayCount}', $workDayCount, $sql);
        $sql = str_replace('#{monthNums}', implode(',', $monthNums), $sql);
        $sql = str_replace('#{sortField}', $sortField, $sql);
        $sql = str_replace('#{amibaId}', $amibaId, $sql);
        return $sql;
    }

    /* ============================== prdsortmore end ================================== */

    /* ============================== sort start ================================== */
    public function getPersonTop30Flot($userRootId, $monthNums, $begin, $end)
    {
        $lastWorklogs = array();
        $worklogs = $this->dao->query($this->getPersonTimeTop30Sql($userRootId, $monthNums, $begin, $end));
        foreach($worklogs as $w)
        {
            array_push($lastWorklogs, $w);
        }
        $lastWorklogs = $this->insertSort($lastWorklogs, 'total_time');
        $PersonTimeTop30Data = $this->getPersonTimeTop30Flot($lastWorklogs);
        
        $lastWorklogs = $this->insertSort($lastWorklogs, 'output_efficiency');
        $PersonOutputEfficiencyTop30Data = $this->getPersonOutputEfficiencyTop30Flot($lastWorklogs);
        
        $lastWorklogs = $this->insertSort($lastWorklogs, 'total_output');
        $PersonOutputTop30Data = $this->getPersonOutputTop30Flot($lastWorklogs);
        return array($PersonTimeTop30Data, $PersonOutputEfficiencyTop30Data, $PersonOutputTop30Data);
    }

    public function getPersonTimeTop30Flot($worklogs)
    {
        $PersonTop30Data['labels'] = '[';
        $PersonTop30Data['burnBar'] = '[';
        $PersonTop30Data['burnBar1'] = '[';
        $i = 1;
        foreach($worklogs as $worklog) 
        {
            $PersonTop30Data['labels'] .= '"' . $worklog->realname . '"' . ',';
            
            $PersonTop30Data['burnBar'] .= '"' . $this->setDefaultIfIntEmpty($worklog->total_time)  . '"' . ',';
            $PersonTop30Data['burnBar1'] .= '"' . $this->setDefaultIfIntEmpty($worklog->day_avg_time) . '"' . ',';
            $i++;
            if($i >= 30) 
            {
                break;
            }
        }
        
        $PersonTop30Data['labels']   = rtrim($PersonTop30Data['labels'], ',');
        $PersonTop30Data['labels'] .= ']';
        
        $PersonTop30Data['burnBar'] = ltrim($PersonTop30Data['burnBar'], ',');
        $PersonTop30Data['burnBar'] .= ']';
        $PersonTop30Data['burnBar1'] = ltrim($PersonTop30Data['burnBar1'], ',');
        $PersonTop30Data['burnBar1'] .= ']';
        
        return $PersonTop30Data;
    }

    public function getPersonOutputTop30Flot($worklogs)
    {
        $PersonTop30Data['labels'] = '[';
        $PersonTop30Data['burnBar'] = '[';
        $i = 1;
        foreach($worklogs as $worklog) 
        {
            $PersonTop30Data['labels'] .= '"' . $worklog->realname . '"' . ',';
            $PersonTop30Data['burnBar'] .= '"' . $this->setDefaultIfIntEmpty($worklog->total_output) . '"' . ',';
            $i++;
            if($i >= 30) 
            {
                break;
            }
        }
        
        $PersonTop30Data['labels']   = rtrim($PersonTop30Data['labels'], ',');
        $PersonTop30Data['labels'] .= ']';
        
        $PersonTop30Data['burnBar'] = ltrim($PersonTop30Data['burnBar'], ',');
        $PersonTop30Data['burnBar'] .= ']';
        
        return $PersonTop30Data;
    }

    public function getPersonOutputEfficiencyTop30Flot($worklogs)
    {
        $PersonTop30Data['labels'] = '[';
        $PersonTop30Data['burnBar'] = '[';
        $i = 1;
        foreach($worklogs as $worklog) 
        {
            $PersonTop30Data['labels'] .= '"' . $worklog->realname . '"' . ',';
            $PersonTop30Data['burnBar'] .= '"' . $this->setDefaultIfIntEmpty($worklog->output_efficiency) . '"' . ',';
            $i++;
            if($i >= 30) 
            {
                break;
            }
        }
        
        $PersonTop30Data['labels']   = rtrim($PersonTop30Data['labels'], ',');
        $PersonTop30Data['labels'] .= ']';
        
        $PersonTop30Data['burnBar'] = ltrim($PersonTop30Data['burnBar'], ',');
        $PersonTop30Data['burnBar'] .= ']';
        
        return $PersonTop30Data;
    }

    function getPersonTimeTop30Sql($userRootId, $monthNums, $begin, $end)
    {
        $andWhereWorkdate = " and w.work_date between '$begin' and '$end'";
        $workDayCount = $this->getWorkDayCount($begin, $end);
        
        $sql = str_replace('#{andWhereWorkdate}', $andWhereWorkdate, $this->config->quantizedoutput->PersonTimeTop30Sql);
        $sql = str_replace('#{workDayCount}', $workDayCount, $sql);
        $sql = str_replace('#{monthNums}', implode(',', $monthNums), $sql);
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

        $sql1 = str_replace('#{andWhereWorkdate}', $andWhereWorkdate, $this->config->quantizedoutput->PersonAvgAmibaTimeTopSql);
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
    /* ============================== sort end ================================== */

    /* ============================== prjsort start ================================== */
    public function getPrjPersonTop30Flot($amibaId, $monthNums, $begin, $end)
    {
        $lastWorklogs = array();
        $worklogs = $this->dao->query($this->getPrjPersonTimeTop30Sql($amibaId, $monthNums, $begin, $end));
        foreach($worklogs as $w)
        {
            array_push($lastWorklogs, $w);
        }
        $lastWorklogs = $this->insertSort($lastWorklogs, 'total_time');
        $PersonTimeTop30Data = $this->getPrjPersonTimeTop30Flot($lastWorklogs);
        
        $lastWorklogs = $this->insertSort($lastWorklogs, 'output_efficiency');
        $PersonOutputEfficiencyTop30Data = $this->getPrjPersonOutputEfficiencyTop30Flot($lastWorklogs);
        
        $lastWorklogs = $this->insertSort($lastWorklogs, 'total_output');
        $PersonOutputTop30Data = $this->getPrjPersonOutputTop30Flot($lastWorklogs);
        return array($PersonTimeTop30Data, $PersonOutputEfficiencyTop30Data, $PersonOutputTop30Data);
    }

    public function getPrjPersonTimeTop30Flot($worklogs)
    {
        $PersonTop30Data['labels'] = '[';
        $PersonTop30Data['burnBar'] = '[';
        $PersonTop30Data['burnBar1'] = '[';
        $i = 0;
        foreach($worklogs as $worklog) 
        {
            $PersonTop30Data['labels'] .= '"' . $worklog->realname . '"' . ',';
            
            $PersonTop30Data['burnBar'] .= '"' . $this->setDefaultIfIntEmpty($worklog->total_time)  . '"' . ',';
            $PersonTop30Data['burnBar1'] .= '"' . $this->setDefaultIfIntEmpty($worklog->day_avg_time) . '"' . ',';
            $i++;
            if($i >= 30) 
            {
                break;
            }
        }
        
        $PersonTop30Data['labels']   = rtrim($PersonTop30Data['labels'], ',');
        $PersonTop30Data['labels'] .= ']';
        
        $PersonTop30Data['burnBar'] = ltrim($PersonTop30Data['burnBar'], ',');
        $PersonTop30Data['burnBar'] .= ']';
        $PersonTop30Data['burnBar1'] = ltrim($PersonTop30Data['burnBar1'], ',');
        $PersonTop30Data['burnBar1'] .= ']';
        
        return $PersonTop30Data;
    }

    public function getPrjPersonOutputTop30Flot($worklogs)
    {
        $PersonTop30Data['labels'] = '[';
        $PersonTop30Data['burnBar'] = '[';
        $i = 0;
        foreach($worklogs as $worklog) 
        {
            $PersonTop30Data['labels'] .= '"' . $worklog->realname . '"' . ',';
            $PersonTop30Data['burnBar'] .= '"' . $this->setDefaultIfIntEmpty($worklog->total_output) . '"' . ',';
            $i++;
            if($i >= 30) 
            {
                break;
            }
        }
        
        $PersonTop30Data['labels']   = rtrim($PersonTop30Data['labels'], ',');
        $PersonTop30Data['labels'] .= ']';
        
        $PersonTop30Data['burnBar'] = ltrim($PersonTop30Data['burnBar'], ',');
        $PersonTop30Data['burnBar'] .= ']';
        
        return $PersonTop30Data;
    }

    public function getPrjPersonOutputEfficiencyTop30Flot($worklogs)
    {
        $PersonTop30Data['labels'] = '[';
        $PersonTop30Data['burnBar'] = '[';
        $i = 0;
        foreach($worklogs as $worklog) 
        {
            $PersonTop30Data['labels'] .= '"' . $worklog->realname . '"' . ',';
            $PersonTop30Data['burnBar'] .= '"' . $this->setDefaultIfIntEmpty($worklog->output_efficiency) . '"' . ',';
            $i++;
            if($i >= 30) 
            {
                break;
            }
        }
        
        $PersonTop30Data['labels']   = rtrim($PersonTop30Data['labels'], ',');
        $PersonTop30Data['labels'] .= ']';
        
        $PersonTop30Data['burnBar'] = ltrim($PersonTop30Data['burnBar'], ',');
        $PersonTop30Data['burnBar'] .= ']';
        
        return $PersonTop30Data;
    }

    function getPrjPersonTimeTop30Sql($amibaId, $monthNums, $begin, $end, $sortField)
    {
        $andWhereWorkdate = " and w.work_date between '$begin' and '$end'";
        $workDayCount = $this->getWorkDayCount($begin, $end);
        
        $sql = str_replace('#{andWhereWorkdate}', $andWhereWorkdate, $this->config->quantizedoutput->PrjPersonTimeTop30Sql);
        $sql = str_replace('#{workDayCount}', $workDayCount, $sql);
        $sql = str_replace('#{monthNums}', implode(',', $monthNums), $sql);
        $sql = str_replace('#{amibaId}', $amibaId, $sql);
        return $sql;
    }
    /* ============================== prjsort end ================================== */

    /* ============================== prdsort start ================================== */
    public function getPrdPersonTop30Flot($amibaId, $monthNums, $begin, $end)
    {
        $lastWorklogs = array();
        $worklogs = $this->dao->query($this->getPrdPersonTimeTop30Sql($amibaId, $monthNums, $begin, $end));
        foreach($worklogs as $w)
        {
            array_push($lastWorklogs, $w);
        }
        $lastWorklogs = $this->insertSort($lastWorklogs, 'total_time');
        $PersonTimeTop30Data = $this->getPrdPersonTimeTop30Flot($lastWorklogs);
        
        $lastWorklogs = $this->insertSort($lastWorklogs, 'output_efficiency');
        $PersonOutputEfficiencyTop30Data = $this->getPrdPersonOutputEfficiencyTop30Flot($lastWorklogs);
        
        $lastWorklogs = $this->insertSort($lastWorklogs, 'total_output');
        $PersonOutputTop30Data = $this->getPrdPersonOutputTop30Flot($lastWorklogs);
        return array($PersonTimeTop30Data, $PersonOutputEfficiencyTop30Data, $PersonOutputTop30Data);
    }

    public function getPrdPersonTimeTop30Flot($worklogs)
    {
        $PersonTop30Data['labels'] = '[';
        $PersonTop30Data['burnBar'] = '[';
        $PersonTop30Data['burnBar1'] = '[';
        $i = 0;
        foreach($worklogs as $worklog) 
        {
            $PersonTop30Data['labels'] .= '"' . $worklog->realname . '"' . ',';
            
            $PersonTop30Data['burnBar'] .= '"' . $this->setDefaultIfIntEmpty($worklog->total_time)  . '"' . ',';
            $PersonTop30Data['burnBar1'] .= '"' . $this->setDefaultIfIntEmpty($worklog->day_avg_time) . '"' . ',';
            $i++;
            if($i >= 30) 
            {
                break;
            }
        }
        
        $PersonTop30Data['labels']   = rtrim($PersonTop30Data['labels'], ',');
        $PersonTop30Data['labels'] .= ']';
        
        $PersonTop30Data['burnBar'] = ltrim($PersonTop30Data['burnBar'], ',');
        $PersonTop30Data['burnBar'] .= ']';
        $PersonTop30Data['burnBar1'] = ltrim($PersonTop30Data['burnBar1'], ',');
        $PersonTop30Data['burnBar1'] .= ']';
        
        return $PersonTop30Data;
    }

    public function getPrdPersonOutputTop30Flot($worklogs)
    {
        $PersonTop30Data['labels'] = '[';
        $PersonTop30Data['burnBar'] = '[';
        $i = 0;
        foreach($worklogs as $worklog) 
        {
            $PersonTop30Data['labels'] .= '"' . $worklog->realname . '"' . ',';
            $PersonTop30Data['burnBar'] .= '"' . $this->setDefaultIfIntEmpty($worklog->total_output) . '"' . ',';
            $i++;
            if($i >= 30) 
            {
                break;
            }
        }
        
        $PersonTop30Data['labels']   = rtrim($PersonTop30Data['labels'], ',');
        $PersonTop30Data['labels'] .= ']';
        
        $PersonTop30Data['burnBar'] = ltrim($PersonTop30Data['burnBar'], ',');
        $PersonTop30Data['burnBar'] .= ']';
        
        return $PersonTop30Data;
    }

    public function getPrdPersonOutputEfficiencyTop30Flot($worklogs)
    {
        $PersonTop30Data['labels'] = '[';
        $PersonTop30Data['burnBar'] = '[';
        $i = 0;
        foreach($worklogs as $worklog) 
        {
            $PersonTop30Data['labels'] .= '"' . $worklog->realname . '"' . ',';
            $PersonTop30Data['burnBar'] .= '"' . $this->setDefaultIfIntEmpty($worklog->output_efficiency) . '"' . ',';
            $i++;
            if($i >= 30) 
            {
                break;
            }
        }
        
        $PersonTop30Data['labels']   = rtrim($PersonTop30Data['labels'], ',');
        $PersonTop30Data['labels'] .= ']';
        
        $PersonTop30Data['burnBar'] = ltrim($PersonTop30Data['burnBar'], ',');
        $PersonTop30Data['burnBar'] .= ']';
        
        return $PersonTop30Data;
    }

    function getPrdPersonTimeTop30Sql($amibaId, $monthNums, $begin, $end, $sortField)
    {
        $andWhereWorkdate = " and w.work_date between '$begin' and '$end'";
        $workDayCount = $this->getWorkDayCount($begin, $end);
        
        $sql = str_replace('#{andWhereWorkdate}', $andWhereWorkdate, $this->config->quantizedoutput->PrdPersonTimeTop30Sql);
        $sql = str_replace('#{workDayCount}', $workDayCount, $sql);
        $sql = str_replace('#{monthNums}', implode(',', $monthNums), $sql);
        $sql = str_replace('#{amibaId}', $amibaId, $sql);
        return $sql;
    }
    /* ============================== prdsort end ================================== */

    /* ============================== monthreport start ================================== */
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
        
        $monthReportSql = str_replace('#{andWhereWorkdate}', $andWhereWorkdate, $this->config->quantizedoutput->MonthAmibasSql);
        $monthReportSql = str_replace('#{workDayCount}', $workDayCount, $monthReportSql);
        $monthReportSql = str_replace('#{standardOutput}', $this->config->quantizedoutput->standardOutput, $monthReportSql);
        $monthReportSql = str_replace('#{monthNum}', $monthNum, $monthReportSql);
        $monthReportSql = str_replace('#{userRootId}', $userRootId, $monthReportSql);
        
        return $monthReportSql;
    }
    /* ============================== monthreport end ================================== */

    /* ============================== prjmonthreport start ================================== */
    public function getPrjMonthAmibas($monthNum, $begin, $end)
    {
        $worklogs = $this->getPrjMonthReportData($monthNum, $begin, $end);
        
        $deptUserCount = 0;
        $amibas = array();
        $deptAllTime = 0;
        $deptAllOutput = 0;
        $deptHasOutputUserCount = 0;
        foreach ($worklogs as $worklog)
        {
            if (!isset($amibas[$worklog->amiba_id]))
            {
                $amibas[$worklog->amiba_id] = new stdclass();
                $amibas[$worklog->amiba_id]->usercount = 0;
                $amibas[$worklog->amiba_id]->realusercount = 0;
                $amibas[$worklog->amiba_id]->total_time = 0;
                $amibas[$worklog->amiba_id]->total_output = 0;
                $amibas[$worklog->amiba_id]->mod_merge_count = 0;
                $amibas[$worklog->amiba_id]->amiba_id = $worklog->amiba_id;
                $amibas[$worklog->amiba_id]->amiba_name = $worklog->project_name;
            }
            
            if (!isset($amibas[$worklog->amiba_id]->groups[$worklog->group_id]))
            {
                $amibas[$worklog->amiba_id]->groups[$worklog->group_id] = new stdclass();
                $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->usercount = 0;
                $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->realusercount = 0;
                $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->total_time = 0;
                $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->total_output = 0;
                $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->mod_merge_count = 0;
                $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->group_id = $worklog->group_id;
                $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->group_name = $worklog->product_name;
            }
            
            $amibas[$worklog->amiba_id]->usercount++;
            $amibas[$worklog->amiba_id]->realusercount++;
            $amibas[$worklog->amiba_id]->total_time += $worklog->total_time;
            $amibas[$worklog->amiba_id]->total_output += $worklog->total_output;
            $amibas[$worklog->amiba_id]->mod_merge_count += $worklog->mod_merge_count;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->usercount++;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->realusercount++;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->total_time += $worklog->total_time;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->total_output += $worklog->total_output;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->mod_merge_count += $worklog->mod_merge_count;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account] = new stdclass();
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->realname = $worklog->realname;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->total_time = $worklog->total_time;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->total_output = $worklog->total_output;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->mod_merge_count = $worklog->mod_merge_count;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->account = $worklog->account;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->day_avg_time = $worklog->day_avg_time;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->extra_time = $worklog->extra_time;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->day_avg_output = $worklog->day_avg_output;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->extra_output = $worklog->extra_output;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->output_efficiency = $worklog->output_efficiency;
            
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

    public function getPrjMonthReportData($monthNum, $begin, $end)
    {
        return $this->dao->query($this->getPrjMonthReportSql($monthNum, $begin, $end));
    }

    function getPrjMonthReportSql($monthNum, $begin, $end)
    {
        $andWhereWorkdate = "and w.work_date between '" . $begin . "' and '" . $end . "'";
        $workDayCount = $this->getWorkDayCount($begin, $end);
        
        $monthReportSql = str_replace('#{andWhereWorkdate}', $andWhereWorkdate, $this->config->quantizedoutput->prjMonthAmibasSql);
        $monthReportSql = str_replace('#{workDayCount}', $workDayCount, $monthReportSql);
        $monthReportSql = str_replace('#{standardOutput}', $this->config->quantizedoutput->standardOutput, $monthReportSql);
        $monthReportSql = str_replace('#{monthNum}', $monthNum, $monthReportSql);
        
        return $monthReportSql;
    }
    /* ============================== prjmonthreport end ================================== */

    /* ============================== prdmonthreport start ================================== */
    public function getPrdMonthAmibas($monthNum, $begin, $end)
    {
        $worklogs = $this->getPrdMonthReportData($monthNum, $begin, $end);
        
        $deptUserCount = 0;
        $amibas = array();
        $deptAllTime = 0;
        $deptAllOutput = 0;
        $deptHasOutputUserCount = 0;
        foreach ($worklogs as $worklog)
        {
            if (!isset($amibas[$worklog->amiba_id]))
            {
                $amibas[$worklog->amiba_id] = new stdclass();
                $amibas[$worklog->amiba_id]->usercount = 0;
                $amibas[$worklog->amiba_id]->realusercount = 0;
                $amibas[$worklog->amiba_id]->total_time = 0;
                $amibas[$worklog->amiba_id]->total_output = 0;
                $amibas[$worklog->amiba_id]->mod_merge_count = 0;
                $amibas[$worklog->amiba_id]->amiba_id = $worklog->amiba_id;
                $amibas[$worklog->amiba_id]->amiba_name = $worklog->product_name;
            }
            
            if (!isset($amibas[$worklog->amiba_id]->groups[$worklog->group_id]))
            {
                $amibas[$worklog->amiba_id]->groups[$worklog->group_id] = new stdclass();
                $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->usercount = 0;
                $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->realusercount = 0;
                $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->total_time = 0;
                $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->total_output = 0;
                $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->mod_merge_count = 0;
                $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->group_id = $worklog->group_id;
                $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->group_name = $worklog->project_name;
            }
            
            $amibas[$worklog->amiba_id]->usercount++;
            $amibas[$worklog->amiba_id]->realusercount++;
            $amibas[$worklog->amiba_id]->total_time += $worklog->total_time;
            $amibas[$worklog->amiba_id]->total_output += $worklog->total_output;
            $amibas[$worklog->amiba_id]->mod_merge_count += $worklog->mod_merge_count;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->usercount++;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->realusercount++;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->total_time += $worklog->total_time;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->total_output += $worklog->total_output;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->mod_merge_count += $worklog->mod_merge_count;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account] = new stdclass();
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->realname = $worklog->realname;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->total_time = $worklog->total_time;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->total_output = $worklog->total_output;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->mod_merge_count = $worklog->mod_merge_count;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->account = $worklog->account;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->day_avg_time = $worklog->day_avg_time;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->extra_time = $worklog->extra_time;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->day_avg_output = $worklog->day_avg_output;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->extra_output = $worklog->extra_output;
            $amibas[$worklog->amiba_id]->groups[$worklog->group_id]->users[$worklog->account]->output_efficiency = $worklog->output_efficiency;
            
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

    public function getPrdMonthReportData($monthNum, $begin, $end)
    {
        return $this->dao->query($this->getPrdMonthReportSql($monthNum, $begin, $end));
    }

    function getPrdMonthReportSql($monthNum, $begin, $end)
    {
        $andWhereWorkdate = "and w.work_date between '" . $begin . "' and '" . $end . "'";
        $workDayCount = $this->getWorkDayCount($begin, $end);
        
        $monthReportSql = str_replace('#{andWhereWorkdate}', $andWhereWorkdate, $this->config->quantizedoutput->prdMonthAmibasSql);
        $monthReportSql = str_replace('#{workDayCount}', $workDayCount, $monthReportSql);
        $monthReportSql = str_replace('#{standardOutput}', $this->config->quantizedoutput->standardOutput, $monthReportSql);
        $monthReportSql = str_replace('#{monthNum}', $monthNum, $monthReportSql);
        
        return $monthReportSql;
    }
    /* ============================== prdmonthreport end ================================== */

    /* ============================== taskperformancescoredetail start ================================== */
    public function getTaskScoreDetail($taskId)
    {
        $outputDetailSql = str_replace('#{taskId}', $taskId, $this->config->quantizedoutput->TaskPerformanceScoreDetailSql);
        $outputDetailList = $this->dao->query($outputDetailSql);
        
        return $outputDetailList;
    }
    /* ============================== taskperformancescoredetail end ================================== */

    /* ============================== monthperformancescoredetail start ================================== */
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
        if(in_array($scoreType, $this->config->quantizedoutput->bugTypes)) {
            $sqlKey = 'BugDetailSql';
        } else if(in_array($scoreType, $this->config->quantizedoutput->caseTypes)) {
            $sqlKey = 'CaseDetailSql';
        }
        
        $outputDetailSql = str_replace('#{monthNum}', $monthNum, $this->config->quantizedoutput->$sqlKey);
        $outputDetailSql = str_replace('#{begin}', $begin, $outputDetailSql);
        $outputDetailSql = str_replace('#{end}', $end, $outputDetailSql);
        $outputDetailSql = str_replace('#{andWhereAmibaGroupAccount}', $andWhereAmibaGroupAccount, $outputDetailSql);
        $outputDetailSql = str_replace('#{bugType}', $scoreType, $outputDetailSql);
        $outputDetailSql = str_replace('#{caseType}', $scoreType, $outputDetailSql);
        $outputDetailSql = str_replace('#{userRootId}', $userRootId, $outputDetailSql);
        
        return $outputDetailSql;
    }
    /* ============================== monthperformancescoredetail end ================================== */

    /* ============================== prjmonthperformancescoredetail start ================================== */
    public function getPrjScoreDetail($scoreType, $amibaId, $groupId, $account, $orgType, $monthNum)
    {
        $outputDetailSql = $this->getPrjScoreDetailSql($scoreType, $amibaId, $groupId, $account, $orgType, $monthNum, $begin, $end);
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

    public function getPrjScoreDetailSql($scoreType, $amibaId, $groupId, $account, $orgType, $monthNum, $begin, $end)
    {
        $andWhereAmibaGroupAccount = '';
        if($orgType == 'dept')
        {
            $andWhereAmibaGroupAccount .= "";
        }
        else if($orgType == 'amiba')
        {
            $andWhereAmibaGroupAccount .= " and a.project = '$amibaId'";
        }
        else if($orgType == 'group')
        {
            $andWhereAmibaGroupAccount .= " and a.project = '$amibaId'";
            $andWhereAmibaGroupAccount .= " and a.product = '$groupId'";
        } 
        else
        {
            $andWhereAmibaGroupAccount .= " and a.project = '$amibaId'";
            $andWhereAmibaGroupAccount .= " and a.product = '$groupId'";
            $andWhereAmibaGroupAccount .= " and a.account = '$account'";
        }
        
        $sqlKey = 'Prj';
        if(in_array($scoreType, $this->config->quantizedoutput->bugTypes)) {
            $sqlKey .= 'BugDetailSql';
        } else if(in_array($scoreType, $this->config->quantizedoutput->caseTypes)) {
            $sqlKey .= 'CaseDetailSql';
        } else {
            $sqlKey .= $scoreType . 'DetailSql';
        }
        
        $outputDetailSql = str_replace('#{monthNum}', $monthNum, $this->config->quantizedoutput->$sqlKey);
        $outputDetailSql = str_replace('#{begin}', $begin, $outputDetailSql);
        $outputDetailSql = str_replace('#{end}', $end, $outputDetailSql);
        $outputDetailSql = str_replace('#{andWhereAmibaGroupAccount}', $andWhereAmibaGroupAccount, $outputDetailSql);
        $outputDetailSql = str_replace('#{bugType}', $scoreType, $outputDetailSql);
        $outputDetailSql = str_replace('#{caseType}', $scoreType, $outputDetailSql);
        
        return $outputDetailSql;
    }
    /* ============================== prjmonthperformancescoredetail end ================================== */

    /* ============================== prdmonthperformancescoredetail start ================================== */
    public function getPrdScoreDetail($scoreType, $amibaId, $groupId, $account, $orgType, $monthNum)
    {
        $outputDetailSql = $this->getPrdScoreDetailSql($scoreType, $amibaId, $groupId, $account, $orgType, $monthNum, $begin, $end);
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

    public function getPrdScoreDetailSql($scoreType, $amibaId, $groupId, $account, $orgType, $monthNum, $begin, $end)
    {
        $andWhereAmibaGroupAccount = '';
        if($orgType == 'dept')
        {
            $andWhereAmibaGroupAccount .= "";
        }
        else if($orgType == 'amiba')
        {
            $andWhereAmibaGroupAccount .= " and a.product = '$amibaId'";
        }
        else if($orgType == 'group')
        {
            $andWhereAmibaGroupAccount .= " and a.product = '$amibaId'";
            $andWhereAmibaGroupAccount .= " and a.project = '$groupId'";
        } 
        else
        {
            $andWhereAmibaGroupAccount .= " and a.product = '$amibaId'";
            $andWhereAmibaGroupAccount .= " and a.project = '$groupId'";
            $andWhereAmibaGroupAccount .= " and a.account = '$account'";
        }
        
        $sqlKey = 'Prd';
        if(in_array($scoreType, $this->config->quantizedoutput->bugTypes)) {
            $sqlKey .= 'BugDetailSql';
        } else if(in_array($scoreType, $this->config->quantizedoutput->caseTypes)) {
            $sqlKey .= 'CaseDetailSql';
        } else {
            $sqlKey .= $scoreType . 'DetailSql';
        }
        
        $outputDetailSql = str_replace('#{monthNum}', $monthNum, $this->config->quantizedoutput->$sqlKey);
        $outputDetailSql = str_replace('#{begin}', $begin, $outputDetailSql);
        $outputDetailSql = str_replace('#{end}', $end, $outputDetailSql);
        $outputDetailSql = str_replace('#{andWhereAmibaGroupAccount}', $andWhereAmibaGroupAccount, $outputDetailSql);
        $outputDetailSql = str_replace('#{bugType}', $scoreType, $outputDetailSql);
        $outputDetailSql = str_replace('#{caseType}', $scoreType, $outputDetailSql);
        
        return $outputDetailSql;
    }
    /* ============================== prdmonthperformancescoredetail end ================================== */

    /* ============================== monthperformance start ================================== */
    public function getOutputInfo($userRootId, $amibaName, $groupName, $account, $orgType, $monthNum)
    {
        $outputInfoSql = $this->getOutputInfoSql($userRootId, $amibaName, $groupName, $account, $orgType, $monthNum);
        $outputInfo = $this->dao->query($outputInfoSql)->fetch();
        return $outputInfo;
    }

    public function getOutputInfoSql($userRootId, $amibaName, $groupName, $account, $orgType, $monthNum)
    {
        if($orgType == 'dept')
        {
            $andWhereAmibaGroupAccount = "";
            $groupBy = "";
        }
        else if($orgType == 'amiba')
        {
            $andWhereAmibaGroupAccount = "and a.amiba_name = '$amibaName'";
            $groupBy = "group by a.amiba_name";
        }
        else if($orgType == 'group')
        {
            $andWhereAmibaGroupAccount = "and a.group_name = '$groupName'";
            $groupBy = "group by a.group_name";
        } 
        else
        {
            $andWhereAmibaGroupAccount = "and a.account = '$account'";
            $groupBy = "group by a.account";
        }
        
        $outputInfoSql = str_replace('#{monthNum}', $monthNum, $this->config->quantizedoutput->outputInfoSql);
        $outputInfoSql = str_replace('#{andWhereAmibaGroupAccount}', $andWhereAmibaGroupAccount, $outputInfoSql);
        $outputInfoSql = str_replace('#{groupBy}', $groupBy, $outputInfoSql);
        $outputInfoSql = str_replace('#{userRootId}', $userRootId, $outputInfoSql);
        
        return $outputInfoSql;
    }
    /* ============================== monthperformance end ================================== */

    /* ============================== prjmonthperformance start ================================== */
    public function getPrjOutputInfo($amibaId, $groupId, $account, $orgType, $monthNum)
    {
        $outputInfoSql = $this->getPrjOutputInfoSql($amibaId, $groupId, $account, $orgType, $monthNum);
        $outputInfo = $this->dao->query($outputInfoSql)->fetch();
        return $outputInfo;
    }

    public function getPrjOutputInfoSql($amibaId, $groupId, $account, $orgType, $monthNum)
    {
        if($orgType == 'dept')
        {
            $andWhereAmibaGroupAccount = "";
            $groupBy = "";
        }
        else if($orgType == 'amiba')
        {
            $andWhereAmibaGroupAccount = " and a.amiba_id = '$amibaId'";
            $groupBy = " group by a.amiba_id";
        }
        else if($orgType == 'amibaSkipGroupAccount')
        {
            $andWhereAmibaGroupAccount = " and a.amiba_id = '$amibaId'";
            $andWhereAmibaGroupAccount .= " and a.account = '$account'";
            $groupBy = " group by a.account";
        }
        else if($orgType == 'group')
        {
            $andWhereAmibaGroupAccount  = " and a.amiba_id = '$amibaId'";
            $andWhereAmibaGroupAccount .= " and a.group_id = '$groupId'";
            $groupBy = " group by a.group_id";
        } 
        else
        {
            $andWhereAmibaGroupAccount  = " and a.amiba_id = '$amibaId'";
            $andWhereAmibaGroupAccount .= " and a.group_id = '$groupId'";
            $andWhereAmibaGroupAccount .= " and a.account = '$account'";
            $groupBy = " group by a.account";
        }
        
        $outputInfoSql = str_replace('#{monthNum}', $monthNum, $this->config->quantizedoutput->prjoutputInfoSql);
        $outputInfoSql = str_replace('#{andWhereAmibaGroupAccount}', $andWhereAmibaGroupAccount, $outputInfoSql);
        $outputInfoSql = str_replace('#{groupBy}', $groupBy, $outputInfoSql);
        
        return $outputInfoSql;
    }
    /* ============================== prjmonthperformance end ================================== */
   
    /* ============================== prdmonthperformance start ================================== */
    public function getPrdOutputInfo($amibaId, $groupId, $account, $orgType, $monthNum)
    {
        $outputInfoSql = $this->getPrdOutputInfoSql($amibaId, $groupId, $account, $orgType, $monthNum);
        $outputInfo = $this->dao->query($outputInfoSql)->fetch();
        return $outputInfo;
    }

    public function getPrdOutputInfoSql($amibaId, $groupId, $account, $orgType, $monthNum)
    {
        if($orgType == 'dept')
        {
            $andWhereAmibaGroupAccount = "";
            $groupBy = "";
        }
        else if($orgType == 'amiba')
        {
            $andWhereAmibaGroupAccount = " and a.amiba_id = '$amibaId'";
            $groupBy = " group by a.amiba_id";
        }
        else if($orgType == 'amibaSkipGroupAccount')
        {
            $andWhereAmibaGroupAccount = " and a.amiba_id = '$amibaId'";
            $andWhereAmibaGroupAccount .= " and a.account = '$account'";
            $groupBy = " group by a.account";
        }
        else if($orgType == 'group')
        {
            $andWhereAmibaGroupAccount  = " and a.amiba_id = '$amibaId'";
            $andWhereAmibaGroupAccount .= " and a.group_id = '$groupId'";
            $groupBy = " group by a.group_id";
        } 
        else
        {
            $andWhereAmibaGroupAccount  = " and a.amiba_id = '$amibaId'";
            $andWhereAmibaGroupAccount .= " and a.group_id = '$groupId'";
            $andWhereAmibaGroupAccount .= " and a.account = '$account'";
            $groupBy = " group by a.account";
        }
        
        $outputInfoSql = str_replace('#{monthNum}', $monthNum, $this->config->quantizedoutput->prdoutputInfoSql);
        $outputInfoSql = str_replace('#{andWhereAmibaGroupAccount}', $andWhereAmibaGroupAccount, $outputInfoSql);
        $outputInfoSql = str_replace('#{groupBy}', $groupBy, $outputInfoSql);
        
        return $outputInfoSql;
    }
    /* ============================== prdmonthperformance end ================================== */
     
}