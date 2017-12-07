<?php
/**feature-1509**/
/**
 * The report module zh-cn file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     report
 * @version     $Id: zh-cn.php 5080 2013-07-10 00:46:59Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
 
/* --------------------------------- menu start --------------------------------- */
$lang->quantizedoutput->common     = '有效输出';
$lang->quantizedoutput->list       = '统计报表';
$lang->quantizedoutput->quantizedoutput = '有效输出';

// 组织维度
$lang->quantizedoutput->t_dayreport = '日报工';
$lang->quantizedoutput->t_monthreport = '月统计';
$lang->quantizedoutput->t_monthsort = '月排名';
$lang->quantizedoutput->t_yearsort = '年排名';
$lang->quantizedoutputList->staff->lists[0]    = '日报工|quantizedoutput|dayreport';
$lang->quantizedoutputList->staff->lists[1]    = '月统计|quantizedoutput|monthreport';
$lang->quantizedoutputList->staff->lists[2]    = '月排名|quantizedoutput|sort|userRootId=&timeType=月';
$lang->quantizedoutputList->staff->lists[3]    = '年排名|quantizedoutput|sort|userRootId=&timeType=年';

// 项目维度
$lang->quantizedoutput->t_prjmonthreport = '月统计';
$lang->quantizedoutput->t_prjmonthsort = '月排名';
$lang->quantizedoutput->t_prjyearsort = '年排名';
$lang->quantizedoutputList->prj->lists[1]    = '月统计|quantizedoutput|prjmonthreport';
$lang->quantizedoutputList->prj->lists[2]    = '月排名|quantizedoutput|prjsort|amibaId=&timeType=月';
$lang->quantizedoutputList->prj->lists[3]    = '年排名|quantizedoutput|prjsort|amibaId=&timeType=年';

// 产品维度
$lang->quantizedoutput->t_prdmonthreport = '月统计';
$lang->quantizedoutput->t_prdmonthsort = '月排名';
$lang->quantizedoutput->t_prdyearsort = '年排名';
$lang->quantizedoutputList->prd->lists[1]    = '月统计|quantizedoutput|prdmonthreport';
$lang->quantizedoutputList->prd->lists[2]    = '月排名|quantizedoutput|prdsort|amibaId=&timeType=月';
$lang->quantizedoutputList->prd->lists[3]    = '年排名|quantizedoutput|prdsort|amibaId=&timeType=年';

/* --------------------------------- menu end --------------------------------- */


/* --------------------------------- holidays start --------------------------------- */
// 节假日
$lang->quantizedoutput->holidays = [
        '20170403',
        '20170404',
        '20170501',
        '20170529',
        '20170530',
        '20171002',
        '20171003',
        '20171004',
        '20171005',
        '20171006',
        '20171007',
        '20171008',
    ];
    
//周末上班日
$lang->quantizedoutput->weekendWorkDays = [
        '20170401',
        '20170527',
        '20170930',
    ];
    
/* --------------------------------- holidays end --------------------------------- */

/* --------------------------------- monthperformancescoredetail start --------------------------------- */
$lang->quantizedoutput->detail = '明细';

