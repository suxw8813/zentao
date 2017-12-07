<?php
/**feature-1245**/
/**
 * Get amibas. 
 * 
 * @access public
 * @return void
 */
public function getMergeInfo($userRootId, $mergeId){
    $mergeInfoSql = str_replace('#{mergeId}', $mergeId, $this->config->report->mergeInfoSql);
    $mergeInfoSql = str_replace('#{userRootId}', $userRootId, $mergeInfoSql);
    $mergeInfo = $this->dao->query($mergeInfoSql)->fetch();
    
    $mergeDetailInfoSql = str_replace('#{mergeId}', $mergeId, $this->config->report->mergeDetailInfoSql);
    $mergeInfo->mergeDetailInfo = $this->dao->query($mergeDetailInfoSql);
    
    // die(js::alert($mergeDetailInfoSql   ));
    return $mergeInfo;
}

/**
 * 更新有效输出修正信息
 */
public function updateMergeDetailInfo(){
    $data = fixer::input('post')->get();
    if($this->validateInputError($data)){
        exit;
    }
    
    foreach($data->file_type as $id => $file_type){
        // 如果修正记录已经存在，删除该记录。
        if($data->id_new[$id] != ''){
            $this->deleteModRecord($data->related_pr_cuid_new[$id], $data->file_type_new[$id]);
        }
        
        $neededMod = false;
        // 如果不可统计行数，修正文件个数；否则，修正代码行数。
        
        if($data->line_add[$id] == 0 && $data->line_del[$id] == 0){
            // 如果文件个数不相同，修正文件个数
            if(!$this->compareFieldValue($id, 'file_count')){
                $neededMod = true;
            }
        } else {
            if(!$this->compareFieldValue($id, 'line_add') || !$this->compareFieldValue($id, 'line_del')){
                $neededMod = true;
            }
        }
        
        if($neededMod){
            $record = array();
            $record['department']       = '';
            $record['related_pr_cuid']  = $data->related_pr_cuid_new[$id];
            $record['line_add']         = $data->line_add_new[$id];
            $record['line_del']         = $data->line_del_new[$id];
            $record['line_modify']      = $data->line_modify_new[$id];
            $record['file_type']        = $data->file_type_new[$id];
            $record['file_count']       = $data->file_count_new[$id];
            $record['remark']           = $data->remark_new[$id];
            $record['time_stamp']       = date('Y-m-d H:i');
            
            // 修改历史修正人
            $lastIndex = strrpos($data->modifier_new[$id], ',');
            if($lastIndex > 0){
                $lastAccount = substr($data->modifier_new[$id], $lastIndex + 1);
                // die(js::alert( $this->app->user->account . '0011' . $lastAccount));
                
                if($lastAccount == $this->app->user->account){
                    $record['modifier'] = $data->modifier_new[$id];
                } else {
                    $record['modifier'] = $data->modifier_new[$id] . ',' . $this->app->user->account;
                }
            } else {
                $record['modifier'] = $this->app->user->account;
            }
            
            $this->insertModRecord($record);
        }
    }
}

public function validateInputError($data){
    $hasEmptyMark = false;
    foreach($data->file_type as $id => $file_type){
        $hasMod = !$this->compareFieldValue($id, 'file_count') ||
                  !$this->compareFieldValue($id, 'line_add') ||
                  !$this->compareFieldValue($id, 'line_del');
        if($hasMod && empty($data->remark_new[$id])){
            $hasEmptyMark = true;
            break;
        }
    }
    if($hasEmptyMark){
        print(js::alert('修改分数后，请填写备注！'));
    }
    
    return $hasEmptyMark;
}

public function compareFieldValue($id, $fieldName){
    $data = fixer::input('post')->get();
    
    $fieldValues = $data->$fieldName;
    
    $fieldNameNew = $fieldName . '_new';
    $fieldValuesNew = $data->$fieldNameNew;
    if($fieldValues[$id] == $fieldValuesNew[$id]){
        return true;
    } else {
        return false;
    }
}

public function insertModRecord($record){
   $sql = $this->config->report->insertModRecordSql;
   foreach($record as $fieldName => $fieldValue){
       $sql = str_replace('#{' . $fieldName . '}', $fieldValue, $sql);
   }
   
   $this->dao->exec($sql);
}

public function deleteModRecord($related_pr_cuid_new, $file_type_new){
    $sql = str_replace('#{related_pr_cuid}', $related_pr_cuid_new, $this->config->report->deleteModRecordSql);
    $sql = str_replace('#{file_type}', $file_type_new, $sql);
    
    $this->dao->exec($sql);
}