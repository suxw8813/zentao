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
     * worklogs of a report.
     * 
     * @param  string    $account 
     * @param  date    $beginNum
     * @param  date    $endNum 
     * @access public
     * @return void
     */
    public function timetendency($userRootId, $amibaName, $isAmibaChanged, $groupName, $account, $orgType, $timeType, $endNum, $beginNum = '')
    {
        if($timeType == '日')
        {
            if(empty($beginNum))
            {
                $month = $this->report->getMonth($endNum);
                list($month, $begin, $end) = $this->report->getMonthBeginEnd(date('Ym', strtotime($month)), true);
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
        list($amibaNames, $accountDict, $amibas) = $this->report->getAmibaGroupPerson($userRootId);
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
            $title = $this->lang->report->deptname . '(' . $deptUserCount . '人' . ')';
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
        $title .= '-' . $timeType . '-' . $this->lang->report->timeTendency;
        
        $this->view->amibas = $amibas;
        $this->view->chartData =  $this->report->getTimeTendencyData($userRootId, $amibas, $amibaName, $groupName, $account, $orgType, $timeType, $begin, $end);
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
}
