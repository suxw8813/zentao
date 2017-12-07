<?php
/**feature-1509**/
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
class quantizedoutput extends control
{
    /* ============================== monthperformance start ================================== */
    public function monthperformance($userRootId, $amibaName, $isAmibaChanged, $groupName, $account, $orgType, $monthNum)
    {
        list($month, $begin, $end) = $this->quantizedoutput->getMonthBeginEnd($monthNum, false);
        
        $amibaNames = array();
        $amibas = array();
        $groupNames = array();
        $accountDict = array();
        list($amibaNames, $accountDict, $amibas) = $this->quantizedoutput->getAmibaGroupPerson($userRootId);
        if($orgType == 'group')
        {
            if($isAmibaChanged == 'true')
            {
                foreach($amibas as $amiba)
                {
                    if($amiba->amiba_name == $amibaName)
                    {
                        $groupNames = $amibas[$amiba->amiba_name]->groupDict;
                        $groupName = current($groupNames);
                        reset($groupNames);
                        break;
                    }
                }
            }
            else
            {
                foreach($amibas as $amiba)
                {
                    if($amiba->amiba_name == $amibaName)
                    {
                        $groupNames = $amibas[$amiba->amiba_name]->groupDict;
                        break;
                    }
                }
            }
        }
        
        $title = '';
        if($orgType == 'amiba')
        {
            $title = $amibaName . '(' . $amibas[$amibaName]->usercount . '人' . ')';
        }
        else if($orgType == 'group')
        {
            $title = $amibaName . '-' . $groupName . '(' . $amibas[$amibaName]->groups[$groupName]->usercount . '人' . ')';
        }
        else
        {
            $this->loadModel('user');
            $users = $this->user->getPairs('noletter');
            $title = $users[$account];
            
            $this->view->users = $users;
        }
        $title .= '-' . $this->lang->quantizedoutput->monthperformance;
        
        if($this->config->quantizedoutput->log)
        {
            $this->view->outputInfoSql = $this->quantizedoutput->getOutputInfoSql($userRootId, $amibaName, $groupName, $account, $orgType, $monthNum);
        }
        $this->view->outputInfo = $this->quantizedoutput->getOutputInfo($userRootId, $amibaName, $groupName, $account, $orgType, $monthNum);
        $this->view->userRootId = $userRootId;
        $this->view->amibas = $amibas;
        $this->view->title = $title;
        $this->view->dealTitle = $title;
        $this->view->orgType = $orgType;
        $this->view->amibaNames = $amibaNames;
        $this->view->amibaName = $amibaName;
        $this->view->groupNames = $groupNames;
        $this->view->groupName = $groupName;
        $this->view->userNames = $accountDict;
        $this->view->account = $account;
        $this->view->month = $month;
        $this->view->begin = $begin;
        $this->view->end = $end;
        $this->view->workDayCount = $this->quantizedoutput->getworkDayCount($begin, $end);
        $this->display();
    }
    /* ============================== monthperformance end ================================== */
    
    /* ============================== prjmonthperformance start ================================== */
    public function prjmonthperformance($amibaId = '', $groupId = '', $account = '', $monthNum = '', $tag = '')
    {
        list($month, $begin, $end) = $this->quantizedoutput->getMonthBeginEnd($monthNum, false);
        $amibas = $this->quantizedoutput->getPrjAmibaGroupPerson(array($monthNum));
        
        if($tag == 'skipGroupId')
        {
            $orgType = 'amibaSkipGroupAccount';
            $this->loadModel('user');
            $users = $this->user->getPairs('noletter');
            $title =  $amibas[$amibaId]->amiba_name . '(' . $users[$account] . ')';
        }
        else 
        {
            $amibaNameDict = array();
            $groupNameDict = array('' => '');
            $userNameDict = array('' => '');
            
            
            $this->loadModel('user');
            $users = $this->user->getPairs('noletter');
            $this->view->users = $users;
            
            foreach($amibas as $amiba)
            {
                $amibaNameDict[$amiba->amiba_id] = $amiba->amiba_name;
            }
            
            foreach($amibas[$amibaId]->groups as $group)
            {
                $groupNameDict[$group->group_id] = $group->group_name;
            }
            
            $orgType = 'amiba';
            if(isset($groupId) && !empty($groupId)) // 组名为空查询一级组织下所有组的用户
            {
                $orgType = 'group';
                
                foreach($amibas[$amibaId]->groups[$groupId]->users as $user)
                {
                    $userNameDict[$user->account] = $users[$user->account];
                }
                
                // die(js::alert(json_encode($amibas[$amibaId]->groups[$groupId]->users)));

                if(isset($account) && !empty($account)) //用户为空查询组下所有的用户
                {
                    $orgType = 'person';
                }
            }
            
            $title = '';
            if($orgType == 'amiba')
            {
                $title = $amibas[$amibaId]->amiba_name . '(' . $amibas[$amibaId]->usercount . '人' . ')';
            }
            else if($orgType == 'group')
            {
                $title = $amibas[$amibaId]->amiba_name . '-' . $amibas[$amibaId]->groups[$groupId]->group_name . '(' . $amibas[$amibaId]->groups[$groupId]->usercount . '人' . ')';
            }
            else
            {
                $this->loadModel('user');
                $users = $this->user->getPairs('noletter');
                $title = $users[$account];
                
                $this->view->users = $users;
            }
        }
        $title .= '-' . $this->lang->quantizedoutput->quantizedoutput;
        if($this->config->quantizedoutput->log)
        {
            $this->view->outputInfoSql = $this->quantizedoutput->getPrjOutputInfoSql($amibaId, $groupId, $account, $orgType, $monthNum);
        }
        $this->view->outputInfo = $this->quantizedoutput->getPrjOutputInfo($amibaId, $groupId, $account, $orgType, $monthNum);
        $this->view->amibas = $amibas;
        $this->view->title = $title;
        $this->view->dealTitle = $title;
        $this->view->tag = $tag;
        $this->view->amibaNameDict = $amibaNameDict;
        $this->view->amibaId = $amibaId;
        $this->view->groupNameDict = $groupNameDict;
        $this->view->groupId = $groupId;
        $this->view->userNameDict = $userNameDict;
        $this->view->account = $account;
        $this->view->month = $month;
        $this->view->begin = $begin;
        $this->view->end = $end;
        $this->view->workDayCount = $this->quantizedoutput->getworkDayCount($begin, $end);
        $this->display();
    }
    /* ============================== prjmonthperformance end ================================== */
    
    /* ============================== prdmonthperformance start ================================== */
    public function prdmonthperformance($amibaId = '', $groupId = '', $account = '', $monthNum = '', $tag = '')
    {
        list($month, $begin, $end) = $this->quantizedoutput->getMonthBeginEnd($monthNum, false);
        $amibas = $this->quantizedoutput->getPrdAmibaGroupPerson(array($monthNum));
        
        if($tag == 'skipGroupId')
        {
            $orgType = 'amibaSkipGroupAccount';
            $this->loadModel('user');
            $users = $this->user->getPairs('noletter');
            $title =  $amibas[$amibaId]->amiba_name . '(' . $users[$account] . ')';
        }
        else 
        {
            $amibaNameDict = array();
            $groupNameDict = array('' => '');
            $userNameDict = array('' => '');
            
            
            $this->loadModel('user');
            $users = $this->user->getPairs('noletter');
            $this->view->users = $users;
            
            foreach($amibas as $amiba)
            {
                $amibaNameDict[$amiba->amiba_id] = $amiba->amiba_name;
            }
            
            foreach($amibas[$amibaId]->groups as $group)
            {
                $groupNameDict[$group->group_id] = $group->group_name;
            }
            
            $orgType = 'amiba';
            if(isset($groupId) && !empty($groupId)) // 组名为空查询一级组织下所有组的用户
            {
                $orgType = 'group';
                
                foreach($amibas[$amibaId]->groups[$groupId]->users as $user)
                {
                    $userNameDict[$user->account] = $users[$user->account];
                }
                
                // die(js::alert(json_encode($amibas[$amibaId]->groups[$groupId]->users)));

                if(isset($account) && !empty($account)) //用户为空查询组下所有的用户
                {
                    $orgType = 'person';
                }
            }
            
            $title = '';
            if($orgType == 'amiba')
            {
                $title = $amibas[$amibaId]->amiba_name . '(' . $amibas[$amibaId]->usercount . '人' . ')';
            }
            else if($orgType == 'group')
            {
                $title = $amibas[$amibaId]->amiba_name . '-' . $amibas[$amibaId]->groups[$groupId]->group_name . '(' . $amibas[$amibaId]->groups[$groupId]->usercount . '人' . ')';
            }
            else
            {
                $this->loadModel('user');
                $users = $this->user->getPairs('noletter');
                $title = $users[$account];
                
                $this->view->users = $users;
            }
        }
        $title .= '-' . $this->lang->quantizedoutput->quantizedoutput;
        
        if($this->config->quantizedoutput->log)
        {
            $this->view->outputInfoSql = $this->quantizedoutput->getPrdOutputInfoSql($amibaId, $groupId, $account, $orgType, $monthNum);
        }
        $this->view->outputInfo = $this->quantizedoutput->getPrdOutputInfo($amibaId, $groupId, $account, $orgType, $monthNum);
        $this->view->amibas = $amibas;
        $this->view->title = $title;
        $this->view->dealTitle = $title;
        $this->view->orgType = $orgType;
        $this->view->amibaNameDict = $amibaNameDict;
        $this->view->amibaId = $amibaId;
        $this->view->groupNameDict = $groupNameDict;
        $this->view->groupId = $groupId;
        $this->view->userNameDict = $userNameDict;
        $this->view->tag = $tag;
        $this->view->account = $account;
        $this->view->month = $month;
        $this->view->begin = $begin;
        $this->view->end = $end;
        $this->view->workDayCount = $this->quantizedoutput->getworkDayCount($begin, $end);
        $this->display();
    }
    /* ============================== prdmonthperformance end ================================== */
    
