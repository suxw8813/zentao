<?php 
/**feature-1405**/
    public function getTasksByProductPlanID($planID = 0, $status = 'all', $orderBy = 'id_desc')
    {
        $tasks = $this->dao->select('t1.*,t2.title,t3.id AS planID')
            ->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story = t2.id')
            ->leftJoin(TABLE_PRODUCTPLAN)->alias('t3')->on('t2.plan = t3.id')
            ->where('t1.deleted')->eq(0)
			->beginIF($status and $status != 'all')->andWhere('t1.status')->in($status)->fi()
            ->andWhere('t3.id')->eq($planID)
			->orderBy($orderBy)
            ->fetchAll();
        if($tasks) return $tasks;
        return array();
    }	