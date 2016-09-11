# ZJUBTVEnrollSystem2016

## 改进
- 将“屏幕”账户与“引导”账户权限合并，其余各账户权限号不变 
- 支持同一用户名下的不同权限账户 
- 优化数据管理体验 
- 修正了原版“成绩考核”中的bug 
- 添加了考核结果查看功能 

## 隐患
- 未在面试者及面试官人数达到上限的情况下进行测试(建议4-5人开不同浏览器以不同账号登录进行测试)
- 未在多人添加多备注的情况下进行测试，不确定备注显示效果如何
- 未在错误操作的情况下测试系统异常处理能力
- 未在面试过程中出现面试官变更的情况下进行测试

## 已解决问题
- 有辅面试官未打完分也可结束面试
	* 建议主面在结束面试前确认所有辅面完成评分 *
- 未报名成绩会显示在grades里面 
	* 采用了一种较暴力方法(相关代码以*violence注释标记)解决，以后最好将rates分离为两张表 *
- 需在controller's addintogroup 增加回滚保护机制
	* 已添加必要的回滚保护机制 *
- 每场面试人数上限固定（一面5人，二面10人）
	* 已更改为面试者分组上限10人，面试官分组上限12人，人数限制代码段均以"inter_number_control"进行标记 *
- 可以添加查看最终录取情况的显示页面
	* 已添加，可在面试界面查看 *
- 管理员界面显示数据的易读性应当提高
	* 由于相关表信息的限制，暂不作处理，如在测试过程中发现有改进必要再另行打算 *
- 面试者照片链接需要等报名系统域名确定
	* 本次报名系统并未接受面试者照片信息 *
- 引导界面无法自动刷新
	* 已解决，可以自动刷新 *
- 面试题目等待汪老师确定后添加进screen内
	* 面试问题太长且微信格式不容易调整...决定放弃这个方案 *

## 部署说明
-本系统没有自动初始化程序，需手动配置

	**配置数据库**

	mysql> source database.sql
	(面试者报名信息需从报名系统处获得，测试时可手动添加至enroll2016_application表内)

	**修改MySQL数据库登录配置**

	*[EAS PATH]/Application/Home/Conf/config.php* 中修改对应配置

	**手动设置初始配置**

        设置 STATUS_ROUND表 中当前面试的进度为 1
        INSERT INTO status_round VALUES(1, 1);

        设置 Users表 中超级管理员账户，其权限值为 16
        INSERT INTO users VALUES(1, 'username', 'password', 'email', 'sector_id', 16);

    **各字段值** 对应参阅 enroll_admission_db_segment_def.md(不完整)

## 使用说明
- 操作流程
	* 1.管理员使用系统配置时的账号密码登录，在“用户管理”内添加其余低权限用户;*
	* 2.管理员在“考核地点管理”添加考点门牌号(本系统默认仅一处考点)及相应面试官ID(可在“用户管理”处查看);*
	* 3.管理员在“状态管理”中选择进行第一/二轮面试;*
	* 4.检录人员登录账号，并开始根据学号进行检录;
		引导人员在候考区大屏幕上登录账号，并进行准备工作;*
	* 5.检录成功的面试者进入候考区待考，屏幕上会显示考场状态及下一场面试人员;*
	* 6.在2中被添加进指定考场的面试官进入考点并登录账号进行准备;*
	* 7.准备工作结束后由主面点击“开始面试”按钮，此时所有本场面试官的显示界面将出现本场面试者考核信息，点击“详细信息”可查看该面试者的报名志愿等资料;*
	* 8.在7中主面点击“开始面试”按钮后，候考区大屏幕的“考场状态”将转换为红色的“开始面试”标志，此时由引导人员将面试者带至考场;*
	* 9.面试过程中所有面试官可反复提交成绩及添加备注，最新成绩将覆盖上一次成绩;*
	* 10.在本场面试结束后，由主面点击“结束面试”按钮，此时主面可进入“成绩考核”界面进行考核(如不即刻进行审核，直接开始下一场面试也可)，辅面可以查看面试者成绩并帮助主面进行考核;*
	* 11.在该轮所有面试结束后，由主面确认所有面试者成绩均已考核完毕，再由管理员在“状态管理”中选择“一面/二面结束”;*
	* 12.所有面试官均可在其显示界面下查看该轮面试通过/录取情况，并对通过者进行短信/电话联系;*
	
- 长者的一些建议
	* 1.建议用户名取用户的姓名拼音全拼，以便进行用户管理;*
	* 2.候考区中可根据屏幕显示将下一场面试者组织在一起，以便后期快速引导至考场;*
	* 3.能想到的好像就这么多，欢迎试坑，欢迎打脸(smile);*

## 其他说明
- 正式流水号生成规则

	**SerialNumber Rule: YYYY--M---stMDudenDH--HItn---u--mISS----**

		//$serialDate = "YYYYMMDDHHIISS";
		YYYY-M-MD-DH-HI-ISS			//2015-0-81-01-21-010

		//$stuId = "studentnum";
		st-uden-tn-u-m				//31-3000-07-3-8

