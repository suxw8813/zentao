<?php
/**feature-1509**/
/* --------------------------- common start ------------------------------------ */
$config->quantizedoutput->log = false;
$config->quantizedoutput->deptInfoSql = "
    select
        *
    from 
        zt_dept
    order by id asc limit 1;";
$config->quantizedoutput->AmibaGroupPersonSql = "
    select 
        * 
    from 
        v_report_user 
    where 
        root_path like concat(',', '#{userRootId}', ',%')";
$config->quantizedoutput->PrjAmibaGroupPersonSql = "
    select 
        ppa.project amiba_id,
        case 
            when pj.name is null then '公共'
            when pj.name = '' then '公共'
            else pj.name
        end as amiba_name,
        ppa.product group_id,
        case
            when pd.name is null then '公共'
            when pd.name = '' then '公共'
            else pd.name
        end as group_name,
        ppa.account
    from
        boco_product_project_account ppa
        left join zt_project pj on pj.id = ppa.project
        inner join zt_product pd on pd.id = ppa.product
    where 
        ppa.account != '' and ppa.pr_date in (#{monthNums})
        and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
        and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))";
$config->quantizedoutput->PrdAmibaGroupPersonSql = "
    select 
        ppa.project group_id,
        case 
            when pj.name is null then '公共'
            when pj.name = '' then '公共'
            else pj.name
        end as group_name,
        ppa.product amiba_id,
        case
            when pd.name is null then '公共'
            when pd.name = '' then '公共'
            else pd.name
        end as amiba_name,
        ppa.account
    from
        boco_product_project_account ppa
        left join zt_project pj on pj.id = ppa.project
        inner join zt_product pd on pd.id = ppa.product
    where 
        ppa.account != '' and ppa.pr_date in (#{monthNums})
        and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
        and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))";
/* --------------------------- common end ------------------------------------ */
        
/* --------------------------- dayreport start ------------------------------------ */
$config->quantizedoutput->DayAmibasSql = "
    select 
        a.amiba_name,
        a.group_name,
        a.realname as realname,
        a.account,
        b.total_time
    from
        (
            select 
                * 
            from 
                v_report_user 
            where 
                root_path like concat(',', '#{userRootId}', ',%')
        ) a
        left join (select 
            w.account,
            sum(w.work_time) as total_time
        from 
            dw_worklog_sync w 
        where 
            w.task_type in (1,2,3,21,31) 
            and w.work_date = '#{workdate}'
            /* and w.work_date between '2017-04-01' and '2017-04-30' */
        group by w.account
        ) b on a.account = b.account
    order by a.amiba_id desc, a.group_id desc, b.total_time desc";
/* --------------------------- dayreport end ------------------------------------ */
    
/* --------------------------- monthperformance start------------------------------------ */
$config->quantizedoutput->bugTypes = array('InsideBugReqResponse', 'ProvinceBugReqResponse', 'TestBug', 
    'InsideBugTestResponse', 'ProvinceBugTestResponse', 'CloseBug', 'QAOpenBug', 'QACloseBug');
$config->quantizedoutput->caseTypes = array('TestCase', 'ExecuteCase');
$config->quantizedoutput->outputInfoSql = "
    select 
        a.amiba_name,
        a.group_name,
        b.account, 
        count(b.account),
        
        /* 研发有效输出 */
        ifnull(sum(b.dev_total_output), 0) dev_total_output /* 总有效输出(1+2+3) */,
        sum(b.month_wp_task) month_wp_task /* 任务有效输出(1) */,
        sum(b.month_pq_reject) month_pq_reject /* PR被驳回次数 */,
        sum(b.month_pq_passed) month_pq_passed /* PR被通过次数 */,
        sum(b.month_leader_pq_passed) month_leader_pq_passed /* 组长PR通过次数 */,
        sum(b.month_leader_pq_reject) month_leader_pq_reject /* 组长PR驳回次数 */,
        sum(b.month_wp_pr) month_wp_pr /* PR有效输出(2) */,
        sum(b.month_bug_inside_fix) month_bug_inside_fix /* 修改自测Bug数 */,
        sum(b.it_bug_fix) it_bug_fix /* 修改问题平台bug个数 */,
        sum(b.month_bug_inside_create) month_bug_inside_create /* 产生自测Bug数 */,
        sum(b.month_bug_province_fix) month_bug_province_fix /* 修改现场Bug数 */,
        sum(b.month_bug_province_create) month_bug_province_create /* 产生现场Bug数 */,
        sum(b.Bug计划工时) Bug计划工时 /* Bug计划工时 */,
        sum(b.Bug报工工时) Bug报工工时 /* Bug报工工时 */,
        sum(b.month_wp_bug) month_wp_bug /* Bug有效输出(3) */,
        sum(b.总有效输出) 总有效输出 /* 总有效输出 */,
        sum(b.mod_merge_count) mod_merge_count,
        
        /* 需求有效输出 */
        ifnull(sum(e.req_total_output), 0) req_total_output,
        sum(e.onetimepass_store) onetimepass_store /* 评审一次通过数 */,
        sum(e.norecord_store) norecord_store /* 未评审个数 */,
        sum(e.repeatedpass_store) repeatedpass_store /* 评审多次通过数 */,
        sum(e.reviewed_store_wp) reviewed_store_wp /* 评审通过得分 */,
        sum(e.itReq_count) itReq_count /* 需求平台分析需求个数 */,
        sum(e.itReq_wp) itReq_wp /* 需求平台分析需求得分 */,
        sum(e.store_rel_task_wp) store_rel_task_wp /* 有效输出得分（根据研发得分） */,
        sum(e.province_bug_response) story_province_bug_response /* 现场bug承担责任个数 */,
        sum(e.inside_bug_response) story_inside_bug_response /* 内部bug承担责任个数 */,
        sum(e.province_bug_response_wp) story_province_bug_response_wp /* bug承担责任总分（负分） */,
        sum(e.inside_bug_response_wp) story_inside_bug_response_wp /* bug承担责任总分（负分） */,
        sum(e.store_val_wp) store_val_wp /* 现场满意度得分 */,
        
        /* 测试有效输出 */
        ifnull(sum(f.test_total_output), 0) test_total_output,
        sum(f.test_case_count) test_case_count /* 创建用例个数 */,
        sum(f.test_case_wp) test_case_wp /* 创建用例得分 */,
        sum(f.execute_case_count) execute_case_count /* 执行用例个数 */,
        sum(f.execute_case_wp) execute_case_wp /* 执行用例得分 */,
        sum(f.test_bug_count) test_bug_count /* 上报bug个数 */,
        sum(f.close_bug_wp) close_bug_wp /* 关闭Bug得分 */,
        sum(f.close_bug_count) close_bug_count /* 关闭Bug个数 */,
        sum(f.province_bug_response) bug_province_bug_response /* 现场bug承担责任个数 */,
        sum(f.inside_bug_response) bug_inside_bug_response /* 内部bug承担责任个数 */,
        sum(f.province_bug_response_wp) bug_province_bug_response_wp /* bug承担责任总分（负分） */,
        sum(f.inside_bug_response_wp) bug_inside_bug_response_wp /* bug承担责任总分（负分） */,
        sum(f.test_bug_wp) test_bug_wp /* 上报bug得分 */,
        
        /* QA有效输出 */
        ifnull(sum(g.qa_total_output), 0) qa_total_output,
        ifnull(sum(g.qa_open_bug_wp), 0) qa_open_bug_wp,
        ifnull(sum(g.qa_open_bug_count), 0) qa_open_bug_count,
        ifnull(sum(g.qa_close_bug_wp), 0) qa_close_bug_wp,
        ifnull(sum(g.qa_close_bug_count), 0) qa_close_bug_count,
        
        /* 市场或售前有效输出 */
        ifnull(sum(h.market_total_output), 0) market_total_output,
        ifnull(sum(h.work_split_wp), 0) work_split_wp,
        ifnull(sum(h.work_split_count), 0) work_split_count,
        
        ifnull(sum(b.dev_total_output), 0) + ifnull(sum(e.req_total_output), 0) + ifnull(sum(f.test_total_output), 0) 
            + ifnull(sum(g.qa_total_output), 0) + ifnull(sum(h.market_total_output), 0) total_output
        
    from
        (
            select 
                * 
            from 
                v_report_user 
            where 
                root_path like concat(',', '#{userRootId}', ',%')
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
                w.it_bug_fix /* 修改问题平台bug个数 */,
                w.month_bug_inside_create /* 产生自测Bug数 */,
                w.month_bug_province_fix /* 修改现场Bug数 */,
                w.month_bug_province_create /* 产生现场Bug数 */,
                '' as Bug计划工时/* Bug计划工时 */,
                '' as Bug报工工时/* Bug报工工时 */,
                w.month_wp_bug /* Bug有效输出(3) */,
                '' as 总有效输出/* 总有效输出 */ ,
                sum(case when b.mod_merge_count is not null and b.mod_merge_count != 0 then b.mod_merge_count else 0 end) as mod_merge_count
            from 
                boco_report_wp_byaccount w
                left join 
                (
                    select 
                        p.account,
                        count(cm.id) mod_merge_count
                    from 
                        boco_calculate_process p
                        inner join  boco_gitlab_code_mod cm on p.act_id = cm.related_pr_cuid
                    where 
                        p.calc_type = 'DevTask'
                        and p.pr_date = '#{monthNum}'
                    group by p.account
                ) b on b.account = w.account
            where
                w.pr_date = '#{monthNum}'
            group by w.account
        ) b on a.account = b.account
        left join 
        (
            /* 需求有效输出 */
            select 
                s.account /* 账号 */,
                s.reviewed_store_wp + s.itReq_wp + s.store_rel_task_wp + s.store_val_wp as req_total_output,
                s.onetimepass_store /* 评审一次通过数 */,
                s.norecord_store /* 未评审个数 */,
                s.repeatedpass_store /* 评审多次通过数 */,
                s.reviewed_store_wp /* 评审通过得分 */,
                s.itReq_count /* 需求平台分析需求个数 */,
                s.itReq_wp /* 需求平台分析需求得分 */,
                s.store_rel_task_wp /* 有效输出得分（根据研发得分） */,
                s.province_bug_response /* 现场bug承担责任个数 */,
                s.inside_bug_response /* 内部bug承担责任个数 */,
                s.province_bug_response_wp /* 现场bug承担责任总分（负分） */,
                s.inside_bug_response_wp /* 内部bug承担责任总分（负分） */,
                s.store_val_wp /* 现场满意度得分 */,
                s.pr_date /* 时间，标记是哪个月 */
            from 
                boco_report_storywp_byaccount s
            where
                s.pr_date = '#{monthNum}'
        ) e on a.account = e.account
        left join 
        (
            /* 测试有效输出 */
            select 
                t.account /* 账号 */,
                t.test_case_wp + t.execute_case_wp + t.test_bug_wp + t.province_bug_response_wp 
                + t.inside_bug_response_wp + t.close_bug_wp as test_total_output,
                t.test_case_count /* 创建用例个数 */,
                t.test_case_wp /* 创建用例得分 */,
                t.execute_case_count /* 执行用例个数 */,
                t.execute_case_wp /* 执行用例得分 */,
                t.test_bug_count /* 上报bug个数 */,
                t.test_bug_wp /* 上报bug得分 */,
                t.close_bug_count /* 关闭Bug个数 */,
                t.close_bug_wp /* 关闭Bug得分 */,
                t.province_bug_response /* 现场bug承担责任个数 */,
                t.inside_bug_response /* 内部bug承担责任个数 */,
                t.province_bug_response_wp /* 现场bug承担责任总分（负分） */,
                t.inside_bug_response_wp /* 内部bug承担责任总分（负分） */,
                t.pr_date /* 时间，标记是哪个月 */
            from 
                boco_report_testwp_byaccount t
            where
                t.pr_date = '#{monthNum}'
        ) f on a.account = f.account
        left join 
        (
            /* QA有效输出 */
            select 
                t.account /* 账号 */,
                t.open_bug_wp + t.close_bug_wp as qa_total_output,
                t.open_bug_wp as qa_open_bug_wp,
                t.open_bug_count as qa_open_bug_count,
                t.close_bug_wp as qa_close_bug_wp,
                t.close_bug_count as qa_close_bug_count,
                t.pr_date /* 时间，标记是哪个月 */
            from 
                boco_report_qawp_byaccount t
            where
                t.pr_date = '#{monthNum}'
        ) g on a.account = g.account
        left join 
        (
            /* 市场或售前有效输出 */
            select 
                t.account /* 账号 */,
                t.work_split_wp as market_total_output,
                t.work_split_wp,
                t.work_split_count,
                t.pr_date /* 时间，标记是哪个月 */
            from 
                boco_report_marketwp_byaccount t
            where
                t.pr_date = '#{monthNum}'
        ) h on a.account = h.account
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        #{groupBy}
        ";
/* --------------------------- monthperformance end ------------------------------------ */

