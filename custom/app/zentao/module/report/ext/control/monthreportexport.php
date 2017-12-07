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
     * @param  date    $monthNum 
     * @access public
     * @return void
     */
    public function monthreportexport($userRootId, $monthNum)
    {
        list($month, $begin, $end) = $this->report->getMonthBeginEnd($monthNum, false);
        
        /* Get exportData. */
        $exportData = $this->report->getMonthReportData($userRootId, $monthNum, $begin, $end);

        $reportLang   = $this->lang->report;
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
        $this->view->fileName = date('Y年m月-', strtotime($month)) . '加班统计-' . date('YmdHis');
        $this->view->charset = 'gbk';
        $this->view->allExportFields = $monthreportexportConfig->list->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }
}
