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
     * daywork information report.
     * 
     * @access public
     * @return void
     */
    public function sort($userRootId, $timeType, $timeNum = '')
    {
        list($time, $monthNums, $begin, $end) = $this->report->getMonthNums($timeType, $timeNum);
        
        // 获取根字典
        $userRootDict = $this->report->getUserRootDict();
        
        // 得到userRootId
        if(empty($userRootId)){
            list($userRootId, $userRootName) = $this->report->getMinUserRoot($userRootDict);
        }
        
        // $this->view->zcj = $this->report->getPersonTimeTop30Sql($userRootId, $monthNums, $begin, $end, 'total_time');
        $this->view->PersonTimeTop30Data = $this->report->getPersonTimeTop30Flot($userRootId, $monthNums, $begin, $end);
        $this->view->PersonOutputEfficiencyTop30Data = $this->report->getPersonOutputEfficiencyTop30Flot($userRootId, $monthNums, $begin, $end);
        $this->view->PersonOutputTop30Data = $this->report->getPersonOutputTop30Flot($userRootId, $monthNums, $begin, $end);
        $this->view->PersonAvgAmibaTimeTopData = $this->report->getPersonAvgAmibaTimeTopFlot($userRootId, $begin, $end);
        // print(js::alert(json_encode($this->view->PersonTimeTop30Data['burnBar'])));
        
        $this->view->time = $time;
        $this->view->begin = $begin;
        $this->view->end = $end;
        
        $this->view->userRootDict = $userRootDict;
        $this->view->userRootId = $userRootId;
        $this->view->timeType = $timeType;
        $this->view->workDayCount = $this->report->getWorkDayCount($begin, $end);
        if($timeType == '月') {
            $this->view->title      = $this->lang->report->monthsort;
            $this->view->position[] = $this->lang->report->monthsort;
        } else {
            $this->view->title      = $this->lang->report->yearsort;
            $this->view->position[] = $this->lang->report->yearsort;
        }
        
        $this->view->submenu    = 'work';
        $this->display();
    }
}
