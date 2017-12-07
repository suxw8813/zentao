<?php
/**feature-1245**/
/* 有效输出统计服务地址 */
$config->report->performanceServiceUrls["te"] = "http://182.18.57.7:8501/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->report->performanceServiceUrls["wx"] = "http://182.18.57.7:8502/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->report->performanceServiceUrls["ty"] = "http://182.18.57.7:8503/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->report->performanceServiceUrls["dj"] = "http://182.18.57.7:8504/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->report->performanceServiceUrls["eo"] = "http://182.18.57.7:8505/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->report->performanceServiceUrls["mi"] = "http://182.18.57.7:8506/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->report->performanceServiceUrls["dg"] = "http://182.18.57.7:8507/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->report->performanceServiceUrls["xy"] = "http://182.18.57.7:8509/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->report->performanceServiceUrls["yc"] = "http://182.18.57.7:8510/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->report->performanceServiceUrls["ww"] = "http://182.18.57.7:8511/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->report->performanceServiceUrls["ossh"] = "http://182.18.57.7:8508/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";

$config->report->mergeInfoSql = "
select 
        a.amiba_name,
        a.group_name,
        b.account, 
        b.project_name /* 所属项目 */,
        b.source_branch /* 来源分支 */,
        b.target_branch /* 目标分支 */,
        b.state /* PR状态 */,
        b.pr_time /* 请求日期 */,
        b.check_time /* 审核日期 */,
        b.committer /* 提交人 */,
        b.auditor /* 审核人 */,
        b.month_wp_task /* 计算结果 */,
        b.web_url,
        b.pr_url,
        b.git_id,
        b.task_id /* 任务编号 */,
        b.pr_desc /* 备注 */,
        b.merge_id,
        b.calc_process,
        b.calc_result
    from
        (/* 归属到阿米巴的禅道用户 */
        select
            d.name as amiba_name,
            d.name as group_name,
            u.realname,
            u.account
        from 
            zt_user u,
            zt_dept d 
        where 
            u.dept = d.id and u.showreportwork = 'y'
            and d.path like concat(',', '#{userRootId}', ',%')
            and d.grade = 2
            

        union all
        /* 归属的组的禅道用户 */
        select
            d.amiba_name,
            d.group_name,
            u.realname,
            u.account
        from 
            zt_user u,
            (select 
                d.id as dept_id1,
                e.id as dept_id2,
                d.name as amiba_name,
                e.name as group_name
            from 
                zt_dept d 
                left join zt_dept e on d.id = e.parent
            where 
                d.path like concat(',', '#{userRootId}', ',%')
                and d.grade = 2
                
            ) d
        where 
            u.dept = d.dept_id2 and u.showreportwork = 'y'
        ) a
        left join (select 
            g.id git_id,
            w.pr_date,
            w.account,
            g.project_name /* 所属项目 */,
            g.source_branch /* 来源分支 */,
            g.target_branch /* 目标分支 */,
            g.state /* PR状态 */,
            g.pr_time /* 请求日期 */,
            g.check_time /* 审核日期 */,
            g.committer /* 提交人 */,
            g.auditor /* 审核人 */,
            w.month_wp_task /* 计算结果 */,
            g.web_url,
            g.pr_url,
            g.task_id /* 任务编号 */,
            g.pr_desc /* 备注 */,
            p.act_id merge_id,
            p.calc_process,
            p.calc_result
        from 
            bi_report_wp_byaccount w
            inner join dw_gitlab_pr g on g.committer = w.account
            inner join bi_calculate_process p on p.act_id = g.id
            inner join zt_task t on g.task_id = concat(t.id, '')
        where 
            p.calc_type = 'DevTask'
            and p.act_id = '#{mergeId}'
        ) b on a.account = b.account
    where 
        b.merge_id = '#{mergeId}'";
        

$config->report->mergeDetailInfoSql = "
select 
    c.id,
    c.related_pr_cuid,
    c.file_type,
    c.file_count,
    c.line_add,
    c.line_del,
    c.remark,
    cm.id as id_new,
    cm.related_pr_cuid as related_pr_cuid_new,
    cm.file_type as file_type_new,
    cm.file_count as file_count_new,
    cm.line_add as line_add_new,
    cm.line_del as line_del_new,
    cm.remark as remark_new,
    cm.modifier as modifier_new,
    cm.time_stamp as time_stamp_new
from 
    dw_gitlab_code c
    left join dw_gitlab_code_mod cm on c.related_pr_cuid = cm.related_pr_cuid and c.file_type = cm.file_type
where 
    c.related_pr_cuid = '#{mergeId}'
order by c.line_add desc, c.file_count desc";

$config->report->deleteModRecordSql = "
delete 
from 
    dw_gitlab_code_mod 
where 
    related_pr_cuid='#{related_pr_cuid}' 
    and file_type='#{file_type}'";
    
$config->report->insertModRecordSql = "
insert into dw_gitlab_code_mod(department, related_pr_cuid, line_add, 
    line_del, line_modify, file_type, file_count, remark, modifier, time_stamp)
values ('#{department}', '#{related_pr_cuid}', '#{line_add}', '#{line_del}',
     '#{line_modify}', '#{file_type}', '#{file_count}', 
     '#{remark}', '#{modifier}', '#{time_stamp}');";
     
$config->report->userRootDictSql = "
select id,name from zt_dept where grade = 1
";