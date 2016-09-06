## Enroll Admission System

# 仿真模拟系统

配置好系统后，可以在管理员界面的 **状态管理** 设置初号机（一面仿真）启动

之后在 **全局不可逆控制** 开始 以中文基准神经连接

后续代码继续跟进中，目前可以模拟到一面结束（尚不能出成绩）

### 部署说明
- 初始化系统

    本系统没有自动初始化程序，需手动配置

    **配置数据库**

    mysql> source database.sql

    **修改MySQL数据库登录配置**

    *[EAS PATH]/Application/Home/Conf/config.php* 中修改对应配置

    将 *M('', 'enroll2015_application', '');* 全部替换为 *M('enroll2015_application');* 即可； 这一代码仅在**IndexController.php**中出现

    **手动设置初始配置**

        设置 STATUS_ROUND表 中当前面试的进度为 1
        INSERT INTO status_round VALUES(1, 1);

        设置 Users表 中超级管理员账户，其权限值为 16
        INSERT INTO users VALUES(1, 'username', 'password', 'email', 'sector_id', 16);

    **各字段值** 对应参阅 enroll_admission_db_segment_def.md

    **问题列表** 对应参阅 issue.md

<br />

- 2015-2016 Fall/Winter 配置说明(可在管理界面手动配置)

    **Sectors表**

    	1 - 电视台 - television
    	2 - 广播台 - radio
    	3 - 校报社 - newspaper

### 其他说明
- 正式流水号生成规则

	**SerialNumber Rule: YYYY--M---stMDudenDH--HItn---u--mISS----**

		//$serialDate = "YYYYMMDDHHIISS";
		YYYY-M-MD-DH-HI-ISS			//2015-0-81-01-21-010

		//$stuId = "studentnum";
    	st-uden-tn-u-m				//31-3000-07-3-8
