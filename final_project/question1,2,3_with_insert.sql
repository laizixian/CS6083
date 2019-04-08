# create predefined neighborhood
insert into Neighborhood(N_name, area_polygon) values 
	('Midtown', st_geomfromtext('polygon(
	(40.768175 -73.982487, 40.762319 -73.968191, 40.751625 -73.975957, 40.757361 -73.989895, 40.768175 -73.982487))'));
insert into Neighborhood(N_name, area_polygon) values
	('Midtown East', st_geomfromtext('polygon(
	(40.758338 -73.970990, 40.754190 -73.961080, 40.747862 -73.966814, 40.749359 -73.969042, 40.751444 -73.973959,
	 40.750986 -73.974319, 40.751631 -73.975960, 40.758338 -73.970990))'));

#(1) create users
insert into Users(username, last_name, first_name, pass_word, email, login_status, time_stamp, point_location, state) values
	('username1', 'Harris', 'Rocky', 'password1', 'RockyHarris@email.com', True, now(), point(40.759683, -73.978079), 'working');
	
insert into Users(username, last_name, first_name, pass_word, email) values
	('username2', 'Hubbard', 'Milton', 'password2', 'MiltonHubbard@email.com');

INSERT INTO `Users` (`username`, `last_name`, `first_name`, `pass_word`, `email`, `login_status`, `time_stamp`, `point_location`, `state`)
VALUES
	('username2', 'Hubbard', 'Milton', 'password2', 'MiltonHubbard@email.com', NULL, '2018-11-27 18:03:12', X'000000000101000000FA9CBB5D2F614440DBA6785C547E52C0', NULL);


#create predefined tags
insert into Tag(tag_name) values
	("cute"),
	("shopping"),
	("me"),
	("tourism"),
	("food"),
	("transportation"),
	("friends"),
	("bars"),
	("art"),
	("nature"),
	("photo"),
	("dog"),
	("cat");

#(2) creating testing note
insert into Schedules(start_time, end_time, start_date, repeat_flag, weekdays, week_flag) values
	('17:00:00', '19:00:00', '2018-11-27', 0, 0, false);
insert into Note(username, scheduleID, point_location, radius, content, allowC, access) values
	('username1', (select last_insert_id()), point(40.755756, -73.977648), 1000, 
	'note show on 2018-11-27 5pm-7pm; does not repeat; in Midtown with radius 1000; allow comment; everyone can see(1)',
	 true, 1);
insert into Has_tag(noteID, tag_name) values
	((select last_insert_id()), 'shopping'),
	((select last_insert_id()), 'friends'),
	((select last_insert_id()), 'me'),
	((select last_insert_id()), 'food');

insert into Schedules(start_time, end_time, start_date, repeat_flag, weekdays, week_flag) values
	('18:00:00', '20:00:00', null, 1, 0, false);
insert into Note(username, scheduleID, point_location, radius, content, allowC, access) values
	('username1', (select last_insert_id()), point(40.761518, -73.972707), 500,
	'note show 6pm - 8pm; repeat every day; in midtown with radius 500, does not allow comment; everyone can see(1)',
	true, 1);
insert into Has_tag(noteID, tag_name) values
	((select last_insert_id()), 'food');
	

# create a schedule that repeat every day during 5pm - 7pm
insert into Schedules(start_time, end_time, start_date, repeat_flag, weekdays, week_flag) values
	('17:00:00', '19:00:00', null, 1, 0, false); 
# create filter where give note very day between 5pm - 7pm with food tag in Midtown when hungry
insert into Rules(username, tag_name, scheduleID, NID, friend_flag, state) values 
	('username2', 'food', (select last_insert_id()), 2, 0, 'hungry');
	
# (3) show friend list
select 'username2' in (select T.friend from(select case when username = 'username2' then friend
								when friend = 'username2' then username
								end as `friend`
					from Friend
					where flag = true) T
where T.friend is not NULL);


# test user 2 with notes
# find filters base on the user's time and location
set @user_location = (select point_location from Users where username = 'username2');
set @user_time = (select time(time_stamp) from Users where username = 'username2');
set @user_date = (select date(time_stamp) from Users where username = 'username2');
set @user_state = (select state from Users where username = 'username2');
set @user_timestamp = (select time_stamp from Users where username = 'username2');

select tag_name, friend_flag from Rules R left join Neighborhood N on R.NID = N.NID left join Schedules S on R.scheduleID = S.ScheduleID;

drop temporary table if exists filtered_tags;
create temporary table filtered_tags	#tempory table for user which contains all the tags and people user want to see base on filter and state
select tag_name, friend_flag from Rules R left join Neighborhood N on R.NID = N.NID left join Schedules S on R.scheduleID = S.ScheduleID
where username = 'username2' and (st_contains(area_polygon, @user_location) or (R.NID is null)) and
((@user_time between start_time and end_time) or start_time is null or end_time is null) and 
(R.state = @user_state or R.state is NULL) and 
(repeat_flag = 1 or (repeat_flag = 2 and day(@user_date) = day(start_date)) 
				or (repeat_flag = 3 and day(@user_date) = day(start_date) and month(@user_date) = month(start_date))
				or (repeat_flag = 0 and @user_date = start_date)
				or (week_flag = true and weekdays = dayofweek(@user_date)));
select * from filtered_tags;
# show all the note visible now 
select * from Note N left join Schedules S on N.scheduleID = S.scheduleID left join Has_tag H on N.noteID = H.noteID
where (st_distance_sphere(@user_location, point_location) < radius or point_location is NULL or radius is NULL) and # checking the user's location with notes
		((@user_time between start_time and end_time) or start_time is NULL or end_time is NULL) and #checking the user's time with note's schedule
		(repeat_flag = 1 
				or (repeat_flag = 2 and day(@user_date) = day(start_date)) 
				or (repeat_flag = 3 and day(@user_date) = day(start_date) and month(@user_date) = month(start_date))
				or (repeat_flag = 0 and @user_date = start_date)
				or (week_flag = true and weekdays = dayofweek(@user_date))) and
				(access = 1 or (access = 2 and (		#checking the access right 
												exists (select 1 from Friend F 
														where (F.username = N.username and F.friend = 'username2' and F.flag = true) or (F.username = 'username2' and F.friend = N.username and F.flag = true) )
												))
							or (access = 3 and N.username = 'username2'))
				and (exists (		# checking with the user's filter's tag
							select 1 from filtered_tags FT where FT.tag_name is null or FT.tag_name = H.tag_name
				))
;

select * from Note N left join Has_tag H on N.noteID = H.noteID
where (st_distance_sphere(@user_location, point_location) < radius or point_location is NULL or radius is NULL) and # checking the user's location with notes
		check_s(scheduleID)

drop view if exists user_location;
create view user_location as select username, point_location, state from Users;
select * from user_location;
set @testNote = 2;
set @testNote_point = (select point_location from Note where noteID = 2);
set @testNote_radius = (select radius from Note where noteID = 2);
set @testNote_owner = (select username from Note where noteID = 2);
set @testNote_access = (select access from Note where noteID = 2);

# find all users who can see the note
select * from Rules R left join Neighborhood N on R.NID = N.NID left join Schedules S on R.scheduleID = S.ScheduleID left join user_location U on U.username = R.username
where (st_contains(area_polygon, point_location) or (R.NID is null)) and
((time(now()) between start_time and end_time) or start_time is null or end_time is null) and 
(R.state = U.state or R.state is NULL) and 
(repeat_flag = 1 or (repeat_flag = 2 and day(date(now())) = day(start_date)) 
				or (repeat_flag = 3 and day(date(now())) = day(start_date) and month(date(now())) = month(start_date))
				or (repeat_flag = 0 and date(now()) = start_date)
				or (week_flag = true and weekdays = dayofweek(date(now())))) and
				(st_distance_sphere(point_location, @testNote_point) < @testNote_radius or @testNote_point is NULL or @testNote_radius is NULL) and 
				(@testNote_access = 1 or (@testNote_access = 2 and (
															exists (select 1 from Friend F
																	where (F.username = R.username and F.friend = @testNote_owner and F.flag = true) or (F.username = @testNote_owner and F.friend = true)))))
;
select 1 from Schedules
where repeat_flag = 1;
select (exists 
		(select 1 from Schedules
		where ScheduleID = 2 and repeat_flag = 1
		));
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
select check_s(2, '2018-11-28 23:01:00');