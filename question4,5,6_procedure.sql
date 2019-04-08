# question 4
set @test_user = 'test_user';
set @user_location = (select point_location from Users where username = @test_user);
set @user_time = (select time(time_stamp) from Users where username = @test_user);
set @user_date = (select date(time_stamp) from Users where username = @test_user);
set @user_state = (select state from Users where username = @test_user);
set @user_timestamp = (select time_stamp from Users where username = @test_user);

drop temporary table if exists filtered_tags;

create temporary table filtered_tags	#tempory table for user which contains all the tags and people user want to see base on filter and state
select tag_name, friend_flag from Rules R left join Neighborhood N on R.NID = N.NID
where username = @test_user and (st_contains(area_polygon, @user_location) or (R.NID is null)) and 
check_s(R.scheduleID, @user_timestamp) and (R.state = @user_state or R.state is NULL)
;
select * from filtered_tags;
select (select tag_name from filtered_tags) = "";

select N.noteID, content from Note N left join Has_tag H on N.noteID = H.noteID
where (st_distance_sphere(@user_location, point_location) < radius or point_location is NULL or radius is NULL) and # checking the user's location with notes
		check_s(scheduleID, @user_timestamp) and check_access(access, N.username, @test_user)
				and (exists (		# checking with the user's filter's tag
							select 1 from filtered_tags FT 
							where (FT.tag_name is null and check_access(FT.friend_flag, N.username, @test_user)) or 
							(FT.tag_name = H.tag_name and check_access(FT.friend_flag, N.username, @test_user))
				));
		
#question 5			
drop view if exists user_location;
create view user_location as select username, point_location, state from Users;
select * from user_location;
set @testNote = 6;
set @testNote_point = (select point_location from Note where noteID = @testNote);
set @testNote_radius = (select radius from Note where noteID = @testNote);
set @testNote_owner = (select username from Note where noteID = @testNote);
set @testNote_access = (select access from Note where noteID = @testNote);
set @testNote_schedule = (select scheduleID from Note where noteID = @testNote);
set @curr_time = '2018-11-28 04:00:00';


select R.username from Rules R left join Neighborhood N on R.NID = N.NID left join user_location U on U.username = R.username
where (st_contains(area_polygon, point_location) or (R.NID is null)) and
check_s(R.`scheduleID`, @curr_time) and (R.state = U.state or R.state is NULL) and check_s(@testNote_schedule, @curr_time) and 
				(st_distance_sphere(point_location, @testNote_point) < @testNote_radius or @testNote_point is NULL or @testNote_radius is NULL) and 
				check_access(R.friend_flag, @testNote_owner, R.username) and check_access(@testNote_access, @testNote_owner, R.username) and 
				(R.tag_name is NULL or exists(select 1 from Has_tag H where H.`noteID` = @testNote and H.tag_name = R.tag_name))
;

#question 6
set @test_user = 'username3';
set @user_location = (select point_location from Users where username = @test_user);
set @user_time = (select time(time_stamp) from Users where username = @test_user);
set @user_date = (select date(time_stamp) from Users where username = @test_user);
set @user_state = (select state from Users where username = @test_user);
set @user_timestamp = (select time_stamp from Users where username = @test_user);

drop temporary table if exists filtered_tags;

create temporary table filtered_tags	#tempory table for user which contains all the tags and people user want to see base on filter and state
select tag_name, friend_flag from Rules R left join Neighborhood N on R.NID = N.NID
where username = @test_user and (st_contains(area_polygon, @user_location) or (R.NID is null)) and 
check_s(R.scheduleID, @user_timestamp) and (R.state = @user_state or R.state is NULL)
;

drop temporary table if exists all_notes;
create temporary table all_notes
select N.noteID, content from Note N left join Has_tag H on N.noteID = H.noteID
where (st_distance_sphere(@user_location, point_location) < radius or point_location is NULL or radius is NULL) and # checking the user's location with notes
		check_s(scheduleID, @user_timestamp) and check_access(access, N.username, @test_user)
				and (exists (		# checking with the user's filter's tag
							select 1 from filtered_tags FT 
							where (FT.tag_name is null and check_access(FT.friend_flag, N.username, @test_user)) or 
							(FT.tag_name = H.tag_name and check_access(FT.friend_flag, N.username, @test_user))
				));
select * from all_notes; 

set @keywords = 'NOTE month';
set @pattern = (select replace(@keywords, ' ', '.+'));
select * from all_notes where content REGEXP @pattern;

# function for checking schedule and access
drop function if exists check_s;
create function check_s(sID INT, c_time datetime)
returns boolean deterministic
return (select (exists 
		(select 1 from Schedules
		where ScheduleID = sID and
		((time(c_time) between start_time and end_time) or start_time is null or end_time is null) and
		(repeat_flag = 1 or (repeat_flag = 2 and day(date(c_time)) = day(start_date)) 
				or (repeat_flag = 3 and day(date(c_time)) = day(start_date) and month(date(c_time)) = month(start_date))
				or (repeat_flag = 0 and date(c_time) = start_date)
				or (week_flag = true and weekdays = dayofweek(date(c_time))))
		)));