// scoreTypeNames
$lang->quantizedoutput->scoreTypeNames['DevTask'] = '任务代码量(研发)';
$lang->quantizedoutput->scoreTypeNames['DevBeRejectedAndLeaderCheck'] = '审核量(研发)';
$lang->quantizedoutput->scoreTypeNames['DevBug'] = 'Bug得分(研发)';
$lang->quantizedoutput->scoreTypeNames['ReviewPass'] = '禅道评审通过(需求)';
$lang->quantizedoutput->scoreTypeNames['ITReq'] = '需求平台的需求分析(需求)';
$lang->quantizedoutput->scoreTypeNames['ReqDev'] = '代码量有效输出(需求)';
$lang->quantizedoutput->scoreTypeNames['InsideBugReqResponse'] = '内部bug承担责任总分(需求)';
$lang->quantizedoutput->scoreTypeNames['ProvinceBugReqResponse'] = '现场bug承担责任总分(需求)';
$lang->quantizedoutput->scoreTypeNames['ReqSatisfy'] = '满意度(需求)';
$lang->quantizedoutput->scoreTypeNames['TestCase'] = '创建用例得分(测试)';
$lang->quantizedoutput->scoreTypeNames['ExecuteCase'] = '执行用例得分(测试)';
$lang->quantizedoutput->scoreTypeNames['TestBug'] = '上报bug得分(测试)';
$lang->quantizedoutput->scoreTypeNames['InsideBugTestResponse'] = '内部bug承担责任总分(测试)';
$lang->quantizedoutput->scoreTypeNames['ProvinceBugTestResponse'] = '现场bug承担责任总分(测试)';
$lang->quantizedoutput->scoreTypeNames['CloseBug'] = '关闭Bug得分(测试)';
$lang->quantizedoutput->scoreTypeNames['QAOpenBug'] = '上报Bug得分(QA)';
$lang->quantizedoutput->scoreTypeNames['QACloseBug'] = '关闭Bug得分(QA)';
$lang->quantizedoutput->scoreTypeNames['WorkSplit'] = '工作量拆分得分(市场或售前)';

// DevTask
$lang->quantizedoutput->belongProject = '所属项目';
$lang->quantizedoutput->sourceBranch = '来源分支';
$lang->quantizedoutput->targetBranch = '目标分支';
$lang->quantizedoutput->prStatus = 'PR状态';
$lang->quantizedoutput->requestDate = '请求日期';
$lang->quantizedoutput->auditDate = '审核日期';
$lang->quantizedoutput->commitPerson = '提交人';
$lang->quantizedoutput->auditPerson = '审核人';
$lang->quantizedoutput->taskId = '任务编号';
$lang->quantizedoutput->calculateProcess = '计算过程';
$lang->quantizedoutput->calculateResult = '计算结果';
$lang->quantizedoutput->mark = '备注';
$lang->quantizedoutput->operation = '操作';

// DevBeRejectedAndLeaderCheck
$lang->quantizedoutput->belongProject      = '所属项目';
$lang->quantizedoutput->sourceBranch         = '来源分支';
$lang->quantizedoutput->targetBranch         = '目标分支';
$lang->quantizedoutput->prStatus          = 'PR状态';
$lang->quantizedoutput->requestDate           = '请求日期';
$lang->quantizedoutput->auditDate            = '审核日期';
$lang->quantizedoutput->commitPerson      = '提交人';
$lang->quantizedoutput->auditPerson          = '审核人';
$lang->quantizedoutput->taskId                 = '任务编号';
$lang->quantizedoutput->calculateProcess     = '计算过程';
$lang->quantizedoutput->calculateResult      = '计算结果';
$lang->quantizedoutput->mark                     = '备注';
$lang->quantizedoutput->operation                 = '操作';

// DevBug
$lang->quantizedoutput->bug_id       = 'Bug编号';
$lang->quantizedoutput->bug_title        = 'Bug标题';
$lang->quantizedoutput->bug_type      = 'Bug类型';
$lang->quantizedoutput->bug_source       = 'Bug来源';
$lang->quantizedoutput->close_time        = '关闭日期';
$lang->quantizedoutput->openedBy      = '创建人';
$lang->quantizedoutput->resolvedDate         = '解决日期';
$lang->quantizedoutput->calc_process     = '计算过程';
$lang->quantizedoutput->calc_result   = '计算结果';

