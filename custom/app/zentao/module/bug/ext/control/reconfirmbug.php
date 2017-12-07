<?php
/**feature-1053**/
class bug extends control
{
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



    public function reconfirmBug($bugID)
    {
	     if(!empty($_POST))
        {

			if (($this->post->issimple=='1')&&(!(($this->post->rdresponser)||($this->post->testresponser)||($this->post->reqresponser))))
			{
				$this->display();
			}
				else
			{
				$this->bug->confirm($bugID);
				if(dao::isError()) die(js::error(dao::getError()));
				$actionID = $this->action->create('bug', $bugID, 'bugConfirmed', $this->post->comment);
				$this->bug->sendmail($bugID, $actionID);
				if(isonlybody()) die(js::closeModal('parent.parent'));
				die(js::locate($this->createLink('bug', 'view', "bugID=$bugID"), 'parent'));
			}
		}
        $bug             = $this->bug->getById($bugID);
        $productID       = $bug->product;
        $this->bug->setMenu($this->products, $productID, $bug->branch);
		if ($bug->issimple <>'1'){
			$this->view->responserdisplay = "display:none";
		}
        $this->view->title      = $this->products[$productID] . $this->lang->colon . $this->lang->bug->confirmBug;
        $this->view->position[] = html::a($this->createLink('bug', 'browse', "productID=$productID"), $this->products[$productID]);
        $this->view->position[] = $this->lang->bug->confirmBug;

        $this->view->bug     = $bug;
        $this->view->users   = $this->user->getPairs('nodeleted', $bug->assignedTo);
        $this->view->actions = $this->action->getList('bug', $bugID);
        $this->display();
    }	
}		