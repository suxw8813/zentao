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
     * monthreport information report.
     * 
     * @access public
     * @return void
     */
    public function monthreport($userRootId='', $amibaName = '', $groupName = '', $account = '', $monthNum = '')
    {
        // 重新计算
        if(!empty($_POST))
        {
            // 从界面上获取开始和结束时间
            $data = fixer::input('post')->get();
            $performanceServiceUrl = $this->config->report->performanceServiceUrls[$this->config->worklog->depcode];
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
            die(js::locate($this->createLink('report', 'monthreport', "userRootId=$data->fresh_userRootId&amibaname=$data->fresh_amibaName&groupname=$data->fresh_groupName&account=$data->fresh_account&monthNum=$data->fresh_monthNum"), 'parent'));
        }
        
        // 月报工及有效输出
        list($month, $begin, $end) = $this->report->getMonthBeginEnd($monthNum, false);
        
        if(empty($monthNum))
        {
            $monthNum = date('Ym', strtotime($month));
        }
        
        // 获取根字典
        $userRootDict = $this->report->getUserRootDict();
        // 得到userRootId
        if(empty($userRootId)){
            // print(js::alert($userRootId));
            list($userRootId, $userRootName) = $this->report->getMinUserRoot($userRootDict);
        }
        list($deptUserCount, $deptAllTime, $deptAvgOutput, $amibas) = $this->report->getMonthAmibas($userRootId, $monthNum, $begin, $end);
        
        // $this->view->zcj = $this->report->getMonthReportSql($monthNum, $begin, $end);
        
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
            $lastAmibas[$amibaName]->total_time = $amibas[$amibaName]->total_time;
            $lastAmibas[$amibaName]->total_output = $amibas[$amibaName]->total_output;
            $lastAmibas[$amibaName]->mod_merge_count = $amibas[$amibaName]->mod_merge_count;
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
        $this->view->workDayCount = $this->report->getworkDayCount($begin, $end);
        
        $this->view->amibas = $lastAmibas;
        
        $this->view->title      = $this->lang->report->monthreport;
        $this->view->position[] = $this->lang->report->monthreport;
        $this->view->submenu    = 'work';
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
}
