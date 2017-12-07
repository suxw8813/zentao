<?php 
/**feature-1049**/
include '../../common/view/header.html.php';
js::set('deptID', $deptID);
?>
<div id='titlebar'>
  <div class='heading'><?php echo $lang->worklog->worklogbrowse;?></div>
</div>
<div><?php echo $queryID?></div>
<div id='querybox' class='show'><?php echo $searchForm?></div>
<div class='side'>
  <a class='side-handle' data-id='companyTree'><i class='icon-caret-left'></i></a>
  <div class='side-body'>
    <div class='panel panel-sm'>
      <div class='panel-heading nobr'><?php echo html::icon($lang->icons['dept']);?> <strong><?php echo $lang->dept->common;?></strong></div>
      <div class='panel-body'>
        <?php echo $deptTree;?>
      </div>
    </div>
  </div>
</div>
<div class='main'>
  <script>setTreeBox();</script>
  <form method='post' id='worklogListForm'>
    <table class='table table-condensed table-hover table-striped tablesorter table-fixed table-selectable' id='worklogList'>
      <thead>
      <tr class='colhead'>
        <?php $vars = "param=$param&type=$type&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}";?>
        <th class='w-100px'><?php common::printOrderLink('workdate',  $orderBy, $vars, $lang->worklog->workdate);?></th>
        <th class='w-100px'><?php common::printorderlink('realname', $orderBy, $vars, $lang->worklog->realname);?></th>
		<th class='w-100px'><?php common::printorderlink('account', $orderBy, $vars, $lang->worklog->account);?></th>
        <th class='w-100px'><?php common::printOrderLink('tasktype',     $orderBy, $vars, $lang->worklog->tasktype);?></th>
		<th class='w-id'><?php common::printOrderLink('id',    $orderBy, $vars, $lang->worklog->id);?></th>
		<th class='w-p40'><?php common::printOrderLink('workcontent',    $orderBy, $vars, $lang->worklog->workcontent);?></th>
        <th class='w-100px'><?php common::printOrderLink('worktime',   $orderBy, $vars, $lang->worklog->worktime);?></th>
		<th class='w-100px'><?php common::printOrderLink('workovertime',   $orderBy, $vars, $lang->worklog->workovertime);?></th>
      </tr>
      </thead>
      <tbody>
      <?php foreach($worklogs as $worklog):?>
      <tr class='text-center'>
        <td><?php echo $worklog->workdate;?></td>  
        <td><?php echo $worklog->realname;?></td>
		<td><?php echo $worklog->account;?></td>
		<td><?php echo $lang->worklog->tasktypeList[$worklog->tasktype];?></td>
		<td>
			<?php 
			if($worklog->tasktype=='1')
			{
				common::printLink('story', 'view', "story=$worklog->id", sprintf('%03d', $worklog->id));
			}
			elseif($worklog->tasktype=='2')
			{
				common::printLink('task', 'view', "task=$worklog->id", sprintf('%03d', $worklog->id));
			}elseif($worklog->tasktype=='3')
			{
				common::printLink('bug', 'view', "bug=$worklog->id", sprintf('%03d', $worklog->id));
			}else
			{
				printf('%03d', $worklog->id);
			}
			?>		
		</td>
		<td title='<?php echo $worklog->workcontent;?>'><?php echo $worklog->workcontent;?></td>
		<td><?php echo $worklog->worktime;?></td>	
		<td><?php echo $worklog->workovertime;?></td>
      </tr>
      <?php endforeach;?>
      </tbody>
      <tfoot>
      <tr>
        <td colspan='12'>
        <?php echo $pager->show();?>
        </td>
      </tr>
      </tfoot>
    </table>
  </form>
</div>
<script lanugage='javascript'>$('#dept<?php echo $deptID;?>').addClass('active');</script>
<?php include '../../common/view/footer.html.php';?>
