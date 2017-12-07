# Date: 2017-9-26
# update to custom for zentao 

# -- 1.0
# -- feature-880
alter table zt_story add passnote varchar(30); 

# -- feature-965
alter table zt_story add(satisficingeval varchar(10)); 

# -- feature-1597
alter table zt_story add (estimatestory  float unsigned NOT NULL);
alter table zt_story add (estimatetask  float unsigned NOT NULL);
alter table zt_story add (storytype varchar(20));
alter table zt_story add (storybsa char(1));
alter table zt_story add (storyvaluelevel char(1));

# -- 1.1
# -- feature-1488
alter table zt_task add (satisficingeval char(1));