    /* ============================== modmerge start ================================== */
    public function modmerge($dimType, $userRootId = '', $monthNum = '', $encodeMergeId = 0)
    {
        if($_POST)
        {
            $data = fixer::input('post')->get();
            
            if($this->post->file_type) $this->quantizedoutput->updateMergeDetailInfo();
            
            $fromUrl = preg_replace('/http(s?):\/\/.*(?=\/zentao\/)/', '', $data->fromUrl);
            die(js::locate($fromUrl, 'parent'));
        }
        
        $mergeId = str_replace('___', '-', $encodeMergeId);
        $this->view->mergeInfo = $this->quantizedoutput->getMergeInfo($dimType, $userRootId, $monthNum, $mergeId);
        
        $this->view->fromUrl   = $_SERVER["HTTP_REFERER"];
        
        $this->loadModel('user');
        $this->view->users = $this->user->getPairs('noletter');
        $this->display();
    }
    /* ============================== modmerge end ================================== */
    
    /* ============================== dayreport start ================================== */
    /**
     * dayreport information report.
     * 
     * @access public
     * @return void
     */
    public function dayreport($userRootId='', $amibaName = '', $groupName = '', $userName = '', $dayNum = '')
    {
        $day = empty($dayNum) ? date('Y-m-d', strtotime('-1 day')) : date('Y-m-d', strtotime($dayNum));
        
        // 获取根字典
        $userRootDict = $this->quantizedoutput->getUserRootDict();
        
        // 得到userRootId
        if(empty($userRootId)){
            // print(js::alert($userRootId));
            list($userRootId, $userRootName) = $this->quantizedoutput->getMinUserRoot($userRootDict);
        }
        
        if($this->config->quantizedoutput->log)
        {
            $this->view->zcj = $this->quantizedoutput->getDayAmibasSql($userRootId, $workdate);
        }
        list($deptUserCount, $deptAllTime, $amibas) = $this->quantizedoutput->getDayAmibas($userRootId, $day);
        
        $amibaNames = array('' => '');
        $groupNames = array('' => '');
        $userNames = array('' => '');
        $lastAmibas = array();
        
        foreach($amibas as $amiba)
        {
            $amibaNames[$amiba->amiba_name] = $amiba->amiba_name;
        }
        
        if(!isset($amibaName) || empty($amibaName)) // 一级组织为空查询全部
        {
            $lastAmibas = $amibas;
        }
        else
        {
            $lastAmibas[$amibaName] = new stdclass();
            $lastAmibas[$amibaName]->amiba_name = $amibaName;
            $lastAmibas[$amibaName]->realusercount = $amibas[$amibaName]->realusercount;
            $lastAmibas[$amibaName]->usercount = 0;
            
            foreach($amibas[$amibaName]->groups as $group)
            {
                $groupNames[$group->group_name] = $group->group_name;
            }
            
            if(!isset($groupName) || empty($groupName)) // 组名为空查询一级组织下所有组的用户
            {
                $lastAmibas[$amibaName]->groups = $amibas[$amibaName]->groups;
                foreach($amibas[$amibaName]->groups as $group)
                {
                    $lastAmibas[$amibaName]->usercount += $group->usercount;
                }
            }
            else
            {
                $lastAmibas[$amibaName]->groups[$groupName] = new stdclass();
                $lastAmibas[$amibaName]->groups[$groupName]->group_name = $groupName;
                $lastAmibas[$amibaName]->groups[$groupName]->usercount = 0;
                $lastAmibas[$amibaName]->groups[$groupName]->realusercount = $amibas[$amibaName]->groups[$groupName]->realusercount;
                
                foreach($amibas[$amibaName]->groups[$groupName]->users as $user)
                {
                    $userNames[$user->realname] = $user->realname;
                }
                
                if(!isset($userName) || empty($userName)) //用户为空查询组下所有的用户
                {
                    $lastAmibas[$amibaName]->groups[$groupName]->users = $amibas[$amibaName]->groups[$groupName]->users;
                    
                    $lastAmibas[$amibaName]->usercount = $amibas[$amibaName]->groups[$groupName]->usercount;
                    $lastAmibas[$amibaName]->groups[$groupName]->usercount = $amibas[$amibaName]->groups[$groupName]->usercount;
                }
                else
                {
                    $lastAmibas[$amibaName]->groups[$groupName]->users[$userName] = new stdclass();
                    $lastAmibas[$amibaName]->groups[$groupName]->users[$userName] = $amibas[$amibaName]->groups[$groupName]->users[$userName];
                    
                    $lastAmibas[$amibaName]->usercount = 1;
                    $lastAmibas[$amibaName]->groups[$groupName]->usercount = 1;
                }
            }
        }
        
        $this->view->userRootDict = $userRootDict;
        $this->view->userRootId = $userRootId;
        
        $this->view->deptUserCount = $deptUserCount;
        $this->view->deptAllTime = $deptAllTime;
        $this->view->amibaNames = $amibaNames;
        $this->view->amibaName = $amibaName;
        $this->view->groupNames = $groupNames;
        $this->view->groupName = $groupName;
        $this->view->userNames = $userNames;
        $this->view->userName = $userName;
        $this->view->day = $day;
        
        $this->view->amibas = $lastAmibas;
        
        $this->view->title      = $this->lang->quantizedoutput->t_dayreport;
        $this->view->position[] = $this->lang->quantizedoutput->t_dayreport;
        $this->view->submenu    = 'staff';
        $this->display();
    }
    /* ============================== dayreport end ================================== */
    
    /* ============================== dailywork start ================================== */
    public function dailywork($account)
    {
        $this->view->account     = $account;
        $this->view->url         = $this->config->worklog->itaskouter;
        
        $this->display();
    }
    /* ============================== dailywork end ================================== */
    
    /* ============================== worklogs start ================================== */
    public function worklogs($dimType = '', $amibaId = '', $groupId = '', $account = '', $dateNum = '', $timeType = '')
    {
        if($timeType == '日')
        {
            $begin = date('Y-m-d', strtotime($dateNum));
            $end = '';
        }
        else
        {
            list($month, $begin, $end) = $this->quantizedoutput->getMonthBeginEnd($dateNum, false);
            $this->view->month = $month;
        }
        
        if($this->config->quantizedoutput->log)
        {
            $this->view->zcj = $this->quantizedoutput->getWorklogsSql($dimType, $amibaId, $groupId, $account, $begin, $end);
        }
        list($total_time, $worklogs) = $this->quantizedoutput->getWorklogs($dimType, $amibaId, $groupId, $account, $begin, $end);
        
        $this->view->dimType = $dimType;
        $this->view->amibaId = $amibaId;
        $this->view->groupId = $groupId;
        $this->view->account = $account;
        $this->view->timeType = $timeType;
        
        $this->view->total_time = $total_time;
        $this->view->worklogs = $worklogs;
        $this->view->begin = $begin;
        $this->view->end = $end;
        
        $this->loadModel('user');
        $this->view->users = $this->user->getPairs('noletter');
        $this->display();
    }
    /* ============================== worklogs end ================================== */
    
