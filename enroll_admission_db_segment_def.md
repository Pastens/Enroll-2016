#Enroll-Exam System DB Schema
各字段声明及字段值定义

## Users
- uid			***int***
- username		***varchar(255)***
- password		***varchar(255)***
- email			***varchar(255)***
- sector		***int***
- authority		***int***
	- 0x00 Unknown  
    - 0x01 检录
	- 0x03 引导
	- 0x04 辅面试官
	- 0x08 主面试官
	- 0x10 管理员

## STATUS_ROUND
- id
	- 0x01 ROUND （此值恒定为1 方便后端修改数据）
- round
	- 0x00 Unknown
	- 0x01 等待面试开始
	- 0x02 第一轮面试
	- 0x06 第二轮面试
	- 0x0E 面试结束

## Interviewees
- studentId		***varchar***
- serialNumber	***varchar*** 正式流水号（40位混合）
- serialDigit 	***varchar*** 记录流水号（5位随机数）
- status		***int***
	- 0x00 Unknown
	- 0x01 等待第一次检录，报名表已经提交
	- 0x02 自主放弃面试（未使用）
	- 0x04 等待进行第一轮面试，已经过第一次检录
	- 0x08 正在进行第一轮面试
    - 0x0C 等待第二次检录，第一轮面试已通过
	- 0x10 等待进行第二轮面试，已经过第二次检录
	- 0x20 正在进行第二轮面试
	- 0x40 未录取
	- 0x80 已录取
- time1tid		***int***
- time2tid		***int***
- checkInTime	***varchar(255)***
- multiSector	***int***
	- 0x00 Unknown  二进制从低位到高位依次表示 电视台、广播台
	- 0x01 电视台
	- 0x02 广播台

## Rates
- studentId		***int***
- serialNumber	***varchar(255)***
- uid			***int***
- round			***int***
- sector		***int***
- t_rates_0~4	***int*** *# 10-Rate*
- r_rates_0~4	***int*** *# 10-Rate*

## Comments
- studentId		***int***
- serialNumber	***varchar(255)***
- uid			***int***
- round			***int***
- sector		***int***
- comments		***varchar(255)***

## Rooms
- rid			***int***
- roomnumber	***varchar(10)*** *# 607-2*
- roomstatus	***int***
	- 0x00 Unknown
	- 0x01 等待，可以分配面试
	- 0x02 进行，不可以分配面试
- sector		***int***
- gid			***int***
- susr_count	***int***
	面试人数统计
- suid_core		***int***
	主面试官
- suid			***int***
	最多允许4+1位面试官分配在同一房间

## Groups
- gid			***int***
- round			***int***
- status        ***int***
	- 0x00 Unknown
	- 0x01 等待，准备面试问题
	- 0x02 正在面试
	- 0x03 面试结束
- member_cnt	***int***
- member_sn		***varchar(255)***
	最多允许10位面试者分配在同一组别

## Times
- tid			***int***
- dateSpec		***varchar(255)***
- timeSpec		***varchar(255)***
- description	***varchar(255)***

## Waiting_List
- studentId		***int***
- serialNumber	***varchar(255)***
- sector		***int***

