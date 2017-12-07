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
    public function monthperformancescoredetail($scoreType, $userRootId, $amibaName, $isAmibaChanged, $groupName, $account, $orgType, $monthNum)
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
        $title .= '-' . $this->lang->report->monthperformance . '-' . $this->lang->report->scoreTypeNames[$scoreType] . '-' . $this->lang->report->detail;
        
        
        // $this->view->zcj = $this->report->getScoreDetailSql($scoreType, $userRootId, $amibaName, $groupName, $account, $orgType, $monthNum, $begin, $end);
        $this->view->tasks = $this->report->getScoreDetail($scoreType, $userRootId, $amibaName, $groupName, $account, $orgType, $monthNum, $begin, $end);
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
}
