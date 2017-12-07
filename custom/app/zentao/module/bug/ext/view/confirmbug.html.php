<?php
/**feature-878-zh**/
/**
 * The confirm file of bug module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     bug
 * @version     $Id: resolve.html.php 1914 2011-06-24 10:11:25Z yidong@cnezsoft.com $
 * @link        http://www.zentao.net
 */
?>
<?php
include '../../../common/view/header.html.php';
include '../../../common/view/kindeditor.html.php';
js::set('holders', $lang->bug->placeholder);
js::set('page', 'confirmbug');
?>
<div id='titlebar'>
  <div class='heading'>
    <span class='prefix'><?php echo html::icon($lang->icons['bug']);?> <strong><?php echo $bug->id;?></strong></span>
    <strong><?php echo html::a($this->createLink('bug', 'view', 'bug=' . $bug->id), $bug->title, '_blank');?></strong>
    <small class='text-muted'> <?php echo $lang->bug->confirmBug;?> <?php echo html::icon($lang->icons['confirm']);?></small>
  </div>
</div>

<form class='form-condensed' method='post' target='hiddenwin'>
  <table class='table table-form'>
    <tr>
      <th class='w-80px'><?php echo $lang->bug->assignedTo;?></th>
      <td class='w-p25-f'><?php echo html::select('assignedTo', $users, $bug->assignedTo, "class='select-2 chosen'");?></td>
      <td></td>
    </tr>  
    <tr>
      <th class='w-80px'><?php echo $lang->bug->pri;?></th>
      <td class='w-p25-f'><?php echo html::select('pri', $lang->bug->priList, $bug->pri, 'class=form-control');?></td><td></td>
    </tr>  
    <tr>
      <th class='w-80px'><?php echo $lang->bug->mailto;?></th>
      <td colspan='2' width ='800px'><?php echo html::select('mailto[]', $users, str_replace(' ' , '', $bug->mailto), 'class="form-control chosen" multiple');?></td>
    </tr>
    <tr>
      <th class='w-80px'><?php echo $lang->comment;?></th>
      <td colspan='2' width ='800px'><?php echo html::textarea('comment', '', "rows='6' class='w-p94'");?></td>
    </tr>
<?php /*add by yangjinlian at 20161227*/		?>	
    <tr>
      <th class='w-80px'><?php echo $lang->bug->source;?></th>
	  <td class='w-p25-f'><?php echo html::select('source', $lang->bug->sourceList,  '', 'class=form-control');?></td><td></td>
    </tr>	
    <tr id='backtrackingBox'>
      <th class='w-80px'><?php echo $lang->bug->backtracking;?></th>
	  <td bgcolor="red"><?php echo $lang->bug->backtdrackingnote;?></td>
    </tr>
    <tr>
      <th class='w-80px'><?php echo $lang->bug->issimple;?></th>
	  <td class='w-p25-f'><?php echo html::select('issimple', $lang->bug->issimpleList,  '1', 'class=form-control onchange="switchShow(this.value)"');?></td><td></td>
    </tr>	
	<tr id='rdresponserBox'>
      <th class='w-80px'><?php echo $lang->bug->rdresponser;?></th>
      <td class='w-p25-f'><?php echo html::select('rdresponser', $users, $bug->rdresponser,  "class='form-control chosen'");?></td><td></td>
    </tr> 
	<tr id='testresponserBox' >
      <th class='w-80px'><?php echo $lang->bug->testresponser;?></th>
      <td class='w-p25-f'><?php echo html::select('testresponser', $users, $bug->testresponser,  "class='form-control chosen'");?></td><td></td>
    </tr>
	<tr id='reqresponserBox' >
      <th class='w-80px'><?php echo $lang->bug->reqresponser;?></th>
      <td class='w-p25-f'><?php echo html::select('reqresponser', $users, $bug->reqresponser,  "class='form-control chosen'");?></td><td></td>
    </tr>	
	
<?php /*------------------------------*/		?>		
    <tr>
      <th></th><td colspan='2'><?php echo html::submitButton() . html::linkButton($lang->goback, $this->server->http_referer);?></td>
    </tr>
  </table>
</form>
<div class='main'>
  <?php include '../../../common/view/action.html.php';?>
</div>
<?php include '../../../common/view/footer.html.php';?>
