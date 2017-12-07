<?php 
/**feature-1077**/
/**feature-1245**/

/* ---------------------------------holidays--------------------------------- */
// 节假日
$lang->report->holidays = [
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
    ];
    
//周末上班日
$lang->report->weekendWorkDays = [
        '20170401',
        '20170527',
        '20170930',
    ];
    
/* ---------------------------------monthperformance--------------------------------- */
$lang->report->monthperformance = '月有效输出';

/* ---------------------------------monthperformancescoredetail--------------------------------- */
$lang->report->detail = '明细';

// scoreTypeNames
$lang->report->scoreTypeNames['DevTask'] = '任务代码量(研发)';
$lang->report->scoreTypeNames['DevBeRejectedAndLeaderCheck'] = '审核量(研发)';
$lang->report->scoreTypeNames['DevBug'] = 'Bug得分(研发)';
$lang->report->scoreTypeNames['OncePass'] = '一次评审(需求)';
$lang->report->scoreTypeNames['ReqDev'] = '代码量有效输出(需求)';
$lang->report->scoreTypeNames['InsideBugReqResponse'] = '内部bug承担责任总分(需求)';
$lang->report->scoreTypeNames['ProvinceBugReqResponse'] = '现场bug承担责任总分(需求)';
$lang->report->scoreTypeNames['ReqSatisfy'] = '满意度(需求)';
$lang->report->scoreTypeNames['TestCase'] = '测试用例得分(测试)';
$lang->report->scoreTypeNames['TestBug'] = '测试上报bug得分(测试)';
$lang->report->scoreTypeNames['InsideBugTestResponse'] = '内部bug承担责任总分(测试)';
$lang->report->scoreTypeNames['ProvinceBugTestResponse'] = '现场bug承担责任总分(测试)';

// DevTask
$lang->report->belongProject = '所属项目';
$lang->report->sourceBranch = '来源分支';
$lang->report->targetBranch = '目标分支';
$lang->report->prStatus = 'PR状态';
$lang->report->requestDate = '请求日期';
$lang->report->auditDate = '审核日期';
$lang->report->commitPerson = '提交人';
$lang->report->auditPerson = '审核人';
$lang->report->taskId = '任务编号';
$lang->report->calculateProcess = '计算过程';
$lang->report->calculateResult = '计算结果';
$lang->report->mark = '备注';
$lang->report->operation = '操作';

// DevBeRejectedAndLeaderCheck
$lang->report->belongProject      = '所属项目';
$lang->report->sourceBranch         = '来源分支';
$lang->report->targetBranch         = '目标分支';
$lang->report->prStatus          = 'PR状态';
$lang->report->requestDate           = '请求日期';
$lang->report->auditDate            = '审核日期';
$lang->report->commitPerson      = '提交人';
$lang->report->auditPerson          = '审核人';
$lang->report->taskId                 = '任务编号';
$lang->report->calculateProcess     = '计算过程';
$lang->report->calculateResult      = '计算结果';
$lang->report->mark                     = '备注';
$lang->report->operation                 = '操作';

// DevBug
$lang->report->bug_id       = 'Bug编号';
$lang->report->bug_title        = 'Bug标题';
$lang->report->bug_type      = 'Bug类型';
$lang->report->bug_source       = 'Bug来源';
$lang->report->openedDate        = '创建日期';
$lang->report->openedBy      = '创建人';
$lang->report->resolvedDate         = '解决日期';
$lang->report->calc_process     = '计算过程';
$lang->report->calc_result   = '计算结果';

// OncePass
$lang->report->product_name = '关联产品';
$lang->report->story_id = '需求编号';
$lang->report->story_name = '需求名称';
$lang->report->source = '需求来源';
$lang->report->openedby = '创建人';
$lang->report->openeddate = '创建日期';
$lang->report->stage = '所处阶段';
$lang->report->status = '状态';
$lang->report->reviewedby = '评审人';
$lang->report->revieweddate = '评审日期';
$lang->report->passnote = '评审结果';
$lang->report->calc_process = '计算过程';
$lang->report->calc_result = '计算结果';
$lang->report->mark = '备注';

// ReqDev
$lang->report->story_id          = '需求编号';
$lang->report->story_name        = '需求名称';
$lang->report->stage             = '所处阶段';
$lang->report->passnote          = '评审结果';
$lang->report->task_id           = '任务编号';
$lang->report->task_name         = '任务名称';
$lang->report->committer         = '任务完成人员';
$lang->report->check_time        = '代码审核日期';
$lang->report->task_output       = '任务有效输出';
$lang->report->calc_process      = '计算过程';
$lang->report->calc_result       = '计算结果';
$lang->report->mark              = '备注';

// ReqSatisfy
$lang->report->t22222              = '需求编号';
$lang->report->t22222              = '需求名称';
$lang->report->t22222              = '所处阶段';
$lang->report->t22222              = '省内项目经理评价';
$lang->report->t22222              = '任务编号';
$lang->report->t22222              = '任务名称';
$lang->report->t22222              = '任务完成人员';
$lang->report->t22222              = '代码审核日期';
$lang->report->t22222              = '任务有效输出';
$lang->report->t22222              = '计算过程';
$lang->report->t22222              = '计算结果';
$lang->report->t22222              = '备注';

