<?php
/**feature-1077**/
/**feature-1245**/
include '../../../common/view/header.lite.html.php';
include '../../../common/view/chart.html.php';
include '../../../common/view/datepicker.html.php';
include '../../../common/view/chosen.html.php';

$statPeriod = "统计周期:工作日" . $workDayCount . "天" . "(" . date('Y-m-d', strtotime($begin)) . "~" . date('Y-m-d', strtotime($end)) . ")";
$monthNum = date('Ym',strtotime($month));
?>
<div id='titlebar'>
  <div class='heading'>
    <?php 
    echo "<span class='tendency-icon' style='background-image:url(" . $defaultTheme . "images/ext/stat-feature-1077.png)'></span>";
    echo html::hidden('orgType', $orgType);
    echo html::hidden('scoreType', $scoreType);
    
    echo html::hidden('amibaName', $amibaName);
    echo html::hidden('groupName', $groupName);
    echo html::hidden('account', $account);    
    echo $outputInfoSql;
    ?>
    <small class='text-muted'> <?php echo $title;?></small>
  </div>
</div>
<div class='worklogs'>
  
    <div class='main' style='margin:auto;'>
        <form method='post'>
            <div class='row' style='margin-bottom:5px;'>
                <?php echo html::hidden('userRootId', $userRootId);?>
                <?php if($orgType == 'amiba' || $orgType == 'group'):?>
                <div class='col-sm-3'>
                    <div class='input-group input-group-sm'>
                      <span class='input-group-addon'><?php echo $lang->report->amiba_name;?></span>
                      <?php echo html::select('amibaName', $amibaNames, $amibaName, "class='form-control chosen' onchange='changeParams(this)'");?>
                    </div>
                </div>
                <?php endif?>
                <?php if($orgType == 'group'):?>
                <div class='col-sm-3'>
                    <div class='input-group input-group-sm'>
                      <span class='input-group-addon'><?php echo $lang->report->group_name;?></span>
                      <?php echo html::select('groupName', $groupNames, $groupName, "class='form-control chosen' onchange='changeParams(this)'");?>
                    </div>
                </div>
                <?php endif?>
                <?php if($orgType == 'person'):?>
                <div class='col-sm-3'>
                    <div class='input-group input-group-sm'>
                      <span class='input-group-addon'><?php echo $lang->report->realname;?></span>
                      <?php echo html::select('account', $userNames, $account, "class='form-control chosen' onchange='changeParams(this)'");?>
                    </div>
                </div>                
                <?php endif?>
                <div class='col-sm-3' data-container='body' data-toggle='popover' data-placement='top' data-trigger='hover' data-content='<?php echo $statPeriod;?>'>
                    <div class='input-group input-group-sm'>
                      <span class='input-group-addon'><?php echo $lang->report->month;?></span>
                      <div class='datepicker-wrapper datepicker-date'><?php echo html::input('month', $month, "class='w-100px form-control' onchange='changeParams(this)'");?></div>
                    </div>
                </div>
              </div>
            </div>
          </form>
        <?php 
        // table table-condensed table-fixed table-borderless table-data
        // table table-condensed table-fixed table-bordered table-striped tablesorter active-disabled
        ?>
         <table class='table table-condensed table-fixed table-bordered table-data' id='performanceTable'>
            <tbody>
                <tr class="a-center">
                    <th align='left'></th>
                    <th align='left'>
                    <?php
                    if($orgType == 'amiba') {
                        echo $lang->report->amiba_name;
                    }
                    else if($orgType == 'group') {
                        echo $lang->report->group_name;
                    }
                    else {
                        echo $lang->report->realname;
                    }
                    echo '：';
                    ?>
                    </th>
                    <td align='left'>
                    <?php
                    if($orgType == 'amiba')
                    {
                        echo $amibaName;
                    }
                    else if($orgType == 'group')
                    {
                        echo $amibaName . '/' . $groupName;
                    }
                    else
                    {
                        echo $users[$account];
                    }
                    ?>
                    </td>
                    <th align='left'></th>
                    <td align='left'>
                    </td>
                </tr>
                
                <?php 
                $mergeRowCount = 0;
                $firstRowType = '';
                if($outputInfo->dev_total_output != 0){
                    if($firstRowType == ''){
                        $firstRowType = 'dev';
                    }
                    $mergeRowCount += 7;
                }
                if($outputInfo->req_total_output != 0){
                    if($firstRowType == ''){
                        $firstRowType = 'req';
                    }
                    $mergeRowCount += 6;
                } 
                if($outputInfo->test_total_output != 0){
                    if($firstRowType == ''){
                        $firstRowType = 'test';
                    }
                    $mergeRowCount += 4;
                }
                $firstRowType = '';
                ?>
                <?php if($outputInfo->dev_total_output != 0):?>
                <tr class="a-center">
                    <?php if($firstRowType == 'dev'):?>
                    <th align='left' rowspan="<?php echo $mergeRowCount;?>" class='role'>综合监控产品线</th>
                    <?php endif;?>
                    <th align='left' rowspan='7' class='role'>研发</th>
                    <th align='left'>任务有效输出：</th>
                    <td align='left'>
                    <?php
                    if(!empty($outputInfo->month_wp_task)) {
                        $fontColor = $outputInfo->mod_merge_count > 0 ? 'orange' : '#03c';
                        $monthPerformanceDetailLink = $this->createLink('report', 'monthperformancescoredetail', "scoreType=DevTask&userRootId=$userRootId&amibaName=$amibaName&isAmibaChanged=false&groupName=$groupName&account=$account&orgType=$orgType&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->month_wp_task . '分', '', "class='iframe' data-min-height='800' style='color:$fontColor'");
                    }
                    else {
                        echo '-';
                    }
                    ?>
                    </td>
                    <th align='left'></th>
                    <td align='left'></td>
                </tr>
                
                <tr class="a-center">
                    <th align='left'>PR有效输出：</th>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->month_wp_pr)) {
                        $monthPerformanceDetailLink = $this->createLink('report', 'monthperformancescoredetail', "scoreType=DevBeRejectedAndLeaderCheck&userRootId=$userRootId&amibaName=$amibaName&isAmibaChanged=false&groupName=$groupName&account=$account&orgType=$orgType&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->month_wp_pr . '分', '', "class='iframe'");
                    }
                    else {
                        echo '-';
                    }
                    ?>
                    </td>
                    <th align='left'></th>
                    <td align='left'></td>
                </tr>
                
                <tr class="a-center">
                    <td align='right'>PR被通过次数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->month_pq_passed)){
                        echo $outputInfo->month_pq_passed . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                    <td align='right'>PR被驳回次数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->month_pq_reject)){
                        echo $outputInfo->month_pq_reject . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                </tr>
                <tr class="a-center">
                    <td align='right'>组长PR通过次数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->month_leader_pq_passed)){
                        echo $outputInfo->month_leader_pq_passed . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                    <td align='right'>组长PR驳回次数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->month_leader_pq_reject)){
                        echo $outputInfo->month_leader_pq_reject . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                </tr>
                
                <tr class="a-center">
                    <th align='left'>Bug有效输出：</th>
                    <td align='left'>
                    <?php
                    if(!empty($outputInfo->month_wp_bug))
                    {
                        $monthPerformanceDetailLink = $this->createLink('report', 'monthperformancescoredetail', "scoreType=DevBug&userRootId=$userRootId&amibaName=$amibaName&isAmibaChanged=false&groupName=$groupName&account=$account&orgType=$orgType&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->month_wp_bug . '分', '', "class='iframe'");
                    }
                    else
                    {
                        echo '-';
                    }
                    ?>
                    </td>
                    <th align='left'></th>
                    <td align='left'></td>
                </tr>
                <tr class="a-center">
                    <td align='right'>修改自测Bug数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->month_bug_inside_fix)){
                        echo $outputInfo->month_bug_inside_fix . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                    <td align='right'>产生自测Bug数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->month_bug_inside_create)){
                        echo $outputInfo->month_bug_inside_create . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                </tr>
                
                <tr class="a-center">
                    <td align='right'>修改现场Bug数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->month_bug_province_fix)){
                        echo $outputInfo->month_bug_province_fix . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                    <td align='right'>产生现场Bug数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->month_bug_province_create)){
                        echo $outputInfo->month_bug_province_create . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                </tr>
                <?php endif;?>
                
                <?php if($outputInfo->req_total_output != 0):?>
                <tr class="a-center">
                    <?php if($firstRowType == 'req'):?>
                    <th align='left' rowspan="<?php echo $mergeRowCount;?>" class='role'>综合监控产品线</th>
                    <?php endif;?>
                    <th align='left' rowspan='6' class='role'>需求</th>
                    <th align='right'>评审通过得分：</th>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->reviewed_store_wp))
                    {
                        $monthPerformanceDetailLink = $this->createLink('report', 'monthperformancescoredetail', "scoreType=OncePass&userRootId=$userRootId&amibaName=$amibaName&isAmibaChanged=false&groupName=$groupName&account=$account&orgType=$orgType&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->reviewed_store_wp . '分', '', "class='iframe'");
                    }
                    else
                    {
                        echo '-';
                    }
                    ?>
                    </td>
                    <td align='right'></td>
                    <td align='left'></td>
                </tr>
                <tr class="a-center">
                    <td align='right'>评审一次通过数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->onetimepass_store)){
                        echo $outputInfo->onetimepass_store . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                    <td align='right'>评审多次通过数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->repeatedpass_store)){
                        echo $outputInfo->repeatedpass_store . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                </tr>
                
                <tr class="a-center">
                    <th align='right'>有效输出得分（根据研发得分）：</th>
                    <td align='left'>
                    <?php
                    if(!empty($outputInfo->store_rel_task_wp) && $outputInfo->store_rel_task_wp != '0.00')
                    {
                        $monthPerformanceDetailLink = $this->createLink('report', 'monthperformancescoredetail', "scoreType=ReqDev&userRootId=$userRootId&amibaName=$amibaName&isAmibaChanged=false&groupName=$groupName&account=$account&orgType=$orgType&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->store_rel_task_wp . '分', '', "class='iframe'");
                    }
                    else
                    {
                        echo '-';
                    }
                    ?>
                    </td>
                    <td align='right'></td>
                    <td align='left'></td>
                </tr>
                <tr class="a-center">
                    <th align='right'>现场bug承担责任总分：</th>
                    <td align='left'>
                    <?php 
                    if(empty($outputInfo->story_province_bug_response_wp))
                    {
                        echo '-';
                    }
                    else
                    {
                        $monthPerformanceDetailLink = $this->createLink('report', 'monthperformancescoredetail', "scoreType=ProvinceBugReqResponse&userRootId=$userRootId&amibaName=$amibaName&isAmibaChanged=false&groupName=$groupName&account=$account&orgType=$orgType&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->story_province_bug_response_wp . '分', '', "class='iframe'");
                    }
                    ?>
                    </td>
                    <td align='right'>现场bug承担责任个数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->story_province_bug_response)){
                        echo $outputInfo->story_province_bug_response . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                </tr>
                <tr class="a-center">
                    <th align='right'>内部bug承担责任总分：</th>
                    <td align='left'>
                    <?php 
                    if(empty($outputInfo->story_inside_bug_response_wp))
                    {
                        echo '-';
                    }
                    else
                    {
                        $monthPerformanceDetailLink = $this->createLink('report', 'monthperformancescoredetail', "scoreType=InsideBugReqResponse&userRootId=$userRootId&amibaName=$amibaName&isAmibaChanged=false&groupName=$groupName&account=$account&orgType=$orgType&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->story_inside_bug_response_wp . '分', '', "class='iframe'");
                    }
                    ?>
                    </td>
                    <td align='right'>内部bug承担责任个数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->story_inside_bug_response)){
                        echo $outputInfo->story_inside_bug_response . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                </tr>
                

                <tr class="a-center">
                    <th align='right'>现场满意度得分：</th>
                    <td align='left'>
                    <?php 
                    if(empty($outputInfo->store_val_wp) || $outputInfo->store_val_wp == '0.00')
                    {
                        echo '-';
                    }
                    else
                    {
                        $monthPerformanceDetailLink = $this->createLink('report', 'monthperformancescoredetail', "scoreType=ReqSatisfy&userRootId=$userRootId&amibaName=$amibaName&isAmibaChanged=false&groupName=$groupName&account=$account&orgType=$orgType&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->store_val_wp . '分', '', "class='iframe'");
                    }
                    ?>
                    </td>
                    <td align='right'></td>
                    <td align='left'></td>
                </tr>
                <?php endif;?>
                <?php if($outputInfo->test_total_output != 0):?>
                <tr class="a-center">
                    <?php if($firstRowType == 'test'):?>
                    <th align='left' rowspan="<?php echo $mergeRowCount;?>" class='role'>综合监控产品线</th>
                    <?php endif;?>
                    <th align='left' rowspan='4' class='role'>测试</th>
                    <th align='right'>测试人员用例得分：</th>
                    <td align='left'>
                    <?php 
                    if(empty($outputInfo->test_case_wp))
                    {
                        echo '-';
                    }
                    else
                    {
                        $monthPerformanceDetailLink = $this->createLink('report', 'monthperformancescoredetail', "scoreType=TestCase&userRootId=$userRootId&amibaName=$amibaName&isAmibaChanged=false&groupName=$groupName&account=$account&orgType=$orgType&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->test_case_wp . '分', '', "class='iframe'");
                    }
                    ?></td>
                    <td align='right'>测试人员用例个数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->test_case_count)){
                        echo $outputInfo->test_case_count . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                </tr>
                
                <tr class="a-center">
                    <th align='right'>测试人员上报bug得分：</th>
                    <td align='left'>
                    <?php 
                    if(empty($outputInfo->test_bug_wp))
                    {
                        echo '-';
                    }
                    else
                    {
                        $monthPerformanceDetailLink = $this->createLink('report', 'monthperformancescoredetail', "scoreType=TestBug&userRootId=$userRootId&amibaName=$amibaName&isAmibaChanged=false&groupName=$groupName&account=$account&orgType=$orgType&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->test_bug_wp . '分', '', "class='iframe'");
                    }
                    ?>
                    </td>
                    <td align='right'>测试人员上报bug数量：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->test_bug_count)){
                        echo $outputInfo->test_bug_count . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                </tr>
                <tr class="a-center">
                    <th align='right'>现场bug承担责任总分：</th>
                    <td align='left'>
                    <?php 
                    if(empty($outputInfo->bug_province_bug_response_wp))
                    {
                        echo '-';
                    }
                    else
                    {
                        $monthPerformanceDetailLink = $this->createLink('report', 'monthperformancescoredetail', "scoreType=ProvinceBugTestResponse&userRootId=$userRootId&amibaName=$amibaName&isAmibaChanged=false&groupName=$groupName&account=$account&orgType=$orgType&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->bug_province_bug_response_wp . '分', '', "class='iframe'");
                    }
                    ?>
                    </td>
                    <td align='right'>现场bug承担责任个数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->bug_province_bug_response)){
                        echo $outputInfo->bug_province_bug_response . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                </tr>
                <tr class="a-center">
                    <th align='right'>内部bug承担责任总分：</th>
                    <td align='left'>
                    <?php 
                    if(empty($outputInfo->bug_inside_bug_response_wp))
                    {
                        echo '-';
                    }
                    else
                    {
                        $monthPerformanceDetailLink = $this->createLink('report', 'monthperformancescoredetail', "scoreType=InsideBugTestResponse&userRootId=$userRootId&amibaName=$amibaName&isAmibaChanged=false&groupName=$groupName&account=$account&orgType=$orgType&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->bug_inside_bug_response_wp . '分', '', "class='iframe'");
                    }
                    ?>
                    </td>
                    <td align='right'>内部bug承担责任个数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->bug_inside_bug_response)){
                        echo $outputInfo->bug_inside_bug_response . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                </tr>
                <?php endif;?>
            </tbody>
          </table> 
    </div>
</div>
<?php 
include '../../../common/view/footer.lite.html.php';
?>