// OncePass
$lang->quantizedoutput->product_name = '关联产品';
$lang->quantizedoutput->story_id = '需求编号';
$lang->quantizedoutput->story_name = '需求名称';
$lang->quantizedoutput->source = '需求来源';
$lang->quantizedoutput->openedby = '创建人';
$lang->quantizedoutput->openeddate = '创建日期';
$lang->quantizedoutput->stage = '所处阶段';
$lang->quantizedoutput->status = '状态';
$lang->quantizedoutput->reviewedby = '评审人';
$lang->quantizedoutput->revieweddate = '评审日期';
$lang->quantizedoutput->passnote = '评审结果';
$lang->quantizedoutput->calc_process = '计算过程';
$lang->quantizedoutput->calc_result = '计算结果';
$lang->quantizedoutput->mark = '备注';

// ReqDev
$lang->quantizedoutput->story_id          = '需求编号';
$lang->quantizedoutput->story_name        = '需求名称';
$lang->quantizedoutput->stage             = '所处阶段';
$lang->quantizedoutput->passnote          = '评审结果';
$lang->quantizedoutput->task_id           = '任务编号';
$lang->quantizedoutput->task_name         = '任务名称';
$lang->quantizedoutput->committer         = '任务完成人员';
$lang->quantizedoutput->check_time        = '代码审核日期';
$lang->quantizedoutput->task_output       = '任务有效输出';
$lang->quantizedoutput->calc_process      = '计算过程';
$lang->quantizedoutput->calc_result       = '计算结果';
$lang->quantizedoutput->mark              = '备注';

// ReqSatisfy
$lang->quantizedoutput->t22222              = '需求编号';
$lang->quantizedoutput->t22222              = '需求名称';
$lang->quantizedoutput->t22222              = '所处阶段';
$lang->quantizedoutput->t22222              = '省内项目经理评价';
$lang->quantizedoutput->t22222              = '任务编号';
$lang->quantizedoutput->t22222              = '任务名称';
$lang->quantizedoutput->t22222              = '任务完成人员';
$lang->quantizedoutput->t22222              = '代码审核日期';
$lang->quantizedoutput->t22222              = '任务有效输出';
$lang->quantizedoutput->t22222              = '计算过程';
$lang->quantizedoutput->t22222              = '计算结果';
$lang->quantizedoutput->t22222              = '备注';

// TestBug
$lang->quantizedoutput->story_id = '需求编号';
$lang->quantizedoutput->story_name = '需求名称';
$lang->quantizedoutput->stage = '所处阶段';
$lang->quantizedoutput->bug_id = 'Bug编号';
$lang->quantizedoutput->bug_title = 'Bug标题';
$lang->quantizedoutput->status = '状态';
$lang->quantizedoutput->resolvedBy = '解决人';
$lang->quantizedoutput->resolvedDate = '解决日期';
$lang->quantizedoutput->calc_process = '计算过程';
$lang->quantizedoutput->calc_result = '计算结果';
$lang->quantizedoutput->mark = '备注';

// TestCase
$lang->quantizedoutput->story_id         = '需求编号';
$lang->quantizedoutput->story_name       = '需求名称';
$lang->quantizedoutput->stage            = '所处阶段';
$lang->quantizedoutput->case_id          = '用例编号';
$lang->quantizedoutput->case_title       = '用例标题';
$lang->quantizedoutput->case_type        = '用例类型';
$lang->quantizedoutput->case_status      = '用例状态';
$lang->quantizedoutput->lastrunner       = '执行人';
$lang->quantizedoutput->lastrundate      = '执行日期';
$lang->quantizedoutput->lastrunresult    = '执行结果';
$lang->quantizedoutput->calc_process     = '计算过程';
$lang->quantizedoutput->calc_result      = '计算结果';
$lang->quantizedoutput->mark             = '备注';
/* --------------------------------- monthperformancescoredetail end --------------------------------- */

/* --------------------------------- sort start --------------------------------- */
$lang->quantizedoutput->PersonTotalTimeTop30 = '月-人总工时-Top30-排名';
$lang->quantizedoutput->PersonAvgTimeTop30 = '月-人均-Top30-排名';
$lang->quantizedoutput->PersonAvgAmibaTimeTop = '月-人均一级组织-排名';

