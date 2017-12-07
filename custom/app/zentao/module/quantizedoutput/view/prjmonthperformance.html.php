<?php
/**feature-1509**/
include '../../common/view/header.lite.html.php';
include '../../common/view/chart.html.php';
include '../../common/view/datepicker.html.php';
include '../../common/view/chosen.html.php';

$statPeriod = "统计周期:工作日" . $workDayCount . "天" . "(" . date('Y-m-d', strtotime($begin)) . "~" . date('Y-m-d', strtotime($end)) . ")";
$monthNum = date('Ym',strtotime($month));
?>
<div id='titlebar'>
  <div class='heading'>
    <?php 
    echo "<span class='tendency-icon' style='background-image:url(" . $defaultTheme . "images/ext/stat-feature-1509.png)'></span>";
    
    ?>
    <small class='text-muted'> <?php echo $title;?></small>
  </div>
</div>
<div class='worklogs'>
  
    <div class='main' style='margin:auto;'>
        <?php if($tag != 'skipGroupId'):?>
        <form method='post'>
            <div class='row' style='margin-bottom:5px;'>
                <div class='col-sm-3'>
                    <div class='input-group input-group-sm'>
                      <span class='input-group-addon'><?php echo $lang->projectCommon;?></span>
                      <?php echo html::select('amibaId', $amibaNameDict, $amibaId, "class='form-control chosen' onchange='changeParams(this)'");?>
                    </div>
                </div>
                <div class='col-sm-3'>
                    <div class='input-group input-group-sm'>
                      <span class='input-group-addon'><?php echo $lang->productCommon;?></span>
                      <?php echo html::select('groupId', $groupNameDict, $groupId, "class='form-control chosen' onchange='changeParams(this)'");?>
                    </div>
                </div>
                <div class='col-sm-3'>
                    <div class='input-group input-group-sm'>
                      <span class='input-group-addon'><?php echo $lang->quantizedoutput->realname;?></span>
                      <?php echo html::select('account', $userNameDict, $account, "class='form-control chosen' onchange='changeParams(this)'");?>
                    </div>
                </div>                
                <div class='col-sm-3' data-container='body' data-toggle='popover' data-placement='top' data-trigger='hover' data-content='<?php echo $statPeriod;?>'>
                    <div class='input-group input-group-sm'>
                      <span class='input-group-addon'><?php echo $lang->quantizedoutput->month;?></span>
                      <div class='datepicker-wrapper datepicker-date'><?php echo html::input('month', $month, "class='w-100px form-control' onchange='changeParams(this)'");?></div>
                    </div>
                </div>
              </div>
            </div>
          </form>
          <?php endif;?>
        <?php 
        // table table-condensed table-fixed table-borderless table-data
        // table table-condensed table-fixed table-bordered table-striped tablesorter active-disabled
        ?>
         <table class='table table-condensed table-fixed table-bordered table-data' id='performanceTable'>
            <tbody>
                <?php if($outputInfo->dev_total_output != 0):?>
                <tr class="a-center">
                    <th align='left' rowspan='7' class='role'>研发</th>
                    <th align='left'>任务有效输出：</th>
                    <td align='left'>
                    <?php
                    if(!empty($outputInfo->month_wp_task)) {
                        $fontColor = $outputInfo->mod_merge_count > 0 ? 'orange' : '#03c';
                        $monthPerformanceDetailLink = $this->createLink('quantizedoutput', 'prjmonthperformancescoredetail', 
                            "scoreType=DevTask&amibaId=$amibaId&groupId=$groupId&account=$account&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->month_wp_task . '分', '',
                            "class='iframe' data-min-height='800' style='color:$fontColor'");
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
                    <th align='left'>审核(PR)有效输出：</th>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->month_wp_pr)) {
                        $monthPerformanceDetailLink = $this->createLink('quantizedoutput', 'prjmonthperformancescoredetail', 
                            "scoreType=DevBeRejectedAndLeaderCheck&amibaId=$amibaId&groupId=$groupId&account=$account&monthNum=$monthNum");
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
                    <td align='right'>审核被通过次数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->month_pq_passed)){
                        echo $outputInfo->month_pq_passed . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                    <td align='right'>审核被驳回次数：</td>
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
                    <td align='right'>组长审核通过次数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->month_leader_pq_passed)){
                        echo $outputInfo->month_leader_pq_passed . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                    <td align='right'>组长审核驳回次数：</td>
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
                        $monthPerformanceDetailLink = $this->createLink('quantizedoutput', 'prjmonthperformancescoredetail',
                            "scoreType=DevBug&amibaId=$amibaId&groupId=$groupId&account=$account&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->month_wp_bug . '分', '', "class='iframe'");
                    }
                    else
                    {
                        echo '-';
                    }
                    ?>
                    </td>
                    <td align='right'>修改问题平台bug个数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->it_bug_fix)){
                        echo $outputInfo->it_bug_fix . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
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
                    <th align='left' rowspan='7' class='role'>需求</th>
                    <th align='right'>评审通过得分：</th>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->reviewed_store_wp))
                    {
                        $monthPerformanceDetailLink = $this->createLink('quantizedoutput', 'prjmonthperformancescoredetail',
                            "scoreType=ReviewPass&amibaId=$amibaId&groupId=$groupId&account=$account&monthNum=$monthNum");
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
                    <td align='right'>评审一次通过个数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->onetimepass_store)){
                        echo $outputInfo->onetimepass_store . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                    <td align='right'>评审多次通过个数：</td>
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
                    <th align='right'>需求平台分析需求得分：</th>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->itReq_wp))
                    {
                        $monthPerformanceDetailLink = $this->createLink('quantizedoutput', 'prjmonthperformancescoredetail', 
                            "scoreType=ITReq&amibaId=$amibaId&groupId=$groupId&account=$account&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->itReq_wp . '分', '', "class='iframe'");
                    }
                    else
                    {
                        echo '-';
                    }
                    ?>
                    </td>
                    <td align='right'>需求平台分析需求个数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->itReq_count)){
                        echo $outputInfo->itReq_count . '个';
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
                        $monthPerformanceDetailLink = $this->createLink('quantizedoutput', 'prjmonthperformancescoredetail', 
                            "scoreType=ReqDev&amibaId=$amibaId&groupId=$groupId&account=$account&monthNum=$monthNum");
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
                        $monthPerformanceDetailLink = $this->createLink('quantizedoutput', 'prjmonthperformancescoredetail', 
                            "scoreType=ProvinceBugReqResponse&amibaId=$amibaId&groupId=$groupId&account=$account&monthNum=$monthNum");
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
                        $monthPerformanceDetailLink = $this->createLink('quantizedoutput', 'prjmonthperformancescoredetail', 
                            "scoreType=InsideBugReqResponse&amibaId=$amibaId&groupId=$groupId&account=$account&monthNum=$monthNum");
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
                        $monthPerformanceDetailLink = $this->createLink('quantizedoutput', 'prjmonthperformancescoredetail', 
                            "scoreType=ReqSatisfy&amibaId=$amibaId&groupId=$groupId&account=$account&monthNum=$monthNum");
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
                    <th align='left' rowspan='6' class='role'>测试</th>
                    <th align='right'>创建用例得分：</th>
                    <td align='left'>
                    <?php 
                    if(empty($outputInfo->test_case_wp))
                    {
                        echo '-';
                    }
                    else
                    {
                        $monthPerformanceDetailLink = $this->createLink('quantizedoutput', 'prjmonthperformancescoredetail', 
                            "scoreType=TestCase&amibaId=$amibaId&groupId=$groupId&account=$account&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->test_case_wp . '分', '', "class='iframe'");
                    }
                    ?></td>
                    <td align='right'>创建用例个数：</td>
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
                    <th align='right'>执行用例得分：</th>
                    <td align='left'>
                    <?php 
                    if(empty($outputInfo->execute_case_wp))
                    {
                        echo '-';
                    }
                    else
                    {
                        $monthPerformanceDetailLink = $this->createLink('quantizedoutput', 'prjmonthperformancescoredetail', 
                            "scoreType=ExecuteCase&amibaId=$amibaId&groupId=$groupId&account=$account&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->execute_case_wp . '分', '', "class='iframe'");
                    }
                    ?>
                    </td>
                    <td align='right'>执行用例个数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->execute_case_count)){
                        echo $outputInfo->execute_case_count . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                </tr>
                <tr class="a-center">
                    <th align='right'>上报bug得分：</th>
                    <td align='left'>
                    <?php 
                    if(empty($outputInfo->test_bug_wp))
                    {
                        echo '-';
                    }
                    else
                    {
                        $monthPerformanceDetailLink = $this->createLink('quantizedoutput', 'prjmonthperformancescoredetail', 
                            "scoreType=TestBug&amibaId=$amibaId&groupId=$groupId&account=$account&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->test_bug_wp . '分', '', "class='iframe'");
                    }
                    ?>
                    </td>
                    <td align='right'>上报bug个数：</td>
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
                    <th align='right'>关闭Bug得分：</th>
                    <td align='left'>
                    <?php 
                    if(empty($outputInfo->close_bug_wp))
                    {
                        echo '-';
                    }
                    else
                    {
                        $monthPerformanceDetailLink = $this->createLink('quantizedoutput', 'prjmonthperformancescoredetail', 
                            "scoreType=CloseBug&amibaId=$amibaId&groupId=$groupId&account=$account&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->close_bug_wp . '分', '', "class='iframe'");
                    }
                    ?>
                    </td>
                    <td align='right'>关闭Bug个数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->close_bug_count)){
                        echo $outputInfo->close_bug_count . '个';
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
                        $monthPerformanceDetailLink = $this->createLink('quantizedoutput', 'prjmonthperformancescoredetail', 
                            "scoreType=ProvinceBugTestResponse&amibaId=$amibaId&groupId=$groupId&account=$account&monthNum=$monthNum");
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
                        $monthPerformanceDetailLink = $this->createLink('quantizedoutput', 'prjmonthperformancescoredetail', 
                            "scoreType=InsideBugTestResponse&amibaId=$amibaId&groupId=$groupId&account=$account&monthNum=$monthNum");
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
                
                <?php if($outputInfo->qa_total_output != 0):?>
                <tr class="a-center">
                    <th align='left' rowspan='2' class='role'>QA</th>
                    <th align='right'>上报bug得分：</th>
                    <td align='left'>
                    <?php 
                    if(empty($outputInfo->qa_open_bug_wp))
                    {
                        echo '-';
                    }
                    else
                    {
                        $monthPerformanceDetailLink = $this->createLink('quantizedoutput', 'prjmonthperformancescoredetail', 
                            "scoreType=QAOpenBug&amibaId=$amibaId&groupId=$groupId&account=$account&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->qa_open_bug_wp . '分', '', "class='iframe'");
                    }
                    ?></td>
                    <td align='right'>上报bug个数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->qa_open_bug_count)){
                        echo $outputInfo->qa_open_bug_count . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                </tr>
                
                <tr class="a-center">
                    <th align='right'>关闭bug得分：</th>
                    <td align='left'>
                    <?php 
                    if(empty($outputInfo->qa_close_bug_wp))
                    {
                        echo '-';
                    }
                    else
                    {
                        $monthPerformanceDetailLink = $this->createLink('quantizedoutput', 'prjmonthperformancescoredetail', 
                            "scoreType=QACloseBug&amibaId=$amibaId&groupId=$groupId&account=$account&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->qa_close_bug_wp . '分', '', "class='iframe'");
                    }
                    ?></td>
                    <td align='right'>关闭bug个数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->qa_close_bug_count)){
                        echo $outputInfo->qa_close_bug_count . '个';
                    } else {
                        echo '-';
                    }
                    ?>
                    </td>
                </tr>
                <?php endif;?>
                
                <?php if($outputInfo->market_total_output != 0):?>
                <tr class="a-center">
                    <th align='left' rowspan='1' class='role'>市场或售前</th>
                    <th align='right'>工作量拆分任务的得分：</th>
                    <td align='left'>
                    <?php 
                    if(empty($outputInfo->work_split_wp))
                    {
                        echo '-';
                    }
                    else
                    {
                        $monthPerformanceDetailLink = $this->createLink('quantizedoutput', 'prjmonthperformancescoredetail', 
                            "scoreType=WorkSplit&amibaId=$amibaId&groupId=$groupId&account=$account&monthNum=$monthNum");
                        echo html::a($monthPerformanceDetailLink, $outputInfo->work_split_wp . '分', '', "class='iframe'");
                    }
                    ?></td>
                    <td align='right'>工作量拆分任务的个数：</td>
                    <td align='left'>
                    <?php 
                    if(!empty($outputInfo->work_split_count)){
                        echo $outputInfo->work_split_count . '个';
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
echo $outputInfoSql;
include '../../common/view/footer.lite.html.php';
?>
