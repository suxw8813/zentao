<?php
global $lang, $app;
$app->loadLang('worklog');
$config->worklog->worklogbrowse = new stdClass();
$config->worklog->worklogbrowse->search['module'] = 'worklog';
$config->worklog->worklogbrowse->search['fields']['realname'] = $lang->worklog->realname;
$config->worklog->worklogbrowse->search['fields']['work_date'] = $lang->worklog->workdate;
$config->worklog->worklogbrowse->search['params']['realname'] = array('operator' => 'include',  'control' => 'input',  'values' => '');
$config->worklog->worklogbrowse->search['params']['work_date']       = array('operator' => '=',  'control' => 'input',  'values' => '', 'class' => 'date');