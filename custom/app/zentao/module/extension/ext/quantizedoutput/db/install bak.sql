# Date: 2017-9-27
# update to custom for zentao 

# -- 1.0
#
# Structure for table "boco_gitlab_pr" 
#
CREATE TABLE  if not exists boco_gitlab_pr (
department varchar(32),					 
id varchar(72) NOT NULL,  				 
source_branch varchar(50) NOT NULL,		 
target_branch varchar(50) NOT NULL, 	 
task_id varchar(50),   					 
committer char(20) NOT NULL, 			 
auditor char(20) NOT NULL, 				 
state enum('merged','closed','opened') NOT NULL,  
pr_time datetime NOT NULL, 				 
check_time datetime, 					 
pr_desc text(1000), 					 
project_name varchar(200), 				 
web_url varchar(255), 					 
pr_url varchar(255), 					  
isDeleted mediumint(8) DEFAULT '0', 	 
time_stamp datetime, 	
PRIMARY KEY (id)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Structure for table "boco_gitlab_code" 
CREATE TABLE  if not exists boco_gitlab_code (
department varchar(32),						 
id mediumint(8) unsigned NOT NULL AUTO_INCREMENT, 
related_pr_cuid varchar(72) NOT NULL, 		 
line_add int DEFAULT '0' NOT NULL, 			 
line_del int DEFAULT '0' NOT NULL, 			 
line_modify int DEFAULT '0', 				 
file_type char(255) NOT NULL, 				 
file_count mediumint(8) DEFAULT '0' NOT NULL,  
file_size mediumint(8) DEFAULT '0' NOT NULL,  
remark varchar(200), 						 
time_stamp datetime, 	
PRIMARY KEY (id)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Structure for table "boco_gitlab_code_mod" 
CREATE TABLE  if not exists boco_gitlab_code_mod (
department varchar(32),						 
id mediumint(8) unsigned NOT NULL AUTO_INCREMENT, 
related_pr_cuid varchar(72) NOT NULL, 		 
line_add int DEFAULT '0' NOT NULL, 			 
line_del int DEFAULT '0' NOT NULL, 			 
line_modify int DEFAULT '0', 				 
file_type char(255) NOT NULL, 				 
file_count mediumint(8) DEFAULT '0' NOT NULL,  
file_size mediumint(8) DEFAULT '0' NOT NULL,  
remark text(1000), 						     
modifier varchar(72),                          
time_stamp datetime, 	                     
PRIMARY KEY (id)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Structure for table "boco_calculate_process" 
CREATE TABLE  if not exists boco_calculate_process(
department varchar(32),								 
id mediumint(8) unsigned NOT NULL AUTO_INCREMENT, 
account varchar(20) NOT NULL, 						 
calc_type varchar(100) NOT NULL,						 
act_id varchar(50), 								 
calc_process  text(1000),							  
calc_result int DEFAULT '0',						 
pr_date varchar(20),								 
tag varchar(255),									 
project mediumint(8) DEFAULT '0' NOT NULL, 		 
product mediumint(8) DEFAULT '0' NOT NULL, 		 
time_stamp datetime, 	
PRIMARY KEY (id),
 INDEX (calc_type,act_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Structure for table "boco_product_project" 
CREATE TABLE  if not exists boco_product_project (
work_type varchar(100) NOT NULL,				
work_id mediumint(8) DEFAULT '0' NOT NULL,		 
product mediumint(8) DEFAULT '0' NOT NULL,		 
project mediumint(8) DEFAULT '0' NOT NULL,		 
deleted enum('0','1') DEFAULT '0' NOT NULL,		 
PRIMARY KEY (work_type,work_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Structure for table "boco_product_project_temp" 
CREATE TABLE  if not exists boco_product_project_temp (
work_type varchar(100) NOT NULL,				 
work_id mediumint(8) DEFAULT '0' NOT NULL,		
product mediumint(8) DEFAULT '0' NOT NULL,		 
project mediumint(8) DEFAULT '0' NOT NULL,		 
deleted enum('0','1') DEFAULT '0' NOT NULL,	 
PRIMARY KEY (work_type,work_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Structure for table "boco_product_project_account" 
CREATE TABLE  if not exists boco_product_project_account (
product mediumint(8) DEFAULT '0' NOT NULL,		 
project mediumint(8) DEFAULT '0' NOT NULL,		 
account varchar(155),
pr_date varchar(100),							 
deleted enum('0','1') DEFAULT '0' NOT NULL,		 
PRIMARY KEY (product,project,account,pr_date)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Structure for table "boco_it_task2zt" 
CREATE TABLE  if not exists boco_it_task2zt (
id int NOT NULL AUTO_INCREMENT, 
task_id varchar(255) COLLATE utf8_general_ci ,
task_type int , 
sheet_id int , 
title text COLLATE utf8_general_ci , 
participant varchar(255) COLLATE utf8_general_ci , 
partiname varchar(255) COLLATE utf8_general_ci , 
province_name varchar(64) COLLATE utf8_general_ci , 
project_id varchar(30) COLLATE utf8_general_ci , 
project_name varchar(255) COLLATE utf8_general_ci, 
status varchar(64) COLLATE utf8_general_ci , 
close_time datetime, 
specialty_name varchar(30) COLLATE utf8_general_ci , 
create_time datetime, 
last_rd_handler varchar(255) COLLATE utf8_general_ci, 		 
demand_analyst varchar(255) COLLATE utf8_general_ci,		 
PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Structure for table "boco_report_wp_byaccount" 
CREATE TABLE  if not exists boco_report_wp_byaccount (
department varchar(32),										 
id mediumint(8) unsigned NOT NULL AUTO_INCREMENT, 
account varchar(20) NOT NULL, 								 
month_wp_task mediumint(8) DEFAULT '0' NOT NULL,  			 
month_wp_task_zentao mediumint(8) DEFAULT '0' NOT NULL,		 
month_wp_task_it mediumint(8) DEFAULT '0' NOT NULL,			 
month_pq_reject mediumint(8) DEFAULT '0' NOT NULL, 			 
month_pq_passed mediumint(8) DEFAULT '0' NOT NULL, 			 
month_leader_pq_passed mediumint(8) DEFAULT '0' NOT NULL, 	 
month_leader_pq_reject mediumint(8) DEFAULT '0' NOT NULL, 	 
month_wp_pr mediumint(8) DEFAULT '0' NOT NULL, 				  
month_bug_inside_fix mediumint(8) DEFAULT '0' NOT NULL, 	 
month_bug_inside_create mediumint(8) DEFAULT '0' NOT NULL, 	 
month_bug_province_fix mediumint(8) DEFAULT '0' NOT NULL, 	 
month_bug_province_create mediumint(8) DEFAULT '0' NOT NULL,  
it_bug_fix mediumint(8) DEFAULT '0' NOT NULL, 				 
month_wp_bug mediumint(8) DEFAULT '0' NOT NULL, 			 
pr_date varchar(20) NOT NULL, 								 
time_stamp datetime, 	
PRIMARY KEY (id)				
)ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Structure for table "boco_report_wp_bypdpj" 
CREATE TABLE  if not exists boco_report_wp_bypdpj (
department varchar(32),										 
id mediumint(8) unsigned NOT NULL AUTO_INCREMENT, 
product mediumint(8) DEFAULT '0' NOT NULL, 					 
project mediumint(8) DEFAULT '0' NOT NULL, 					 
account varchar(20) NOT NULL, 								 
month_wp_task mediumint(8) DEFAULT '0' NOT NULL,  			 
month_wp_task_zentao mediumint(8) DEFAULT '0' NOT NULL,		 
month_wp_task_it mediumint(8) DEFAULT '0' NOT NULL,			 
month_pq_reject mediumint(8) DEFAULT '0' NOT NULL, 			 
month_pq_passed mediumint(8) DEFAULT '0' NOT NULL, 			 
month_leader_pq_passed mediumint(8) DEFAULT '0' NOT NULL, 	 
month_leader_pq_reject mediumint(8) DEFAULT '0' NOT NULL, 	 
month_wp_pr mediumint(8) DEFAULT '0' NOT NULL, 				 
month_bug_inside_fix mediumint(8) DEFAULT '0' NOT NULL, 	 
month_bug_inside_create mediumint(8) DEFAULT '0' NOT NULL, 	 
month_bug_province_fix mediumint(8) DEFAULT '0' NOT NULL, 	 
month_bug_province_create mediumint(8) DEFAULT '0' NOT NULL,  
it_bug_fix mediumint(8) DEFAULT '0' NOT NULL, 				 
month_wp_bug mediumint(8) DEFAULT '0' NOT NULL, 			 
pr_date varchar(20) NOT NULL, 								 
time_stamp datetime, 	
PRIMARY KEY (id)				
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

 
# Structure for table "boco_report_storywp_byaccount" 
CREATE TABLE  if not exists boco_report_storywp_byaccount (
department varchar(32),										 
id mediumint(8) unsigned NOT NULL AUTO_INCREMENT, 
account varchar(50) NOT NULL, 								 
onetimepass_store mediumint(8) DEFAULT '0' NOT NULL, 		 
repeatedpass_store mediumint(8) DEFAULT '0' NOT NULL, 		 
norecord_store mediumint(8) DEFAULT '0' NOT NULL, 			 
reviewed_store_wp mediumint(8) DEFAULT '0' NOT NULL, 
itReq_count  mediumint(8) DEFAULT '0' NOT NULL,				 
itReq_wp  mediumint(8) DEFAULT '0' NOT NULL,	
province_bug_response  mediumint(8) DEFAULT '0' NOT NULL,	 
inside_bug_response  mediumint(8) DEFAULT '0' NOT NULL,  	 
province_bug_response_wp  mediumint(8) DEFAULT '0' NOT NULL, 
inside_bug_response_wp  mediumint(8) DEFAULT '0' NOT NULL,   
store_rel_task_wp mediumint(8) DEFAULT '0' NOT NULL,  		 
store_val_wp mediumint(8) DEFAULT '0' NOT NULL, 			 
pr_date varchar(20) NOT NULL,								 
time_stamp datetime, 	
PRIMARY KEY (id)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Structure for table "boco_report_storywp_bypdpj" 
CREATE TABLE  if not exists boco_report_storywp_bypdpj (
department varchar(32),										 
id mediumint(8) unsigned NOT NULL AUTO_INCREMENT, 
product mediumint(8) DEFAULT '0' NOT NULL, 					 
project mediumint(8) DEFAULT '0' NOT NULL, 					 
account varchar(50) NOT NULL, 								 
onetimepass_store mediumint(8) DEFAULT '0' NOT NULL, 		 
repeatedpass_store mediumint(8) DEFAULT '0' NOT NULL, 		 
norecord_store mediumint(8) DEFAULT '0' NOT NULL, 			 
reviewed_store_wp mediumint(8) DEFAULT '0' NOT NULL, 
itReq_count  mediumint(8) DEFAULT '0' NOT NULL,				 
itReq_wp  mediumint(8) DEFAULT '0' NOT NULL,	
province_bug_response  mediumint(8) DEFAULT '0' NOT NULL,	 
inside_bug_response  mediumint(8) DEFAULT '0' NOT NULL,  	 
province_bug_response_wp  mediumint(8) DEFAULT '0' NOT NULL, 
inside_bug_response_wp  mediumint(8) DEFAULT '0' NOT NULL,   
store_rel_task_wp mediumint(8) DEFAULT '0' NOT NULL,  		 
store_val_wp mediumint(8) DEFAULT '0' NOT NULL, 			 
pr_date varchar(20) NOT NULL,								 
time_stamp datetime, 	
PRIMARY KEY (id)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Structure for table "boco_report_testwp_byaccount" 
CREATE TABLE  if not exists boco_report_testwp_byaccount (
department varchar(32),										 
id mediumint(8) unsigned NOT NULL AUTO_INCREMENT, 
account varchar(50) NOT NULL, 								 
test_case_count mediumint(8) DEFAULT '0' NOT NULL, 			 
test_case_wp mediumint(8) DEFAULT '0' NOT NULL, 
execute_case_count mediumint(8) DEFAULT '0' NOT NULL, 		 
execute_case_wp mediumint(8) DEFAULT '0' NOT NULL, 
test_bug_count mediumint(8) DEFAULT '0' NOT NULL, 			 
test_bug_wp mediumint(8) DEFAULT '0' NOT NULL, 	
close_bug_count mediumint(8) DEFAULT '0' NOT NULL, 			 
close_bug_wp mediumint(8) DEFAULT '0' NOT NULL, 	
province_bug_response  mediumint(8) DEFAULT '0' NOT NULL,	 
inside_bug_response  mediumint(8) DEFAULT '0' NOT NULL,  	 
province_bug_response_wp  mediumint(8) DEFAULT '0' NOT NULL, 
inside_bug_response_wp  mediumint(8) DEFAULT '0' NOT NULL,   
pr_date varchar(20) NOT NULL,								 
time_stamp datetime, 	
PRIMARY KEY (id)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Structure for table "boco_report_testwp_bypdpj" 
CREATE TABLE  if not exists boco_report_testwp_bypdpj (
department varchar(32),										 
id mediumint(8) unsigned NOT NULL AUTO_INCREMENT, 
product mediumint(8) DEFAULT '0' NOT NULL, 					 
project mediumint(8) DEFAULT '0' NOT NULL, 					 
account varchar(50) NOT NULL, 								 
test_case_count mediumint(8) DEFAULT '0' NOT NULL, 			 
test_case_wp mediumint(8) DEFAULT '0' NOT NULL,  
execute_case_count mediumint(8) DEFAULT '0' NOT NULL, 		 
execute_case_wp mediumint(8) DEFAULT '0' NOT NULL, 
test_bug_count mediumint(8) DEFAULT '0' NOT NULL, 			 
test_bug_wp mediumint(8) DEFAULT '0' NOT NULL, 	
close_bug_count mediumint(8) DEFAULT '0' NOT NULL, 			 
close_bug_wp mediumint(8) DEFAULT '0' NOT NULL, 	
province_bug_response  mediumint(8) DEFAULT '0' NOT NULL,	 
inside_bug_response  mediumint(8) DEFAULT '0' NOT NULL,  	 
province_bug_response_wp  mediumint(8) DEFAULT '0' NOT NULL, 
inside_bug_response_wp  mediumint(8) DEFAULT '0' NOT NULL,   
pr_date varchar(20) NOT NULL,								 
time_stamp datetime, 	
PRIMARY KEY (id)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

 
# Structure for table "boco_report_qawp_byaccount" 
CREATE TABLE  if not exists boco_report_qawp_byaccount (
department varchar(32),										 
id mediumint(8) unsigned NOT NULL AUTO_INCREMENT, 
account varchar(50) NOT NULL, 
open_bug_count mediumint(8) DEFAULT '0' NOT NULL, 			 
open_bug_wp mediumint(8) DEFAULT '0' NOT NULL, 	
close_bug_count mediumint(8) DEFAULT '0' NOT NULL, 			 
close_bug_wp mediumint(8) DEFAULT '0' NOT NULL, 	
pr_date varchar(20) NOT NULL,								 
time_stamp datetime, 	
PRIMARY KEY (id)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Structure for table "boco_report_qawp_bypdpj" 
CREATE TABLE  if not exists boco_report_qawp_bypdpj (
department varchar(32),										 
id mediumint(8) unsigned NOT NULL AUTO_INCREMENT, 
product mediumint(8) DEFAULT '0' NOT NULL, 					 
project mediumint(8) DEFAULT '0' NOT NULL, 					 
account varchar(50) NOT NULL, 		
open_bug_count mediumint(8) DEFAULT '0' NOT NULL, 			 
open_bug_wp mediumint(8) DEFAULT '0' NOT NULL, 	
close_bug_count mediumint(8) DEFAULT '0' NOT NULL, 			 
close_bug_wp mediumint(8) DEFAULT '0' NOT NULL, 
pr_date varchar(20) NOT NULL,								 
time_stamp datetime, 	
PRIMARY KEY (id)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Structure for table "boco_report_marketwp_byaccount" 
CREATE TABLE  if not exists boco_report_marketwp_byaccount (
department varchar(32),										 
id mediumint(8) unsigned NOT NULL AUTO_INCREMENT, 
account varchar(50) NOT NULL, 	
work_split_count mediumint(8) DEFAULT '0' NOT NULL, 		 
work_split_wp mediumint(8) DEFAULT '0' NOT NULL, 	
pr_date varchar(20) NOT NULL,								 
time_stamp datetime, 	
PRIMARY KEY (id)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Structure for table "boco_report_marketwp_bypdpj" 
CREATE TABLE  if not exists boco_report_marketwp_bypdpj (
department varchar(32),										 
id mediumint(8) unsigned NOT NULL AUTO_INCREMENT, 
product mediumint(8) DEFAULT '0' NOT NULL, 					 
project mediumint(8) DEFAULT '0' NOT NULL, 					 
account varchar(50) NOT NULL, 	
work_split_count mediumint(8) DEFAULT '0' NOT NULL, 		 
work_split_wp mediumint(8) DEFAULT '0' NOT NULL,
pr_date varchar(20) NOT NULL,								 
time_stamp datetime, 	
PRIMARY KEY (id)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

 
# Structure for table "boco_git_groups" 
CREATE TABLE  if not exists boco_git_groups(
department varchar(32),						 
id mediumint(8), 
name varchar(255) NOT NULL, 				 
path varchar(255) NOT NULL,					 
description varchar(255), 					 
visibility_level  varchar(255), 	
web_url varchar(255),						 
full_name varchar(255) NOT NULL, 			 
full_path varchar(255) NOT NULL,
parent_id mediumint(8),	
userIds varchar(255),
userNames text(1000),
projects text(2000),
usr_owner text(500),
usr_master text(1000),
usr_developer text(1000),
usr_reporter text(1000),
usr_guest text(1000),
time_stamp datetime, 	
PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Structure for table "boco_git_projects" 
CREATE TABLE  if not exists boco_git_projects(
department varchar(32),							 
id mediumint(8), 
name varchar(255) NOT NULL, 					 
name_with_namespace varchar(255) NOT NULL, 
path varchar(255) NOT NULL,						 
description varchar(255), 						 
web_url varchar(255),							 
creator_id varchar(255) NOT NULL, 				 
created_at varchar(255) NOT NULL,
userIds varchar(255),
userNames text(1000),
usr_owner text(500),
usr_master text(1000),
usr_developer text(1000),
usr_reporter text(1000),
usr_guest text(1000),
time_stamp datetime, 	
PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Structure for table "boco_git_users" 
CREATE TABLE  if not exists boco_git_users(
department varchar(32),							 
id mediumint(8), 
name varchar(255) NOT NULL, 					 
username varchar(255) NOT NULL, 
email varchar(255) NOT NULL,					 
web_url varchar(255),						     
projects_limit varchar(255) NOT NULL, 			 
can_create_project varchar(255) NOT NULL,
time_stamp datetime, 	
PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Structure for table "boco_git_branches" 
CREATE TABLE  if not exists boco_git_branches(
department varchar(32),							 
name varchar(100) NOT NULL, 					 
project_id mediumint(8),
project_name varchar(200) NOT NULL, 
project_url varchar(200) NOT NULL,				 
protected boolean,								 
time_stamp datetime, 	
PRIMARY KEY (name,project_url)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Structure for table "boco_git_commits" 
CREATE TABLE  if not exists boco_git_commits(
department varchar(32),							 
project_id mediumint(8),
project_name varchar(255),
id varchar(255) NOT NULL,
short_id varchar(255) NOT NULL,  				 
title varchar(255), 
created_at datetime,							 
parent_id0 varchar(255),
parent_id1 varchar(255),
message text(1000),
author_name varchar(255) ,
author_email varchar(255),						 
authored_date datetime,
committer_name varchar(255) ,						
committer_email varchar(255) ,						
committed_date datetime,						
time_stamp datetime, 	
PRIMARY KEY (id,project_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Structure for table "boco_git_issues" 
CREATE TABLE  if not exists boco_git_issues(
department varchar(32),							 
project_id mediumint(8),
id varchar(255) NOT NULL,
iid varchar(255) NOT NULL,  				 
title varchar(255), 
created_at datetime,	
updated_at datetime,						 
label0 varchar(255),
label1 varchar(255),
milestoneId varchar(255),
milestoneTitle varchar(255),
milestoneState varchar(255),
milestoneStartDate varchar(255),
milestoneDueDate varchar(255),
message text(1000),
author_name varchar(255) ,
author_username varchar(255) ,
author_id varchar(255),						 
assignees varchar(255) ,	
assignee_name varchar(255) ,
assignee_username varchar(255) ,
assignee_id varchar(255),								
web_url varchar(255) ,	
time_stamp datetime, 	
PRIMARY KEY (id,project_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


#ALTER Structure for table zt_task;
ALTER TABLE `zt_task` ADD `contractsum`  mediumint(8) DEFAULT '0' NOT NULL ;

# Structure for view "v_report_user" 
create view if not exists v_report_user as 
(
    select
        d.id as amiba_id,
        d.name as amiba_name,
        d.path as root_path,
        d.id as group_id,
        d.name as group_name,
        u.account,
        u.realname
    from 
        zt_user u
        inner join zt_dept d on d.grade = 2 and d.id = u.dept
	where 
        u.showreportwork = 'y'
)
union all
(
    select
        d.id as amiba_id,
        d.name as amiba_name,
        d.path root_path,
        e.id as group_id,
        e.name as group_name,
        u.account,
        u.realname
    from 
        zt_user u
        inner join zt_dept d on d.grade = 2
        inner join zt_dept e on d.id = e.parent and e.id = u.dept
	where 
        u.showreportwork = 'y'		
);

-- 1.1

# Structure for table "dw_worklog_sync" 
CREATE TABLE  if not exists dw_worklog_sync(
  `id` int(11) DEFAULT NULL,
  `work_date` date DEFAULT NULL,
  `dep_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `account` varchar(255) DEFAULT NULL,
  `work_type` int(11) DEFAULT NULL,
  `contract_pro_id` int(11) DEFAULT NULL,
  `product` int(11) DEFAULT NULL,
  `module` int(11) DEFAULT NULL,
  `work_time` float DEFAULT NULL,
  `work_over_time` int(11) DEFAULT NULL,
  `cc_code` int(11) DEFAULT NULL,
  `work_sub_type` int(11) DEFAULT NULL,
  `work_content` varchar(255) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `task_id` int(11) DEFAULT NULL,
  `province` int(11) DEFAULT NULL,
  `province_name` varchar(255) DEFAULT NULL,
  `vender` int(11) DEFAULT NULL,
  `vender_name` varchar(255) DEFAULT NULL,
  `dev_project` int(11) DEFAULT NULL,
  `dev_project_name` varchar(255) DEFAULT NULL,
  `task_type` int(11) DEFAULT NULL,
  `support_dep` int(11) DEFAULT NULL,
  `support_dep_name` varchar(255) DEFAULT NULL,
  `zentao_product_id` int(11) DEFAULT NULL,
  `zentao_project_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#ALTER Structure for table boco_git_commits;
alter table boco_git_commits add branch varchar(100)  AFTER `project_name`;

#ALTER Structure for table boco_gitlab_pr;
alter table boco_gitlab_pr add base_commit varchar(255) AFTER `pr_url`;