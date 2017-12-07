<?php
/**feature-1509**/
/* 主导航菜单。*/
$lang->menu->quantizedoutput   = '有效输出|quantizedoutput|monthreport';

/* 有效输出模块视图菜单设置*/
$lang->quantizedoutput = new stdclass();
$lang->quantizedoutput->menu = new stdclass();

$lang->quantizedoutput->menu->staff   = array('link' => '组织|quantizedoutput|monthreport', 'alias' => 'dayreport,monthreport,sort');
$lang->quantizedoutput->menu->prd     = array('link' => $lang->productCommon . '|quantizedoutput|prdmonthreport', 'alias' => 'prdmonthreport,prdsort');
$lang->quantizedoutput->menu->prj     = array('link' => $lang->projectCommon . '|quantizedoutput|prjmonthreport', 'alias' => 'prjmonthreport,prjsort');
// $lang->quantizedoutput->menu->gitproject   = array('link' => 'gitLab权限|quantizedoutput|gitproject');

$lang->quantizedoutput->notice = new stdclass();
$lang->quantizedoutput->notice->help = '注：统计报表的数据，来源于列表页面的检索结果，生成统计报表前请先在列表页面进行检索。';