drop function if exists check_access;
create function check_access(access INT, user1 varchar(50), user_me varchar(50))
returns boolean deterministic
return ((access = 1 or (access = 2 and (
		exists (select 1 from Friend F 
		where (F.username = user1 and F.friend = user_me and F.flag = true) or (F.username = user_me and F.friend = user1 and F.flag = true) )
												))
					or (access = 3 and user1 = user_me)));
					
					
					
select T.friend from(select case when username = 'username2' then friend
								when friend = 'username2' then username
								end as friend
								from Friend
								where flag = true) T
where T.friend is not NULL;

UPDATE Friend SET flag = 0 where (username = 'username2' and friend = 'username3') or (friend = 'username2' and username = 'username3');


drop PROCEDURE if exists get_notes;
DELIMITER //
CREATE PROCEDURE get_notes(IN p_username varchar(50), IN p_user_location point, IN p_user_state varchar(50), IN p_user_timestamp datetime)
begin
drop temporary table if exists filtered_tags;

create temporary table filtered_tags	#tempory table for user which contains all the tags and people user want to see base on filter and state
select tag_name, friend_flag from Rules R left join Neighborhood N on R.NID = N.NID
where username = p_username and (st_contains(area_polygon, p_user_location) or (R.NID is null)) and 
check_s(R.scheduleID, p_user_timestamp) and (R.state = p_user_state or R.state is NULL)
;

select N.noteID, content, allowC from Note N left join Has_tag H on N.noteID = H.noteID
where (st_distance_sphere(p_user_location, point_location) < radius or point_location is NULL or radius is NULL) and # checking the user's location with notes
		check_s(scheduleID, p_user_timestamp) and check_access(access, N.username, p_username)
				and (exists (		# checking with the user's filter's tag
							select 1 from filtered_tags FT 
							where (FT.tag_name is null and check_access(FT.friend_flag, N.username, p_username)) or 
							(FT.tag_name = H.tag_name and check_access(FT.friend_flag, N.username, p_username))
				));
end //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE search_note(IN p_username varchar(50), IN p_user_location point, IN p_user_state varchar(50), IN p_user_timestamp datetime, IN pattern varchar(50))
begin
drop temporary table if exists filtered_tags;

create temporary table filtered_tags	#tempory table for user which contains all the tags and people user want to see base on filter and state
select tag_name, friend_flag from Rules R left join Neighborhood N on R.NID = N.NID
where username = p_username and (st_contains(area_polygon, p_user_location) or (R.NID is null)) and 
check_s(R.scheduleID, p_user_timestamp) and (R.state = p_user_state or R.state is NULL)
;

drop temporary table if exists all_notes;
create temporary table all_notes
select N.noteID, content from Note N left join Has_tag H on N.noteID = H.noteID
where (st_distance_sphere(p_user_location, point_location) < radius or point_location is NULL or radius is NULL) and # checking the user's location with notes
		check_s(scheduleID, p_user_timestamp) and check_access(access, N.username, p_username)
				and (exists (		# checking with the user's filter's tag
							select 1 from filtered_tags FT 
							where (FT.tag_name is null and check_access(FT.friend_flag, N.username, p_username)) or 
							(FT.tag_name = H.tag_name and check_access(FT.friend_flag, N.username, p_username))
				));
select * from all_notes where content REGEXP pattern;
end //
DELIMITER ;


call get_notes(@test_user, @user_location, @user_state, @user_timestamp);
call search_note(@test_user, @user_location, @user_state, @user_timestamp, @pattern);
insert into Neighborhood(N_name, area_polygon) values
	('Sutton Place', st_geomfromtext('polygon(
	(40.762333 -73.968077, 40.758355 -73.970987, 40.754209 -73.961127, 40.758020 -73.957816, 40.762333 -73.968077))'));
    
    insert into Neighborhood(N_name, area_polygon) values
	('Columbus Circle', st_geomfromtext('polygon(
	(40.762808 -73.982481, 40.762397 -73.986269, 40.768144 -74.001494, 40.774067 -73.997022, 40.762808 -73.982481))'));
    
    insert into Neighborhood(N_name, area_polygon) values
	('Upper East Side', st_geomfromtext('polygon(
	(40.764514 -73.973339, 40.781737 -73.960583, 40.779521 -73.955610, 40.785934 -73.951091, 40.782449 -73.942806, 40.775819 -73.941067, 40.758016 -73.957796, 40.764514 -73.973339))'));
    
	insert into Neighborhood(N_name, area_polygon) values
	('Central Park', st_geomfromtext('polygon(
	(40.764514 -73.973339, 40.767827 -73.981649, 40.800714 -73.958353, 40.796899 -73.949308, 40.764514 -73.973339))'));
    
	insert into Neighborhood(N_name, area_polygon) values
	('Upper West Side', st_geomfromtext('polygon(
	(40.768006 -73.982015, 40.774085 -73.997053, 40.806281 -73.971838, 40.800719 -73.958376, 40.768006 -73.982015))'));
    
    select 1 from Neighborhood where NID = 5 and (st_contains(area_polygon, point(40.767127, -73.989635)));
