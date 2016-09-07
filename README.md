# ZJUBTVEnrollSystem2016

## 改进
	* 将“屏幕”账户与“引导”账户权限合并，其余各账户权限号不变
	* 支持同一用户名下的不同权限账户
	* 优化数据管理体验
	* 修正了原版“成绩考核”中的bug

## 问题
### 有辅面试官未打完分也可结束面试
	* 建议主面在结束面试前确认所有辅面完成评分
### 未报名成绩会显示在grades里面 
	* 采用了一种较暴力方法(相关代码以*violence注释标记)解决，以后最好将rates分离为两张表
### 需在controller's addintogroup 增加回滚保护机制
	* 已添加必要的回滚保护机制
### 面试题目等待汪老师确定后添加进screen内
### 每场面试人数上限固定（一面5人，二面10人）
### 部分资料链接需要等报名系统域名确定(grades.html line63; interview.html line241)
### 管理员界面显示数据的易读性应当提高
### 引导界面无法自动刷新
### 可以添加查看最终录取情况的显示页面

## 部署说明
	本系统没有自动初始化程序，需手动配置

	**配置数据库**

	mysql> source database.sql

	**修改MySQL数据库登录配置**

	*[EAS PATH]/Application/Home/Conf/config.php* 中修改对应配置

	**手动设置初始配置**

        设置 STATUS_ROUND表 中当前面试的进度为 1
        INSERT INTO status_round VALUES(1, 1);

        设置 Users表 中超级管理员账户，其权限值为 16
        INSERT INTO users VALUES(1, 'username', 'password', 'email', 'sector_id', 16);

    **各字段值** 对应参阅 enroll_admission_db_segment_def.md

## 使用说明
	*建议用户名取用户的姓名拼音全拼



## 其他说明
- 正式流水号生成规则

	**SerialNumber Rule: YYYY--M---stMDudenDH--HItn---u--mISS----**

		//$serialDate = "YYYYMMDDHHIISS";
		YYYY-M-MD-DH-HI-ISS			//2015-0-81-01-21-010

		//$stuId = "studentnum";
		st-uden-tn-u-m				//31-3000-07-3-8


