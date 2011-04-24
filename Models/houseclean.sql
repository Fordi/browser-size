-- Data older than 10 months has a weight of 1/1024; we won't need it cluttering up our main tables.

-- Make sure the table's there; don't copy data yet, as we'll do that in a second via a replace
create table if not exists archive_widths as (select * from browser_widths where 1=0);
create table if not exists archive_heights as (select * from browser_heights where 1=0);

-- copy the old data into the archive, overwriting anything that matches the primary key (pixels/stamp)
replace into archive_widths (select * from browser_widths);
replace into archive_heights (select * from browser_heights);

-- Drop anything older than 10 months.
delete from browser_widths where stamp < DATE(FROM_UNIXTIME(UNIX_TIMESTAMP()-26298000));
delete from browser_heights where stamp < DATE(FROM_UNIXTIME(UNIX_TIMESTAMP()-26298000));