    /* ============================== timetendency start ================================== */
    public function timetendency($userRootId, $amibaName, $isAmibaChanged, $groupName, $account, $orgType, $timeType, $endNum, $beginNum = '')
    {
        if($timeType == '日')
        {
            if(empty($beginNum))
            {
                $month = $this->quantizedoutput->getMonth($endNum);
                list($month, $begin, $end) = $this->quantizedoutput->getMonthBeginEnd(date('Ym', strtotime($month)), true);
            }
            else
            {
                $begin = date('Y-m-d', strtotime($beginNum));
                $end = date('Y-m-d', strtotime($endNum));
            }
        }
        else
        {
            if(empty($beginNum))
            {
                $year=date("Y", time());
                $begin = $year . "-01";
                $end = $year . "-12";
            }
            else
            {
                $begin = date('Y-m', strtotime($beginNum . '01'));
                $end = date('Y-m', strtotime($endNum . '01'));
            }
        }
        
        $amibaNames = array();
        $amibas = array();
        $groupNames = array();
        $accountDict = array();
        list($amibaNames, $accountDict, $amibas) = $this->quantizedoutput->getAmibaGroupPerson($userRootId);
        if($orgType == 'group')
        {
            if($isAmibaChanged == 'true')
            {
                foreach($amibas as $amiba)
                {
                    if($amiba->amiba_name == $amibaName)
                    {
                        $groupNames = $amibas[$amiba->amiba_name]->groupDict;
                        $groupName = current($groupNames);
                        reset($groupNames);
                        break;
                    }
                }
            }
            else
            {
                foreach($amibas as $amiba)
                {
                    if($amiba->amiba_name == $amibaName)
                    {
                        $groupNames = $amibas[$amiba->amiba_name]->groupDict;
                        break;
                    }
                }
            }
        }
        
        $title = '';
        
        if($orgType == 'dept')
        {
            $deptUserCount = 0;
            foreach($amibas as $amiba) {
                $deptUserCount += $amiba->usercount;
            }
            $title = $this->lang->quantizedoutput->deptname . '(' . $deptUserCount . '人' . ')';
        }
        else if($orgType == 'amiba')
        {
            $title = $amibaName . '(' . $amibas[$amibaName]->usercount . '人' . ')';
        }
        else if($orgType == 'group')
        {
            $title = $amibaName . '-' . $groupName . '(' . $amibas[$amibaName]->groups[$groupName]->usercount . '人' . ')';
        }
        else
        {
            $this->loadModel('user');
            $users = $this->user->getPairs('noletter');
            $title = $users[$account];
            
            $this->view->users = $users;
        }
        $title .= '-' . $timeType . '-' . $this->lang->quantizedoutput->timeTendency;
        
        $this->view->amibas = $amibas;
        
        if($this->config->quantizedoutput->log)
        {
            if($timeType == '日')
            {
                $this->view->zcj = $this->quantizedoutput->getDayTimeTendencyDataSql($userRootId, $amibaName, $groupName, $account, $orgType, $begin, $end);
            }
            else
            {
                $this->view->zcj = $this->quantizedoutput->getMonthTimeTendencyDataSql($userRootId, $amibaName, $groupName, $account, $orgType, $begin, $end);
            }
        }
        $this->view->chartData =  $this->quantizedoutput->getTimeTendencyData($userRootId, $amibas, $amibaName, $groupName, $account, $orgType, $timeType, $begin, $end);
        $this->view->title = $title;
        $this->view->orgType = $orgType;
        $this->view->timeType = $timeType;
        $this->view->userRootId = $userRootId;
        $this->view->amibaNames = $amibaNames;
        $this->view->amibaName = $amibaName;
        $this->view->groupNames = $groupNames;
        $this->view->groupName = $groupName;
        $this->view->userNames = $accountDict;
        $this->view->account = $account;
        $this->view->begin = $begin;
        $this->view->end = $end;
        $this->display();
    }
    /* ============================== timetendency end ================================== */
    
    /* ============================== sortmore start ================================== */
    public function sortmore($timeType, $sortField, $userRootId, $amibaName = '', $groupName = '', $userName = '', $timeNum = '')
    {
        list($time, $monthNums, $begin, $end) = $this->quantizedoutput->getMonthNums($timeType, $timeNum);
        
        if($this->config->quantizedoutput->log)
        {
            $this->view->zcj = $this->quantizedoutput->getMonthWorkSortSql($userRootId, $sortField,$monthNums, $begin, $end);
        }
        $monthWorkSortData = $this->quantizedoutput->getMonthWorkSort($userRootId, $sortField, $monthNums, $begin, $end);
        
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
        
        if(!isset($amibaName) || empty($amibaName)) // 一级组织为空查询全部
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
            
            if(!isset($groupName) || empty($groupName))// 组名为空查询一级组织下所有组的用户
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
        $this->view->workDayCount = $this->quantizedoutput->getWorkDayCount($begin, $end);
        $this->view->timeType = $timeType;
        $this->view->time = $time;
        $this->view->begin = $begin;
        $this->view->end = $end;
        $this->view->sortField = $sortField;
        $this->display();
    }
    /* ============================== sortmore end ================================== */
    
    /* ============================== prjsortmore start ================================== */
    public function prjsortmore($timeType, $sortField, $amibaId = '', $timeNum = '')
    {
        list($time, $monthNums, $begin, $end) = $this->quantizedoutput->getMonthNums($timeType, $timeNum);
        
        if($this->config->quantizedoutput->log)
        {
            $this->view->zcj = $this->quantizedoutput->getPrjMonthWorkSortSql($amibaId, $sortField, $monthNums, $begin, $end);
        }
        $monthWorkSortData = $this->quantizedoutput->getPrjMonthWorkSort($amibaId, $sortField, $monthNums, $begin, $end);
        $amibaNameDict = array();
        $amibas = $this->quantizedoutput->getPrjAmibaGroupPerson($monthNums);
        foreach($amibas as $amiba)
        {
            $amibaNameDict[$amiba->amiba_id] = $amiba->amiba_name;
        }
        
        $this->view->monthWorkSortData = $monthWorkSortData;
        $this->view->amibaNameDict = $amibaNameDict;
        $this->view->amibaId = $amibaId;
        $this->view->workDayCount = $this->quantizedoutput->getWorkDayCount($begin, $end);
        $this->view->timeType = $timeType;
        $this->view->time = $time;
        $this->view->begin = $begin;
        $this->view->end = $end;
        $this->view->sortField = $sortField;
        $this->display();
    }
    /* ============================== prjsortmore end ================================== */
    
    /* ============================== prdsortmore start ================================== */
    public function prdsortmore($timeType, $sortField, $amibaId = '', $timeNum = '')
    {
        list($time, $monthNums, $begin, $end) = $this->quantizedoutput->getMonthNums($timeType, $timeNum);
        
        if($this->config->quantizedoutput->log)
        {
            $this->view->zcj = $this->quantizedoutput->getPrdMonthWorkSortSql($amibaId, $sortField, $monthNums, $begin, $end);
        }
        $monthWorkSortData = $this->quantizedoutput->getPrdMonthWorkSort($amibaId, $sortField, $monthNums, $begin, $end);
        $amibaNameDict = array();
        $amibas = $this->quantizedoutput->getPrdAmibaGroupPerson($monthNums);
        foreach($amibas as $amiba)
        {
            $amibaNameDict[$amiba->amiba_id] = $amiba->amiba_name;
        }
        
        $this->view->monthWorkSortData = $monthWorkSortData;
        $this->view->amibaNameDict = $amibaNameDict;
        $this->view->amibaId = $amibaId;
        $this->view->workDayCount = $this->quantizedoutput->getWorkDayCount($begin, $end);
        $this->view->timeType = $timeType;
        $this->view->time = $time;
        $this->view->begin = $begin;
        $this->view->end = $end;
        $this->view->sortField = $sortField;
        $this->display();
    }
    /* ============================== prdsortmore end ================================== */
    