// TestBug
$lang->report->story_id = '需求编号';
$lang->report->story_name = '需求名称';
$lang->report->stage = '所处阶段';
$lang->report->bug_id = 'Bug编号';
$lang->report->bug_title = 'Bug标题';
$lang->report->status = '状态';
$lang->report->resolvedBy = '解决人';
$lang->report->resolvedDate = '解决日期';
$lang->report->calc_process = '计算过程';
$lang->report->calc_result = '计算结果';
$lang->report->mark = '备注';

// TestCase
$lang->report->story_id         = '需求编号';
$lang->report->story_name       = '需求名称';
$lang->report->stage            = '所处阶段';
$lang->report->case_id          = '用例编号';
$lang->report->case_title       = '用例标题';
$lang->report->case_type        = '用例类型';
$lang->report->case_status      = '用例状态';
$lang->report->lastrunner       = '执行人';
$lang->report->lastrundate      = '执行日期';
$lang->report->lastrunresult    = '执行结果';
$lang->report->calc_process     = '计算过程';
$lang->report->calc_result      = '计算结果';
$lang->report->mark             = '备注';

/* ---------------------------------sort--------------------------------- */
$lang->report->PersonTotalTimeTop30 = '月-人总工时-Top30-排名';
$lang->report->PersonAvgTimeTop30 = '月-人均-Top30-排名';
$lang->report->PersonAvgAmibaTimeTop = '月-人均阿米巴-排名';

$lang->report->more = '更多';
$lang->report->sort = '排名';

/* ---------------------------------report--------------------------------- */
// 日报工界面
$lang->report->amiba_name = '阿米巴';
$lang->report->group_name = '组名';
$lang->report->group = '组';
$lang->report->person = '个人';
$lang->report->realname = '姓名';
$lang->report->day = '日';
$lang->report->total_time = '实报工时';
// 月报工界面
$lang->report->month = '月';
$lang->report->day_avg_time = '日均工时';
$lang->report->extra_time = '额外工时';
$lang->report->total_output = '实际输出';
$lang->report->extra_output = '额外输出';
$lang->report->day_avg_output = '日均输出';
$lang->report->output_efficiency = '输出效率';
$lang->report->name = '名称';
$lang->report->output = '输出';
$lang->report->tasktime = '工时';
$lang->report->efficiency = '效率';
$lang->report->real = '实际';
$lang->report->total = '总';
$lang->report->reported = '实报';
$lang->report->avg = '平均';
$lang->report->person_avg = '人均';
$lang->report->day_avg = '日均';
$lang->report->extra = '额外';
$lang->report->standard = '标准';

$lang->report->year = '月';

// 报工菜单
$lang->report->dayreport = '日报工';
$lang->report->monthreport = '月报工';
$lang->report->monthreportexport = '月报工导出';
$lang->report->sort = '排名';
$lang->report->sortmore = '排名更多';
$lang->report->monthsort = '月排名';
$lang->report->monthsortmore = '月排名更多';
$lang->report->yearsort = '年排名';
$lang->report->yearsortmore = '年排名更多';
$lang->report->monthperformance = '有效输出';
$lang->report->monthperformancescoredetail = '有效输出得分详情';

$lang->report->timetendency = '趋势分析';
$lang->report->worklogs = '报工详情';

$lang->reportList->work->lists[0]    = '日报工|report|dayreport';
$lang->reportList->work->lists[1]    = '月报工|report|monthreport';
$lang->reportList->work->lists[2]    = '月排名|report|sort|userRootId=&timeType=月';
$lang->reportList->work->lists[3]    = '年排名|report|sort|userRootId=&timeType=年';

/* ---------------------------------timetendency--------------------------------- */
$lang->report->deptallperson = '全体';
$lang->report->deptalltime = '总工时';
$lang->report->timeTendency = '报工趋势';
// 趋势标题-日报工
$lang->report->personDayTendency = '人-日-报工趋势';
$lang->report->groupDayTendency = '组-日-报工趋势';
$lang->report->amibaDayTendency = '阿米巴-日-报工趋势';
// 趋势标题-月报工
$lang->report->personMonthTendency = '人-月-报工趋势';
$lang->report->groupMonthTendency = '组-月-报工趋势';
$lang->report->amibaMonthTendency = '阿米巴-月-报工趋势';

$lang->report->dateSect = '区间';

/* ---------------------------------worklogs--------------------------------- */
// 报工-详情列表
$lang->report->worklog->task_type_name = '任务类型';
$lang->report->worklog->task_id = '编号';
$lang->report->worklog->name = '名称';
$lang->report->worklog->time_sect = '工时';
$lang->report->worklog->work_content = '工作内容';

$lang->report->zentao->story = '禅道需求';
$lang->report->zentao->task = '禅道任务';
$lang->report->zentao->bug = '禅道Bug';
$lang->report->zentao->reqplat = '需求平台';
$lang->report->zentao->queplat = '禅道问题';
/* ---------------------------------end--------------------------------- */