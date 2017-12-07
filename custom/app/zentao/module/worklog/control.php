<?php

class worklog extends control
{
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);
        $this->loadModel('dept');
    }	
	
	public function index()
    {   
		$this->locate(inlink('recordestimate',"netType=inner"));
    }   


    public function recordestimate($netType = 'inner')
    {
		if ($netType=='inner')
		{
			$this->view->url         = $this->config->worklog->itaskinner;
			$this->view->title = $this->lang->worklog->recordestimateinner;
		}
		else
		{
			$this->view->url         = $this->config->worklog->itaskouter;
			$this->view->title = $this->lang->worklog->recordestimateouter;
		}
		$this->display();
    }	

	
	public function worklogbrowse($param = 0, $type = 'bydept', $orderBy = 'workdate_desc,realname_asc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {
        $this->loadModel('search');
        $this->lang->set('menugroup.company', 'company');

        $deptID = $type == 'bydept' ? (int)$param : 0;
        $this->loadModel('company');
		$this->loadModel('dept');
		$this->loadModel('worklog');
		$this->company->setMenu($deptID);
        /* Save session. */
        $this->session->set('worklogList', $this->app->getURI(true));

        /* Set the pager. */
        $this->app->loadClass('pager', $static = true);
        $pager = pager::init($recTotal, $recPerPage, $pageID);

        /* Append id for secend sort. */
        /*$sort = $this->loadModel('common')->appendOrder($orderBy);*/

        /* Build the search form. */
        $queryID   = $type == 'bydept' ? 0 : (int)$param;
        $actionURL = $this->createLink('worklog', 'worklogbrowse', "param=myQueryID&type=bysearch");
        $this->worklog->buildSearchForm($queryID, $actionURL);
		/* Get all data of dw_worklog */
		$worklogs = $this->worklog->getWorklogs($type, $queryID, $deptID, $orderBy, $pager);


        /* Assign. */
        $this->view->title       = $lang->worklog->worklogbrowse;
        $this->view->position[]  = $this->lang->dept->common;
        $this->view->worklogs    = $worklogs;
        $this->view->searchForm  = $this->fetch('search', 'buildForm', $this->config->worklog->worklogbrowse->search);
        $this->view->deptTree    = $this->worklog->getTreeMenu($rooteDeptID = 0, array('worklogModel', 'createMemberLink'));
        /*$this->view->parentDepts = $this->dept->getParents($deptID);*/
        $this->view->orderBy     = $orderBy;
        $this->view->deptID      = $deptID;
        $this->view->pager       = $pager;
        $this->view->param       = $param;
        $this->view->type        = $type;
        $this->display();
    }
	
}
?>