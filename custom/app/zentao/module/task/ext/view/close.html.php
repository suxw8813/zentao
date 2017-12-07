<?php
/**feature-1488**/
/**feature-899-wx**/
/**
 * The close file of task module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      chunsheng wang <chunsheng@cnezsoft.com>
 * @package     task
 * @version     $Id: cancel.html.php 935 2010-07-06 07:49:24Z jajacn@126.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../../common/view/header.html.php';?>
<?php include '../../../common/view/kindeditor.html.php';?>
<div id='titlebar'>
  <div class='heading'>
    <span class='prefix'><?php echo html::icon($lang->icons['task']);?> <strong><?php echo $task->id;?></strong></span>
    <strong><?php echo html::a($this->createLink('task', 'view', 'task=' . $task->id), $task->name, '_blank');?></strong>
    <small class='text-danger'> <?php echo $lang->task->close;?> <?php echo html::icon($lang->icons['close']);?></small>
  </div>
</div>


<form class='form-condensed' method='post' target='hiddenwin'>
  <table class='table table-form'>
 
<?php /**feature-899-wx**/?>
   	<tr>
	<th></th>
	<td><?php echo $lang->task->scorenote;?></td><td></td>
	</tr>

     <?php if($lang->task->score == '评分'):?>	
	<tr>
      <th><?php echo $lang->task->score;?></th>
      <td>	  
	  <div class='required required-wrapper'></div>
	  <?php echo html::input('score', '', "class='form-control' ");?>
	  </td>
    </tr>
    <tr>
      <th><?php echo $lang->task->scorecomment;?></th>
      <td><?php echo html::input('scorecomment', '', "class='form-control' ");?></td>
    </tr>  
<?php endif;?>	
<?php /**--------------**/?>  
<?php/**feature-1488**/?>
	  <tr>
        <th><?php echo $lang->task->satisficingeval;?></th>
        <td><?php echo html::select('satisficingeval', $lang->task->sevalList, '', 'class=form-control');?></td>
 	  </tr>	
<?php/**--------------**/?>	  


    <tr>
      <th><?php echo $lang->comment;?></th>
      <td><?php echo html::textarea('comment', '', "rows='6' class='form-control'");?></td>
    </tr> 

    <tr>
      <th></th><td><?php echo html::submitButton($lang->task->close);?></td>
    </tr>
  </table>
</form>
<div class='main'>
  <?php include '../../../common/view/action.html.php';?>
</div>
<?php include '../../../common/view/footer.html.php';?>