    /* ============================== sort start ================================== */
    public function sort($userRootId, $timeType, $timeNum = '')
    {
        list($time, $monthNums, $begin, $end) = $this->quantizedoutput->getMonthNums($timeType, $timeNum);
        
        // 获取根字典
        $userRootDict = $this->quantizedoutput->getUserRootDict();
        
        // 得到userRootId
        if(empty($userRootId)){
            list($userRootId, $userRootName) = $this->quantizedoutput->getMinUserRoot($userRootDict);
        }
        
        if($this->config->quantizedoutput->log)
        {
            $this->view->zcj = $this->quantizedoutput->getPersonTimeTop30Sql($userRootId, $monthNums, $begin, $end);
        }
        list($PersonTimeTop30Data, $PersonOutputEfficiencyTop30Data, $PersonOutputTop30Data) = 
            $this->quantizedoutput->getPersonTop30Flot($userRootId, $monthNums, $begin, $end);
        $this->view->PersonTimeTop30Data = $PersonTimeTop30Data;
        $this->view->PersonOutputEfficiencyTop30Data = $PersonOutputEfficiencyTop30Data;
        $this->view->PersonOutputTop30Data = $PersonOutputTop30Data;
        $this->view->PersonAvgAmibaTimeTopData = $this->quantizedoutput->getPersonAvgAmibaTimeTopFlot($userRootId, $begin, $end);
        // print(js::alert(json_encode($this->view->PersonTimeTop30Data['burnBar'])));
        
        $this->view->time = $time;
        $this->view->begin = $begin;
        $this->view->end = $end;
        
        $this->view->userRootDict = $userRootDict;
        $this->view->userRootId = $userRootId;
        $this->view->timeType = $timeType;
        $this->view->workDayCount = $this->quantizedoutput->getWorkDayCount($begin, $end);
        if($timeType == '月') {
            $this->view->title      = $this->lang->quantizedoutput->t_monthsort;
            $this->view->position[] = $this->lang->quantizedoutput->t_monthsort;
        } else {
            $this->view->title      = $this->lang->quantizedoutput->t_yearsort;
            $this->view->position[] = $this->lang->quantizedoutput->t_yearsort;
        }
        
        $this->view->submenu    = 'staff';
        $this->display();
    }
    /* ============================== sort end ================================== */
    
    /* ============================== prjsort start ================================== */
    public function prjsort($amibaId = '', $timeType = '', $timeNum = '')
    {
        list($time, $monthNums, $begin, $end) = $this->quantizedoutput->getMonthNums($timeType, $timeNum);
        
        $amibaNameDict = array();
        // 获取根字典
        $amibas = $this->quantizedoutput->getPrjAmibaGroupPerson($monthNums);
        foreach($amibas as $amiba)
        {
            $amibaNameDict[$amiba->amiba_id] = $amiba->amiba_name;
        }
        
        if(empty($amibaId))
        {
            $amibaId = current(array_keys($amibaNameDict));
        }
        if(empty($amibaId) || $amibaId == 'undefined')
        {
            $amibaId = 0;
        }
        
        if($this->config->quantizedoutput->log)
        {
            $this->view->zcj = $this->quantizedoutput->getPrjPersonTimeTop30Sql($amibaId, $monthNums, $begin, $end, 'total_output');
        }
        list($PersonTimeTop30Data, $PersonOutputEfficiencyTop30Data, $PersonOutputTop30Data) = 
            $this->quantizedoutput->getPrjPersonTop30Flot($amibaId, $monthNums, $begin, $end);
        $this->view->PersonTimeTop30Data = $PersonTimeTop30Data;
        $this->view->PersonOutputEfficiencyTop30Data = $PersonOutputEfficiencyTop30Data;
        $this->view->PersonOutputTop30Data = $PersonOutputTop30Data;
        // print(js::alert(json_encode($this->view->PersonTimeTop30Data['burnBar'])));
        
        $this->view->time = $time;
        $this->view->begin = $begin;
        $this->view->end = $end;
        
        $this->view->amibaNameDict = $amibaNameDict;
        $this->view->amibaId = $amibaId;
        $this->view->timeType = $timeType;
        $this->view->workDayCount = $this->quantizedoutput->getWorkDayCount($begin, $end);
        if($timeType == '月') {
            $this->view->title      = $this->lang->quantizedoutput->t_prjmonthsort;
            $this->view->position[] = $this->lang->quantizedoutput->t_prjmonthsort;
        } else {
            $this->view->title      = $this->lang->quantizedoutput->t_prjyearsort;
            $this->view->position[] = $this->lang->quantizedoutput->t_prjyearsort;
        }
        
        $this->view->submenu    = 'prj';
        $this->display();
    }
    /* ============================== prjsort end ================================== */
    
    /* ============================== prdsort start ================================== */
    public function prdsort($amibaId, $timeType, $timeNum = '')
    {
        list($time, $monthNums, $begin, $end) = $this->quantizedoutput->getMonthNums($timeType, $timeNum);
        
        $amibaNameDict = array();
        // 获取根字典
        $amibas = $this->quantizedoutput->getPrdAmibaGroupPerson($monthNums);
        foreach($amibas as $amiba)
        {
            $amibaNameDict[$amiba->amiba_id] = $amiba->amiba_name;
        }
        
        if(empty($amibaId))
        {
            $amibaId = current(array_keys($amibaNameDict));
        }
        if(empty($amibaId) || $amibaId == 'undefined')
        {
            $amibaId = 0;
        }
        
        if($this->config->quantizedoutput->log)
        {
            $this->view->zcj = $this->quantizedoutput->getPrdPersonTimeTop30Sql($amibaId, $monthNums, $begin, $end, 'total_output');
        }
        list($PersonTimeTop30Data, $PersonOutputEfficiencyTop30Data, $PersonOutputTop30Data) = 
            $this->quantizedoutput->getPrdPersonTop30Flot($amibaId, $monthNums, $begin, $end);
        $this->view->PersonTimeTop30Data = $PersonTimeTop30Data;
        $this->view->PersonOutputEfficiencyTop30Data = $PersonOutputEfficiencyTop30Data;
        $this->view->PersonOutputTop30Data = $PersonOutputTop30Data;
        // print(js::alert(json_encode($this->view->PersonTimeTop30Data['burnBar'])));
        
        $this->view->time = $time;
        $this->view->begin = $begin;
        $this->view->end = $end;
        
        $this->view->amibaNameDict = $amibaNameDict;
        $this->view->amibaId = $amibaId;
        $this->view->timeType = $timeType;
        $this->view->workDayCount = $this->quantizedoutput->getWorkDayCount($begin, $end);
        if($timeType == '月') {
            $this->view->title      = $this->lang->quantizedoutput->t_prdmonthsort;
            $this->view->position[] = $this->lang->quantizedoutput->t_prdmonthsort;
        } else {
            $this->view->title      = $this->lang->quantizedoutput->t_prdyearsort;
            $this->view->position[] = $this->lang->quantizedoutput->t_prdyearsort;
        }
        
        $this->view->submenu    = 'prd';
        $this->display();
    }
    /* ============================== prdsort end ================================== */
    
