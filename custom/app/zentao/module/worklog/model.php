<?php
/**feature-1049**/
/**fixbug-485**/
class worklogModel extends model
{
     public function getWorklogs($type, $queryID, $deptID, $sort, $pager)
    {
        if($type == 'bydept')
        {
            $childDeptIds = $this->loadModel('dept')->getAllChildID($deptID);
			return $this->dao->select('c.realname,a.account,a.work_date as workdate,a.task_type as tasktype,a.task_id as id,a.work_content as workcontent,a.work_time as worktime,a.work_over_time as workovertime')
			->from(TABLE_WORKLOG) ->alias('a')
			->leftJoin(TABLE_USER)->alias('c')
			->on('a.account=c.account')
			->where('c.deleted')->eq('0')
			->beginIF($deptID)->andWhere('c.dept')->in($childDeptIds)->fi()
			->orderBy($sort)
            ->page($pager)
            ->fetchAll();	
         }
		else
		{
            if($queryID)
            {
                $query = $this->loadModel('search')->getQuery($queryID);
                if($query)
                {
                    $this->session->set('worklogQuery', $query->sql);
                    $this->session->set('worklogForm', $query->form);
                }
                else
                {
                    $this->session->set('worklogQuery', ' 1 = 1');
                }
            }
            /*return $this->loadModel('user')->getByQuery($this->session->userQuery, $pager, $sort);*/
			return $this->dao->select('c.realname,a.account,a.work_date as workdate,a.task_type as tasktype,a.task_id as id,a.work_content as workcontent,a.work_time as worktime,a.work_over_time as workovertime')
			->from(TABLE_WORKLOG) ->alias('a')
			->leftJoin(TABLE_USER)->alias('c')
			->on('a.account=c.account')
			->where ('c.deleted')->eq('0')
			->andWhere($this->session->worklogQuery)
			->orderBy($sort)
            ->page($pager)
            ->fetchAll();	
		
		}
	}
	public function getWorklogsById($taskType, $taskId)
	{
		return $this->dao->select('c.realname,a.account,a.work_date as workdate,a.task_type as tasktype,a.task_id as id,a.work_content as workcontent,a.work_time as worktime,a.work_over_time as workovertime')
			->from(TABLE_WORKLOG) ->alias('a')
			->leftJoin(TABLE_USER)->alias('c')
			->on('a.account=c.account')
			->where ('c.deleted')->eq('0')
			->andWhere('a.task_id')->eq($taskId)
			->andWhere('a.task_type')->eq($taskType)
			->orderBy(workdate_desc)
            ->fetchAll();
	}

    public function getTreeMenu($rootDeptID = 0, $userFunc, $param = 0)
    {
        $deptMenu = array();
        $stmt = $this->dbh->query($this->buildMenuQuery($rootDeptID));
        while($dept = $stmt->fetch())
        {
            $linkHtml = call_user_func($userFunc, $dept, $param);

            if(isset($deptMenu[$dept->id]) and !empty($deptMenu[$dept->id]))
            {
                if(!isset($deptMenu[$dept->parent])) $deptMenu[$dept->parent] = '';
                $deptMenu[$dept->parent] .= "<li>$linkHtml";  
                $deptMenu[$dept->parent] .= "<ul>".$deptMenu[$dept->id]."</ul>\n";
            }
            else
            {
                if(isset($deptMenu[$dept->parent]) and !empty($deptMenu[$dept->parent]))
                {
                    $deptMenu[$dept->parent] .= "<li>$linkHtml\n";  
                }
                else
                {
                    $deptMenu[$dept->parent] = "<li>$linkHtml\n";  
                }    
            }
            $deptMenu[$dept->parent] .= "</li>\n"; 
        }

        $lastMenu = "<ul class='tree tree-lines'>" . @array_pop($deptMenu) . "</ul>\n";
        return $lastMenu; 
    }

    public function buildMenuQuery($rootDeptID)
    {
        $rootDept = $this->getByID($rootDeptID);
        if(!$rootDept)
        {
            $rootDept = new stdclass();
            $rootDept->path = '';
        }

        return $this->dao->select('*')->from(TABLE_DEPT)
            ->beginIF($rootDeptID > 0)->where('path')->like($rootDept->path . '%')->fi()
            ->orderBy('grade desc, `order`')
            ->get();
    }	
	
    public function createMemberLink($dept)
    {
        $linkHtml = html::a(helper::createLink('worklog', 'worklogbrowse', "dept={$dept->id}"), $dept->name, '_self', "id='dept{$dept->id}'");
        return $linkHtml;
    }	
	
    public function getByID($deptID)
    {
        return $this->dao->findById($deptID)->from(TABLE_DEPT)->fetch();
    }
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->worklog->worklogbrowse->search['actionURL'] = $actionURL;
        $this->config->worklog->worklogbrowse->search['queryID']   = $queryID;
        $this->config->worklog->worklogbrowse->search['params']['dept']['values'] = array('' => '') + $this->loadModel('dept')->getOptionMenu();

        $this->loadModel('search')->setSearchParams($this->config->worklog->worklogbrowse->search);
    }
}