$lang->quantizedoutput->more = '更多';
/* --------------------------------- sort end --------------------------------- */

/* --------------------------------- dayreport start --------------------------------- */
// 日报工界面
$lang->quantizedoutput->amiba_name = '一级组织';
$lang->quantizedoutput->group_name = '二级组织';
$lang->quantizedoutput->person = '个人';
$lang->quantizedoutput->realname = '姓名';
$lang->quantizedoutput->day = '日';
$lang->quantizedoutput->total_time = '实报工时';
/* --------------------------------- dayreport end --------------------------------- */

/* --------------------------------- monthreport start --------------------------------- */
// 月统计界面
$lang->quantizedoutput->month = '月';
$lang->quantizedoutput->day_avg_time = '日均工时';
$lang->quantizedoutput->extra_time = '额外工时';
$lang->quantizedoutput->total_output = '实际输出';
$lang->quantizedoutput->extra_output = '额外输出';
$lang->quantizedoutput->day_avg_output = '日均输出';
$lang->quantizedoutput->output_efficiency = '输出效率';
$lang->quantizedoutput->name = '名称';
$lang->quantizedoutput->output = '输出';
$lang->quantizedoutput->tasktime = '工时';
$lang->quantizedoutput->efficiency = '效率';
$lang->quantizedoutput->real = '实际';
$lang->quantizedoutput->total = '总';
$lang->quantizedoutput->quantizedoutputed = '实报';
$lang->quantizedoutput->avg = '平均';
$lang->quantizedoutput->person_avg = '人均';
$lang->quantizedoutput->day_avg = '日均';
$lang->quantizedoutput->extra = '额外';
$lang->quantizedoutput->standard = '标准';

$lang->quantizedoutput->year = '月';
/* --------------------------------- monthreport end --------------------------------- */

/* --------------------------------- prjmonthreport start --------------------------------- */
$lang->quantizedoutput->account = '账号';
$lang->quantizedoutput->project_name = '项目';
$lang->quantizedoutput->product_name = '产品';
/* --------------------------------- prjmonthreport end --------------------------------- */

/* --------------------------------- timetendency start --------------------------------- */
$lang->quantizedoutput->deptallperson = '全体';
$lang->quantizedoutput->deptalltime = '总工时';
$lang->quantizedoutput->timeTendency = '报工趋势';
// 趋势标题-日报工
$lang->quantizedoutput->personDayTendency = '人-日-报工趋势';
$lang->quantizedoutput->groupDayTendency = '组-日-报工趋势';
$lang->quantizedoutput->amibaDayTendency = '一级组织-日-报工趋势';
// 趋势标题-月统计
$lang->quantizedoutput->personMonthTendency = '人-月-报工趋势';
$lang->quantizedoutput->groupMonthTendency = '组-月-报工趋势';
$lang->quantizedoutput->amibaMonthTendency = '一级组织-月-报工趋势';

$lang->quantizedoutput->dateSect = '区间';
/* --------------------------------- timetendency end --------------------------------- */

/* --------------------------------- worklogs start--------------------------------- */
// 报工-详情列表
$lang->quantizedoutput->worklog->task_type_name = '任务类型';
$lang->quantizedoutput->worklog->task_id = '编号';
$lang->quantizedoutput->worklog->name = '名称';
$lang->quantizedoutput->worklog->time_sect = '工时';
$lang->quantizedoutput->worklog->work_content = '工作内容';

$lang->quantizedoutput->zentao->story = '禅道需求';
$lang->quantizedoutput->zentao->task = '禅道任务';
$lang->quantizedoutput->zentao->bug = '禅道Bug';
$lang->quantizedoutput->zentao->reqplat = '需求平台';
$lang->quantizedoutput->zentao->queplat = '禅道问题';
/* --------------------------------- worklogs end --------------------------------- */
