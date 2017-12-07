<?php
$config->story->create->requiredFields = 'product, plan, source, title, spec, module';
$config->story->edit->requiredFields = $config->story->create->requiredFields;
$config->story->change->requiredFields = $config->story->create->requiredFields;