    /* ============================== monthreportexport start ================================== */
    public function monthreportexport($userRootId, $monthNum)
    {
        list($month, $begin, $end) = $this->quantizedoutput->getMonthBeginEnd($monthNum, false);
        
        /* Get exportData. */
        $exportData = $this->quantizedoutput->getMonthReportData($userRootId, $monthNum, $begin, $end);

        $reportLang   = $this->lang->quantizedoutput;
        $monthreportexportConfig = $this->config->monthreportexport;

        /* Create field lists. */
        $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $monthreportexportConfig->list->exportFields);
        foreach($fields as $key => $fieldName)
        {
            $fieldName = trim($fieldName);
            $fields[$fieldName] = isset($reportLang->$fieldName) ? $reportLang->$fieldName : $fieldName;
            unset($fields[$key]);
        }
        $this->post->set('fields', $fields);
        $this->post->set('rows', $exportData);
        $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        
        // $this->view->zcj = $fields;
        $this->view->fileName = '组织-月统计-' . date('Y年m月-', strtotime($month)) . date('YmdHis');
        $this->view->charset = 'gbk';
        $this->view->allExportFields = $monthreportexportConfig->list->exportFields;
        $this->view->customExport    = false;
        $this->display();
    }
    /* ============================== monthreportexport end ================================== */
    
    /* ============================== prjmonthreportexport start ================================== */
    public function prjmonthreportexport($monthNum)
    {
        list($month, $begin, $end) = $this->quantizedoutput->getMonthBeginEnd($monthNum, false);
        
        /* Get exportData. */
        $exportData = $this->quantizedoutput->getPrjMonthReportData($monthNum, $begin, $end);

        $reportLang   = $this->lang->quantizedoutput;
        $monthreportexportConfig = $this->config->prjmonthreportexport;

        /* Create field lists. */
        $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $monthreportexportConfig->list->exportFields);
        foreach($fields as $key => $fieldName)
        {
            $fieldName = trim($fieldName);
            $fields[$fieldName] = isset($reportLang->$fieldName) ? $reportLang->$fieldName : $fieldName;
            unset($fields[$key]);
        }
        $this->post->set('fields', $fields);
        $this->post->set('rows', $exportData);
        $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        
        // $this->view->zcj = $fields;
        $this->view->fileName = '项目-月统计-' . date('Y年m月-', strtotime($month)) . date('YmdHis');
        $this->view->charset = 'gbk';
        $this->view->allExportFields = $monthreportexportConfig->list->exportFields;
        $this->view->customExport    = false;
        $this->display();
    }
    /* ============================== prjmonthreportexport end ================================== */
    
    /* ============================== prdmonthreportexport start ================================== */
    public function prdmonthreportexport($monthNum)
    {
        list($month, $begin, $end) = $this->quantizedoutput->getMonthBeginEnd($monthNum, false);
        
        /* Get exportData. */
        $exportData = $this->quantizedoutput->getPrdMonthReportData($monthNum, $begin, $end);

        $reportLang   = $this->lang->quantizedoutput;
        $monthreportexportConfig = $this->config->prdmonthreportexport;

        /* Create field lists. */
        $fields = $this->post->exportFields ? $this->post->exportFields : explode(',', $monthreportexportConfig->list->exportFields);
        foreach($fields as $key => $fieldName)
        {
            $fieldName = trim($fieldName);
            $fields[$fieldName] = isset($reportLang->$fieldName) ? $reportLang->$fieldName : $fieldName;
            unset($fields[$key]);
        }
        $this->post->set('fields', $fields);
        $this->post->set('rows', $exportData);
        $this->fetch('file', 'export2' . $this->post->fileType, $_POST);
        
        // $this->view->zcj = $fields;
        $this->view->fileName = '产品-月统计-' . date('Y年m月-', strtotime($month)) . date('YmdHis');
        $this->view->charset = 'gbk';
        $this->view->allExportFields = $monthreportexportConfig->list->exportFields;
        $this->view->customExport    = false;
        $this->display();
    }
    /* ============================== prdmonthreportexport end ================================== */
    
    /* ============================== monthreport start ================================== */
    public function monthreport($userRootId='', $amibaName = '', $groupName = '', $account = '', $monthNum = '')
    {
        // 重新计算
        if(!empty($_POST))
        {
            // 从界面上获取开始和结束时间
            $data = fixer::input('post')->get();
            $performanceServiceUrl = $this->config->quantizedoutput->performanceServiceUrls[$this->config->worklog->depcode];
            $performanceServiceUrl = str_replace('#{startNum}', $data->startNum, $performanceServiceUrl);
            $performanceServiceUrl = str_replace('#{endNum}', $data->endNum, $performanceServiceUrl);
            
            // 访问有效输出统计服务
            $this->app->loadClass('bocorestrequest', true);
            $request = new bocorestrequest($performanceServiceUrl, 'GET');  
            $request->execute();  
            // echo '<pre>' . $request->getResponseBody() . '</pre>'; 
            
            // 统计服务返回8秒后，提示消息
            sleep(8);
            
            // 界面上显示提示信息
            $msg = date("Y年m月d日", strtotime($data->startNum)) . "-" . date("Y年m月d日", strtotime($data->endNum)) 
                . "的有效输出重新计算完成。 ";
            print(js::alert($msg));

            // 刷新当前页面            
            // die(js::locate($this->createLink('quantizedoutput', 'monthreport', "userRootId=$data->fresh_userRootId&amibaname=$data->fresh_amibaName&groupname=$data->fresh_groupName&account=$data->fresh_account&monthNum=$data->fresh_monthNum"), 'parent'));
            $fromUrl = preg_replace('/http(s?):\/\/.*(?=\/zentao\/)/', '', $_SERVER["HTTP_REFERER"]);
            die(js::locate($fromUrl, 'parent'));
        }
        
        // 月统计及有效输出
        list($month, $begin, $end) = $this->quantizedoutput->getMonthBeginEnd($monthNum, false);
        
        if(empty($monthNum))
        {
            $monthNum = date('Ym', strtotime($month));
        }
        
        // 获取根字典
        $userRootDict = $this->quantizedoutput->getUserRootDict();
        // 得到userRootId
        if(empty($userRootId)){
            // print(js::alert($userRootId));
            list($userRootId, $userRootName) = $this->quantizedoutput->getMinUserRoot($userRootDict);
        }
        list($deptUserCount, $deptAllTime, $deptAvgOutput, $amibas) = $this->quantizedoutput->getMonthAmibas($userRootId, $monthNum, $begin, $end);
        
        if($this->config->quantizedoutput->log)
        {
            // 在界面上输出sql
            $this->view->zcj = $this->quantizedoutput->getMonthReportSql($userRootId, $monthNum, $begin, $end);
        }
        
        $amibaNames = array('' => '');
        $groupNames = array('' => '');
        $userNames = array('' => '');
        $lastAmibas = array();
        
        foreach($amibas as $amiba)
        {
            $amibaNames[$amiba->amiba_name] = $amiba->amiba_name;
        }
        
        if(!isset($amibaName) || empty($amibaName)) // 一级组织为空查询全部
        {
            $lastAmibas = $amibas;
        }
        else
        {
            $lastAmibas[$amibaName] = new stdclass();
            $lastAmibas[$amibaName]->amiba_name = $amibaName;
            $lastAmibas[$amibaName]->realusercount = $amibas[$amibaName]->realusercount;
            $lastAmibas[$amibaName]->total_time = $amibas[$amibaName]->total_time;
            $lastAmibas[$amibaName]->total_output = $amibas[$amibaName]->total_output;
            $lastAmibas[$amibaName]->mod_merge_count = $amibas[$amibaName]->mod_merge_count;
            $lastAmibas[$amibaName]->usercount = 0;
            
            foreach($amibas[$amibaName]->groups as $group)
            {
                $groupNames[$group->group_name] = $group->group_name;
            }
            
            if(!isset($groupName) || empty($groupName)) // 组名为空查询一级组织下所有组的用户
            {
                $lastAmibas[$amibaName]->groups = $amibas[$amibaName]->groups;
                foreach($amibas[$amibaName]->groups as $group)
                {
                    $lastAmibas[$amibaName]->usercount += $group->usercount;
                }
            }
            else
            {
                $lastAmibas[$amibaName]->groups[$groupName] = new stdclass();
                $lastAmibas[$amibaName]->groups[$groupName]->group_name = $groupName;
                $lastAmibas[$amibaName]->groups[$groupName]->usercount = 0;
                $lastAmibas[$amibaName]->groups[$groupName]->realusercount = $amibas[$amibaName]->groups[$groupName]->realusercount;
                $lastAmibas[$amibaName]->groups[$groupName]->total_time = $amibas[$amibaName]->groups[$groupName]->total_time;
                $lastAmibas[$amibaName]->groups[$groupName]->total_output = $amibas[$amibaName]->groups[$groupName]->total_output;
                $lastAmibas[$amibaName]->groups[$groupName]->mod_merge_count = $amibas[$amibaName]->groups[$groupName]->mod_merge_count;
                
                foreach($amibas[$amibaName]->groups[$groupName]->users as $user)
                {
                    $userNames[$user->account] = $user->realname;
                }
                
                if(!isset($account) || empty($account)) //用户为空查询组下所有的用户
                {
                    $lastAmibas[$amibaName]->groups[$groupName]->users = $amibas[$amibaName]->groups[$groupName]->users;
                    
                    $lastAmibas[$amibaName]->usercount = $amibas[$amibaName]->groups[$groupName]->usercount;
                    $lastAmibas[$amibaName]->groups[$groupName]->usercount = $amibas[$amibaName]->groups[$groupName]->usercount;
                }
                else
                {
                    $lastAmibas[$amibaName]->groups[$groupName]->users[$account] = $amibas[$amibaName]->groups[$groupName]->users[$account];
                    
                    $lastAmibas[$amibaName]->usercount = 1;
                    $lastAmibas[$amibaName]->groups[$groupName]->usercount = 1;
                }
            }
        }
        $this->view->userRootDict = $userRootDict;
        $this->view->userRootId = $userRootId;
        
        $this->view->deptUserCount = $deptUserCount;
        $this->view->deptAvgOutput = $deptAvgOutput;
        $this->view->deptAllTime = $deptAllTime;
        $this->view->amibaNames = $amibaNames;
        $this->view->amibaName = $amibaName;
        $this->view->groupNames = $groupNames;
        $this->view->groupName = $groupName;
        $this->view->userNames = $userNames;
        $this->view->account = $account;
        $this->view->month = $month;
        $this->view->begin = $begin;
        $this->view->end = $end;
        $this->view->workDayCount = $this->quantizedoutput->getworkDayCount($begin, $end);
        
        $this->view->amibas = $lastAmibas;
        
        $this->view->title      = $this->lang->quantizedoutput->t_monthreport;
        $this->view->position[] = $this->lang->quantizedoutput->t_monthreport;
        $this->view->submenu    = 'staff';
        $this->display();
    }
    
    /**
     * 重新计算有效输出
     * 
     */
    public function recalperformance(){
        $this->app->loadClass('restrequest', true);
        $request = new restrequest('https://182.18.57.7:6443/api/v4/projects/17/merge_requests?private_token=znM7bN5W6RSwuKmmei1H', 'GET');  
        $request->execute();  
        die(js::alert($request->getResponseBody()));
    }
    /* ============================== monthreport end ================================== */
    
    /* ============================== prjmonthreport start ================================== */
    public function prjmonthreport($amibaId = '', $groupId = '', $account = '', $monthNum = '')
    {
        // 重新计算
        if(!empty($_POST))
        {
            // 从界面上获取开始和结束时间
            $data = fixer::input('post')->get();
            $performanceServiceUrl = $this->config->quantizedoutput->performanceServiceUrls[$this->config->worklog->depcode];
            $performanceServiceUrl = str_replace('#{startNum}', $data->startNum, $performanceServiceUrl);
            $performanceServiceUrl = str_replace('#{endNum}', $data->endNum, $performanceServiceUrl);
            
            // 访问有效输出统计服务
            $this->app->loadClass('bocorestrequest', true);
            $request = new bocorestrequest($performanceServiceUrl, 'GET');  
            $request->execute();  
            // echo '<pre>' . $request->getResponseBody() . '</pre>'; 
            
            // 统计服务返回8秒后，提示消息
            sleep(8);
            
            // 界面上显示提示信息
            $msg = date("Y年m月d日", strtotime($data->startNum)) . "-" . date("Y年m月d日", strtotime($data->endNum)) 
                . "的有效输出重新计算完成。 ";
            print(js::alert($msg));

            // 刷新当前页面            
            // die(js::locate($this->createLink('quantizedoutput', 'prjmonthreport', "amibaId=$data->fresh_amibaId&groupId=$data->fresh_groupId&account=$data->fresh_account&monthNum=$data->fresh_monthNum"), 'parent'));
            $fromUrl = preg_replace('/http(s?):\/\/.*(?=\/zentao\/)/', '', $_SERVER["HTTP_REFERER"]);
            die(js::locate($fromUrl, 'parent'));
        }
        
        // 月统计及有效输出
        list($month, $begin, $end) = $this->quantizedoutput->getMonthBeginEnd($monthNum, false);
        
        if(empty($monthNum))
        {
            $monthNum = date('Ym', strtotime($month));
        }
        
        if($this->config->quantizedoutput->log)
        {
            $this->view->zcj = $this->quantizedoutput->getPrjMonthReportSql($monthNum, $begin, $end);
        }
        list($deptUserCount, $deptAllTime, $deptAvgOutput, $amibas) = $this->quantizedoutput->getPrjMonthAmibas($monthNum, $begin, $end);
        
        $amibaNameDict = array('' => '');
        $groupNameDict = array('' => '');
        $userNameDict = array('' => '');
        $lastAmibas = array();
        
        $this->loadModel('user');
        $users = $this->user->getPairs('noletter');
        $this->view->users = $users;
        
        foreach($amibas as $amiba)
        {
            $amibaNameDict[$amiba->amiba_id] = $amiba->amiba_name;
        }
        
        if(!isset($amibaId) || empty($amibaId) || $amibaId == 0) // 一级组织为空查询全部
        {
            $lastAmibas = $amibas;
        }
        else
        {
            $lastAmibas[$amibaId] = new stdclass();
            $lastAmibas[$amibaId]->amiba_id = $amibas[$amibaId]->amiba_id;
            $lastAmibas[$amibaId]->amiba_name = $amibas[$amibaId]->amiba_name;
            $lastAmibas[$amibaId]->realusercount = $amibas[$amibaId]->realusercount;
            $lastAmibas[$amibaId]->total_time = $amibas[$amibaId]->total_time;
            $lastAmibas[$amibaId]->total_output = $amibas[$amibaId]->total_output;
            $lastAmibas[$amibaId]->mod_merge_count = $amibas[$amibaId]->mod_merge_count;
            $lastAmibas[$amibaId]->usercount = 0;
            
            foreach($amibas[$amibaId]->groups as $group)
            {
                $groupNameDict[$group->group_id] = $group->group_name;
            }
            
            if(!isset($groupId) || empty($groupId)) // 组名为空查询一级组织下所有组的用户
            {
                $lastAmibas[$amibaId]->groups = $amibas[$amibaId]->groups;
                foreach($amibas[$amibaId]->groups as $group)
                {
                    $lastAmibas[$amibaId]->usercount += $group->usercount;
                }
            }
            else
            {
                // die(js::alert(json_encode($amibas)));
                
                $lastAmibas[$amibaId]->groups[$groupId] = new stdclass();
                $lastAmibas[$amibaId]->groups[$groupId]->group_id = $amibas[$amibaId]->groups[$groupId]->group_id;
                $lastAmibas[$amibaId]->groups[$groupId]->group_name = $amibas[$amibaId]->groups[$groupId]->group_name;
                $lastAmibas[$amibaId]->groups[$groupId]->usercount = 0;
                $lastAmibas[$amibaId]->groups[$groupId]->realusercount = $amibas[$amibaId]->groups[$groupId]->realusercount;
                $lastAmibas[$amibaId]->groups[$groupId]->total_time = $amibas[$amibaId]->groups[$groupId]->total_time;
                $lastAmibas[$amibaId]->groups[$groupId]->total_output = $amibas[$amibaId]->groups[$groupId]->total_output;
                $lastAmibas[$amibaId]->groups[$groupId]->mod_merge_count = $amibas[$amibaId]->groups[$groupId]->mod_merge_count;
                
                foreach($amibas[$amibaId]->groups[$groupId]->users as $user)
                {
                    $userNameDict[$user->account] = $users[$user->account];
                }
                
                if(!isset($account) || empty($account)) //用户为空查询组下所有的用户
                {
                    $lastAmibas[$amibaId]->groups[$groupId]->users = $amibas[$amibaId]->groups[$groupId]->users;
                    
                    $lastAmibas[$amibaId]->usercount = $amibas[$amibaId]->groups[$groupId]->usercount;
                    $lastAmibas[$amibaId]->groups[$groupId]->usercount = $amibas[$amibaId]->groups[$groupId]->usercount;
                }
                else
                {
                    $lastAmibas[$amibaId]->groups[$groupId]->users[$account] = $amibas[$amibaId]->groups[$groupId]->users[$account];
                    
                    $lastAmibas[$amibaId]->usercount = 1;
                    $lastAmibas[$amibaId]->groups[$groupId]->usercount = 1;
                }
            }
        }
        $this->view->userRootDict = $userRootDict;
        
        $this->view->deptUserCount = $deptUserCount;
        $this->view->deptAvgOutput = $deptAvgOutput;
        $this->view->deptAllTime = $deptAllTime;
        $this->view->amibaNameDict = $amibaNameDict;
        $this->view->amibaId = $amibaId;
        $this->view->groupNameDict = $groupNameDict;
        $this->view->groupId = $groupId;
        $this->view->userNameDict = $userNameDict;
        $this->view->account = $account;
        $this->view->month = $month;
        $this->view->begin = $begin;
        $this->view->end = $end;
        $this->view->workDayCount = $this->quantizedoutput->getworkDayCount($begin, $end);
        
        $this->view->amibas = $lastAmibas;
        
        $this->view->title      = $this->lang->quantizedoutput->t_prjmonthreport;
        $this->view->position[] = $this->lang->quantizedoutput->t_prjmonthreport;
        $this->view->submenu    = 'prj';
        $this->display();
    }
    /* ============================== prjmonthreport end ================================== */
    
    /* ============================== prdmonthreport start ================================== */
    public function prdmonthreport($amibaId = '', $groupId = '', $account = '', $monthNum = '')
    {
        // 重新计算
        if(!empty($_POST))
        {
            // 从界面上获取开始和结束时间
            $data = fixer::input('post')->get();
            $performanceServiceUrl = $this->config->quantizedoutput->performanceServiceUrls[$this->config->worklog->depcode];
            $performanceServiceUrl = str_replace('#{startNum}', $data->startNum, $performanceServiceUrl);
            $performanceServiceUrl = str_replace('#{endNum}', $data->endNum, $performanceServiceUrl);
            
            // 访问有效输出统计服务
            $this->app->loadClass('bocorestrequest', true);
            $request = new bocorestrequest($performanceServiceUrl, 'GET');  
            $request->execute();  
            // echo '<pre>' . $request->getResponseBody() . '</pre>'; 
            
            // 统计服务返回8秒后，提示消息
            sleep(8);
            
            // 界面上显示提示信息
            $msg = date("Y年m月d日", strtotime($data->startNum)) . "-" . date("Y年m月d日", strtotime($data->endNum)) 
                . "的有效输出重新计算完成。 ";
            print(js::alert($msg));

            // 刷新当前页面            
            // die(js::locate($this->createLink('quantizedoutput', 'prdmonthreport', "amibaId=$data->fresh_amibaId&groupId=$data->fresh_groupId&account=$data->fresh_account&monthNum=$data->fresh_monthNum"), 'parent'));
            $fromUrl = preg_replace('/http(s?):\/\/.*(?=\/zentao\/)/', '', $_SERVER["HTTP_REFERER"]);
            die(js::locate($fromUrl, 'parent'));
        }
        
        // 月统计及有效输出
        list($month, $begin, $end) = $this->quantizedoutput->getMonthBeginEnd($monthNum, false);
        
        if(empty($monthNum))
        {
            $monthNum = date('Ym', strtotime($month));
        }
        
        if($this->config->quantizedoutput->log)
        {
            $this->view->zcj = $this->quantizedoutput->getPrdMonthReportSql($monthNum, $begin, $end);
        }
        list($deptUserCount, $deptAllTime, $deptAvgOutput, $amibas) = $this->quantizedoutput->getPrdMonthAmibas($monthNum, $begin, $end);
        
        $amibaNameDict = array('' => '');
        $groupNameDict = array('' => '');
        $userNameDict = array('' => '');
        $lastAmibas = array();
        
        $this->loadModel('user');
        $users = $this->user->getPairs('noletter');
        $this->view->users = $users;
        
        foreach($amibas as $amiba)
        {
            $amibaNameDict[$amiba->amiba_id] = $amiba->amiba_name;
        }
        
        if(!isset($amibaId) || empty($amibaId) || $amibaId == 0) // 一级组织为空查询全部
        {
            $lastAmibas = $amibas;
        }
        else
        {
            $lastAmibas[$amibaId] = new stdclass();
            $lastAmibas[$amibaId]->amiba_id = $amibas[$amibaId]->amiba_id;
            $lastAmibas[$amibaId]->amiba_name = $amibas[$amibaId]->amiba_name;
            $lastAmibas[$amibaId]->realusercount = $amibas[$amibaId]->realusercount;
            $lastAmibas[$amibaId]->total_time = $amibas[$amibaId]->total_time;
            $lastAmibas[$amibaId]->total_output = $amibas[$amibaId]->total_output;
            $lastAmibas[$amibaId]->mod_merge_count = $amibas[$amibaId]->mod_merge_count;
            $lastAmibas[$amibaId]->usercount = 0;
            
            foreach($amibas[$amibaId]->groups as $group)
            {
                $groupNameDict[$group->group_id] = $group->group_name;
            }
            
            if(!isset($groupId) || empty($groupId)) // 组名为空查询一级组织下所有组的用户
            {
                $lastAmibas[$amibaId]->groups = $amibas[$amibaId]->groups;
                foreach($amibas[$amibaId]->groups as $group)
                {
                    $lastAmibas[$amibaId]->usercount += $group->usercount;
                }
            }
            else
            {
                // die(js::alert(json_encode($amibas)));
                
                $lastAmibas[$amibaId]->groups[$groupId] = new stdclass();
                $lastAmibas[$amibaId]->groups[$groupId]->group_id = $amibas[$amibaId]->groups[$groupId]->group_id;
                $lastAmibas[$amibaId]->groups[$groupId]->group_name = $amibas[$amibaId]->groups[$groupId]->group_name;
                $lastAmibas[$amibaId]->groups[$groupId]->usercount = 0;
                $lastAmibas[$amibaId]->groups[$groupId]->realusercount = $amibas[$amibaId]->groups[$groupId]->realusercount;
                $lastAmibas[$amibaId]->groups[$groupId]->total_time = $amibas[$amibaId]->groups[$groupId]->total_time;
                $lastAmibas[$amibaId]->groups[$groupId]->total_output = $amibas[$amibaId]->groups[$groupId]->total_output;
                $lastAmibas[$amibaId]->groups[$groupId]->mod_merge_count = $amibas[$amibaId]->groups[$groupId]->mod_merge_count;
                
                foreach($amibas[$amibaId]->groups[$groupId]->users as $user)
                {
                    $userNameDict[$user->account] = $users[$user->account];
                }
                
                if(!isset($account) || empty($account)) //用户为空查询组下所有的用户
                {
                    $lastAmibas[$amibaId]->groups[$groupId]->users = $amibas[$amibaId]->groups[$groupId]->users;
                    
                    $lastAmibas[$amibaId]->usercount = $amibas[$amibaId]->groups[$groupId]->usercount;
                    $lastAmibas[$amibaId]->groups[$groupId]->usercount = $amibas[$amibaId]->groups[$groupId]->usercount;
                }
                else
                {
                    $lastAmibas[$amibaId]->groups[$groupId]->users[$account] = $amibas[$amibaId]->groups[$groupId]->users[$account];
                    
                    $lastAmibas[$amibaId]->usercount = 1;
                    $lastAmibas[$amibaId]->groups[$groupId]->usercount = 1;
                }
            }
        }
        $this->view->userRootDict = $userRootDict;
        
        $this->view->deptUserCount = $deptUserCount;
        $this->view->deptAvgOutput = $deptAvgOutput;
        $this->view->deptAllTime = $deptAllTime;
        $this->view->amibaNameDict = $amibaNameDict;
        $this->view->amibaId = $amibaId;
        $this->view->groupNameDict = $groupNameDict;
        $this->view->groupId = $groupId;
        $this->view->userNameDict = $userNameDict;
        $this->view->account = $account;
        $this->view->month = $month;
        $this->view->begin = $begin;
        $this->view->end = $end;
        $this->view->workDayCount = $this->quantizedoutput->getworkDayCount($begin, $end);
        
        $this->view->amibas = $lastAmibas;
        
        $this->view->title      = $this->lang->quantizedoutput->t_prdmonthreport;
        $this->view->position[] = $this->lang->quantizedoutput->t_prdmonthreport;
        $this->view->submenu    = 'prd';
        $this->display();
    }
    /* ============================== prdmonthreport end ================================== */
    
    /* ============================== taskperformancescoredetail start ================================== */
    public function taskperformancescoredetail($taskId)
    {
        $this->view->tasks = $this->quantizedoutput->getTaskScoreDetail($taskId);
        $this->view->title = '任务有效输出';
        
        $this->loadModel('user');
        $this->view->users = $this->user->getPairs('noletter');
        $this->display();
    }
    /* ============================== taskperformancescoredetail end ================================== */
    
    /* ============================== monthperformancescoredetail start ================================== */
    public function monthperformancescoredetail($scoreType, $userRootId, $amibaName, $isAmibaChanged, $groupName, $account, $orgType, $monthNum)
    {
        list($month, $begin, $end) = $this->quantizedoutput->getMonthBeginEnd($monthNum, false);
        
        $amibaNames = array();
        $amibas = array();
        $groupNames = array();
        $accountDict = array();
        list($amibaNames, $accountDict, $amibas) = $this->quantizedoutput->getAmibaGroupPerson($userRootId);
        if($orgType == 'group')
        {
            if($isAmibaChanged == 'true')
            {
                foreach($amibas as $amiba)
                {
                    if($amiba->amiba_name == $amibaName)
                    {
                        $groupNames = $amibas[$amiba->amiba_name]->groupDict;
                        $groupName = current($groupNames);
                        reset($groupNames);
                        break;
                    }
                }
            }
            else
            {
                foreach($amibas as $amiba)
                {
                    if($amiba->amiba_name == $amibaName)
                    {
                        $groupNames = $amibas[$amiba->amiba_name]->groupDict;
                        break;
                    }
                }
            }
        }
        
        $this->loadModel('user');
        $users = $this->user->getPairs('noletter');
        
        $title = '';
        if($orgType == 'amiba')
        {
            $title = $amibaName . '(' . $amibas[$amibaName]->usercount . '人' . ')';
        }
        else if($orgType == 'group')
        {
            $title = $amibaName . '-' . $groupName . '(' . $amibas[$amibaName]->groups[$groupName]->usercount . '人' . ')';
        }
        else
        {
            $title = $users[$account];
        }
        $title .= '-' . $this->lang->quantizedoutput->monthperformance . '-' . $this->lang->quantizedoutput->scoreTypeNames[$scoreType] . '-' . $this->lang->quantizedoutput->detail;
        
        
        if($this->config->quantizedoutput->log)
        {
            $this->view->zcj = $this->quantizedoutput->getScoreDetailSql($scoreType, $userRootId, $amibaName, $groupName, $account, $orgType, $monthNum, $begin, $end);
        }
        $this->view->tasks = $this->quantizedoutput->getScoreDetail($scoreType, $userRootId, $amibaName, $groupName, $account, $orgType, $monthNum, $begin, $end);
        $this->view->amibas = $amibas;
        $this->view->title = $title;
        $this->view->orgType = $orgType;
        $this->view->monthNum = $monthNum;
        $this->view->scoreType = $scoreType;
        $this->view->userRootId = $userRootId;
        $this->view->amibaNames = $amibaNames;
        $this->view->amibaName = $amibaName;
        $this->view->groupNames = $groupNames;
        $this->view->groupName = $groupName;
        $this->view->userNames = $accountDict;
        $this->view->account = $account;
        $this->view->month = $month;
        $this->view->users = $users;
        $this->display();
    }
    /* ============================== monthperformancescoredetail end ================================== */
    
    /* ============================== prjmonthperformancescoredetail start ================================== */
    public function prjmonthperformancescoredetail($scoreType, $amibaId, $groupId = '', $account = '', $monthNum = '')
    {
        list($month, $begin, $end) = $this->quantizedoutput->getMonthBeginEnd($monthNum, false);
        
        $amibaNameDict = array();
        $groupNameDict = array('' => '');
        $userNameDict = array('' => '');
        
        $amibas = $this->quantizedoutput->getPrjAmibaGroupPerson(array($monthNum));
        
        $this->loadModel('user');
        $users = $this->user->getPairs('noletter');
        $this->view->users = $users;
        
        foreach($amibas as $amiba)
        {
            $amibaNameDict[$amiba->amiba_id] = $amiba->amiba_name;
        }
        
        foreach($amibas[$amibaId]->groups as $group)
        {
            $groupNameDict[$group->group_id] = $group->group_name;
        }
        
        $orgType = 'amiba';
        if(isset($groupId) && !empty($groupId)) // 组名为空查询一级组织下所有组的用户
        {
            $orgType = 'group';
            
            foreach($amibas[$amibaId]->groups[$groupId]->users as $user)
            {
                $userNameDict[$user->account] = $users[$user->account];
            }
            
            // die(js::alert(json_encode($amibas[$amibaId]->groups[$groupId]->users)));

            if(isset($account) && !empty($account)) //用户为空查询组下所有的用户
            {
                $orgType = 'person';
            }
        }
        
        $title = '';
        if($orgType == 'amiba')
        {
            $title = $amibas[$amibaId]->amiba_name . '(' . $amibas[$amibaId]->usercount . '人' . ')';
        }
        else if($orgType == 'group')
        {
            $title = $amibas[$amibaId]->amiba_name . '-' . $groups[$groupId]->group_name . '(' 
                . $amibas[$amibaId]->groups[$groupId]->usercount . '人' . ')';
        }
        else
        {
            $title = $users[$account];
        }
        $title .= '-' . $this->lang->quantizedoutput->monthperformance . '-' 
            . $this->lang->quantizedoutput->scoreTypeNames[$scoreType] . '-' . $this->lang->quantizedoutput->detail;
        
        if($this->config->quantizedoutput->log)
        {
            $this->view->zcj = $this->quantizedoutput->getPrjScoreDetailSql($scoreType, $amibaId, 
                $groupId, $account, $orgType, $monthNum, $begin, $end);
        }
        $this->view->tasks = $this->quantizedoutput->getPrjScoreDetail($scoreType, $amibaId, 
            $groupId, $account, $orgType, $monthNum, $begin, $end);
        $this->view->amibas = $amibas;
        $this->view->title = $title;
        $this->view->monthNum = $monthNum;
        $this->view->scoreType = $scoreType;
        
        $this->view->amibaNameDict = $amibaNameDict;
        $this->view->groupNameDict = $groupNameDict;
        $this->view->userNameDict = $userNameDict;
        $this->view->amibaId = $amibaId;
        $this->view->groupId = $groupId;
        $this->view->account = $account;
        
        $this->view->month = $month;
        $this->view->users = $users;
        $this->display();
    }
    /* ============================== prjmonthperformancescoredetail end ================================== */
    
    /* ============================== prdmonthperformancescoredetail start ================================== */
    public function prdmonthperformancescoredetail($scoreType, $amibaId, $groupId = '', $account = '', $monthNum = '')
    {
        list($month, $begin, $end) = $this->quantizedoutput->getMonthBeginEnd($monthNum, false);
        
        $amibaNameDict = array();
        $groupNameDict = array('' => '');
        $userNameDict = array('' => '');
        
        $amibas = $this->quantizedoutput->getPrdAmibaGroupPerson(array($monthNum));
        
        $this->loadModel('user');
        $users = $this->user->getPairs('noletter');
        $this->view->users = $users;
        
        foreach($amibas as $amiba)
        {
            $amibaNameDict[$amiba->amiba_id] = $amiba->amiba_name;
        }
        
        foreach($amibas[$amibaId]->groups as $group)
        {
            $groupNameDict[$group->group_id] = $group->group_name;
        }
        
        $orgType = 'amiba';
        if(isset($groupId) && !empty($groupId)) // 组名为空查询一级组织下所有组的用户
        {
            $orgType = 'group';
            
            foreach($amibas[$amibaId]->groups[$groupId]->users as $user)
            {
                $userNameDict[$user->account] = $users[$user->account];
            }
            
            // die(js::alert(json_encode($amibas[$amibaId]->groups[$groupId]->users)));

            if(isset($account) && !empty($account)) //用户为空查询组下所有的用户
            {
                $orgType = 'person';
            }
        }
        
        $title = '';
        if($orgType == 'amiba')
        {
            $title = $amibas[$amibaId]->amiba_name . '(' . $amibas[$amibaId]->usercount . '人' . ')';
        }
        else if($orgType == 'group')
        {
            $title = $amibas[$amibaId]->amiba_name . '-' . $groups[$groupId]->group_name . '(' 
                . $amibas[$amibaId]->groups[$groupId]->usercount . '人' . ')';
        }
        else
        {
            $title = $users[$account];
        }
        $title .= '-' . $this->lang->quantizedoutput->monthperformance . '-' 
            . $this->lang->quantizedoutput->scoreTypeNames[$scoreType] . '-' . $this->lang->quantizedoutput->detail;
        
        if($this->config->quantizedoutput->log)
        {
            $this->view->zcj = $this->quantizedoutput->getPrdScoreDetailSql($scoreType, $amibaId, 
                $groupId, $account, $orgType, $monthNum, $begin, $end);
        }
        $this->view->tasks = $this->quantizedoutput->getPrdScoreDetail($scoreType, $amibaId, 
            $groupId, $account, $orgType, $monthNum, $begin, $end);
        $this->view->amibas = $amibas;
        $this->view->title = $title;
        $this->view->monthNum = $monthNum;
        $this->view->scoreType = $scoreType;
        
        $this->view->amibaNameDict = $amibaNameDict;
        $this->view->groupNameDict = $groupNameDict;
        $this->view->userNameDict = $userNameDict;
        $this->view->amibaId = $amibaId;
        $this->view->groupId = $groupId;
        $this->view->account = $account;
        
        $this->view->month = $month;
        $this->view->users = $users;
        $this->display();
    }
    /* ============================== prdmonthperformancescoredetail end ================================== */
    
}