/* --------------------------- prjmonthperformance start------------------------------------ */
$config->quantizedoutput->prjoutputInfoSql = "
    select 
        a.amiba_id,
        a.amiba_name,
        a.group_id,
        a.group_name,
        b.account, 
        count(b.account),
        
        /* 研发有效输出 */
        ifnull(sum(b.dev_total_output), 0) dev_total_output /* 总有效输出(1+2+3) */,
        sum(b.month_wp_task) month_wp_task /* 任务有效输出(1) */,
        sum(b.month_pq_reject) month_pq_reject /* PR被驳回次数 */,
        sum(b.month_pq_passed) month_pq_passed /* PR被通过次数 */,
        sum(b.month_leader_pq_passed) month_leader_pq_passed /* 组长PR通过次数 */,
        sum(b.month_leader_pq_reject) month_leader_pq_reject /* 组长PR驳回次数 */,
        sum(b.month_wp_pr) month_wp_pr /* PR有效输出(2) */,
        sum(b.month_bug_inside_fix) month_bug_inside_fix /* 修改自测Bug数 */,
        sum(b.it_bug_fix) it_bug_fix /* 修改问题平台bug个数 */,
        sum(b.month_bug_inside_create) month_bug_inside_create /* 产生自测Bug数 */,
        sum(b.month_bug_province_fix) month_bug_province_fix /* 修改现场Bug数 */,
        sum(b.month_bug_province_create) month_bug_province_create /* 产生现场Bug数 */,
        sum(b.Bug计划工时) Bug计划工时 /* Bug计划工时 */,
        sum(b.Bug报工工时) Bug报工工时 /* Bug报工工时 */,
        sum(b.month_wp_bug) month_wp_bug /* Bug有效输出(3) */,
        sum(b.总有效输出) 总有效输出 /* 总有效输出 */,
        sum(b.mod_merge_count) mod_merge_count,
        
        /* 需求有效输出 */
        ifnull(sum(e.req_total_output), 0) req_total_output,
        sum(e.onetimepass_store) onetimepass_store /* 评审一次通过数 */,
        sum(e.norecord_store) norecord_store /* 未评审个数 */,
        sum(e.repeatedpass_store) repeatedpass_store /* 评审多次通过数 */,
        sum(e.reviewed_store_wp) reviewed_store_wp /* 评审通过得分 */,
        sum(e.itReq_count) itReq_count /* 需求平台分析需求个数 */,
        sum(e.itReq_wp) itReq_wp /* 需求平台分析需求得分 */,
        sum(e.store_rel_task_wp) store_rel_task_wp /* 有效输出得分（根据研发得分） */,
        sum(e.province_bug_response) story_province_bug_response /* 现场bug承担责任个数 */,
        sum(e.inside_bug_response) story_inside_bug_response /* 内部bug承担责任个数 */,
        sum(e.province_bug_response_wp) story_province_bug_response_wp /* bug承担责任总分（负分） */,
        sum(e.inside_bug_response_wp) story_inside_bug_response_wp /* bug承担责任总分（负分） */,
        sum(e.store_val_wp) store_val_wp /* 现场满意度得分 */,
        
        /* 测试有效输出 */
        ifnull(sum(f.test_total_output), 0) test_total_output,
        sum(f.test_case_count) test_case_count /* 创建用例个数 */,
        sum(f.test_case_wp) test_case_wp /* 创建用例得分 */,
        sum(f.execute_case_count) execute_case_count /* 执行用例个数 */,
        sum(f.execute_case_wp) execute_case_wp /* 执行用例得分 */,
        sum(f.test_bug_count) test_bug_count /* 上报bug个数 */,
        sum(f.close_bug_wp) close_bug_wp /* 关闭Bug得分 */,
        sum(f.close_bug_count) close_bug_count /* 关闭Bug个数 */,
        sum(f.province_bug_response) bug_province_bug_response /* 现场bug承担责任个数 */,
        sum(f.inside_bug_response) bug_inside_bug_response /* 内部bug承担责任个数 */,
        sum(f.province_bug_response_wp) bug_province_bug_response_wp /* bug承担责任总分（负分） */,
        sum(f.inside_bug_response_wp) bug_inside_bug_response_wp /* bug承担责任总分（负分） */,
        sum(f.test_bug_wp) test_bug_wp /* 上报bug得分 */,
        
        /* QA有效输出 */
        ifnull(sum(g.qa_total_output), 0) qa_total_output,
        ifnull(sum(g.qa_open_bug_wp), 0) qa_open_bug_wp,
        ifnull(sum(g.qa_open_bug_count), 0) qa_open_bug_count,
        ifnull(sum(g.qa_close_bug_wp), 0) qa_close_bug_wp,
        ifnull(sum(g.qa_close_bug_count), 0) qa_close_bug_count,
        
        /* 市场或售前有效输出 */
        ifnull(sum(h.market_total_output), 0) market_total_output,
        ifnull(sum(h.work_split_wp), 0) work_split_wp,
        ifnull(sum(h.work_split_count), 0) work_split_count,
        
        ifnull(sum(b.dev_total_output), 0) + ifnull(sum(e.req_total_output), 0) + ifnull(sum(f.test_total_output), 0) 
            + ifnull(sum(g.qa_total_output), 0) + ifnull(sum(h.market_total_output), 0) total_output
        
    from
        (
            select 
                ppa.project amiba_id,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as amiba_name,
                ppa.product group_id,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as group_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        left join 
        (
            /* 研发有效输出 */
            select
                w.account,
                w.project,
                w.product,
                w.pr_date,
                w.month_wp_task + w.month_wp_pr + w.month_wp_bug as dev_total_output/* 总有效输出(1+2+3) */,
                w.month_wp_task /* 任务有效输出(1) */,
                w.month_pq_reject /* PR被驳回次数 */,
                w.month_pq_passed /* PR被通过次数 */,
                w.month_leader_pq_passed /* 组长PR通过次数 */,
                w.month_leader_pq_reject /* 组长PR驳回次数 */,
                w.month_wp_pr /* PR有效输出(2) */,
                w.month_bug_inside_fix /* 修改自测Bug数 */,
                w.it_bug_fix /* 修改问题平台bug个数 */,
                w.month_bug_inside_create /* 产生自测Bug数 */,
                w.month_bug_province_fix /* 修改现场Bug数 */,
                w.month_bug_province_create /* 产生现场Bug数 */,
                '' as Bug计划工时/* Bug计划工时 */,
                '' as Bug报工工时/* Bug报工工时 */,
                w.month_wp_bug /* Bug有效输出(3) */,
                '' as 总有效输出/* 总有效输出 */ ,
                sum(case when b.mod_merge_count is not null and b.mod_merge_count != 0 then b.mod_merge_count else 0 end) as mod_merge_count
            from 
                boco_report_wp_bypdpj w
                left join 
                (
                    select 
                        p.account,
                        p.project,
                        p.product,
                        count(cm.id) mod_merge_count
                    from 
                        boco_calculate_process p
                        inner join boco_gitlab_code_mod cm on p.act_id = cm.related_pr_cuid
                    where 
                        p.calc_type = 'DevTask'
                        and p.pr_date = '#{monthNum}'
                    group by p.account,p.project,p.product
                ) b on b.account = w.account and b.project = w.project and b.product = w.product
            where
                w.pr_date = '#{monthNum}'
            group by w.account, w.project, w.product
        ) b on b.account = a.account and b.project = a.amiba_id and b.product = a.group_id
        left join 
        (
            /* 需求有效输出 */
            select 
                s.account /* 账号 */,
                s.project,
                s.product,
                s.reviewed_store_wp + s.itReq_wp + s.store_rel_task_wp + s.store_val_wp as req_total_output,
                s.onetimepass_store /* 评审一次通过数 */,
                s.norecord_store /* 未评审个数 */,
                s.repeatedpass_store /* 评审多次通过数 */,
                s.reviewed_store_wp /* 评审通过得分 */,
                s.itReq_count /* 需求平台分析需求个数 */,
                s.itReq_wp /* 需求平台分析需求得分 */,
                s.store_rel_task_wp /* 有效输出得分（根据研发得分） */,
                s.province_bug_response /* 现场bug承担责任个数 */,
                s.inside_bug_response /* 内部bug承担责任个数 */,
                s.province_bug_response_wp /* 现场bug承担责任总分（负分） */,
                s.inside_bug_response_wp /* 内部bug承担责任总分（负分） */,
                s.store_val_wp /* 现场满意度得分 */,
                s.pr_date /* 时间，标记是哪个月 */
            from 
                boco_report_storywp_bypdpj s
            where
                s.pr_date = '#{monthNum}'
        ) e on e.account = a.account and e.project = a.amiba_id and e.product = a.group_id
        left join 
        (
            /* 测试有效输出 */
            select 
                t.account /* 账号 */,
                t.project,
                t.product,
                t.test_case_wp + t.execute_case_wp + t.test_bug_wp + t.province_bug_response_wp 
                + t.inside_bug_response_wp + t.close_bug_wp as test_total_output,
                t.test_case_count /* 创建用例个数 */,
                t.test_case_wp /* 创建用例得分 */,
                t.execute_case_count /* 执行用例个数 */,
                t.execute_case_wp /* 执行用例得分 */,
                t.test_bug_count /* 上报bug个数 */,
                t.test_bug_wp /* 上报bug得分 */,
                t.close_bug_count /* 关闭Bug个数 */,
                t.close_bug_wp /* 关闭Bug得分 */,
                t.province_bug_response /* 现场bug承担责任个数 */,
                t.inside_bug_response /* 内部bug承担责任个数 */,
                t.province_bug_response_wp /* 现场bug承担责任总分（负分） */,
                t.inside_bug_response_wp /* 内部bug承担责任总分（负分） */,
                t.pr_date /* 时间，标记是哪个月 */
            from 
                boco_report_testwp_bypdpj t
            where
                t.pr_date = '#{monthNum}'
        ) f on f.account = a.account and f.project = a.amiba_id and f.product = a.group_id
        left join 
        (
            /* QA有效输出 */
            select 
                t.account /* 账号 */,
                t.project,
                t.product,
                t.open_bug_wp + t.close_bug_wp as qa_total_output,
                t.open_bug_wp as qa_open_bug_wp,
                t.open_bug_count as qa_open_bug_count,
                t.close_bug_wp as qa_close_bug_wp,
                t.close_bug_count as qa_close_bug_count,
                t.pr_date /* 时间，标记是哪个月 */
            from 
                boco_report_qawp_bypdpj t
            where
                t.pr_date = '#{monthNum}'
        ) g on g.account = a.account and g.project = a.amiba_id and g.product = a.group_id
        left join 
        (
            /* 市场或售前有效输出 */
            select 
                t.account /* 账号 */,
                t.project,
                t.product,
                t.work_split_wp as market_total_output,
                t.work_split_wp,
                t.work_split_count,
                t.pr_date /* 时间，标记是哪个月 */
            from 
                boco_report_marketwp_bypdpj t
            where
                t.pr_date = '#{monthNum}'
        ) h on h.account = a.account and h.project = a.amiba_id and h.product = a.group_id
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        #{groupBy}
        ";
/* --------------------------- prjmonthperformance end ------------------------------------ */

/* --------------------------- prdmonthperformance start------------------------------------ */
$config->quantizedoutput->prdoutputInfoSql = "
    select 
        a.amiba_id,
        a.amiba_name,
        a.group_id,
        a.group_name,
        b.account, 
        count(b.account),
        
        /* 研发有效输出 */
        ifnull(sum(b.dev_total_output), 0) dev_total_output /* 总有效输出(1+2+3) */,
        sum(b.month_wp_task) month_wp_task /* 任务有效输出(1) */,
        sum(b.month_pq_reject) month_pq_reject /* PR被驳回次数 */,
        sum(b.month_pq_passed) month_pq_passed /* PR被通过次数 */,
        sum(b.month_leader_pq_passed) month_leader_pq_passed /* 组长PR通过次数 */,
        sum(b.month_leader_pq_reject) month_leader_pq_reject /* 组长PR驳回次数 */,
        sum(b.month_wp_pr) month_wp_pr /* PR有效输出(2) */,
        sum(b.month_bug_inside_fix) month_bug_inside_fix /* 修改自测Bug数 */,
        sum(b.it_bug_fix) it_bug_fix /* 修改问题平台bug个数 */,
        sum(b.month_bug_inside_create) month_bug_inside_create /* 产生自测Bug数 */,
        sum(b.month_bug_province_fix) month_bug_province_fix /* 修改现场Bug数 */,
        sum(b.month_bug_province_create) month_bug_province_create /* 产生现场Bug数 */,
        sum(b.Bug计划工时) Bug计划工时 /* Bug计划工时 */,
        sum(b.Bug报工工时) Bug报工工时 /* Bug报工工时 */,
        sum(b.month_wp_bug) month_wp_bug /* Bug有效输出(3) */,
        sum(b.总有效输出) 总有效输出 /* 总有效输出 */,
        sum(b.mod_merge_count) mod_merge_count,
        
        /* 需求有效输出 */
        ifnull(sum(e.req_total_output), 0) req_total_output,
        sum(e.onetimepass_store) onetimepass_store /* 评审一次通过数 */,
        sum(e.norecord_store) norecord_store /* 未评审个数 */,
        sum(e.repeatedpass_store) repeatedpass_store /* 评审多次通过数 */,
        sum(e.reviewed_store_wp) reviewed_store_wp /* 评审通过得分 */,
        sum(e.itReq_count) itReq_count /* 需求平台分析需求个数 */,
        sum(e.itReq_wp) itReq_wp /* 需求平台分析需求得分 */,
        sum(e.store_rel_task_wp) store_rel_task_wp /* 有效输出得分（根据研发得分） */,
        sum(e.province_bug_response) story_province_bug_response /* 现场bug承担责任个数 */,
        sum(e.inside_bug_response) story_inside_bug_response /* 内部bug承担责任个数 */,
        sum(e.province_bug_response_wp) story_province_bug_response_wp /* bug承担责任总分（负分） */,
        sum(e.inside_bug_response_wp) story_inside_bug_response_wp /* bug承担责任总分（负分） */,
        sum(e.store_val_wp) store_val_wp /* 现场满意度得分 */,
        
        /* 测试有效输出 */
        ifnull(sum(f.test_total_output), 0) test_total_output,
        sum(f.test_case_count) test_case_count /* 创建用例个数 */,
        sum(f.test_case_wp) test_case_wp /* 创建用例得分 */,
        sum(f.execute_case_count) execute_case_count /* 执行用例个数 */,
        sum(f.execute_case_wp) execute_case_wp /* 执行用例得分 */,
        sum(f.test_bug_count) test_bug_count /* 上报bug个数 */,
        sum(f.close_bug_wp) close_bug_wp /* 关闭Bug得分 */,
        sum(f.close_bug_count) close_bug_count /* 关闭Bug个数 */,
        sum(f.province_bug_response) bug_province_bug_response /* 现场bug承担责任个数 */,
        sum(f.inside_bug_response) bug_inside_bug_response /* 内部bug承担责任个数 */,
        sum(f.province_bug_response_wp) bug_province_bug_response_wp /* bug承担责任总分（负分） */,
        sum(f.inside_bug_response_wp) bug_inside_bug_response_wp /* bug承担责任总分（负分） */,
        sum(f.test_bug_wp) test_bug_wp /* 上报bug得分 */,
        
        /* QA有效输出 */
        ifnull(sum(g.qa_total_output), 0) qa_total_output,
        ifnull(sum(g.qa_open_bug_wp), 0) qa_open_bug_wp,
        ifnull(sum(g.qa_open_bug_count), 0) qa_open_bug_count,
        ifnull(sum(g.qa_close_bug_wp), 0) qa_close_bug_wp,
        ifnull(sum(g.qa_close_bug_count), 0) qa_close_bug_count,
        
        /* 市场或售前有效输出 */
        ifnull(sum(h.market_total_output), 0) market_total_output,
        ifnull(sum(h.work_split_wp), 0) work_split_wp,
        ifnull(sum(h.work_split_count), 0) work_split_count,
        
        ifnull(sum(b.dev_total_output), 0) + ifnull(sum(e.req_total_output), 0) + ifnull(sum(f.test_total_output), 0) 
            + ifnull(sum(g.qa_total_output), 0) + ifnull(sum(h.market_total_output), 0) total_output
        
    from
        (
            select 
                ppa.project group_id,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as group_name,
                ppa.product amiba_id,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as amiba_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        left join 
        (
            /* 研发有效输出 */
            select
                w.account,
                w.project,
                w.product,
                w.pr_date,
                w.month_wp_task + w.month_wp_pr + w.month_wp_bug as dev_total_output/* 总有效输出(1+2+3) */,
                w.month_wp_task /* 任务有效输出(1) */,
                w.month_pq_reject /* PR被驳回次数 */,
                w.month_pq_passed /* PR被通过次数 */,
                w.month_leader_pq_passed /* 组长PR通过次数 */,
                w.month_leader_pq_reject /* 组长PR驳回次数 */,
                w.month_wp_pr /* PR有效输出(2) */,
                w.month_bug_inside_fix /* 修改自测Bug数 */,
                w.it_bug_fix /* 修改问题平台bug个数 */,
                w.month_bug_inside_create /* 产生自测Bug数 */,
                w.month_bug_province_fix /* 修改现场Bug数 */,
                w.month_bug_province_create /* 产生现场Bug数 */,
                '' as Bug计划工时/* Bug计划工时 */,
                '' as Bug报工工时/* Bug报工工时 */,
                w.month_wp_bug /* Bug有效输出(3) */,
                '' as 总有效输出/* 总有效输出 */ ,
                sum(case when b.mod_merge_count is not null and b.mod_merge_count != 0 then b.mod_merge_count else 0 end) as mod_merge_count
            from 
                boco_report_wp_bypdpj w
                left join 
                (
                    select 
                        p.account,
                        p.project,
                        p.product,
                        count(cm.id) mod_merge_count
                    from 
                        boco_calculate_process p
                        inner join boco_gitlab_code_mod cm on p.act_id = cm.related_pr_cuid
                    where 
                        p.calc_type = 'DevTask'
                        and p.pr_date = '#{monthNum}'
                    group by p.account,p.project,p.product
                ) b on b.account = w.account and b.project = w.project and b.product = w.product
            where
                w.pr_date = '#{monthNum}'
            group by w.account, w.project, w.product
        ) b on b.account = a.account and b.project = a.group_id and b.product = a.amiba_id
        left join 
        (
            /* 需求有效输出 */
            select 
                s.account /* 账号 */,
                s.project,
                s.product,
                s.reviewed_store_wp + s.itReq_wp + s.store_rel_task_wp + s.store_val_wp as req_total_output,
                s.onetimepass_store /* 评审一次通过数 */,
                s.norecord_store /* 未评审个数 */,
                s.repeatedpass_store /* 评审多次通过数 */,
                s.reviewed_store_wp /* 评审通过得分 */,
                s.itReq_count /* 需求平台分析需求个数 */,
                s.itReq_wp /* 需求平台分析需求得分 */,
                s.store_rel_task_wp /* 有效输出得分（根据研发得分） */,
                s.province_bug_response /* 现场bug承担责任个数 */,
                s.inside_bug_response /* 内部bug承担责任个数 */,
                s.province_bug_response_wp /* 现场bug承担责任总分（负分） */,
                s.inside_bug_response_wp /* 内部bug承担责任总分（负分） */,
                s.store_val_wp /* 现场满意度得分 */,
                s.pr_date /* 时间，标记是哪个月 */
            from 
                boco_report_storywp_bypdpj s
            where
                s.pr_date = '#{monthNum}'
        ) e on e.account = a.account and e.project = a.group_id and e.product = a.amiba_id
        left join 
        (
            /* 测试有效输出 */
            select 
                t.account /* 账号 */,
                t.project,
                t.product,
                t.test_case_wp + t.execute_case_wp + t.test_bug_wp + t.province_bug_response_wp 
                + t.inside_bug_response_wp + t.close_bug_wp as test_total_output,
                t.test_case_count /* 创建用例个数 */,
                t.test_case_wp /* 创建用例得分 */,
                t.execute_case_count /* 执行用例个数 */,
                t.execute_case_wp /* 执行用例得分 */,
                t.test_bug_count /* 上报bug个数 */,
                t.test_bug_wp /* 上报bug得分 */,
                t.close_bug_count /* 关闭Bug个数 */,
                t.close_bug_wp /* 关闭Bug得分 */,
                t.province_bug_response /* 现场bug承担责任个数 */,
                t.inside_bug_response /* 内部bug承担责任个数 */,
                t.province_bug_response_wp /* 现场bug承担责任总分（负分） */,
                t.inside_bug_response_wp /* 内部bug承担责任总分（负分） */,
                t.pr_date /* 时间，标记是哪个月 */
            from 
                boco_report_testwp_bypdpj t
            where
                t.pr_date = '#{monthNum}'
        ) f on f.account = a.account and f.project = a.group_id and f.product = a.amiba_id
        left join 
        (
            /* QA有效输出 */
            select 
                t.account /* 账号 */,
                t.project,
                t.product,
                t.open_bug_wp + t.close_bug_wp as qa_total_output,
                t.open_bug_wp as qa_open_bug_wp,
                t.open_bug_count as qa_open_bug_count,
                t.close_bug_wp as qa_close_bug_wp,
                t.close_bug_count as qa_close_bug_count,
                t.pr_date /* 时间，标记是哪个月 */
            from 
                boco_report_qawp_bypdpj t
            where
                t.pr_date = '#{monthNum}'
        ) g on g.account = a.account and g.project = a.group_id and g.product = a.amiba_id
        left join 
        (
            /* 市场或售前有效输出 */
            select 
                t.account /* 账号 */,
                t.project,
                t.product,
                t.work_split_wp as market_total_output,
                t.work_split_wp,
                t.work_split_count,
                t.pr_date /* 时间，标记是哪个月 */
            from 
                boco_report_marketwp_bypdpj t
            where
                t.pr_date = '#{monthNum}'
        ) h on h.account = a.account and h.project = a.group_id and h.product = a.amiba_id
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        #{groupBy}
        ";
/* --------------------------- prdmonthperformance end ------------------------------------ */

/* --------------------------- taskperformancescoredetail start ------------------------------------ */
$config->quantizedoutput->TaskPerformanceScoreDetailSql = "
select 
    g.id git_id,
    p.pr_date,
    g.committer account,
    g.project_name /* 所属项目 */,
    g.source_branch /* 来源分支 */,
    g.target_branch /* 目标分支 */,
    g.state /* PR状态 */,
    g.pr_time /* 请求日期 */,
    g.check_time /* 审核日期 */,
    g.committer /* 提交人 */,
    g.auditor /* 审核人 */,
    g.web_url,
    g.pr_url,
    g.task_id /* 任务编号 */,
    g.pr_desc /* 备注 */,
    p.act_id merge_id,
    sum(case when a.related_pr_cuid is not null then 1 else 0 end) as mod_merge_count,
    p.calc_process,
    p.calc_result
from 
    boco_gitlab_pr g
    inner join boco_calculate_process p on p.act_id = g.id
    left join boco_gitlab_code_mod a on a.related_pr_cuid = p.act_id
where 
    p.calc_type = 'DevTask'
    and g.task_id = #{taskId}
group by g.id
        ";
        
/* --------------------------- taskperformancescoredetail start ------------------------------------ */

/* --------------------------- monthperformancescoredetail start ------------------------------------ */
$config->quantizedoutput->DevTaskDetailSql = "
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
            select 
                * 
            from 
                v_report_user 
            where 
                root_path like concat(',', '#{userRootId}', ',%')
        ) a
        inner join 
        (
            select 
                g.id git_id,
                p.pr_date,
                g.committer account,
                g.project_name /* 所属项目 */,
                g.source_branch /* 来源分支 */,
                g.target_branch /* 目标分支 */,
                g.state /* PR状态 */,
                g.pr_time /* 请求日期 */,
                g.check_time /* 审核日期 */,
                g.committer /* 提交人 */,
                g.auditor /* 审核人 */,
                g.web_url,
                g.pr_url,
                g.task_id /* 任务编号 */,
                g.pr_desc /* 备注 */,
                p.act_id merge_id,
                sum(case when a.related_pr_cuid is not null then 1 else 0 end) as mod_merge_count,
                p.calc_process,
                p.calc_result
            from 
                boco_gitlab_pr g
                inner join boco_calculate_process p on p.act_id = g.id
                left join boco_gitlab_code_mod a on a.related_pr_cuid = p.act_id
            where 
                p.calc_type = 'DevTask'
                and p.pr_date = '#{monthNum}'
            group by g.id
        ) b on a.account = b.account
    where 
        1 =1 
        #{andWhereAmibaGroupAccount}
        ";
        
$config->quantizedoutput->DevBeRejectedAndLeaderCheckDetailSql = "
    select 
        a.amiba_name,
        a.group_name,
        b.account, 
        b.project_name /* 所属项目 */,
        b.source_branch /* 来源分支 */,
        b.target_branch /* 目标分支 */,
        b.state /* PR状态 */,
        b.check_time /* 审核日期 */,
        b.committer /* 提交人 */,
        b.auditor /* 审核人 */,
        b.web_url,
        b.pr_url,
        b.git_id,
        b.task_id /* 任务编号 */,
        b.pr_desc /* 备注 */,
        b.calc_process,
        b.calc_result
    from
        (
            select 
                * 
            from 
                v_report_user 
            where 
                root_path like concat(',', '#{userRootId}', ',%')
        ) a
        inner join 
        (
            select 
                g.id git_id,
                g.committer account,
                g.project_name /* 所属项目 */,
                g.source_branch /* 来源分支 */,
                g.target_branch /* 目标分支 */,
                g.state /* PR状态 */,
                g.check_time /* 审核日期 */,
                g.committer /* 提交人 */,
                g.auditor /* 审核人 */,
                g.web_url,
                g.pr_url,
                g.task_id /* 任务编号 */,
                g.pr_desc /* 备注 */,
                p.calc_process,
                p.calc_result
            from 
                boco_gitlab_pr g
                inner join boco_calculate_process p on p.act_id = g.id
            where 
                1 = 1
                and p.calc_type in('BeRejected')
                and p.pr_date = '#{monthNum}'
            /* 组长审核代码 */
            union all
            select 
                g.id git_id,
                g.auditor account,
                g.project_name /* 所属项目 */,
                g.source_branch /* 来源分支 */,
                g.target_branch /* 目标分支 */,
                g.state /* PR状态 */,
                g.check_time /* 审核日期 */,
                g.committer /* 提交人 */,
                g.auditor /* 审核人 */,
                g.web_url,
                g.pr_url,
                g.task_id /* 任务编号 */,
                g.pr_desc /* 备注 */,
                p.calc_process,
                p.calc_result
            from 
                boco_gitlab_pr g
                inner join boco_calculate_process p on p.act_id = g.id
            where 
                p.calc_type in('LeaderCheck')
                and p.pr_date = '#{monthNum}'
        ) b on a.account = b.account
    where 
        1 =1 
        #{andWhereAmibaGroupAccount}
        ";
        
$config->quantizedoutput->DevBugDetailSql = "
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
        (
            select 
                * 
            from 
                v_report_user 
            where 
                root_path like concat(',', '#{userRootId}', ',%')
        ) a
        inner join 
        (
            /* 禅道Bug */
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
                boco_calculate_process cal,
                zt_bug c
            where 
                cal.act_id = c.id
                and cal.calc_type in('ProvinceBugFix', 'InsideBugFix', 'ProvinceBugCreate', 'InsideBugCreate')
                and cal.pr_date = '#{monthNum}'
                
            union all
            /* 问题平台Bug */
            select 
                i.task_id bug_id,
                i.title bug_title,
                '' bug_type,
                i.province_name bug_source,
                i.create_time openedDate,
                '' openedBy,
                i.close_time resolvedDate,
                cal.account,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                boco_it_task2zt i
            where 
                cal.act_id = i.task_id
                and cal.calc_type in('ProvinceBugFix', 'InsideBugFix', 'ProvinceBugCreate', 'InsideBugCreate')
                and cal.pr_date = '#{monthNum}'
                and i.task_type = 21
        ) b on a.account = b.account
    where 
        1 =1 
        #{andWhereAmibaGroupAccount}
        ";
// 
$config->quantizedoutput->ReviewPassDetailSql = "
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
        (
            select 
                * 
            from 
                v_report_user 
            where 
                root_path like concat(',', '#{userRootId}', ',%')
        ) a
        inner join 
        (
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
                s.passnote passnote,
                s.revieweddate,
                cal.account,
                cal.pr_date,
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                zt_story s,
                zt_product p
            where 
                cal.act_id = s.id
                and s.product = p.id
                and cal.calc_type in ('OncePass', 'RepeatedPass')
                and cal.pr_date = '#{monthNum}'
        ) b on a.account = b.account
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        ";
// 
$config->quantizedoutput->ITReqDetailSql = "
    select 
        a.amiba_name,
        a.group_name,
        a.account, 
        
        b.project_name,
        b.story_id,
        b.story_name,
        b.openeddate,
        b.close_time,
        b.status,
        b.account,
        b.pr_date,
        b.calc_process,
        b.calc_result
    from
        (
            select 
                * 
            from 
                v_report_user 
            where 
                root_path like concat(',', '#{userRootId}', ',%')
        ) a
        inner join 
        (
            select 
                s.project_name,
                s.task_id story_id,
                s.title story_name,
                s.create_time openeddate,
                s.close_time,
                s.status,
                cal.account,
                cal.pr_date,
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                boco_it_task2zt s
            where 
                cal.act_id = s.task_id and s.task_type = 31
                and cal.calc_type in ('ITReq')
                and cal.pr_date = '#{monthNum}'
        ) b on a.account = b.account
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        ";
        
$config->quantizedoutput->ReqDevDetailSql = "
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
        (
            select 
                * 
            from 
                v_report_user 
            where 
                root_path like concat(',', '#{userRootId}', ',%')
        ) a
        inner join 
        (
            /* 关联禅道任务 */
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
                boco_calculate_process cal,
                boco_gitlab_pr b,
                zt_task c,
                zt_story d
            where 
                cal.act_id = b.id
                and b.task_id = c.id
                and c.story = d.id
                and cal.calc_type = 'ReqDev'
                and cal.pr_date = '#{monthNum}'
            
            union all
            /* 关联需求平台需求 */
            select 
                i.task_id story_id,
                i.title story_name,
                '' stage,
                '' passnote,
                '' task_id,
                '' task_name,
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
                boco_calculate_process cal,
                boco_gitlab_pr b,
                boco_it_task2zt i
            where 
                cal.act_id = b.id
                and cal.calc_type = 'ReqDev'
                and cal.pr_date = '#{monthNum}'
                and b.task_id = i.task_id
                and i.task_type = 31
            
        ) b on a.account = b.account
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        ";
        
$config->quantizedoutput->ReqSatisfyDetailSql = "
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
        (
            select 
                * 
            from 
                v_report_user 
            where 
                root_path like concat(',', '#{userRootId}', ',%')
        ) a
        inner join 
        (
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
                boco_calculate_process cal,
                boco_gitlab_pr b,
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
        ";
        
$config->quantizedoutput->BugDetailSql = "
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
        (
            select 
                * 
            from 
                v_report_user 
            where 
                root_path like concat(',', '#{userRootId}', ',%')
        ) a
        inner join 
        (
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
                boco_calculate_process cal,
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
        ";
        
$config->quantizedoutput->CaseDetailSql = "
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
        (
            select 
                * 
            from 
                v_report_user 
            where 
                root_path like concat(',', '#{userRootId}', ',%')
        ) a
        inner join 
        (
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
                boco_calculate_process cal,
                zt_case c
                left join 
                    zt_story s on c.story = s.id
            where 
                cal.act_id = c.id
                and cal.calc_type = '#{caseType}'
                and cal.pr_date = '#{monthNum}'
        ) b on a.account = b.account
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        ";
        
$config->quantizedoutput->WorkSplitDetailSql = "
    select 
        a.amiba_name,
        a.group_name,
        a.account, 
        
        b.*
    from
        (
            select 
                * 
            from 
                v_report_user 
            where 
                root_path like concat(',', '#{userRootId}', ',%')
        ) a
        inner join 
        (
            select 
                s.id story_id,
                s.title story_name,
                s.stage,
                c.id task_id,
                c.name task_name,
                cal.account,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                zt_task c
                left join 
                    zt_story s on c.story = s.id
            where 
                cal.act_id = c.id
                and cal.calc_type = 'WorkSplit'
                and cal.pr_date = '#{monthNum}'
        ) b on a.account = b.account
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        ";
/* --------------------------- monthperformancescoredetail end ------------------------------------ */

/* --------------------------- prjmonthperformancescoredetail start ------------------------------------ */
$config->quantizedoutput->PrjDevTaskDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
        b.account, 
        b.project_name /* 所属项目 */,
        b.source_branch /* 来源分支 */,
        b.target_branch /* 目标分支 */,
        b.state /* PR状态 */,
        b.pr_time /* 请求日期 */,
        b.check_time /* 审核日期 */,
        b.committer /* 提交人 */,
        b.auditor /* 审核人 */,
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
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
            select 
                g.id git_id,
                p.pr_date,
                p.account,
                p.project,
                p.product,
                g.project_name /* 所属项目 */,
                g.source_branch /* 来源分支 */,
                g.target_branch /* 目标分支 */,
                g.state /* PR状态 */,
                g.pr_time /* 请求日期 */,
                g.check_time /* 审核日期 */,
                g.committer /* 提交人 */,
                g.auditor /* 审核人 */,
                g.web_url,
                g.pr_url,
                g.task_id /* 任务编号 */,
                g.pr_desc /* 备注 */,
                p.act_id merge_id,
                (
                    select 
                        count(id) 
                    from 
                        boco_gitlab_code_mod 
                    where 
                        related_pr_cuid=p.act_id
                ) as mod_merge_count,
                p.calc_process,
                p.calc_result
            from 
                boco_gitlab_pr g
                inner join boco_calculate_process p on p.act_id = g.id
            where 
                p.calc_type = 'DevTask'
                and p.pr_date = '#{monthNum}'
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 =1 
        #{andWhereAmibaGroupAccount}
        ";
        
$config->quantizedoutput->PrjDevBeRejectedAndLeaderCheckDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
        b.account, 
        b.project_name /* 所属项目 */,
        b.source_branch /* 来源分支 */,
        b.target_branch /* 目标分支 */,
        b.state /* PR状态 */,
        b.pr_time /* 请求日期 */,
        b.check_time /* 审核日期 */,
        b.committer /* 提交人 */,
        b.auditor /* 审核人 */,
        b.web_url,
        b.pr_url,
        b.git_id,
        b.task_id /* 任务编号 */,
        b.pr_desc /* 备注 */,
        b.calc_process,
        b.calc_result
    from
        (
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
            select 
                g.id git_id,
                p.pr_date,
                p.account,
                p.project,
                p.product,
                g.project_name /* 所属项目 */,
                g.source_branch /* 来源分支 */,
                g.target_branch /* 目标分支 */,
                g.state /* PR状态 */,
                g.pr_time /* 请求日期 */,
                g.check_time /* 审核日期 */,
                g.committer /* 提交人 */,
                g.auditor /* 审核人 */,
                g.web_url,
                g.pr_url,
                g.task_id /* 任务编号 */,
                g.pr_desc /* 备注 */,
                p.calc_process,
                p.calc_result
            from 
                boco_gitlab_pr g
                inner join boco_calculate_process p on p.act_id = g.id
            where 
                p.calc_type in('BeRejected')
                and p.pr_date = '#{monthNum}'
            /* 组长审核代码 */
            union all
            select 
                g.id git_id,
                p.pr_date,
                p.account,
                p.project,
                p.product,
                g.project_name /* 所属项目 */,
                g.source_branch /* 来源分支 */,
                g.target_branch /* 目标分支 */,
                g.state /* PR状态 */,
                g.pr_time /* 请求日期 */,
                g.check_time /* 审核日期 */,
                g.committer /* 提交人 */,
                g.auditor /* 审核人 */,
                g.web_url,
                g.pr_url,
                g.task_id /* 任务编号 */,
                g.pr_desc /* 备注 */,
                p.calc_process,
                p.calc_result
            from 
                boco_gitlab_pr g
                inner join boco_calculate_process p on p.act_id = g.id
            where 
                p.calc_type in('LeaderCheck')
                and p.pr_date = '#{monthNum}'
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 =1 
        #{andWhereAmibaGroupAccount}
        ";
        
$config->quantizedoutput->PrjDevBugDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
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
        (
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
            /* 禅道Bug */
            select 
                c.id bug_id,
                c.title bug_title,
                c.type bug_type,
                c.source bug_source,
                c.openedDate,
                c.openedBy,
                c.resolvedDate,
                cal.account,
                cal.project,
                cal.product,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                zt_bug c
            where 
                cal.act_id = c.id
                and cal.calc_type in('ProvinceBugFix', 'InsideBugFix', 'ProvinceBugCreate', 'InsideBugCreate')
                and cal.pr_date = '#{monthNum}'
                
            union all
            /* 问题平台Bug */
            select 
                i.task_id bug_id,
                i.title bug_title,
                '' bug_type,
                i.province_name bug_source,
                i.create_time openedDate,
                '' openedBy,
                i.close_time resolvedDate,
                cal.account,
                cal.project,
                cal.product,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                boco_it_task2zt i
            where 
                cal.act_id = i.task_id
                and cal.calc_type in('ProvinceBugFix', 'InsideBugFix', 'ProvinceBugCreate', 'InsideBugCreate')
                and cal.pr_date = '#{monthNum}'
                and i.task_type = 21
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 =1 
        #{andWhereAmibaGroupAccount}
        ";
// 
$config->quantizedoutput->PrjReviewPassDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
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
        (
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
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
                s.passnote passnote,
                s.revieweddate,
                cal.account,
                cal.project,
                cal.product,
                cal.pr_date,
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                zt_story s,
                zt_product p
            where 
                cal.act_id = s.id
                and s.product = p.id
                and cal.calc_type in ('OncePass', 'RepeatedPass')
                and cal.pr_date = '#{monthNum}'
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        ";
// 
$config->quantizedoutput->PrjITReqDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
        a.account, 
        
        b.project_name,
        b.story_id,
        b.story_name,
        b.openeddate,
        b.close_time,
        b.status,
        b.account,
        b.pr_date,
        b.calc_process,
        b.calc_result
    from
        (
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
            select 
                s.project_name,
                s.task_id story_id,
                s.title story_name,
                s.create_time openeddate,
                s.close_time,
                s.status,
                cal.account,
                cal.project,
                cal.product,
                cal.pr_date,
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                boco_it_task2zt s
            where 
                cal.act_id = s.task_id and s.task_type = 31
                and cal.calc_type in ('ITReq')
                and cal.pr_date = '#{monthNum}'
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        ";
        
$config->quantizedoutput->PrjReqDevDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
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
        (
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
            /* 关联禅道任务 */
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
                cal.project,
                cal.product,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                boco_gitlab_pr b,
                zt_task c,
                zt_story d
            where 
                cal.act_id = b.id
                and b.task_id = c.id
                and c.story = d.id
                and cal.calc_type = 'ReqDev'
                and cal.pr_date = '#{monthNum}'
            
            union all
            /* 关联需求平台需求 */
            select 
                i.task_id story_id,
                i.title story_name,
                '' stage,
                '' passnote,
                '' task_id,
                '' task_name,
                'A' satis,
                b.committer,
                b.id prId,
                b.check_time,
                b.source_branch,
                b.state,
                cal.account,
                cal.project,
                cal.product,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                boco_gitlab_pr b,
                boco_it_task2zt i
            where 
                cal.act_id = b.id
                and cal.calc_type = 'ReqDev'
                and cal.pr_date = '#{monthNum}'
                and b.task_id = i.task_id
                and i.task_type = 31
            
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        ";
        
$config->quantizedoutput->PrjReqSatisfyDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
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
        (
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
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
                cal.project,
                cal.product,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                boco_gitlab_pr b,
                zt_task c,
                zt_story d
            where 
                cal.act_id = b.id
                and b.task_id = c.id
                and c.story = d.id
                and cal.calc_type = 'ReqDev'
                and cal.pr_date = '#{monthNum}'
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        ";
        
$config->quantizedoutput->PrjBugDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
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
        (
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
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
                cal.project,
                cal.product,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                zt_bug c
                left join 
                    zt_story d on c.story = d.id
            where 
                cal.act_id = c.id
                and cal.calc_type = '#{bugType}'
                and cal.pr_date = '#{monthNum}'
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        ";
        
$config->quantizedoutput->PrjCaseDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
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
        (
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
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
                cal.project,
                cal.product,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                zt_case c
                left join 
                    zt_story s on c.story = s.id
            where 
                cal.act_id = c.id
                and cal.calc_type = '#{caseType}'
                and cal.pr_date = '#{monthNum}'
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        ";
        
$config->quantizedoutput->PrjWorkSplitDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
        a.account, 
        
        b.*
    from
        (
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
            select 
                s.id story_id,
                s.title story_name,
                s.stage,
                c.id task_id,
                c.name task_name,
                cal.account,
                cal.project,
                cal.product,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                zt_task c
                left join 
                    zt_story s on c.story = s.id
            where 
                cal.act_id = c.id
                and cal.calc_type = 'WorkSplit'
                and cal.pr_date = '#{monthNum}'
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        ";
/* --------------------------- prjmonthperformancescoredetail end ------------------------------------ */

/* --------------------------- prdmonthperformancescoredetail start ------------------------------------ */
$config->quantizedoutput->PrdDevTaskDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
        b.account, 
        b.project_name /* 所属项目 */,
        b.source_branch /* 来源分支 */,
        b.target_branch /* 目标分支 */,
        b.state /* PR状态 */,
        b.pr_time /* 请求日期 */,
        b.check_time /* 审核日期 */,
        b.committer /* 提交人 */,
        b.auditor /* 审核人 */,
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
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
            select 
                g.id git_id,
                p.pr_date,
                p.account,
                p.project,
                p.product,
                g.project_name /* 所属项目 */,
                g.source_branch /* 来源分支 */,
                g.target_branch /* 目标分支 */,
                g.state /* PR状态 */,
                g.pr_time /* 请求日期 */,
                g.check_time /* 审核日期 */,
                g.committer /* 提交人 */,
                g.auditor /* 审核人 */,
                g.web_url,
                g.pr_url,
                g.task_id /* 任务编号 */,
                g.pr_desc /* 备注 */,
                p.act_id merge_id,
                (
                    select 
                        count(id) 
                    from 
                        boco_gitlab_code_mod 
                    where 
                        related_pr_cuid=p.act_id
                ) as mod_merge_count,
                p.calc_process,
                p.calc_result
            from 
                boco_gitlab_pr g 
                inner join boco_calculate_process p on p.act_id = g.id
            where 
                p.calc_type = 'DevTask'
                and p.pr_date = '#{monthNum}'
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 =1 
        #{andWhereAmibaGroupAccount}
        ";
        
$config->quantizedoutput->PrdDevBeRejectedAndLeaderCheckDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
        b.account, 
        b.project_name /* 所属项目 */,
        b.source_branch /* 来源分支 */,
        b.target_branch /* 目标分支 */,
        b.state /* PR状态 */,
        b.pr_time /* 请求日期 */,
        b.check_time /* 审核日期 */,
        b.committer /* 提交人 */,
        b.auditor /* 审核人 */,
        b.web_url,
        b.pr_url,
        b.git_id,
        b.task_id /* 任务编号 */,
        b.pr_desc /* 备注 */,
        b.calc_process,
        b.calc_result
    from
        (
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
            select 
                g.id git_id,
                p.pr_date,
                p.account,
                p.project,
                p.product,
                g.project_name /* 所属项目 */,
                g.source_branch /* 来源分支 */,
                g.target_branch /* 目标分支 */,
                g.state /* PR状态 */,
                g.pr_time /* 请求日期 */,
                g.check_time /* 审核日期 */,
                g.committer /* 提交人 */,
                g.auditor /* 审核人 */,
                g.web_url,
                g.pr_url,
                g.task_id /* 任务编号 */,
                g.pr_desc /* 备注 */,
                p.calc_process,
                p.calc_result
            from 
                boco_gitlab_pr g
                inner join boco_calculate_process p on p.act_id = g.id
            where 
                p.calc_type in('BeRejected')
                and p.pr_date = '#{monthNum}'
            /* 组长审核代码 */
            union all
            select 
                g.id git_id,
                p.pr_date,
                p.account,
                p.project,
                p.product,
                g.project_name /* 所属项目 */,
                g.source_branch /* 来源分支 */,
                g.target_branch /* 目标分支 */,
                g.state /* PR状态 */,
                g.pr_time /* 请求日期 */,
                g.check_time /* 审核日期 */,
                g.committer /* 提交人 */,
                g.auditor /* 审核人 */,
                g.web_url,
                g.pr_url,
                g.task_id /* 任务编号 */,
                g.pr_desc /* 备注 */,
                p.calc_process,
                p.calc_result
            from 
                boco_gitlab_pr g
                inner join boco_calculate_process p on p.act_id = g.id
            where 
                p.calc_type in('LeaderCheck')
                and p.pr_date = '#{monthNum}'
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 =1 
        #{andWhereAmibaGroupAccount}
        ";
        
$config->quantizedoutput->PrdDevBugDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
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
        (
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
            /* 禅道Bug */
            select 
                c.id bug_id,
                c.title bug_title,
                c.type bug_type,
                c.source bug_source,
                c.openedDate,
                c.openedBy,
                c.resolvedDate,
                cal.account,
                cal.project,
                cal.product,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                zt_bug c
            where 
                cal.act_id = c.id
                and cal.calc_type in('ProvinceBugFix', 'InsideBugFix', 'ProvinceBugCreate', 'InsideBugCreate')
                and cal.pr_date = '#{monthNum}'
                
            union all
            /* 问题平台Bug */
            select 
                i.task_id bug_id,
                i.title bug_title,
                '' bug_type,
                i.province_name bug_source,
                i.create_time openedDate,
                '' openedBy,
                i.close_time resolvedDate,
                cal.account,
                cal.project,
                cal.product,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                boco_it_task2zt i
            where 
                cal.act_id = i.task_id
                and cal.calc_type in('ProvinceBugFix', 'InsideBugFix', 'ProvinceBugCreate', 'InsideBugCreate')
                and cal.pr_date = '#{monthNum}'
                and i.task_type = 21
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 =1 
        #{andWhereAmibaGroupAccount}
        ";
// 
$config->quantizedoutput->PrdReviewPassDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
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
        (
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
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
                s.passnote passnote,
                s.revieweddate,
                cal.account,
                cal.project,
                cal.product,
                cal.pr_date,
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                zt_story s,
                zt_product p
            where 
                cal.act_id = s.id
                and s.product = p.id
                and cal.calc_type in ('OncePass', 'RepeatedPass')
                and cal.pr_date = '#{monthNum}'
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        ";
// 
$config->quantizedoutput->PrdITReqDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
        a.account, 
        
        b.project_name,
        b.story_id,
        b.story_name,
        b.openeddate,
        b.close_time,
        b.status,
        b.account,
        b.pr_date,
        b.calc_process,
        b.calc_result
    from
        (
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
            select 
                s.project_name,
                s.task_id story_id,
                s.title story_name,
                s.create_time openeddate,
                s.close_time,
                s.status,
                cal.account,
                cal.project,
                cal.product,
                cal.pr_date,
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                boco_it_task2zt s
            where 
                cal.act_id = s.task_id and s.task_type = 31
                and cal.calc_type in ('ITReq')
                and cal.pr_date = '#{monthNum}'
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        ";
        
$config->quantizedoutput->PrdReqDevDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
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
        (
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
            /* 关联禅道任务 */
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
                cal.project,
                cal.product,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                boco_gitlab_pr b,
                zt_task c,
                zt_story d
            where 
                cal.act_id = b.id
                and b.task_id = c.id
                and c.story = d.id
                and cal.calc_type = 'ReqDev'
                and cal.pr_date = '#{monthNum}'
            
            union all
            /* 关联需求平台需求 */
            select 
                i.task_id story_id,
                i.title story_name,
                '' stage,
                '' passnote,
                '' task_id,
                '' task_name,
                'A' satis,
                b.committer,
                b.id prId,
                b.check_time,
                b.source_branch,
                b.state,
                cal.account,
                cal.project,
                cal.product,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                boco_gitlab_pr b,
                boco_it_task2zt i
            where 
                cal.act_id = b.id
                and cal.calc_type = 'ReqDev'
                and cal.pr_date = '#{monthNum}'
                and b.task_id = i.task_id
                and i.task_type = 31
            
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        ";
        
$config->quantizedoutput->PrdReqSatisfyDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
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
        (
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
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
                cal.project,
                cal.product,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                boco_gitlab_pr b,
                zt_task c,
                zt_story d
            where 
                cal.act_id = b.id
                and b.task_id = c.id
                and c.story = d.id
                and cal.calc_type = 'ReqDev'
                and cal.pr_date = '#{monthNum}'
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        ";
        
$config->quantizedoutput->PrdBugDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
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
        (
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
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
                cal.project,
                cal.product,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                zt_bug c
                left join 
                    zt_story d on c.story = d.id
            where 
                cal.act_id = c.id
                and cal.calc_type = '#{bugType}'
                and cal.pr_date = '#{monthNum}'
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        ";
        
$config->quantizedoutput->PrdCaseDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
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
        (
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
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
                cal.project,
                cal.product,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                zt_case c
                left join 
                    zt_story s on c.story = s.id
            where 
                cal.act_id = c.id
                and cal.calc_type = '#{caseType}'
                and cal.pr_date = '#{monthNum}'
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        ";
        
$config->quantizedoutput->PrdWorkSplitDetailSql = "
    select 
        a.project amiba_name,
        a.product group_name,
        a.account, 
        
        b.*
    from
        (
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
            select 
                s.id story_id,
                s.title story_name,
                s.stage,
                c.id task_id,
                c.name task_name,
                cal.account,
                cal.project,
                cal.product,
                cal.pr_date,                
                cal.calc_process,
                cal.calc_result
            from  
                boco_calculate_process cal,
                zt_task c
                left join 
                    zt_story s on c.story = s.id
            where 
                cal.act_id = c.id
                and cal.calc_type = 'WorkSplit'
                and cal.pr_date = '#{monthNum}'
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
    where 
        1 = 1
        #{andWhereAmibaGroupAccount}
        ";
/* --------------------------- prdmonthperformancescoredetail end ------------------------------------ */

/* --------------------------- monthreportexport start ------------------------------------ */
$config->monthreportexport = new stdClass();
$config->monthreportexport->list->exportFields = 'amiba_name,group_name,realname,account,total_time,day_avg_time,
    extra_time,total_output,day_avg_output,extra_output,output_efficiency';
/* --------------------------- monthreportexport end ------------------------------------ */

/* --------------------------- prjmonthreportexport start ------------------------------------ */
$config->prjmonthreportexport = new stdClass();
$config->prjmonthreportexport->list->exportFields = 'project_name,product_name,realname,account,total_time,day_avg_time,
    total_output,day_avg_output,output_efficiency';
/* --------------------------- prjmonthreportexport end ------------------------------------ */

/* --------------------------- prdmonthreportexport start ------------------------------------ */
$config->prdmonthreportexport = new stdClass();
$config->prdmonthreportexport->list->exportFields = 'product_name,project_name,realname,account,total_time,day_avg_time,
    total_output,day_avg_output,output_efficiency';
/* --------------------------- prdmonthreportexport end ------------------------------------ */

/* --------------------------- monthreport start ------------------------------------ */
$config->quantizedoutput->standardOutput = '1500';

$config->quantizedoutput->MonthAmibasSql = "
    select 
        a.amiba_name,
        a.group_name,
        a.realname as realname,
        a.account,
        b.total_time,
        convert(b.total_time / #{workDayCount}, decimal(10, 1)) as day_avg_time,
        b.total_time - #{workDayCount} * 8 as extra_time,
        convert(ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
            + ifnull(g.total_output, 0) + ifnull(h.total_output, 0), decimal(10, 0)) as total_output,
        convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
            + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)) / #{workDayCount}, decimal(10, 1)) as day_avg_output,
        convert(ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
            + ifnull(g.total_output, 0) + ifnull(h.total_output, 0) - #{standardOutput}, decimal(10, 0)) as extra_output,
        convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
            + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)) / b.total_time, decimal(10, 0)) as output_efficiency,
        c.mod_merge_count
    from
        (
            select 
                * 
            from 
                v_report_user 
            where 
                root_path like concat(',', '#{userRootId}', ',%')
        ) a
        left join 
        (
            /* 月工时统计 */
            select 
                w.account,
                sum(w.work_time) as total_time
            from 
                dw_worklog_sync w 
            where 
                w.task_type in (1,2,3,21,31) #{andWhereWorkdate}
                /* and w.work_date between '2017-05-01' and '2017-05-30' */
            group by w.account
        ) b on a.account = b.account
        left join 
        (
            /* 研发任务有效输出 */
            select
                w.account,
                w.month_wp_task + w.month_wp_pr + w.month_wp_bug as total_output,
                sum(case when b.mod_merge_count is not null and b.mod_merge_count != 0 then b.mod_merge_count else 0 end) as mod_merge_count
            from 
                boco_report_wp_byaccount w
                left join 
                (
                    select 
                        p.account,
                        count(cm.id) mod_merge_count
                    from 
                        boco_calculate_process p
                        inner join  boco_gitlab_code_mod cm on p.act_id = cm.related_pr_cuid
                    where 
                        p.calc_type = 'DevTask'
                        and p.pr_date = '#{monthNum}'
                    group by p.account
                ) b on b.account = w.account 
            where
                w.pr_date = '#{monthNum}'
                /* and w.pr_date = '201705' */
            group by w.account
        ) c on a.account = c.account
        left join 
        (
            /* 需求有效输出 */
            select 
                s.account,
                s.reviewed_store_wp + s.itReq_wp + s.store_rel_task_wp + s.province_bug_response_wp 
                + s.inside_bug_response_wp + s.store_val_wp as total_output 
            from 
                boco_report_storywp_byaccount s
            where s.pr_date = '#{monthNum}'
        ) d on a.account = d.account
        left join 
        (
            /* 测试有效输出 */
            select 
                t.account,
                t.test_case_wp + t.execute_case_wp + t.test_bug_wp + t.province_bug_response_wp 
                + t.inside_bug_response_wp + t.close_bug_wp as total_output
            from 
                boco_report_testwp_byaccount t
            where
                t.pr_date = '#{monthNum}'
        ) f on a.account = f.account
        left join 
        (
            /* QA有效输出 */
            select 
                t.account,
                t.open_bug_wp + t.close_bug_wp as total_output
            from 
                boco_report_qawp_byaccount t
            where
                t.pr_date = '#{monthNum}'
        ) g on a.account = g.account
        left join 
        (
            /* 市场或售前有效输出 */
            select 
                t.account,
                t.work_split_wp as total_output
            from 
                boco_report_marketwp_byaccount t
            where
                t.pr_date = '#{monthNum}'
        ) h on a.account = h.account
        
    order by a.amiba_id desc, a.group_id asc, total_output desc";
/* --------------------------- monthreport end ------------------------------------ */

/* --------------------------- prjmonthreport start ------------------------------------ */
$config->quantizedoutput->prjMonthAmibasSql = "
    select 
        a.project amiba_id,
        a.project_name,
        a.product group_id,
        a.product_name,
        a.account,
        a.realname,
        ifnull(b.total_time, 0) total_time,
        convert(b.total_time / #{workDayCount}, decimal(10, 1)) as day_avg_time,
        b.total_time - #{workDayCount} * 8 as extra_time,
        convert(ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
            + ifnull(g.total_output, 0) + ifnull(h.total_output, 0), decimal(10, 0)) as total_output,
        convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
            + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)) / #{workDayCount}, decimal(10, 1)) as day_avg_output,
        convert(ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
            + ifnull(g.total_output, 0) + ifnull(h.total_output, 0) - #{standardOutput}, decimal(10, 0)) as extra_output,
        convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
            + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)) / b.total_time, decimal(10, 0)) as output_efficiency,
        c.mod_merge_count
    from
        (
            select 
                distinct
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account,
                u.realname
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                left join zt_product pd on pd.id = ppa.product
                inner join zt_user u on u.account = ppa.account
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        left join 
        (
            /* 月工时统计 */
            select 
                pp.project,
                pp.product,
                a.account,
                sum(a.total_time) total_time
            from 
                boco_product_project pp
                inner join 
                (
                    select 
                        w.account,
                        w.task_id,
                        case w.task_type
                            when 1 then 'story' 
                            when 2 then 'task'
                            when 3 then 'bug'
                            else 'other'
                        end as task_type,
                        sum(work_time) total_time
                    from 
                        dw_worklog_sync w 
                    where 
                        w.task_type in (1,2,3) #{andWhereWorkdate}
                        /* and w.work_date between '2017-08-01' and '2017-08-30' */
                    group by w.account, w.task_id, w.task_type
                ) a on a.task_id = pp.work_id and a.task_type = pp.work_type
            group by pp.project, pp.product, a.account
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
        left join 
        (
            /* 研发任务有效输出 */
            select
                w.account,
                w.project,
                w.product,
                w.month_wp_task + w.month_wp_pr + w.month_wp_bug as total_output,
                sum(case when b.mod_merge_count is not null and b.mod_merge_count != 0 then b.mod_merge_count else 0 end) as mod_merge_count
            from 
                boco_report_wp_bypdpj w
                left join 
                (
                    select 
                        p.account,
                        p.project,
                        p.product,
                        count(cm.id) mod_merge_count
                    from 
                        boco_calculate_process p
                        inner join  boco_gitlab_code_mod cm on p.act_id = cm.related_pr_cuid
                    where 
                        p.calc_type = 'DevTask'
                        and p.pr_date = '#{monthNum}'
                    group by p.account,p.project,p.product
                ) b on b.account = w.account and b.project = w.project and b.product = w.product
            where 
                w.pr_date = '#{monthNum}'
            group by w.account, w.project, w.product
        ) c on c.account = a.account and c.project = a.project and c.product = a.product
        left join 
        (
            /* 需求有效输出 */
            select 
                s.account,
                s.project,
                s.product,
                s.reviewed_store_wp + s.itReq_wp + s.store_rel_task_wp + s.province_bug_response_wp 
                + s.inside_bug_response_wp + s.store_val_wp as total_output 
            from 
                boco_report_storywp_bypdpj s
            where s.pr_date = '#{monthNum}'
        ) d on d.account = a.account and d.project = a.project and d.product = a.product
        left join 
        (
            /* 测试有效输出 */
            select 
                t.account,
                t.project,
                t.product,
                t.test_case_wp + t.execute_case_wp + t.test_bug_wp + t.province_bug_response_wp 
                + t.inside_bug_response_wp + t.close_bug_wp as total_output
            from 
                boco_report_testwp_bypdpj t
            where
                t.pr_date = '#{monthNum}'
        ) f on f.account = a.account and f.project = a.project and f.product = a.product
        left join 
        (
            /* QA有效输出 */
            select 
                t.account,
                t.project,
                t.product,
                t.open_bug_wp + t.close_bug_wp as total_output
            from 
                boco_report_qawp_bypdpj t
            where
                t.pr_date = '#{monthNum}'
        ) g on g.account = a.account and g.project = a.project and g.product = a.product
        left join 
        (
            /* 市场或售前有效输出 */
            select 
                t.account,
                t.project,
                t.product,
                t.work_split_wp as total_output
            from 
                boco_report_marketwp_bypdpj t
            where
                t.pr_date = '#{monthNum}'
        ) h on h.account = a.account and h.project = a.project and h.product = a.product
    order by a.project desc, a.product asc, total_output desc
    ";
/* --------------------------- prjmonthreport end ------------------------------------ */

/* --------------------------- prdmonthreport start ------------------------------------ */
$config->quantizedoutput->prdMonthAmibasSql = "
    select 
        a.project group_id,
        a.project_name,
        a.product amiba_id,
        a.product_name,
        a.account,
        a.realname,
        b.total_time,
        convert(b.total_time / #{workDayCount}, decimal(10, 1)) as day_avg_time,
        b.total_time - #{workDayCount} * 8 as extra_time,
        convert(ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
            + ifnull(g.total_output, 0) + ifnull(h.total_output, 0), decimal(10, 0)) as total_output,
        convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
            + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)) / #{workDayCount}, decimal(10, 1)) as day_avg_output,
        convert(ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
            + ifnull(g.total_output, 0) + ifnull(h.total_output, 0) - #{standardOutput}, decimal(10, 0)) as extra_output,
        convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
            + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)) / b.total_time, decimal(10, 0)) as output_efficiency,
        c.mod_merge_count
    from
        (
            select 
                distinct
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account,
                u.realname
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                left join zt_product pd on pd.id = ppa.product
                inner join zt_user u on u.account = ppa.account
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        left join 
        (
            /* 月工时统计 */
            select 
                pp.project,
                pp.product,
                a.account,
                sum(a.total_time) total_time
            from 
                boco_product_project pp
                inner join 
                (
                    select 
                        w.account,
                        w.task_id,
                        case w.task_type
                            when 1 then 'story' 
                            when 2 then 'task'
                            when 3 then 'bug'
                            else 'other'
                        end as task_type,
                        sum(work_time) total_time
                    from 
                        dw_worklog_sync w 
                    where 
                        w.task_type in (1,2,3) #{andWhereWorkdate}
                        /* and w.work_date between '2017-08-01' and '2017-08-30' */
                    group by w.account, w.task_id, w.task_type
                ) a on a.task_id = pp.work_id and a.task_type = pp.work_type
            group by pp.project, pp.product, a.account
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
        left join 
        (
            /* 研发任务有效输出 */
            select
                w.account,
                w.project,
                w.product,
                w.month_wp_task + w.month_wp_pr + w.month_wp_bug as total_output,
                sum(case when b.mod_merge_count is not null and b.mod_merge_count != 0 then b.mod_merge_count else 0 end) as mod_merge_count
            from 
                boco_report_wp_bypdpj w
                left join 
                (
                    select 
                        p.account,
                        p.project,
                        p.product,
                        count(cm.id) mod_merge_count
                    from 
                        boco_calculate_process p
                        inner join  boco_gitlab_code_mod cm on p.act_id = cm.related_pr_cuid
                    where 
                        p.calc_type = 'DevTask'
                        and p.pr_date = '#{monthNum}'
                    group by p.account,p.project,p.product
                ) b on b.account = w.account and b.project = w.project and b.product = w.product
            where 
                w.pr_date = '#{monthNum}'
            group by w.account, w.project, w.product
        ) c on c.account = a.account and c.project = a.project and c.product = a.product
        left join 
        (
            /* 需求有效输出 */
            select 
                s.account,
                s.project,
                s.product,
                s.reviewed_store_wp + s.itReq_wp + s.store_rel_task_wp + s.province_bug_response_wp 
                + s.inside_bug_response_wp + s.store_val_wp as total_output 
            from 
                boco_report_storywp_bypdpj s
            where s.pr_date = '#{monthNum}'
        ) d on d.account = a.account and d.project = a.project and d.product = a.product
        left join 
        (
            /* 测试有效输出 */
            select 
                t.account,
                t.project,
                t.product,
                t.test_case_wp + t.execute_case_wp + t.test_bug_wp + t.province_bug_response_wp 
                + t.inside_bug_response_wp + t.close_bug_wp as total_output
            from 
                boco_report_testwp_bypdpj t
            where
                t.pr_date = '#{monthNum}'
        ) f on f.account = a.account and f.project = a.project and f.product = a.product
        left join 
        (
            /* QA有效输出 */
            select 
                t.account,
                t.project,
                t.product,
                t.open_bug_wp + t.close_bug_wp as total_output
            from 
                boco_report_qawp_bypdpj t
            where
                t.pr_date = '#{monthNum}'
        ) g on g.account = a.account and g.project = a.project and g.product = a.product
        left join 
        (
            /* 市场或售前有效输出 */
            select 
                t.account,
                t.project,
                t.product,
                t.work_split_wp as total_output
            from 
                boco_report_marketwp_bypdpj t
            where
                t.pr_date = '#{monthNum}'
        ) h on h.account = a.account and h.project = a.project and h.product = a.product
    order by a.product asc, a.project desc, total_output desc
    ";
/* --------------------------- prdmonthreport end ------------------------------------ */

/* --------------------------- sort start ------------------------------------ */
$config->quantizedoutput->PersonTimeTop30Sql = "
    select 
        a.amiba_name,
        a.group_name,
        a.realname,
        a.account,
        b.work_date,
        b.total_time as total_time,
        convert(b.total_time / #{workDayCount}, decimal(10, 1)) as day_avg_time,
        b.total_time - #{workDayCount} * 8 as extra_time,
        convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)), decimal(10, 0)) as total_output,
        convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)) / #{workDayCount}, decimal(10, 1)) as day_avg_output,
        convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)) / b.total_time, decimal(10, 0))  as output_efficiency
    from
        (
            select 
                * 
            from 
                v_report_user 
            where 
                root_path like concat(',', '#{userRootId}', ',%')
        ) a
        left join 
        (
            select 
                w.account,
                w.work_date,
                sum(w.work_time) as total_time
            from 
                dw_worklog_sync w 
            where 
                w.task_type in (1,2,3,21,31) #{andWhereWorkdate}
            group by w.account
        ) b on a.account = b.account
        left join 
        (
            /* 研发有效输出 */
            select
                w.account,
                sum(w.month_wp_task) + sum(w.month_wp_pr) + sum(w.month_wp_bug) as total_output
            from 
                boco_report_wp_byaccount w
            where
                w.pr_date in (#{monthNums})
                /* and w.pr_date = '201705' */
                group by w.account
        ) c on a.account = c.account
        left join 
        (
            /* 需求类有效输出 */
            select 
                s.account,
                sum(s.reviewed_store_wp) + sum(s.itReq_wp) + sum(s.store_rel_task_wp) + sum(s.province_bug_response_wp) 
                + sum(s.inside_bug_response_wp) + sum(s.store_val_wp) as total_output
            from 
                boco_report_storywp_byaccount s
            where 
                s.pr_date in (#{monthNums})
            group by s.account
        ) d on a.account = d.account
        left join 
        (
            /* 测试类有效输出 */
            select 
                t.account,
                sum(t.test_case_wp) + sum(t.execute_case_wp) + sum(t.test_bug_wp) + sum(t.province_bug_response_wp) 
                + sum(t.inside_bug_response_wp) + sum(t.close_bug_wp) as total_output
            from 
                boco_report_testwp_byaccount t
            where
                t.pr_date in (#{monthNums})
            group by t.account
        ) f on a.account = f.account
        left join 
        (
            /* QA有效输出 */
            select 
                t.account,
                sum(t.open_bug_wp) + sum(t.close_bug_wp) as total_output
            from 
                boco_report_qawp_byaccount t
            where
                t.pr_date in (#{monthNums})
            group by t.account
        ) g on a.account = g.account
        left join 
        (
            /* 市场或售前有效输出 */
            select 
                t.account,
                sum(t.work_split_wp) as total_output
            from 
                boco_report_marketwp_byaccount t
            where
                t.pr_date in (#{monthNums})
            group by t.account
        ) h on a.account = h.account
        ";
    
$config->quantizedoutput->PersonAvgAmibaTimeTopSql = "
    select 
        a.amiba_name,
        convert(sum(b.total_time) / count(a.account), decimal(10, 1)) as amiba_person_time
    from
        (
            select 
                * 
            from 
                v_report_user 
            where 
                root_path like concat(',', '#{userRootId}', ',%')
        ) a
        left join 
        (
            select 
                w.account,
                w.work_date,
                sum(w.work_time) as total_time
            from 
                dw_worklog_sync w 
            where 
                w.task_type in (1,2,3,21,31) #{andWhereWorkdate}
            group by w.account
        ) b on a.account = b.account
    group by a.amiba_name 
    order by amiba_person_time desc";
/* --------------------------- sort end ------------------------------------ */

/* --------------------------- prjsort start ------------------------------------ */
$config->quantizedoutput->PrjPersonTimeTop30Sql = "
    select 
    a.amiba_id,
    a.amiba_name amiba_name,
    a.group_id group_id,
    a.group_name group_name,
    a.account,
    a.realname,
    sum(a.total_time) total_time,
    sum(a.day_avg_time) day_avg_time,
    sum(a.extra_time) extra_time,
    sum(a.total_output) total_output,
    sum(a.day_avg_output) day_avg_output,
    sum(a.output_efficiency) output_efficiency
from 
    (
        select 
            a.project amiba_id,
            a.project_name amiba_name,
            a.product_name group_name,
            a.product group_id,
            a.account,
            a.realname,
            b.total_time as total_time,
            convert(b.total_time / #{workDayCount}, decimal(10, 1)) as day_avg_time,
            b.total_time - #{workDayCount} * 8 as extra_time,
            convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0)  + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)), decimal(10, 0)) as total_output,
            convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0)  + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)) / #{workDayCount}, decimal(10, 1)) as day_avg_output,
            convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0)  + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)) / b.total_time, decimal(10, 0))  as output_efficiency
        from
            (
                select 
                    ppa.project project,
                    case 
                        when pj.name is null then '公共'
                        when pj.name = '' then '公共'
                        else pj.name
                    end as project_name,
                    ppa.product product,
                    case
                        when pd.name is null then '公共'
                        when pd.name = '' then '公共'
                        else pd.name
                    end as product_name,
                    u.realname,
                    ppa.account
                from
                    boco_product_project_account ppa
                    inner join zt_user u on u.account = ppa.account
                    left join zt_project pj on pj.id = ppa.project
                    left join zt_product pd on pd.id = ppa.product
                where 
                    ppa.account != '' and ppa.pr_date in (#{monthNums})
                    and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                    and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
                    and ppa.project = #{amibaId}
            ) a
            left join 
            (
                /* 月工时统计 */
                select 
                    pp.project,
                    pp.product,
                    a.account,
                    sum(a.total_time) total_time
                from 
                    boco_product_project pp
                    inner join 
                    (
                        select 
                            w.account,
                            w.task_id,
                            case w.task_type
                                when 1 then 'story' 
                                when 2 then 'task'
                                when 3 then 'bug'
                                else 'other'
                            end as task_type,
                            sum(work_time) total_time
                        from 
                            dw_worklog_sync w 
                        where 
                            w.task_type in (1,2,3)
                            #{andWhereWorkdate}
                            /* and w.work_date between '2017-08-01' and '2017-08-30' */
                        group by w.account, w.task_id, w.task_type
                    ) a on a.task_id = pp.work_id and a.task_type = pp.work_type
                where
                    pp.project = #{amibaId}
                group by pp.project, pp.product, a.account
            ) b on b.account = a.account and b.project = a.project and b.product = a.product
            left join 
            (
                /* 研发有效输出 */
                select
                    w.account,
                    w.project,
                    w.product,
                    sum(w.month_wp_task) + sum(w.month_wp_pr) + sum(w.month_wp_bug) as total_output
                from 
                    boco_report_wp_bypdpj w
                where
                    w.pr_date in (#{monthNums})
                    and w.project = #{amibaId}
                    /* and w.pr_date = '201705' */
                    group by w.account, w.project, w.product
            ) c on c.account = a.account and c.project = a.project and c.product = a.product
            left join 
            (
                /* 需求类有效输出 */
                select 
                    s.account,
                    s.project,
                    s.product,
                    sum(s.reviewed_store_wp) + sum(s.itReq_wp) + sum(s.store_rel_task_wp) + sum(s.province_bug_response_wp) 
                    + sum(s.inside_bug_response_wp) + sum(s.store_val_wp) as total_output /* 需求有效输出 */
                from 
                    boco_report_storywp_bypdpj s
                where 
                    s.pr_date in (#{monthNums})
                    and s.project = #{amibaId}
                group by s.account, s.project, s.product
            ) d on d.account = a.account and d.project = a.project and d.product = a.product
            left join 
            (
                /* 测试类有效输出 */
                select 
                    t.account,
                    t.project,
                    t.product,
                    sum(t.test_case_wp) + sum(t.execute_case_wp) + sum(t.test_bug_wp) + sum(t.province_bug_response_wp) 
                    + sum(t.inside_bug_response_wp) + sum(t.close_bug_wp) as total_output
                from 
                    boco_report_testwp_bypdpj t
                where
                    t.pr_date in (#{monthNums})
                    and t.project = #{amibaId}
                group by t.account, t.project, t.product
            ) f on f.account = a.account and f.project = a.project and f.product = a.product
            left join 
            (
                /* QA有效输出 */
                select 
                    t.account,
                    t.project,
                    t.product,
                    sum(t.open_bug_wp) + sum(t.close_bug_wp) as total_output
                from 
                    boco_report_qawp_bypdpj t
                where
                    t.pr_date in (#{monthNums})
                    and t.project = #{amibaId}
                group by t.account, t.project, t.product
            ) g on g.account = a.account and g.project = a.project and g.product = a.product
            left join 
            (
                /* 市场或售前有效输出 */
                select 
                    t.account,
                    t.project,
                    t.product,
                    sum(t.work_split_wp) as total_output
                from 
                    boco_report_marketwp_bypdpj t
                where
                    t.pr_date in (#{monthNums})
                    and t.project = #{amibaId}
                group by t.account, t.project, t.product
            ) h on h.account = a.account and h.project = a.project and h.product = a.product
    ) a
group by a.account, a.amiba_id, a.realname
    ";
/* --------------------------- prjsort end ------------------------------------ */

/* --------------------------- prdsort start ------------------------------------ */
$config->quantizedoutput->PrdPersonTimeTop30Sql = "
    select 
    a.amiba_id,
    a.amiba_name amiba_name,
    a.group_id group_id,
    a.group_name group_name,
    a.account,
    a.realname,
    sum(a.total_time) total_time,
    sum(a.day_avg_time) day_avg_time,
    sum(a.extra_time) extra_time,
    sum(a.total_output) total_output,
    sum(a.day_avg_output) day_avg_output,
    sum(a.output_efficiency) output_efficiency
from 
    (
        select 
            a.project group_id,
            a.project_name group_name,
            a.product_name amiba_name,
            a.product amiba_id,
            a.account,
            a.realname,
            b.total_time as total_time,
            convert(b.total_time / #{workDayCount}, decimal(10, 1)) as day_avg_time,
            b.total_time - #{workDayCount} * 8 as extra_time,
            convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
                + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)), decimal(10, 0)) as total_output,
            convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
                + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)) / #{workDayCount}, decimal(10, 1)) as day_avg_output,
            convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
                + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)) / b.total_time, decimal(10, 0))  as output_efficiency
        from
            (
                select 
                    ppa.project project,
                    case 
                        when pj.name is null then '公共'
                        when pj.name = '' then '公共'
                        else pj.name
                    end as project_name,
                    ppa.product product,
                    case
                        when pd.name is null then '公共'
                        when pd.name = '' then '公共'
                        else pd.name
                    end as product_name,
                    u.realname,
                    ppa.account
                from
                    boco_product_project_account ppa
                    inner join zt_user u on u.account = ppa.account
                    left join zt_project pj on pj.id = ppa.project
                    left join zt_product pd on pd.id = ppa.product
                where 
                    ppa.account != '' and ppa.pr_date in (#{monthNums})
                    and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                    and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
                    and ppa.product = #{amibaId}
            ) a
            left join 
            (
                /* 月工时统计 */
                select 
                    pp.project,
                    pp.product,
                    a.account,
                    sum(a.total_time) total_time
                from 
                    boco_product_project pp
                    inner join 
                    (
                        select 
                            w.account,
                            w.task_id,
                            case w.task_type
                                when 1 then 'story' 
                                when 2 then 'task'
                                when 3 then 'bug'
                                else 'other'
                            end as task_type,
                            sum(work_time) total_time
                        from 
                            dw_worklog_sync w 
                        where 
                            w.task_type in (1,2,3)
                            #{andWhereWorkdate}
                            /* and w.work_date between '2017-08-01' and '2017-08-30' */
                        group by w.account, w.task_id, w.task_type
                    ) a on a.task_id = pp.work_id and a.task_type = pp.work_type
                where
                    pp.product = #{amibaId}
                group by pp.project, pp.product, a.account
            ) b on b.account = a.account and b.project = a.project and b.product = a.product
            left join 
            (
                /* 研发有效输出 */
                select
                    w.account,
                    w.project,
                    w.product,
                    sum(w.month_wp_task) + sum(w.month_wp_pr) + sum(w.month_wp_bug) as total_output
                from 
                    boco_report_wp_bypdpj w
                where
                    w.pr_date in (#{monthNums})
                    and w.product = #{amibaId}
                    /* and w.pr_date = '201705' */
                    group by w.account, w.project, w.product
            ) c on c.account = a.account and c.project = a.project and c.product = a.product
            left join 
            (
                /* 需求类有效输出 */
                select 
                    s.account,
                    s.project,
                    s.product,
                    sum(s.reviewed_store_wp) + sum(s.itReq_wp) + sum(s.store_rel_task_wp) + sum(s.province_bug_response_wp) 
                    + sum(s.inside_bug_response_wp) + sum(s.store_val_wp) as total_output /* 需求有效输出 */
                from 
                    boco_report_storywp_bypdpj s
                where 
                    s.pr_date in (#{monthNums})
                    and s.product = #{amibaId}
                group by s.account, s.project, s.product
            ) d on d.account = a.account and d.project = a.project and d.product = a.product
            left join 
            (
                /* 测试类有效输出 */
                select 
                    t.account,
                    t.project,
                    t.product,
                    sum(t.test_case_wp) + sum(t.execute_case_wp) + sum(t.test_bug_wp) + sum(t.province_bug_response_wp) 
                    + sum(t.inside_bug_response_wp) + sum(t.close_bug_wp) as total_output
                from 
                    boco_report_testwp_bypdpj t
                where
                    t.pr_date in (#{monthNums})
                    and t.product = #{amibaId}
                group by t.account, t.project, t.product
            ) f on f.account = a.account and f.project = a.project and f.product = a.product
            left join 
            (
                /* QA有效输出 */
                select 
                    t.account,
                    t.project,
                    t.product,
                    sum(t.open_bug_wp) + sum(t.close_bug_wp) as total_output
                from 
                    boco_report_qawp_bypdpj t
                where
                    t.pr_date in (#{monthNums})
                    and t.product = #{amibaId}
                group by t.account, t.project, t.product
            ) g on g.account = a.account and g.project = a.project and g.product = a.product
            left join 
            (
                /* 市场或售前有效输出 */
                select 
                    t.account,
                    t.project,
                    t.product,
                    sum(t.work_split_wp) as total_output
                from 
                    boco_report_marketwp_bypdpj t
                where
                    t.pr_date in (#{monthNums})
                    and t.product = #{amibaId}
                group by t.account, t.project, t.product
            ) h on h.account = a.account and h.project = a.project and h.product = a.product
    ) a
group by a.account, a.amiba_id, a.realname
    ";
/* --------------------------- prdsort end ------------------------------------ */

/* --------------------------- sortmore start ------------------------------------ */
$config->quantizedoutput->MonthWorkSortSql = "
    select 
        a.amiba_name,
        a.group_name,
        a.realname as realname,
        a.account,
        b.total_time,
        convert(b.total_time / #{workDayCount}, decimal(10, 1)) as day_avg_time,
        b.total_time - #{workDayCount} * 8 as extra_time,
        convert(ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0), decimal(10, 0)) as total_output,
        convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0)) / #{workDayCount}, decimal(10, 1)) as day_avg_output,
        convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0)) / b.total_time, decimal(10, 0)) as output_efficiency,
        c.mod_merge_count
        /* convert(ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) - #{standardOutput}, decimal(10, 0)) as extra_output */
    from
        (
            select 
                * 
            from 
                v_report_user 
            where 
                root_path like concat(',', '#{userRootId}', ',%')
        ) a
        left join (
            select 
                w.account,
                sum(w.work_time) as total_time
            from 
                dw_worklog_sync w 
            where 
                w.task_type in (1,2,3,21,31) #{andWhereWorkdate}
                /* and w.work_date between '2017-04-01' and '2017-04-30' */
            group by w.account
        ) b on a.account = b.account
        left join 
        (
            select
                w.account,
                sum(w.month_wp_task) + sum(w.month_wp_pr) + 
                sum(w.month_wp_bug) as total_output/* 研发有效输出(1+2+3) */ ,
                sum(case when b.mod_merge_count is not null and b.mod_merge_count != 0 then b.mod_merge_count else 0 end) as mod_merge_count
            from 
                boco_report_wp_byaccount w
                left join 
                (
                    select 
                        p.account,
                        count(cm.id) mod_merge_count
                    from 
                        boco_calculate_process p
                        inner join  boco_gitlab_code_mod cm on p.act_id = cm.related_pr_cuid
                    where 
                        p.calc_type = 'DevTask'
                        and p.pr_date in (#{monthNums})
                    group by p.account
                ) b on b.account = w.account 
            where
                w.pr_date in (#{monthNums})
                /* and w.pr_date = '201705' */
            group by w.account
        ) c on a.account = c.account
        left join 
        (
            select 
                s.account /* 账号 */,
                sum(s.reviewed_store_wp) + sum(s.itReq_wp) + sum(s.store_rel_task_wp) + sum(s.province_bug_response_wp) + 
                sum(s.inside_bug_response_wp) + sum(s.store_val_wp) as total_output /* 需求有效输出 */
            from 
                boco_report_storywp_byaccount s
            where s.pr_date in (#{monthNums})
            group by account
        ) d on a.account = d.account
        left join 
        (
            select 
                t.account /* 账号 */,
                sum(t.test_case_wp) + sum(t.execute_case_wp) + sum(t.test_bug_wp) + sum(t.province_bug_response_wp) + 
                sum(t.inside_bug_response_wp) + sum(t.close_bug_wp) as total_output /* 测试有效输出 */
            from 
                boco_report_testwp_byaccount t
            where
                t.pr_date in (#{monthNums})
            group by account
        ) f on a.account = f.account
        left join 
        (
            select 
                t.account /* 账号 */,
                sum(t.open_bug_wp) + sum(t.close_bug_wp) as total_output /* QA有效输出 */
            from 
                boco_report_qawp_byaccount t
            where
                t.pr_date in (#{monthNums})
            group by account
        ) g on a.account = g.account
        left join 
        (
            select 
                t.account /* 账号 */,
                sum(t.work_split_wp)  as total_output /* 市场或售前有效输出 */
            from 
                boco_report_marketwp_byaccount t
            where
                t.pr_date in (#{monthNums})
            group by account
        ) h on a.account = h.account
    order by #{sortField} desc";
/* --------------------------- sortmore end ------------------------------------ */

/* --------------------------- prjsortmore start ------------------------------------ */
$config->quantizedoutput->PrjMonthWorkSortSql = "
    select 
    a.amiba_id,
    a.amiba_name amiba_name,
    a.group_id group_id,
    a.group_name group_name,
    a.account,
    a.realname,
    sum(a.total_time) total_time,
    sum(a.day_avg_time) day_avg_time,
    sum(a.extra_time) extra_time,
    sum(a.total_output) total_output,
    sum(a.day_avg_output) day_avg_output,
    sum(a.output_efficiency) output_efficiency
from 
    (
        select 
            a.project amiba_id,
            a.project_name amiba_name,
            a.product_name group_name,
            a.product group_id,
            a.account,
            a.realname,
            b.total_time as total_time,
            convert(b.total_time / #{workDayCount}, decimal(10, 1)) as day_avg_time,
            b.total_time - #{workDayCount} * 8 as extra_time,
            convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
                + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)), decimal(10, 0)) as total_output,
            convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
                + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)) / #{workDayCount}, decimal(10, 1)) as day_avg_output,
            convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
                + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)) / b.total_time, decimal(10, 0))  as output_efficiency
        from
            (
                select 
                    ppa.project project,
                    case 
                        when pj.name is null then '公共'
                        when pj.name = '' then '公共'
                        else pj.name
                    end as project_name,
                    ppa.product product,
                    case
                        when pd.name is null then '公共'
                        when pd.name = '' then '公共'
                        else pd.name
                    end as product_name,
                    u.realname,
                    ppa.account
                from
                    boco_product_project_account ppa
                    inner join zt_user u on u.account = ppa.account
                    left join zt_project pj on pj.id = ppa.project
                    left join zt_product pd on pd.id = ppa.product
                where 
                    ppa.account != '' and ppa.pr_date in (#{monthNums})
                    and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                    and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
                    and ppa.project = #{amibaId}
            ) a
            left join 
            (
                /* 月工时统计 */
                select 
                    pp.project,
                    pp.product,
                    a.account,
                    sum(a.total_time) total_time
                from 
                    boco_product_project pp
                    inner join 
                    (
                        select 
                            w.account,
                            w.task_id,
                            case w.task_type
                                when 1 then 'story' 
                                when 2 then 'task'
                                when 3 then 'bug'
                                else 'other'
                            end as task_type,
                            sum(work_time) total_time
                        from 
                            dw_worklog_sync w 
                        where 
                            w.task_type in (1,2,3)
                            #{andWhereWorkdate}
                            /* and w.work_date between '2017-08-01' and '2017-08-30' */
                        group by w.account, w.task_id, w.task_type
                    ) a on a.task_id = pp.work_id and a.task_type = pp.work_type
                where
                    pp.project = #{amibaId}
                group by pp.project, pp.product, a.account
            ) b on b.account = a.account and b.project = a.project and b.product = a.product
            left join 
            (
                /* 研发有效输出 */
                select
                    w.account,
                    w.project,
                    w.product,
                    sum(w.month_wp_task) + sum(w.month_wp_pr) + sum(w.month_wp_bug) as total_output
                from 
                    boco_report_wp_bypdpj w
                where
                    w.pr_date in (#{monthNums})
                    and w.project = #{amibaId}
                    /* and w.pr_date = '201705' */
                    group by w.account, w.project, w.product
            ) c on c.account = a.account and c.project = a.project and c.product = a.product
            left join 
            (
                /* 需求类有效输出 */
                select 
                    s.account,
                    s.project,
                    s.product,
                    sum(s.reviewed_store_wp) + sum(s.itReq_wp) + sum(s.store_rel_task_wp) + sum(s.province_bug_response_wp) 
                    + sum(s.inside_bug_response_wp) + sum(s.store_val_wp) as total_output /* 需求有效输出 */
                from 
                    boco_report_storywp_bypdpj s
                where 
                    s.pr_date in (#{monthNums})
                    and s.project = #{amibaId}
                group by s.account, s.project, s.product
            ) d on d.account = a.account and d.project = a.project and d.product = a.product
            left join 
            (
                /* 测试类有效输出 */
                select 
                    t.account,
                    t.project,
                    t.product,
                    sum(t.test_case_wp) + sum(t.execute_case_wp) + sum(t.test_bug_wp) + sum(t.province_bug_response_wp) 
                    + sum(t.inside_bug_response_wp) + sum(t.close_bug_wp) as total_output
                from 
                    boco_report_testwp_bypdpj t
                where
                    t.pr_date in (#{monthNums})
                    and t.project = #{amibaId}
                group by t.account, t.project, t.product
            ) f on f.account = a.account and f.project = a.project and f.product = a.product
            left join 
            (
                /* QA有效输出 */
                select 
                    t.account,
                    t.project,
                    t.product,
                    sum(t.open_bug_wp) + sum(t.close_bug_wp) as total_output
                from 
                    boco_report_qawp_bypdpj t
                where
                    t.pr_date in (#{monthNums})
                    and t.project = #{amibaId}
                group by t.account, t.project, t.product
            ) g on g.account = a.account and g.project = a.project and g.product = a.product
            left join 
            (
                /* 市场或售前有效输出 */
                select 
                    t.account,
                    t.project,
                    t.product,
                    sum(t.work_split_wp) as total_output
                from 
                    boco_report_marketwp_bypdpj t
                where
                    t.pr_date in (#{monthNums})
                    and t.project = #{amibaId}
                group by t.account, t.project, t.product
            ) h on h.account = a.account and h.project = a.project and h.product = a.product
    ) a
group by a.account, a.amiba_id, a.realname
order by #{sortField} desc
    ";
/* --------------------------- prjsortmore end ------------------------------------ */
    
/* --------------------------- prdsortmore start ------------------------------------ */
$config->quantizedoutput->PrdMonthWorkSortSql = "
    select 
    a.amiba_id,
    a.amiba_name amiba_name,
    a.group_id group_id,
    a.group_name group_name,
    a.account,
    a.realname,
    sum(a.total_time) total_time,
    sum(a.day_avg_time) day_avg_time,
    sum(a.extra_time) extra_time,
    sum(a.total_output) total_output,
    sum(a.day_avg_output) day_avg_output,
    sum(a.output_efficiency) output_efficiency
from 
    (
        select 
            a.project group_id,
            a.project_name group_name,
            a.product amiba_id,
            a.product_name amiba_name,
            a.account,
            a.realname,
            b.total_time as total_time,
            convert(b.total_time / #{workDayCount}, decimal(10, 1)) as day_avg_time,
            b.total_time - #{workDayCount} * 8 as extra_time,
            convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
                + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)), decimal(10, 0)) as total_output,
            convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
                + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)) / #{workDayCount}, decimal(10, 1)) as day_avg_output,
            convert((ifnull(c.total_output, 0) + ifnull(d.total_output, 0) + ifnull(f.total_output, 0) 
                + ifnull(g.total_output, 0) + ifnull(h.total_output, 0)) / b.total_time, decimal(10, 0))  as output_efficiency
        from
            (
                select 
                    ppa.project project,
                    case 
                        when pj.name is null then '公共'
                        when pj.name = '' then '公共'
                        else pj.name
                    end as project_name,
                    ppa.product product,
                    case
                        when pd.name is null then '公共'
                        when pd.name = '' then '公共'
                        else pd.name
                    end as product_name,
                    u.realname,
                    ppa.account
                from
                    boco_product_project_account ppa
                    inner join zt_user u on u.account = ppa.account
                    left join zt_project pj on pj.id = ppa.project
                    left join zt_product pd on pd.id = ppa.product
                where 
                    ppa.account != '' and ppa.pr_date in (#{monthNums})
                    and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                    and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
                    and ppa.product = #{amibaId}
            ) a
            left join 
            (
                /* 月工时统计 */
                select 
                    pp.project,
                    pp.product,
                    a.account,
                    sum(a.total_time) total_time
                from 
                    boco_product_project pp
                    inner join 
                    (
                        select 
                            w.account,
                            w.task_id,
                            case w.task_type
                                when 1 then 'story' 
                                when 2 then 'task'
                                when 3 then 'bug'
                                else 'other'
                            end as task_type,
                            sum(work_time) total_time
                        from 
                            dw_worklog_sync w 
                        where 
                            w.task_type in (1,2,3)
                            #{andWhereWorkdate}
                            /* and w.work_date between '2017-08-01' and '2017-08-30' */
                        group by w.account, w.task_id, w.task_type
                    ) a on a.task_id = pp.work_id and a.task_type = pp.work_type
                where
                    pp.product = #{amibaId}
                group by pp.project, pp.product, a.account
            ) b on b.account = a.account and b.project = a.project and b.product = a.product
            left join 
            (
                /* 研发有效输出 */
                select
                    w.account,
                    w.project,
                    w.product,
                    sum(w.month_wp_task) + sum(w.month_wp_pr) + sum(w.month_wp_bug) as total_output
                from 
                    boco_report_wp_bypdpj w
                where
                    w.pr_date in (#{monthNums})
                    and w.product = #{amibaId}
                    /* and w.pr_date = '201705' */
                    group by w.account, w.project, w.product
            ) c on c.account = a.account and c.project = a.project and c.product = a.product
            left join 
            (
                /* 需求类有效输出 */
                select 
                    s.account,
                    s.project,
                    s.product,
                    sum(s.reviewed_store_wp) + sum(s.itReq_wp) + sum(s.store_rel_task_wp) + sum(s.province_bug_response_wp) 
                    + sum(s.inside_bug_response_wp) + sum(s.store_val_wp) as total_output /* 需求有效输出 */
                from 
                    boco_report_storywp_bypdpj s
                where 
                    s.pr_date in (#{monthNums})
                    and s.product = #{amibaId}
                group by s.account, s.project, s.product
            ) d on d.account = a.account and d.project = a.project and d.product = a.product
            left join 
            (
                /* 测试类有效输出 */
                select 
                    t.account,
                    t.project,
                    t.product,
                    sum(t.test_case_wp) + sum(t.execute_case_wp) + sum(t.test_bug_wp) + sum(t.province_bug_response_wp) 
                    + sum(t.inside_bug_response_wp) + sum(t.close_bug_wp) as total_output
                from 
                    boco_report_testwp_bypdpj t
                where
                    t.pr_date in (#{monthNums})
                    and t.product = #{amibaId}
                group by t.account, t.project, t.product
            ) f on f.account = a.account and f.project = a.project and f.product = a.product
            left join 
            (
                /* QA有效输出 */
                select 
                    t.account,
                    t.project,
                    t.product,
                    sum(t.open_bug_wp) + sum(t.close_bug_wp) as total_output
                from 
                    boco_report_qawp_bypdpj t
                where
                    t.pr_date in (#{monthNums})
                    and t.product = #{amibaId}
                group by t.account, t.project, t.product
            ) g on g.account = a.account and g.project = a.project and g.product = a.product
            left join 
            (
                /* 市场或售前有效输出 */
                select 
                    t.account,
                    t.project,
                    t.product,
                    sum(t.work_split_wp) as total_output
                from 
                    boco_report_marketwp_bypdpj t
                where
                    t.pr_date in (#{monthNums})
                    and t.product = #{amibaId}
                group by t.account, t.project, t.product
            ) h on h.account = a.account and h.project = a.project and h.product = a.product
    ) a
group by a.account, a.amiba_id, a.realname
order by #{sortField} desc
    ";
/* --------------------------- prdsortmore end ------------------------------------ */
    
/* --------------------------- timetendency start------------------------------------ */
$config->quantizedoutput->DayTimeTendencyDataSql = "
    select 
        b.work_date,
        sum(b.work_time) as value
    from
        (
            select 
                * 
            from 
                v_report_user 
            where 
                root_path like concat(',', '#{userRootId}', ',%')
        ) a
        left join 
        (
            select 
                w.account,
                w.work_date,
                w.work_time as work_time
            from 
                dw_worklog_sync w 
            where 
                w.task_type in (1,2,3,21,31) 
                /* and w.work_date = '2017-04-01' */ #{andWhereWorkdate}
        ) b on a.account = b.account
    where 
        b.work_date is not null #{andWhereAmibaGroupAccount}
    group by b.work_date";

$config->quantizedoutput->MonthTimeTendencyDataSql = "
    select 
        b.work_date as work_date,
        sum(b.work_time) as value
    from
        (
            select 
                * 
            from 
                v_report_user 
            where 
                root_path like concat(',', '#{userRootId}', ',%')
        ) a
        left join 
        (
            select 
                w.account,
                w.work_date,
                w.work_time as work_time
            from 
                dw_worklog_sync w 
            where 
                w.task_type in (1,2,3,21,31) 
                /* and w.work_date = '2017-04-01' */ #{andWhereWorkdate}
        ) b on a.account = b.account
    where 
        b.work_date is not null #{andWhereAmibaGroupAccount}
        group by b.work_date
        order by b.work_date";
/* --------------------------- timetendency end------------------------------------ */

/* --------------------------- worklogs start ------------------------------------ */
$config->quantizedoutput->staffWorklogsSql = "
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
    w.task_id,
    w.work_time,
    concat(convert(w.work_time, decimal(10, 1)), 'h(', 
        date_format(w.start_time, '%d号%H:%i'), '~', 
        date_format(w.end_time, '%d号%H:%i'), ')') as time_sect,
    w.work_content
from 
    dw_worklog_sync w
where 
    w.task_type in (1, 2, 3, 21, 31)
    #{andWhereAmibaGroupAccount} #{andWhereWorkdate}
order by w.start_time desc";

$config->quantizedoutput->prjprdWorklogsSql = "
select 
    case 
        when w.task_type = 'story'
        then '禅道需求'
        when w.task_type = 'task'
        then '禅道任务'
        else'禅道Bug'
    end as task_type_name,
    w.task_id,
    w.work_time,
    concat(convert(w.work_time, decimal(10, 1)), 'h(', 
        date_format(w.start_time, '%d号%H:%i'), '~', 
        date_format(w.end_time, '%d号%H:%i'), ')') as time_sect,
    w.work_content,
    pp.project,
    pp.product,
    w.account
from 
    boco_product_project pp
    inner join 
    (
        select 
            w.account,
            w.task_id,
            case w.task_type
                when 1 then 'story' 
                when 2 then 'task'
                else 'bug'
            end as task_type,
            w.work_time,
            w.start_time,
            w.end_time,
            w.work_content
        from 
            dw_worklog_sync w 
        where 
            w.task_type in (1,2,3) #{andWhereWorkdate}
    ) w on w.task_id = pp.work_id and w.task_type = pp.work_type
where 
    1 = 1 
    #{andWhereAmibaGroupAccount}
";
/* --------------------------- worklogs end ------------------------------------ */

/* --------------------------- 有效输出修正 start ------------------------------------ */
/* 有效输出统计服务地址 */
$config->quantizedoutput->performanceServiceUrls["te"] = "http://182.18.57.7:8501/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->quantizedoutput->performanceServiceUrls["wx"] = "http://182.18.57.7:8502/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->quantizedoutput->performanceServiceUrls["ty"] = "http://182.18.57.7:8503/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->quantizedoutput->performanceServiceUrls["dj"] = "http://182.18.57.7:8504/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->quantizedoutput->performanceServiceUrls["eo"] = "http://182.18.57.7:8505/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->quantizedoutput->performanceServiceUrls["mi"] = "http://182.18.57.7:8506/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->quantizedoutput->performanceServiceUrls["dg"] = "http://182.18.57.7:8507/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->quantizedoutput->performanceServiceUrls["xy"] = "http://182.18.57.7:8509/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->quantizedoutput->performanceServiceUrls["yc"] = "http://182.18.57.7:8510/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->quantizedoutput->performanceServiceUrls["ww"] = "http://182.18.57.7:8511/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";
$config->quantizedoutput->performanceServiceUrls["ossh"] = "http://182.18.57.7:8508/RecalcTasksInDate?start=#{startNum}&end=#{endNum}";

// 仅可修改文件个数的特殊文件类型
$config->quantizedoutput->specialFileTypes = "jpg,png";

$config->quantizedoutput->staffMergeInfoSql = "
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
        b.web_url,
        b.pr_url,
        b.git_id,
        b.task_id /* 任务编号 */,
        b.pr_desc /* 备注 */,
        b.merge_id,
        b.calc_process,
        b.calc_result
    from
        (
            select 
                * 
            from 
                v_report_user 
            where 
                root_path like concat(',', '#{userRootId}', ',%')
        ) a
        inner join 
        (
            select 
                g.id git_id,
                p.pr_date,
                p.account,
                g.project_name /* 所属项目 */,
                g.source_branch /* 来源分支 */,
                g.target_branch /* 目标分支 */,
                g.state /* PR状态 */,
                g.pr_time /* 请求日期 */,
                g.check_time /* 审核日期 */,
                g.committer /* 提交人 */,
                g.auditor /* 审核人 */,
                g.web_url,
                g.pr_url,
                g.task_id /* 任务编号 */,
                g.pr_desc /* 备注 */,
                p.act_id merge_id,
                p.calc_process,
                p.calc_result
            from 
                boco_gitlab_pr g
                inner join boco_calculate_process p on p.act_id = g.id
                inner join zt_task t on g.task_id = concat(t.id, '')
            where 
                p.calc_type = 'DevTask'
                and p.act_id = '#{mergeId}'
        ) b on a.account = b.account";
        
$config->quantizedoutput->prjMergeInfoSql = "
    select 
        a.project_name amiba_name,
        a.product_name group_name,
        b.account, 
        b.project_name /* 所属项目 */,
        b.source_branch /* 来源分支 */,
        b.target_branch /* 目标分支 */,
        b.state /* PR状态 */,
        b.pr_time /* 请求日期 */,
        b.check_time /* 审核日期 */,
        b.committer /* 提交人 */,
        b.auditor /* 审核人 */,
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
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
            select 
                g.id git_id,
                p.pr_date,
                p.account,
                p.project,
                p.product,
                g.project_name /* 所属项目 */,
                g.source_branch /* 来源分支 */,
                g.target_branch /* 目标分支 */,
                g.state /* PR状态 */,
                g.pr_time /* 请求日期 */,
                g.check_time /* 审核日期 */,
                g.committer /* 提交人 */,
                g.auditor /* 审核人 */,
                g.web_url,
                g.pr_url,
                g.task_id /* 任务编号 */,
                g.pr_desc /* 备注 */,
                p.act_id merge_id,
                (
                    select 
                        count(id) 
                    from 
                        boco_gitlab_code_mod 
                    where 
                        related_pr_cuid=p.act_id
                ) as mod_merge_count,
                p.calc_process,
                p.calc_result
            from 
                boco_gitlab_pr g
                inner join boco_calculate_process p on p.act_id = g.id
            where 
                p.calc_type = 'DevTask'
                and p.act_id = '#{mergeId}'
        ) b on b.account = a.account and b.project = a.project and b.product = a.product

";

$config->quantizedoutput->prdMergeInfoSql = "
    select 
        a.product_name amiba_name,
        a.project_name group_name,
        b.account, 
        b.project_name /* 所属项目 */,
        b.source_branch /* 来源分支 */,
        b.target_branch /* 目标分支 */,
        b.state /* PR状态 */,
        b.pr_time /* 请求日期 */,
        b.check_time /* 审核日期 */,
        b.committer /* 提交人 */,
        b.auditor /* 审核人 */,
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
            select 
                ppa.project project,
                case 
                    when pj.name is null then '公共'
                    when pj.name = '' then '公共'
                    else pj.name
                end as project_name,
                ppa.product product,
                case
                    when pd.name is null then '公共'
                    when pd.name = '' then '公共'
                    else pd.name
                end as product_name,
                ppa.account
            from
                boco_product_project_account ppa
                left join zt_project pj on pj.id = ppa.project
                inner join zt_product pd on pd.id = ppa.product
            where 
                ppa.account != '' and ppa.pr_date = '#{monthNum}'
                and (ppa.product = 0 or (ppa.product != 0 and pd.name is not null))
                and (ppa.project = 0 or (ppa.project != 0 and pj.name is not null))
        ) a
        inner join 
        (
            select 
                g.id git_id,
                p.pr_date,
                p.account,
                p.project,
                p.product,
                g.project_name /* 所属项目 */,
                g.source_branch /* 来源分支 */,
                g.target_branch /* 目标分支 */,
                g.state /* PR状态 */,
                g.pr_time /* 请求日期 */,
                g.check_time /* 审核日期 */,
                g.committer /* 提交人 */,
                g.auditor /* 审核人 */,
                g.web_url,
                g.pr_url,
                g.task_id /* 任务编号 */,
                g.pr_desc /* 备注 */,
                p.act_id merge_id,
                (
                    select 
                        count(id) 
                    from 
                        boco_gitlab_code_mod 
                    where 
                        related_pr_cuid=p.act_id
                ) as mod_merge_count,
                p.calc_process,
                p.calc_result
            from 
                boco_gitlab_pr g 
                inner join boco_calculate_process p on p.act_id = g.id
            where 
                p.calc_type = 'DevTask'
                and p.act_id = '#{mergeId}'
        ) b on b.account = a.account and b.project = a.project and b.product = a.product
";

$config->quantizedoutput->mergeDetailInfoSql = "
select 
    c.id,
    c.related_pr_cuid,
    c.file_type,
    c.file_count,
    c.file_size,
    c.line_add,
    c.line_del,
    c.remark,
    cm.id as id_new,
    cm.related_pr_cuid as related_pr_cuid_new,
    cm.file_type as file_type_new,
    cm.file_count as file_count_new,
    cm.file_size as file_size_new,
    cm.line_add as line_add_new,
    cm.line_del as line_del_new,
    cm.remark as remark_new,
    cm.modifier as modifier_new,
    cm.time_stamp as time_stamp_new
from 
    boco_gitlab_code c
    left join boco_gitlab_code_mod cm on c.related_pr_cuid = cm.related_pr_cuid and c.file_type = cm.file_type
where 
    c.related_pr_cuid = '#{mergeId}'
order by c.line_add desc, c.file_count desc";

$config->quantizedoutput->deleteModRecordSql = "
delete 
from 
    boco_gitlab_code_mod 
where 
    related_pr_cuid='#{related_pr_cuid}' 
    and file_type='#{file_type}'";
    
$config->quantizedoutput->insertModRecordSql = "
insert into boco_gitlab_code_mod(department, related_pr_cuid, file_size, line_add, 
    line_del, line_modify, file_type, file_count, remark, modifier, time_stamp)
values ('#{department}', '#{related_pr_cuid}', '#{file_size}', '#{line_add}', '#{line_del}',
     '#{line_modify}', '#{file_type}', '#{file_count}', 
     '#{remark}', '#{modifier}', '#{time_stamp}');";
     
$config->quantizedoutput->userRootDictSql = "
select id,name from zt_dept where grade = 1
";
/* --------------------------- 有效输出修正 end ------------------------------------ */

