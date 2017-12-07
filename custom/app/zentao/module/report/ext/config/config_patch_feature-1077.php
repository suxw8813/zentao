<?php
/**feature-1077**/
/**feature-1245**/
/* ---------------------------common------------------------------------ */
$config->report->deptInfoSql = "
    select
        *
    from 
        zt_dept
    order by id asc limit 1;";
$config->report->AmibaGroupPersonSql = "
    /* 归属到阿米巴的禅道用户 */
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
        d.dept_name1,
        d.dept_name2,
        u.realname,
        u.account
    from 
        zt_user u,
        (select 
            d.id as dept_id1,
            e.id as dept_id2,
            d.name as dept_name1,
            e.name as dept_name2
        from 
            zt_dept d 
            left join zt_dept e on d.id = e.parent
        where 
            d.path like concat(',', '#{userRootId}', ',%')
            and d.grade = 2
            
        ) d
    where 
        u.dept = d.dept_id2 and u.showreportwork = 'y'";
        
/* ---------------------------dayreport------------------------------------ */
$config->report->DayAmibasSql = "select 
        a.dept_name1 as amiba_name,
        a.dept_name2 as group_name,
        a.realname as realname,
        a.account,
        b.total_time
    from
        (/* 归属到阿米巴的禅道用户 */
        select
            d.id as dept_id1,
            d.id as dept_id2,
            d.name as dept_name1,
            d.name as dept_name2,
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
            d.dept_id1,
            d.dept_id2,
            d.dept_name1,
            d.dept_name2,
            u.realname,
            u.account
        from 
            zt_user u,
            (select 
                d.id as dept_id1,
                e.id as dept_id2,
                d.name as dept_name1,
                e.name as dept_name2
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
            w.account,
            sum(w.work_time) as total_time
        from 
            dw_worklog w 
        where 
            w.task_type in (1,2,3,21,31) 
            and w.work_date = '#{workdate}'
            /* and w.work_date between '2017-04-01' and '2017-04-30' */
        group by w.account
        ) b on a.account = b.account
    order by a.dept_id1 desc, a.dept_id2 desc, b.total_time desc";
    
/* ---------------------------monthperformance------------------------------------ */
$config->report->outputInfoSql = "
    select 
        a.amiba_name,
        a.group_name,
        b.account, 
        count(b.account),
        
        ifnull(sum(b.dev_total_output), 0) dev_total_output /* 总有效输出(1+2+3) */,
        sum(b.month_wp_task) month_wp_task /* 任务有效输出(1) */,
        sum(b.month_pq_reject) month_pq_reject /* PR被驳回次数 */,
        sum(b.month_pq_passed) month_pq_passed /* PR被通过次数 */,
        sum(b.month_leader_pq_passed) month_leader_pq_passed /* 组长PR通过次数 */,
        sum(b.month_leader_pq_reject) month_leader_pq_reject /* 组长PR驳回次数 */,
        sum(b.month_wp_pr) month_wp_pr /* PR有效输出(2) */,
        sum(b.month_bug_inside_fix) month_bug_inside_fix /* 修改自测Bug数 */,
        sum(b.month_bug_inside_create) month_bug_inside_create /* 产生自测Bug数 */,
        sum(b.month_bug_province_fix) month_bug_province_fix /* 修改现场Bug数 */,
        sum(b.month_bug_province_create) month_bug_province_create /* 产生现场Bug数 */,
        sum(b.Bug计划工时) Bug计划工时 /* Bug计划工时 */,
        sum(b.Bug报工工时) Bug报工工时 /* Bug报工工时 */,
        sum(b.month_wp_bug) month_wp_bug /* Bug有效输出(3) */,
        sum(b.总有效输出) 总有效输出 /* 总有效输出 */,
        sum(b.mod_merge_count) mod_merge_count,
        
        ifnull(sum(e.req_total_output), 0) req_total_output,
        sum(e.onetimepass_store) onetimepass_store /* 评审一次通过数 */,
        sum(e.repeatedpass_store) repeatedpass_store /* 评审多次通过数 */,
        sum(e.reviewed_store_wp) reviewed_store_wp /* 评审通过得分 */,
        sum(e.store_rel_task_wp) store_rel_task_wp /* 有效输出得分（根据研发得分） */,
        sum(e.province_bug_response) story_province_bug_response /* 现场bug承担责任个数 */,
        sum(e.inside_bug_response) story_inside_bug_response /* 内部bug承担责任个数 */,
        sum(e.province_bug_response_wp) story_province_bug_response_wp /* bug承担责任总分（负分） */,
        sum(e.inside_bug_response_wp) story_inside_bug_response_wp /* bug承担责任总分（负分） */,
        sum(e.store_val_wp) store_val_wp /* 现场满意度得分 */,
        
        ifnull(sum(f.test_total_output), 0) test_total_output,
        sum(f.test_case_count) test_case_count /* 测试用例个数 */,
        sum(f.test_case_wp) test_case_wp /* 测试用例得分 */,
        sum(f.test_bug_count) test_bug_count /* 测试上报bug数量 */,
        sum(f.province_bug_response) bug_province_bug_response /* 现场bug承担责任个数 */,
        sum(f.inside_bug_response) bug_inside_bug_response /* 内部bug承担责任个数 */,
        sum(f.province_bug_response_wp) bug_province_bug_response_wp /* bug承担责任总分（负分） */,
        sum(f.inside_bug_response_wp) bug_inside_bug_response_wp /* bug承担责任总分（负分） */,
        sum(f.test_bug_wp) test_bug_wp /* 测试上报bug得分 */,
        
        ifnull(sum(b.dev_total_output), 0) + ifnull(sum(e.req_total_output), 0) + ifnull(sum(f.test_total_output), 0) total_output
    from
        (
            /* 归属到阿米巴的禅道用户 */
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
        left join 
        (
            /* 研发有效输出 */
            select
                w.account,
                w.pr_date,
                w.month_wp_task + w.month_wp_pr + w.month_wp_bug as dev_total_output/* 总有效输出(1+2+3) */,
                w.month_wp_task /* 任务有效输出(1) */,
                w.month_pq_reject /* PR被驳回次数 */,
                w.month_pq_passed /* PR被通过次数 */,
                w.month_leader_pq_passed /* 组长PR通过次数 */,
                w.month_leader_pq_reject /* 组长PR驳回次数 */,
                w.month_wp_pr /* PR有效输出(2) */,
                w.month_bug_inside_fix /* 修改自测Bug数 */,
                w.month_bug_inside_create /* 产生自测Bug数 */,
                w.month_bug_province_fix /* 修改现场Bug数 */,
                w.month_bug_province_create /* 产生现场Bug数 */,
                '' as Bug计划工时/* Bug计划工时 */,
                '' as Bug报工工时/* Bug报工工时 */,
                w.month_wp_bug /* Bug有效输出(3) */,
                '' as 总有效输出/* 总有效输出 */ ,
                (select 
                    count(cm.id)
                from 
                    bi_report_wp_byaccount wp
                    inner join dw_gitlab_pr g on g.committer = wp.account
                    inner join  dw_gitlab_code_mod cm on g.id = cm.related_pr_cuid
                where 
                    wp.account = w.account
                    and wp.pr_date = '#{monthNum}'
                ) as mod_merge_count
            from 
                bi_report_wp_byaccount w
            where
                1 =1 
                and w.pr_date = '#{monthNum}'
        ) b on a.account = b.account
        left join 
        (
            /* 需求有效输出 */
            select 
                s.account /* 账号 */,
                s.reviewed_store_wp + s.store_rel_task_wp + s.store_val_wp as req_total_output,
                s.onetimepass_store /* 评审一次通过数 */,
                s.repeatedpass_store /* 评审多次通过数 */,
                s.reviewed_store_wp /* 评审通过得分 */,
                s.store_rel_task_wp /* 有效输出得分（根据研发得分） */,
                s.province_bug_response /* 现场bug承担责任个数 */,
                s.inside_bug_response /* 内部bug承担责任个数 */,
                s.province_bug_response_wp /* 现场bug承担责任总分（负分） */,
                s.inside_bug_response_wp /* 内部bug承担责任总分（负分） */,
                s.store_val_wp /* 现场满意度得分 */,
                s.pr_date /* 时间，标记是哪个月 */
            from 
                bi_report_storywp_byaccount s
            where
                s.pr_date = '#{monthNum}'
        ) e on a.account = e.account
        left join 
        (
            /* 测试有效输出 */
            select 
                t.account /* 账号 */,
                t.test_case_wp + t.test_bug_wp as test_total_output,
                t.test_case_count /* 测试用例个数 */,
                t.test_case_wp /* 测试用例得分 */,
                t.test_bug_count /* 测试上报bug数量 */,
                t.province_bug_response /* 现场bug承担责任个数 */,
                t.inside_bug_response /* 内部bug承担责任个数 */,
                t.province_bug_response_wp /* 现场bug承担责任总分（负分） */,
                t.inside_bug_response_wp /* 内部bug承担责任总分（负分） */,
                t.test_bug_wp /* 测试上报bug得分 */,
                t.pr_date /* 时间，标记是哪个月 */
            from 
                bi_report_testwp_byaccount t
            where
                t.pr_date = '#{monthNum}'
        ) f on a.account = f.account
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        #{groupBy}
        
        /* and b.pr_date = '201705' */
        /* and a.account = 'fengfeng'
        group by account */
        
        /* and a.group_name = 'GAIA'
        group by a.group_name */
        
        /* and a.amiba_name = '服务平台巴'
        group by a.amiba_name */
        ";
/* ---------------------------monthperformancescoredetail------------------------------------ */
$config->report->DevTaskDetailSql = "
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
        b.mod_merge_count,
        b.calc_process,
        b.calc_result
    from
        (
            /* 归属到阿米巴的禅道用户 */
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
                (
                    select 
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
        left join 
        (
            select 
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
                (
                    select 
                        count(id) 
                    from 
                        dw_gitlab_code_mod 
                    where 
                        related_pr_cuid=p.act_id
                ) as mod_merge_count,
                p.calc_process,
                p.calc_result
            from 
                bi_report_wp_byaccount w
                inner join dw_gitlab_pr g on g.committer = w.account
                inner join bi_calculate_process p on p.act_id = g.id
            where 
                1 = 1
                and p.calc_type = 'DevTask'
                and w.pr_date = '#{monthNum}'
                and p.pr_date = '#{monthNum}'
        ) b on a.account = b.account
    where 
        1 =1 
        #{andWhereAmibaGroupAccount}
        
        /* and b.pr_date = '201705' */
        /* and a.account = 'zhangtao1' */
        
        /* and a.group_name = 'GAIA' */
        
        /* and a.amiba_name = '服务平台巴' */
        ";
        
$config->report->DevBeRejectedAndLeaderCheckDetailSql = "
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
            p.calc_process,
            p.calc_result
        from 
            bi_report_wp_byaccount w
            inner join dw_gitlab_pr g on g.committer = w.account
            inner join bi_calculate_process p on p.act_id = g.id
            inner join zt_task t on g.task_id = concat(t.id, '')
        where 
            1 = 1
            and p.calc_type in('BeRejected')
            and w.pr_date = '#{monthNum}'
            and p.pr_date = '#{monthNum}'
        /* 组长审核代码 */
        union all
        select 
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
            p.calc_process,
            p.calc_result
        from 
            bi_report_wp_byaccount w
            inner join dw_gitlab_pr g on g.auditor = w.account
            inner join bi_calculate_process p on p.act_id = g.id
            inner join zt_task t on g.task_id = concat(t.id, '')
        where 
            1 = 1
            and p.calc_type in('LeaderCheck')
            and w.pr_date = '#{monthNum}'
            and p.pr_date = '#{monthNum}'
        ) b on a.account = b.account
    where 
        1 =1 
        #{andWhereAmibaGroupAccount}
        
        /* and b.pr_date = '201705' */
        /* and a.account = 'zhangtao1' */
        
        /* and a.group_name = 'GAIA' */
        
        /* and a.amiba_name = '服务平台巴' */
        ";
        
$config->report->DevBugDetailSql = "
    select 
        a.amiba_name,
        a.group_name,
        a.account, 
        b.bug_id,
        b.bug_title,
        b.bug_type,
        b.bug_source,
        b.openedDate,
        b.openedBy,
        b.resolvedDate,
        b.pr_date,                
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
        left join (
            select 
                c.id bug_id,
                c.title bug_title,
                c.type bug_type,
                c.source bug_source,
                c.openedDate,
                c.openedBy,
                c.resolvedDate,
                cal.account,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                bi_calculate_process cal,
                zt_bug c
            where 
                cal.act_id = c.id
                and cal.calc_type in('ProvinceBugFix', 'InsideBugFix', 'ProvinceBugCreate', 'InsideBugCreate')
                and cal.pr_date = '#{monthNum}'
        ) b on a.account = b.account
    where 
        1 =1 
        #{andWhereAmibaGroupAccount}
        
        /* and b.pr_date = '201705' */
        /* and a.account = 'zhangtao1' */
        
        /* and a.group_name = 'GAIA' */
        
        /* and a.amiba_name = '服务平台巴' */
        ";
// 
$config->report->OncePassDetailSql = "
    select 
        a.amiba_name,
        a.group_name,
        a.account, 
        
        b.product_name,
        b.story_id,
        b.story_name,
        b.source,
        b.openedby,
        b.openeddate,
        b.stage,
        b.status,
        b.reviewedby,
        b.passnote,
        b.revieweddate,
        b.account,
        b.pr_date,
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
        left join (
            select 
                p.name product_name,
                s.id story_id,
                s.title story_name,
                s.source,
                s.openedby,
                s.openeddate,
                s.stage,
                s.status,
                s.reviewedby,
                ifnull(s.passnote, 'onetimepass') passnote,
                s.revieweddate,
                cal.account,
                cal.pr_date,
                cal.calc_process,
                cal.calc_result
            from  
                bi_calculate_process cal,
                zt_story s,
                zt_product p
            where 
                cal.act_id = s.id
                and s.product = p.id
                and cal.calc_type = 'OncePass'
                and cal.pr_date = '#{monthNum}'
        ) b on a.account = b.account
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        
        /* and b.pr_date = '201705' */
        /* and a.account = 'zhangtao1' */
        
        /* and a.group_name = 'GAIA' */
        
        /* and a.amiba_name = '服务平台巴' */
        ";
        
$config->report->ReqDevDetailSql = "
    select 
        a.amiba_name,
        a.group_name,
        a.account, 
        
        b.story_id,
        b.story_name,
        b.stage,
        b.passnote,
        b.task_id,
        b.task_name,
        b.satis,
        b.committer,
        b.prId,
        b.check_time,
        b.source_branch,
        b.state,
        b.account,
        b.pr_date,                
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
        left join (
            select 
                d.id story_id,
                d.title story_name,
                d.stage,
                ifnull(d.passnote, 'onetimepass') as passnote,
                c.id task_id,
                c.name task_name,
                'A' satis,
                b.committer,
                b.id prId,
                b.check_time,
                b.source_branch,
                b.state,
                cal.account,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                bi_calculate_process cal,
                dw_gitlab_pr b,
                zt_task c,
                zt_story d
            where 
                cal.act_id = b.id
                and b.task_id = c.id
                and c.story = d.id
                and cal.calc_type = 'ReqDev'
                and cal.pr_date = '#{monthNum}'
        ) b on a.account = b.account
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        
        /* and b.pr_date = '201705' */
        /* and a.account = 'zhangtao1' */
        
        /* and a.group_name = 'GAIA' */
        
        /* and a.amiba_name = '服务平台巴' */
        ";
        
$config->report->ReqSatisfyDetailSql = "
    select 
        a.amiba_name,
        a.group_name,
        a.account, 
        
        b.story_id,
        b.story_name,
        b.stage,
        b.passnote,
        b.task_id,
        b.task_name,
        b.satis,
        b.committer,
        b.prId,
        b.check_time,
        b.source_branch,
        b.state,
        b.account,
        b.pr_date,                
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
        left join (
            select 
                d.id story_id,
                d.title story_name,
                d.stage,
                ifnull(d.passnote, 'onetimepass') as passnote,
                c.id task_id,
                c.name task_name,
                'A' satis,
                b.committer,
                b.id prId,
                b.check_time,
                b.source_branch,
                b.state,
                cal.account,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                bi_calculate_process cal,
                dw_gitlab_pr b,
                zt_task c,
                zt_story d
            where 
                cal.act_id = b.id
                and b.task_id = c.id
                and c.story = d.id
                and cal.calc_type = 'ReqDev'
                and cal.pr_date = '#{monthNum}'
        ) b on a.account = b.account
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        
        /* and b.pr_date = '201705' */
        /* and a.account = 'zhangtao1' */
        
        /* and a.group_name = 'GAIA' */
        
        /* and a.amiba_name = '服务平台巴' */
        ";
        
$config->report->BugDetailSql = "
    select 
        a.amiba_name,
        a.group_name,
        a.account, 
        
        b.story_id,
        b.story_name,
        b.stage,
        b.bug_id,
        b.bug_title,
        b.status,
        b.resolvedBy,
        b.resolvedDate,
        b.pr_date,                
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
        left join (
            select 
                d.id story_id,
                d.title story_name,
                d.stage,
                c.id bug_id,
                c.title bug_title,
                c.status,
                c.resolvedBy,
                c.resolvedDate,
                cal.account,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                bi_calculate_process cal,
                zt_bug c
                left join 
                    zt_story d on c.story = d.id
            where 
                cal.act_id = c.id
                and cal.calc_type = '#{bugType}'
                and cal.pr_date = '#{monthNum}'
        ) b on a.account = b.account
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        
        /* and b.pr_date = '201705' */
        /* and a.account = 'zhangtao1' */
        
        /* and a.group_name = 'GAIA' */
        
        /* and a.amiba_name = '服务平台巴' */
        ";
        
$config->report->TestCaseDetailSql = "
    select 
        a.amiba_name,
        a.group_name,
        a.account, 
        
        b.story_id,
        b.story_name,
        b.stage,
        b.case_id,
        b.case_title,
        b.case_type,
        b.case_status,
        b.lastrunner,
        b.lastrundate,
        b.lastrunresult,
        b.account,
        b.pr_date,                
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
        left join (
            select 
                s.id story_id,
                s.title story_name,
                s.stage,
                c.id case_id,
                c.title case_title,
                c.type case_type,
                c.status case_status,
                c.lastrunner,
                c.lastrundate,
                c.lastrunresult,
                cal.account,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                bi_calculate_process cal,
                zt_case c
                left join 
                    zt_story s on c.story = s.id
            where 
                cal.act_id = c.id
                and cal.calc_type = 'TestCase'
                and cal.pr_date = '#{monthNum}'
        ) b on a.account = b.account
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        
        /* and b.pr_date = '201705' */
        /* and a.account = 'zhangtao1' */
        
        /* and a.group_name = 'GAIA' */
        
        /* and a.amiba_name = '服务平台巴' */
        ";
/* ---------------------------monthreportexport------------------------------------ */
$config->monthreportexport = new stdClass();
$config->monthreportexport->list->exportFields = 'amiba_name,group_name,realname,total_time,day_avg_time,extra_time,total_output,day_avg_output,extra_output,output_efficiency';

/* ---------------------------monthreport------------------------------------ */
$config->report->standardOutput = '1500';

$config->report->MonthAmibasSql = "select 
        a.dept_name1 as amiba_name,
        a.dept_name2 as group_name,
        a.realname as realname,
        a.account,
        b.total_time,
        convert(b.total_time / #{workDayCount}, decimal(5, 1)) as day_avg_time,
        b.total_time - #{workDayCount} * 8 as extra_time,
        convert(ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0), decimal(5, 0)) as total_output,
        convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0)) / #{workDayCount}, decimal(5, 1)) as day_avg_output,
        convert(ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) - #{standardOutput}, decimal(5, 0)) as extra_output,
        convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0)) / b.total_time, decimal(5, 0)) as output_efficiency,
        c.mod_merge_count
    from
        (/* 归属到阿米巴的禅道用户 */
        select
            d.id as dept_id1,
            d.id as dept_id2,
            d.name as dept_name1,
            d.name as dept_name2,
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
            d.dept_id1,
            d.dept_id2,
            d.dept_name1,
            d.dept_name2,
            u.realname,
            u.account
        from 
            zt_user u,
            (select 
                d.id as dept_id1,
                e.id as dept_id2,
                d.name as dept_name1,
                e.name as dept_name2
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
            w.account,
            sum(w.work_time) as total_time
        from 
            dw_worklog w 
        where 
            w.task_type in (1,2,3,21,31) #{andWhereWorkdate}
            /* and w.work_date between '2017-05-01' and '2017-05-30' */
        group by w.account
        ) b on a.account = b.account
        left join (select
            w.account,
            w.pr_date,
            w.month_wp_task + w.month_wp_pr + w.month_wp_bug as total_output/* 总有效输出(1+2+3) */,
            w.month_wp_task /* 任务有效输出(1) */,
            w.month_pq_reject /* PR被驳回次数 */,
            w.month_pq_passed /* PR被通过次数 */,
            w.month_leader_pq_passed /* 组长PR通过次数 */,
            w.month_leader_pq_reject /* 组长PR驳回次数 */,
            w.month_wp_pr /* PR有效输出(2) */,
            w.month_bug_inside_fix /* 修改自测Bug数 */,
            w.month_bug_inside_create /* 产生自测Bug数 */,
            w.month_bug_province_fix /* 修改现场Bug数 */,
            w.month_bug_province_create /* 产生现场Bug数 */,
            '' as Bug计划工时/* Bug计划工时 */,
            '' as Bug报工工时/* Bug报工工时 */,
            w.month_wp_bug /* Bug有效输出(3) */,
            '' as 总有效输出/* 总有效输出 */,
            (select 
                count(cm.id)
            from 
                bi_report_wp_byaccount wp
                inner join dw_gitlab_pr g on g.committer = wp.account
                inner join  dw_gitlab_code_mod cm on g.id = cm.related_pr_cuid
            where 
                wp.account = w.account
                and wp.pr_date = '#{monthNum}'
            ) as mod_merge_count
        from 
            bi_report_wp_byaccount w
        where
            1 =1 
            and w.pr_date = '#{monthNum}'
            /* and w.pr_date = '201705' */
        ) c on a.account = c.account
        left join (
            select 
                s.account /* 账号 */,
                s.reviewed_store_wp + s.store_rel_task_wp + s.province_bug_response_wp + s.inside_bug_response_wp + s.store_val_wp as total_output /* 需求有效输出 */,
                s.onetimepass_store /* 评审一次通过数 */,
                s.repeatedpass_store /* 评审多次通过数 */,
                s.reviewed_store_wp /* 评审通过得分 */,
                s.store_rel_task_wp /* 有效输出得分（根据研发得分） */,
                s.province_bug_response /* 现场bug承担责任个数 */,
                s.inside_bug_response /* 内部bug承担责任个数 */,
                s.province_bug_response_wp /* bug承担责任总分（负分） */,
                s.inside_bug_response_wp /* bug承担责任总分（负分） */,
                s.store_val_wp /* 现场满意度得分 */,
                s.pr_date /* 时间，标记是哪个月 */
            from 
                bi_report_storywp_byaccount s
            where s.pr_date = '#{monthNum}'
        ) d on a.account = d.account
        left join (
            select 
                t.account /* 账号 */,
                t.test_case_wp + t.test_bug_wp + t.province_bug_response_wp + t.inside_bug_response_wp as total_output,
                t.test_case_count /* 测试用例个数 */,
                t.test_case_wp /* 测试用例得分 */,
                t.test_bug_count /* 测试上报bug数量 */,
                t.test_bug_wp /* 测试上报bug得分 */,
                t.province_bug_response /* 现场bug承担责任个数 */,
                t.inside_bug_response /* 内部bug承担责任个数 */,
                t.province_bug_response_wp /* 现场bug承担责任总分（负分） */,
                t.inside_bug_response_wp /* 内部bug承担责任总分（负分） */,
                t.pr_date /* 时间，标记是哪个月 */
            from 
                bi_report_testwp_byaccount t
            where
                t.pr_date = '#{monthNum}'
        ) f on a.account = f.account
        
    order by a.dept_id1 desc, a.dept_id2 asc, total_output desc";
    
/* ---------------------------sort------------------------------------ */
$config->report->PersonTimeTop30Sql = "select 
        a.dept_name1 as amiba_name,
        a.dept_name2 as group_name,
        a.realname,
        b.account,
        b.work_date,
        b.total_time as total_time,
        convert(b.total_time / #{workDayCount}, decimal(5, 1)) as day_avg_time,
        b.total_time - #{workDayCount} * 8 as extra_time,
        convert(ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0), decimal(5, 0)) as total_output,
        convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0)) / #{workDayCount}, decimal(5, 1)) as day_avg_output,
        convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0)) / b.total_time, decimal(5, 0))  as output_efficiency
        /* convert(ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) - #{standardOutput}, decimal(5, 0)) as extra_output */
    from
        (/* 归属到阿米巴的禅道用户 */
        select
            d.name as dept_name1,
            d.name as dept_name2,
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
            d.dept_name1,
            d.dept_name2,
            u.realname,
            u.account
        from 
            zt_user u,
            (select 
                d.id as dept_id1,
                e.id as dept_id2,
                d.name as dept_name1,
                e.name as dept_name2
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
            w.account,
            w.work_date,
            sum(w.work_time) as total_time
        from 
            dw_worklog w 
        where 
            w.task_type in (1,2,3,21,31) #{andWhereWorkdate}
        group by w.account
        ) b on a.account = b.account
        left join (select
            w.account,
            sum(w.month_wp_task) + sum(w.month_wp_pr) + 
            sum(w.month_wp_bug) as total_output/* 研发有效输出(1+2+3) */
        from 
            bi_report_wp_byaccount w
        where
            1 =1 
            and w.pr_date in (#{monthNums})
            /* and w.pr_date = '201705' */
            group by w.account
        ) c on a.account = c.account
        left join (
            select 
                s.account /* 账号 */,
                sum(s.reviewed_store_wp) + sum(s.store_rel_task_wp) + sum(s.province_bug_response_wp) + 
                sum(s.inside_bug_response_wp) + sum(s.store_val_wp) as total_output /* 需求有效输出 */
            from 
                bi_report_storywp_byaccount s
            where s.pr_date in (#{monthNums})
            group by s.account
        ) d on a.account = d.account
        left join (
            select 
                t.account /* 账号 */,
                sum(t.test_case_wp) + sum(t.test_bug_wp) + sum(t.province_bug_response_wp) + 
                sum(t.inside_bug_response_wp) as total_output /* 测试有效输出 */
            from 
                bi_report_testwp_byaccount t
            where
                t.pr_date in (#{monthNums})
            group by t.account
        ) f on a.account = f.account
    order by #{sortField} desc limit 30";
    
$config->report->PersonAvgAmibaTimeTopSql = "select 
        a.dept_name1 as amiba_name,
        /* a.dept_name2 as group_name,
        a.realname,
        b.account,
        b.work_date,
        b.total_time,
        convert(b.total_time / 19, decimal(5, 1)) as day_avg_time,
        b.total_time - 19 * 8 as extra_time, */
        convert(sum(b.total_time) / count(a.account), decimal(5, 1)) as amiba_person_time
    from
        (/* 归属到阿米巴的禅道用户 */
        select
            d.name as dept_name1,
            d.name as dept_name2,
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
            d.dept_name1,
            d.dept_name2,
            u.realname,
            u.account
        from 
            zt_user u,
            (select 
                d.id as dept_id1,
                e.id as dept_id2,
                d.name as dept_name1,
                e.name as dept_name2
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
            w.account,
            w.work_date,
            sum(w.work_time) as total_time
        from 
            dw_worklog w 
        where 
            w.task_type in (1,2,3,21,31) #{andWhereWorkdate}
        group by w.account
        ) b on a.account = b.account
    group by a.dept_name1 
    order by amiba_person_time desc";
    
/* ---------------------------sortmore------------------------------------ */
$config->report->MonthWorkSortSql = "select 
        a.dept_name1 as amiba_name,
        a.dept_name2 as group_name,
        a.realname as realname,
        a.account,
        b.total_time,
        convert(b.total_time / #{workDayCount}, decimal(5, 1)) as day_avg_time,
        b.total_time - #{workDayCount} * 8 as extra_time,
        convert(ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0), decimal(5, 0)) as total_output,
        convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0)) / #{workDayCount}, decimal(5, 1)) as day_avg_output,
        convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0)) / b.total_time, decimal(5, 0)) as output_efficiency,
        c.mod_merge_count
        /* convert(ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) - #{standardOutput}, decimal(5, 0)) as extra_output */
    from
        (/* 归属到阿米巴的禅道用户 */
        select
            d.name as dept_name1,
            d.name as dept_name2,
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
            d.dept_name1,
            d.dept_name2,
            u.realname,
            u.account
        from 
            zt_user u,
            (select 
                d.id as dept_id1,
                e.id as dept_id2,
                d.name as dept_name1,
                e.name as dept_name2
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
            w.account,
            sum(w.work_time) as total_time
        from 
            dw_worklog w 
        where 
            w.task_type in (1,2,3,21,31) #{andWhereWorkdate}
            /* and w.work_date between '2017-04-01' and '2017-04-30' */
        group by w.account
        ) b on a.account = b.account
        left join (
            select
                w.account,
                sum(w.month_wp_task) + sum(w.month_wp_pr) + 
                sum(w.month_wp_bug) as total_output/* 研发有效输出(1+2+3) */ ,
                (select 
                    count(cm.id)
                from 
                    bi_report_wp_byaccount wp
                    inner join dw_gitlab_pr g on g.committer = wp.account
                    inner join  dw_gitlab_code_mod cm on g.id = cm.related_pr_cuid
                where 
                    wp.account = w.account
                    and wp.pr_date in (#{monthNums})
                ) as mod_merge_count
            from 
                bi_report_wp_byaccount w
            where
                1 =1 
                and w.pr_date in (#{monthNums})
                /* and w.pr_date = '201705' */
            group by account
        ) c on a.account = c.account
        left join (
            select 
                s.account /* 账号 */,
                sum(s.reviewed_store_wp) + sum(s.store_rel_task_wp) + sum(s.province_bug_response_wp) + 
                sum(s.inside_bug_response_wp) + sum(s.store_val_wp) as total_output /* 需求有效输出 */
            from 
                bi_report_storywp_byaccount s
            where s.pr_date in (#{monthNums})
            group by account
        ) d on a.account = d.account
        left join (
            select 
                t.account /* 账号 */,
                sum(t.test_case_wp) + sum(t.test_bug_wp) + sum(t.province_bug_response_wp) + 
                sum(t.inside_bug_response_wp) as total_output /* 测试有效输出 */
            from 
                bi_report_testwp_byaccount t
            where
                t.pr_date in (#{monthNums})
            group by account
        ) f on a.account = f.account
    order by #{sortField} desc";
    
/* ---------------------------timetendency------------------------------------ */
$config->report->DayTimeTendencyDataSql = "
    select 
        b.work_date,
        sum(b.work_time) as value
    from
        (/* 归属到阿米巴的禅道用户 */
        select
            d.name as dept_name1,
            d.name as dept_name2,
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
            d.dept_name1,
            d.dept_name2,
            u.realname,
            u.account
        from 
            zt_user u,
            (select 
                d.id as dept_id1,
                e.id as dept_id2,
                d.name as dept_name1,
                e.name as dept_name2
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
            w.account,
            w.work_date,
            w.work_time as work_time
        from 
            dw_worklog w 
        where 
            w.task_type in (1,2,3,21,31) 
            /* and w.work_date = '2017-04-01' */ #{andWhereWorkdate}
        ) b on a.account = b.account
    where 
        b.work_date is not null #{andWhereAmibaGroupAccount}
    group by b.work_date";

$config->report->MonthTimeTendencyDataSql = "
    select 
        b.work_date as work_date,
        sum(b.work_time) as value
    from
        (/* 归属到阿米巴的禅道用户 */
        select
            d.name as dept_name1,
            d.name as dept_name2,
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
            d.dept_name1,
            d.dept_name2,
            u.realname,
            u.account
        from 
            zt_user u,
            (select 
                d.id as dept_id1,
                e.id as dept_id2,
                d.name as dept_name1,
                e.name as dept_name2
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
            w.account,
            w.work_date,
            w.work_time as work_time
        from 
            dw_worklog w 
        where 
            w.task_type in (1,2,3,21,31) 
            /* and w.work_date = '2017-04-01' */ #{andWhereWorkdate}
        ) b on a.account = b.account
    where 
        b.work_date is not null #{andWhereAmibaGroupAccount}
        group by b.work_date
        order by b.work_date";
/* ---------------------------worklogs------------------------------------ */
$config->report->WorklogsSql = "
select
    case 
        when w.task_type = 1
        then '禅道需求'
        when w.task_type = 2
        then '禅道任务'
        when w.task_type = 3
        then '禅道Bug'
        when w.task_type = 21
        then '问题平台'
        else '需求平台'
    end as task_type_name,
    w.task_type,
    w.task_id,
    w.work_time,
    concat(convert(w.work_time, decimal(5, 1)), 'h(', date_format(w.start_time, '%d号%H:%i'), '~', date_format(w.end_time, '%d号%H:%i'), ')') as time_sect,
    w.work_content
from 
    dw_worklog w
where 
    w.task_type in (1, 2, 3, 21, 31)
    #{andWhereAccountWorkdate}
order by w.start_time desc";
/* ---------------------------end------------------------------------ */
