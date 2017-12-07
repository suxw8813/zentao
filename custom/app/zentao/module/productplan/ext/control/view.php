<?php
/**feature-1405**/
class productplan extends control
{
	    public function commonAction($productID, $branch = 0)
    {
        $this->loadModel('product');
        $this->app->loadConfig('project');
        $product = $this->product->getById($productID);
        $this->view->product  = $product;
        $this->view->branch   = $branch;
        $this->view->branches = $product->type == 'normal' ? array() : $this->loadModel('branch')->getPairs($productID);
        $this->view->position[] = html::a($this->createLink('product', 'browse', "productID={$this->view->product->id}&branch=$branch"), $this->view->product->name);
        $this->product->setMenu($this->product->getPairs(), $productID, $branch);
    }
	
	
    public function view($planID = 0, $type = 'story', $orderBy = 'id_desc', $link = 'false', $param = '')
    {
        $this->session->set('storyList', $this->app->getURI(true) . '&type=' . 'story');
        $this->session->set('bugList', $this->app->getURI(true) . '&type=' . 'bug');

        /* Append id for secend sort. */
        $sort = $this->loadModel('common')->appendOrder($orderBy);

        $plan = $this->productplan->getByID($planID, true);
        if(!$plan) die(js::error($this->lang->notFound) . js::locate('back'));
        $this->commonAction($plan->product, $plan->branch);
        $products                = $this->product->getPairs();

        $this->loadModel('datatable');
        $showModule = !empty($this->config->datatable->productBrowse->showModule) ? $this->config->datatable->productBrowse->showModule : '';
        $this->view->modulePairs = $showModule ? $this->loadModel('tree')->getModulePairs($plan->product, 'story', $showModule) : array();

        $this->view->title       = "PLAN #$plan->id $plan->title/" . $products[$plan->product];
        $this->view->position[]  = $this->lang->productplan->view;
        $this->view->planStories = $this->loadModel('story')->getPlanStories($planID, 'all', $type == 'story' ? $sort : 'id_desc');
        $this->view->planBugs    = $this->loadModel('bug')->getPlanBugs($planID, 'all', $type == 'bug' ? $sort : 'id_desc');
        $this->view->products    = $products;
        $this->view->summary     = $this->product->summary($this->view->planStories);
        $this->view->plan        = $plan;
        $this->view->actions     = $this->loadModel('action')->getList('productplan', $planID);
        $this->view->users       = $this->loadModel('user')->getPairs('noletter');
        $this->view->plans       = $this->productplan->getPairs($plan->product, $plan->branch);
        $this->view->modules     = $this->loadModel('tree')->getOptionMenu($plan->product);
        $this->view->type        = $type;
        $this->view->orderBy     = $orderBy;
        $this->view->link        = $link;
        $this->view->param       = $param;
		$this->view->planTasks	 = $this->productplan->getTasksByProductPlanID($planID,'all', $type == 'task' ? $sort : 'id_desc');
		$this->view->planCases	 = $this->productplan->getTestcasesByProductPlanID($planID,'all', $type == 'testcase' ? $sort : 'id_desc');
        $this->display();
    }
}	