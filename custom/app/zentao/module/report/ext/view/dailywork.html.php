<?php
/**feature-1077**/
include '../../../common/view/header.lite.html.php';
include '../../../common/view/chart.html.php';
?>
<div id='titlebar'>
  <div class='heading'>
    <span><?php echo html::icon($lang->icons['report']);?></span>
    <?php 
    $title = $users[$account] . '-报工(' . date('Y年m月d日',strtotime($workdate)) ;
    if($lastday != '')
    {
        $title .= '~' . date('Y年m月d日',strtotime($lastday));
    }
    $title .= ')';
    ?>
    <small class='text-muted'> <?php echo $title;?></small>
  </div>
</div>
<div class='dailywork'>
    <iframe frameborder=0 width='100%' height ='500' src=<?php echo $url . "/?username=" . $account . "&dep=" . $config->worklog->depcode;?>>
    </iframe>
</div>
<?php 
include '../../../common/view/footer.lite.html.php';
?>
