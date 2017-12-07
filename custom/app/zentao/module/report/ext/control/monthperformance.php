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
    public function monthperformance($userRootId, $amibaName, $isAmibaChanged, $groupName, $account, $orgType, $monthNum)
    {
        list($month, $begin, $end) = $this->report->getMonthBeginEnd($monthNum, false);
        
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
        $title .= '-' . $this->lang->report->monthperformance;
        
        $outputInfo = '';
        
        // $this->view->outputInfoSql = $this->report->getOutputInfoSql($amibaName, $groupName, $account, $orgType, $monthNum);
        $this->view->outputInfo = $this->report->getOutputInfo($userRootId, $amibaName, $groupName, $account, $orgType, $monthNum);
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
        $this->view->workDayCount = $this->report->getworkDayCount($begin, $end);
        $this->display();
    }
}
