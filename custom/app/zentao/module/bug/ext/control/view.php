<?php
/**feature-851**/
/**
 * The control file of bug currentModule of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     bug
 * @version     $Id: control.php 5107 2013-07-12 01:46:12Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class bug extends control
{
    public $products = array();

    /**
     * Construct function, load some modules auto.
     * 
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $methodName = '')
    {
        parent::__construct($moduleName, $methodName);
        $this->loadModel('product');
        $this->loadModel('tree');
        $this->loadModel('user');
        $this->loadModel('action');
        $this->loadModel('story');
        $this->loadModel('task');
        $this->view->products = $this->products = $this->product->getPairs('nocode');
        if(empty($this->products)) die($this->locate($this->createLink('product', 'showErrorNone', "fromModule=bug")));
    }

    public function view($bugID)
    {
        /* Judge bug exits or not. */
        $bug = $this->bug->getById($bugID, true);
        if(!$bug) die(js::error($this->lang->notFound) . js::locate('back'));

        if($bug->project and !$this->loadModel('project')->checkPriv($this->project->getByID($bug->project)))
        {
            echo(js::alert($this->lang->project->accessDenied));
            $loginLink = $this->config->requestType == 'GET' ? "?{$this->config->moduleVar}=user&{$this->config->methodVar}=login" : "user{$this->config->requestFix}login";
            if(strpos($this->server->http_referer, $loginLink) !== false) die(js::locate(inlink('index')));
            die(js::locate('back'));
        }

        /* Update action. */
        if($bug->assignedTo == $this->app->user->account) $this->loadModel('action')->read('bug', $bugID);

        /* Set menu. */
        $this->bug->setMenu($this->products, $bug->product, $bug->branch);

        /* Get product info. */
        $productID   = $bug->product;
        $productName = $this->products[$productID];
      
        /* Header and positon. */
        $this->view->title      = "BUG #$bug->id $bug->title - " . $this->products[$productID];
        $this->view->position[] = html::a($this->createLink('bug', 'browse', "productID=$productID"), $productName);
        $this->view->position[] = $this->lang->bug->view;
		/*Get worklogs */
		$worklogs  	= $this->loadModel(worklog)->getWorklogsById('3', $bugID);
        /* Assign. */
        $this->view->productID   = $productID;
        $this->view->productName = $productName;
        $this->view->modulePath  = $this->tree->getParents($bug->module);
        $this->view->bug         = $bug;
        $this->view->branchName  = $this->session->currentProductType == 'normal' ? '' : $this->loadModel('branch')->getById($bug->branch);
        $this->view->users       = $this->user->getPairs('noletter');
        $this->view->actions     = $this->action->getList('bug', $bugID);
        $this->view->builds      = $this->loadModel('build')->getProductBuildPairs($productID, $branch = 0, $params = '');
        $this->view->preAndNext  = $this->loadModel('common')->getPreAndNextObject('bug', $bugID);
		$this->view->worklogs    = $worklogs;	
        $this->display();
    }

}
