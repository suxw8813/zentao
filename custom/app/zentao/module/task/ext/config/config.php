<?php
$config->task->create->requiredFields      = 'name,type,desc,assignTo,assignedTo,story,deadline';
$config->task->edit->requiredFields        = $config->task->create->requiredFields;