<?php include '../../common/view/header.html.php';?>
<div id='titlebar'>
  <div class='heading'><?php echo $url; ?></div>
</div>
<div class='main'>
<iframe frameborder=0 width='100%' height ='500' src=<?php echo $url."/?username=".$this->app->user->account."&dep=".$config->worklog->depcode;?>>
</iframe>
</div>
<?php include '../../common/view/footer.html.php';?>
