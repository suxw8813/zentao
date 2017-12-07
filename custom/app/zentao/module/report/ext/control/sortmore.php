<?php
/**feature-1077**/
/**feature-1245**/
/**
 * The control file of report module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     report
 * @version     $Id: control.php 4622 2013-03-28 01:09:02Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class report extends control
{
    /**
     * monthwork information report.
     * 
     * @access public
     * @return void
     */
    public function sortmore($timeType, $sortField, $userRootId, $amibaName = '', $groupName = '', $userName = '', $timeNum = '')
    {
        list($time, $monthNums, $begin, $end) = $this->report->getMonthNums($timeType, $timeNum);
        
        $monthWorkSortData = $this->report->getMonthWorkSort($userRootId, $sortField, $monthNums, $begin, $end);
        // $this->view->zcj = $this->report->getMonthWorkSortSql($monthNums, $begin, $end);
        
        $amibaNames = array('' => '');
        $groupNames = array('' => '');
        $userNames = array('' => '');
        $lastMonthWorkSortData = array();
        
        foreach($monthWorkSortData as $amiba)
        {
            if(!isset($amibaNames[$amiba->amiba_name]))
            {
                $amibaNames[$amiba->amiba_name] = $amiba->amiba_name;
            }
        }
        
        if(!isset($amibaName) || empty($amibaName)) // 阿米巴为空查询全部
        {
            $lastMonthWorkSortData = $monthWorkSortData;
        }
        else
        {
            foreach($monthWorkSortData as $amiba)
            {
                if($amiba->amiba_name == $amibaName && !isset($groupNames[$amiba->group_name]))
                {
                    $groupNames[$amiba->group_name] = $amiba->group_name;
                }
            }
            
            if(!isset($groupName) || empty($groupName))// 组名为空查询阿米巴下所有组的用户
            {
                foreach($monthWorkSortData as $amiba)
                {
                    if($amiba->amiba_name == $amibaName)
                    {
                        array_push($lastMonthWorkSortData, $amiba);
                    }
                }
            }
            else
            {
                foreach($monthWorkSortData as $amiba)
                {
                    if($amiba->amiba_name == $amibaName && $amiba->group_name == $groupName && !isset($userNames[$amiba->realname]))
                    {
                        $userNames[$amiba->realname] = $amiba->realname;
                    }
                }
                
                if(!isset($userName) || empty($userName))//用户为空查询组下所有的用户
                {
                    foreach($monthWorkSortData as $amiba)
                    {
                        if($amiba->amiba_name == $amibaName && $amiba->group_name == $groupName)
                        {
                            array_push($lastMonthWorkSortData, $amiba);;
                        }
                    }
                }
                else
                {
                    foreach($monthWorkSortData as $amiba)
                    {
                        if($amiba->amiba_name == $amibaName && $amiba->group_name == $groupName && $amiba->realname == $userName)
                        {
                            array_push($lastMonthWorkSortData, $amiba);;
                        }
                    }
                }
            }
        }
        
        $this->view->monthWorkSortData = $lastMonthWorkSortData;
        $this->view->userRootId = $userRootId;
        $this->view->amibaNames = $amibaNames;
        $this->view->amibaName = $amibaName;
        $this->view->groupNames = $groupNames;
        $this->view->groupName = $groupName;
        $this->view->userNames = $userNames;
        $this->view->userName = $userName;
        $this->view->workDayCount = $this->report->getWorkDayCount($begin, $end);
        $this->view->timeType = $timeType;
        $this->view->time = $time;
        $this->view->begin = $begin;
        $this->view->end = $end;
        $this->view->sortField = $sortField;
        $this->display();
    }
}
