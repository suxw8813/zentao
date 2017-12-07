<?php
/**feature-1077**/
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
    public function worklogs($account, $dateNum, $timeType)
    {
        if($timeType == '日')
        {
            $begin = date('Y-m-d', strtotime($dateNum));
            $end = '';
        }
        else
        {
            list($month, $begin, $end) = $this->report->getMonthBeginEnd($dateNum, false);
            $this->view->month = $month;
        }
        
        list($total_time, $worklogs) = $this->report->getWorklogs($account, $begin, $end);
        
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
}
