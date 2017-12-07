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
     * dayreport information report.
     * 
     * @access public
     * @return void
     */
    public function dayreport($userRootId='', $amibaName = '', $groupName = '', $userName = '', $dayNum = '')
    {
        $day = empty($dayNum) ? date('Y-m-d', strtotime('-1 day')) : date('Y-m-d', strtotime($dayNum));
        
        // 获取根字典
        $userRootDict = $this->report->getUserRootDict();
        
        // 得到userRootId
        if(empty($userRootId)){
            // print(js::alert($userRootId));
            list($userRootId, $userRootName) = $this->report->getMinUserRoot($userRootDict);
        }
        
        list($deptUserCount, $deptAllTime, $amibas) = $this->report->getDayAmibas($userRootId, $day);
        
        $amibaNames = array('' => '');
        $groupNames = array('' => '');
        $userNames = array('' => '');
        $lastAmibas = array();
        
        foreach($amibas as $amiba)
        {
            $amibaNames[$amiba->amiba_name] = $amiba->amiba_name;
        }
        
        if(!isset($amibaName) || empty($amibaName)) // 阿米巴为空查询全部
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
            
            if(!isset($groupName) || empty($groupName)) // 组名为空查询阿米巴下所有组的用户
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
        
        $this->view->title      = $this->lang->report->dayreport;
        $this->view->position[] = $this->lang->report->dayreport;
        $this->view->submenu    = 'work';
        $this->display();
    }
}
