<?php
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
     * @param  date    $dateNum 
     * @access public
     * @return void
     */
    public function modmerge($userRootId, $orgType='', $monthNum='', $encodeMergeId=0)
    {
        
        if($_POST)
        {
            $data = fixer::input('post')->get();
            
            if($this->post->file_type) $this->report->updateMergeDetailInfo();
            die(js::locate($this->createLink('report', 'monthperformancescoredetail', "scoreType=DevTask&" . 
            "userRootId=$data->userRootId&amibaName=$data->amibaName&isAmibaChanged=false&groupName=$data->groupName&account=$data->account&" .
            "orgType=$data->orgType&monthNum=$data->monthNum"), 'parent'));
        }
        
        $mergeId = str_replace('___', '-', $encodeMergeId);
        $this->view->mergeInfo = $this->report->getMergeInfo($userRootId, $mergeId);
        
        $this->view->userRootId   = $userRootId  ;
        $this->view->orgType   = $orgType  ;
        $this->view->monthNum  = $monthNum ;
        
        $this->loadModel('user');
        $this->view->users = $this->user->getPairs('noletter');
        $this->display();
    }
}
