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
     * @param  date    $workdate 
     * @access public
     * @return void
     */
    public function dailywork($account)
    {
        $this->view->account     = $account;
        $this->view->url         = $this->config->worklog->itaskouter;
        
        $this->display();
    }
}
