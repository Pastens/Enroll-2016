CREATE DATABASE enroll_admission;

USE enroll_admission;

CREATE TABLE IF NOT EXISTS users(
	uid int NOT NULL PRIMARY KEY AUTO_INCREMENT,
	username varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	password varchar(255) NOT NULL,
	email varchar(255) NOT NULL,
	sector int NOT NULL,
	authority int NOT NULL
);

CREATE TABLE IF NOT EXISTS STATUS_ROUND(
    id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
	round int NOT NULL
);

CREATE TABLE IF NOT EXISTS interviewees(
	studentid varchar(20) NOT NULL PRIMARY KEY,
    studentname char(15) not null,
	serialnumber varchar(50) NOT NULL,
	serialdigit varchar(10) NOT NULL,
	status int NOT NULL,
	time1tid int NOT NULL,
	time2tid int NOT NULL,
	checkintime varchar(255) NOT NULL,
	multisector int NOT NULL
);

CREATE TABLE IF NOT EXISTS rates(
	rateid int NOT NULL PRIMARY KEY AUTO_INCREMENT,
	studentid varchar(20) NOT NULL,
	serialnumber varchar(255) NOT NULL,
	serialdigit  varchar(10) NOT NULL,
	uid int NOT NULL,
	round int NOT NULL,
	t_rates int NOT NULL,
	r_rates int NOT NULL
);

CREATE TABLE IF NOT EXISTS comments(
	commid int NOT NULL PRIMARY KEY AUTO_INCREMENT,
	studentid varchar(20) NOT NULL,
	serialnumber varchar(255) NOT NULL,
	serialdigit  varchar(10) NOT NULL,
	uid int NOT NULL,
	round int NOT NULL,
	sector int NOT NULL,
	comment varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
);

CREATE TABLE IF NOT EXISTS rooms(
	rid int NOT NULL PRIMARY KEY AUTO_INCREMENT,
  	roomnumber varchar(10) NOT NULL,
	roomstatus int NOT NULL,
	gid int NOT NULL,
	susr_count int NOT NULL,
	suid_core int NOT NULL,
	suid1 int NOT NULL,
	suid2 int NOT NULL,
	suid3 int NOT NULL,
	suid4 int NOT NULL
);

CREATE TABLE IF NOT EXISTS groups(
	gid int NOT NULL PRIMARY KEY AUTO_INCREMENT,
	round int NOT NULL,
	status int NOT NULL,
	member_count int NOT NULL,
	member1_sn varchar(255) default 0 NOT NULL,
	member2_sn varchar(255) default 0 NOT NULL,
	member3_sn varchar(255) default 0 NOT NULL,
	member4_sn varchar(255) default 0 NOT NULL,
	member5_sn varchar(255) default 0 NOT NULL,
	member6_sn varchar(255) default 0 NOT NULL,
	member7_sn varchar(255) default 0 NOT NULL,
	member8_sn varchar(255) default 0 NOT NULL,
  	member9_sn varchar(255) default 0 NOT NULL,
  	member10_sn varchar(255) default 0 NOT NULL,
  	member11_sn varchar(255) default 0 NOT NULL,
  	member12_sn varchar(255) default 0 NOT NULL
);

CREATE TABLE IF NOT EXISTS times(
	tid int NOT NULL PRIMARY KEY AUTO_INCREMENT,
	datespec varchar(255) NOT NULL,
	timespec varchar(255) NOT NULL,
	description varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
);

CREATE TABLE IF NOT EXISTS waiting_list(
	studentid varchar(20) NOT NULL,
    studentname char(15) not null,
	serialnumber varchar(255) NOT NULL PRIMARY KEY,
	serialdigit  varchar(10) NOT NULL,
	inqueuetime varchar(100) NOT NULL,
	sector int NOT NULL
);

create table if not exists enroll2016_application(
        studentid char(10) not null,
        studentname char(15) not null,
        mobilephone char(11) not null,
        shortphone char(6) default null,
        gender boolean not null,
        major char(60) not null,
        grade char(4) not null,
        dormitory char(60) null default '',
        birthplace char(60) not null,
        job1 char(3) default null,
        job2 char(3) default null,
        job3 char(3) default null,
        job4 char(3) default null,
        job5 char(3) default null,
        interviewtime char(1) not null,
        interviewtime2 char(1) not null,
        introduction text not null,
        impression text default null,
        isapplychinesecompetition boolean not null,
        useragent char(255) default null,
        submittime datetime default null,
        version int default 0 not null,
        resultstatus int default 1 not null,
        primary key (studentid,version)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;
