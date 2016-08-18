<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    // PAGE
    public function index(){
        $this->login_url = U('Home/Index/login_post');
        $this->display();
    }

    public function login_post(){
    	
    }

    public function logout_post(){
        session('login_uid', null);
        session('login_auth', null);
        $this->redirect('Home/Index/index');
    }

    // PAGE
    public function profile(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
        }

        $this->logout_url = U('Home/Index/logout_post');
        $this->userid = $_SESSION['login_uid'];

        $User = M('users');
        $map['uid'] = $_SESSION['login_uid'];
        $fetchUser = $User->where($map)->find();
        if(!$fetchUser) $this->error('无效的权限');
        $this->profile = $fetchUser;

        $Round = M('status_round');
        $fetchRound = $Round->find();
        switch ($fetchRound['round']) {
                case 0:
                    $this->round = "未知";
                    break;
                case 1:
                    $this->round = "等待面试开始";
                    break;
                case 2:
                    $this->round = "正在进行第一轮面试";
                    break;
                case 6:
                    $this->round = "正在进行第二轮面试";
                    break;
                case 14:
                    $this->round = "面试已经结束";
                    break;
                case 254:
                    $this->round = "面试仿真系统 第一轮面试";
                    break;
                case 255:
                    $this->round = "面试仿真系统 第二轮面试";
                    break;
                default:
                    $this->round = "未定义状态号";
                    break;
        }

        //switch($_SESSION['login_auth']){
        if(($_SESSION['login_auth'] & 1) == 1){
            $ctrl_msg = "检录";
            $ctrl_url = U('Home/Index/checkin');
            $line = '<a class="btn btn-info" href="' . $ctrl_url . '" role="button">' . $ctrl_msg . '</a> ';
            $actions[] = $line;
        }
        if(($_SESSION['login_auth'] & 2) == 2){
            $ctrl_msg = "引导";
            $ctrl_url = U('Home/Index/guide');
            $line = '<a class="btn btn-info" href="' . $ctrl_url . '" role="button">' . $ctrl_msg . '</a> ';
            $actions[] = $line;
        }
        if(($_SESSION['login_auth'] & 3) == 3){
                $ctrl_msg = "显示进度";
                $ctrl_url = U('Home/Index/screen');
                $line = '<a class="btn btn-info" href="' . $ctrl_url . '" role="button">' . $ctrl_msg . '</a> ';
                $actions[] = $line;
        }
        if(($_SESSION['login_auth'] & 4) == 4 || ($_SESSION['login_auth'] & 8) == 8){
            $Room = M('rooms');
            $fetchAllRoom = $Room->select();
            $i = 0;
            foreach($fetchAllRoom as $allRoom){
                if($allRoom['suid_core'] == $_SESSION['login_uid']){
                    $avaliableRoom[$i] = array($allRoom['rid'], $allRoom['roomnumber']);
                    $i++;
                }
                if($allRoom['suid1'] == $_SESSION['login_uid']){
                    $avaliableRoom[$i] = array($allRoom['rid'], $allRoom['roomnumber']);
                    $i++;
                }
                if($allRoom['suid2'] == $_SESSION['login_uid']){
                    $avaliableRoom[$i] = array($allRoom['rid'], $allRoom['roomnumber']);
                    $i++;
                }
                if($allRoom['suid3'] == $_SESSION['login_uid']){
                    $avaliableRoom[$i] = array($allRoom['rid'], $allRoom['roomnumber']);
                    $i++;
                }
                if($allRoom['suid4'] == $_SESSION['login_uid']){
                    $avaliableRoom[$i] = array($allRoom['rid'], $allRoom['roomnumber']);
                    $i++;
                }
                if($allRoom['suid5'] == $_SESSION['login_uid']){
                    $avaliableRoom[$i] = array($allRoom['rid'], $allRoom['roomnumber']);
                    $i++;
                }
                if($allRoom['suid6'] == $_SESSION['login_uid']){
                    $avaliableRoom[$i] = array($allRoom['rid'], $allRoom['roomnumber']);
                    $i++;
                }
                if($allRoom['suid7'] == $_SESSION['login_uid']){
                    $avaliableRoom[$i] = array($allRoom['rid'], $allRoom['roomnumber']);
                    $i++;
                }
                if($allRoom['suid8'] == $_SESSION['login_uid']){
                    $avaliableRoom[$i] = array($allRoom['rid'], $allRoom['roomnumber']);
                    $i++;
                }
                if($allRoom['suid9'] == $_SESSION['login_uid']){
                    $avaliableRoom[$i] = array($allRoom['rid'], $allRoom['roomnumber']);
                    $i++;
                }
                if($allRoom['suid10'] == $_SESSION['login_uid']){
                    $avaliableRoom[$i] = array($allRoom['rid'], $allRoom['roomnumber']);
                    $i++;
                }
            }

            $allLine = array();
            foreach($avaliableRoom as $avaRoom){
                $ctrl_msg = $avaRoom[1] . "考场";
                $ctrl_url = U('Home/Index/interview', array('rid'=>$avaRoom[0]));
                $line = '<a class="btn btn-info" href="' . $ctrl_url . '" role="button">' . $ctrl_msg . '</a> ';
                $actions[] = $line;
            }
        }
        if(($_SESSION['login_auth'] & 16) == 16){
                $ctrl_msg = "系统管理";
                $ctrl_url = U('Home/Index/administration');
                $line = '<a class="btn btn-info" href="' . $ctrl_url . '" role="button">' . $ctrl_msg . '</a> ';
                $actions[] = $line;
        }
        $this->actions = $actions;

        $this->display();
    }

    // PAGE
    public function administration(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
        }else{
            $session_auth_check = session('login_auth');
            if(($session_auth_check & 16) != 16){
                $this->error('无效的权限');
            }
        }

        $this->profile_url = U('Home/Index/profile');
        $this->logout_url = U('Home/Index/logout_post');
        $this->management_url = U('Home/Index/admin_management_post');
        $this->dangerctl_url = U('Home/Index/admin_dangerous_post');

        $this->assign('uid', session('login_uid'));

        $User = M('users');
        $this->fetchUser = $User->select();
        $Sector = M('sectors');
        $this->fetchSector = $Sector->select();
        $Round = M('status_round');
        $fetchRound = $Round->find();
        switch ($fetchRound['round']){
                case 0:
                    $this->fetchRound = "未知";
                    break;
                case 1:
                    $this->fetchRound = "等待面试开始";
                    break;
                case 2:
                    $this->fetchRound = "正在进行第一轮面试";
                    break;
                case 6:
                    $this->fetchRound = "正在进行第二轮面试";
                    break;
                case 14:
                    $this->fetchRound = "面试已经结束";
                    break;
                case 254:
                    $this->fetchRound = "零号机神经连接正常";
                    break;
                case 255:
                    $this->fetchRound = "初号机神经连接正常";
                    break;
                default:
                    $this->fetchRound = "未定义代码";
                    break;
        }
        $Time = M('times');
        $this->fetchTime = $Time->select();
        $Room = M('rooms');
        $this->fetchRoom = $Room->select();
        $Tags_meta = M('tags_meta');
        $this->fetchTagsmeta = $Tags_meta->select();

        $Interviewee = M('interviewees');
        $this->fetchInterviewee = $Interviewee->select();
        $Enroll = M('', 'enroll2015_application', '');
        $this->fetchEnroll = $Enroll->where('resultstatus = 1')->select();

        $Group = M('groups');
        $this->fetchGroup = $Group->select();

        $this->display();
    }

    public function admin_management_post(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
        }else{
            $session_auth_check = session('login_auth');
            if(($session_auth_check & 16) != 16){
                $this->error('无效的权限');
            }
        }
        if (IS_POST){
            switch($_POST['target']){
                case 'USER':
                    $Model = M('users');
                    switch($_POST['request']){
                        case 'insert':
                            $map['username'] = $_POST['username'];
                            $map['password'] = $_POST['password'];
                            $map['email'] = $_POST['email'];
                            $map['sector'] = $_POST['sector'];
                            $map['authority'] = $_POST['authority'];
                            if($Model->add($map)) $this->redirect('Home/Index/administration/#USER');
                            else $this->error('添加失败');
                            break;
                        case 'delete':
                            if($Model->delete($_POST['uid'])) $this->redirect('Home/Index/administration/#USER');
                            else $this->error('删除失败');
                            break;
                        default:
                            $this->error('无效的请求');
                    }
                    break;
                case 'SECTOR':
                    $Model = M('sectors');
                    switch($_POST['request']){
                        case 'insert':
                            $map['description'] = $_POST['description'];
                            $map['naming'] = $_POST['naming'];
                            if($Model->add($map)) $this->redirect('Home/Index/administration/#SECTOR');
                            else $this->error('添加失败');
                            break;
                        case 'delete':
                            if($Model->delete($_POST['sid'])) $this->redirect('Home/Index/administration/#SECTOR');
                            else $this->error('删除失败');
                            break;
                        default:
                            $this->error('无效的请求');
                    }
                    break;
                case 'STATUS':
                    $Model = M('status_round');
                    switch($_POST['request']){
                        case 'update':
                            $map = $Model->find();
                            $map['round'] = $_POST['value'];
                            if($Model->where('id = 1')->save($map)) $this->redirect('Home/Index/administration/#STATUS');
                            else $this->error('更新失败');
                            break;
                        default:
                            $this->error('无效的请求');
                    }
                    break;
                case 'TIME':
                    $Model = M('times');
                    switch($_POST['request']){
                        case 'insert':
                            $map['datespec'] = $_POST['datespec'];
                            $map['timespec'] = $_POST['timespec'];
                            $map['description'] = $_POST['description'];
                            if($Model->add($map)) $this->redirect('Home/Index/administration/#TIME');
                            else $this->error('添加失败');
                            break;
                        case 'delete':
                            if($Model->delete($_POST['tid'])) $this->redirect('Home/Index/administration/#TIME');
                            else $this->error('删除失败');
                            break;
                        default:
                            $this->error('无效的请求');
                    }
                    break;
                case 'ROOM':
                    $Model = M('rooms');
                    switch($_POST['request']){
                        case 'insert':
                            $map['roomnumber'] = $_POST['roomnumber'];
                            $map['roomstatus'] = 1;
                            $map['sector'] = $_POST['sector'];
                            $map['gid'] = 0;
                            $map['suid_core'] = $_POST['suid_core'];
                            $map['suid1'] = $_POST['suid1'];
                            $map['suid2'] = $_POST['suid2'];
                            $map['suid3'] = $_POST['suid3'];
                            $map['suid4'] = $_POST['suid4'];
                            $map['suid5'] = $_POST['suid5'];
                            $map['suid6'] = $_POST['suid6'];
                            $map['suid7'] = $_POST['suid7'];
                            $map['suid8'] = $_POST['suid8'];
                            $map['suid9'] = $_POST['suid9'];
                            $map['suid10'] = $_POST['suid10'];
                            $count = 0;
                            for($i = 1; $i <= 10; $i++){
                                if($map['suid' . $i] != 0) $count++;
                            }
                            $map['susr_count'] = $count;
                            if($Model->add($map)) $this->redirect('Home/Index/administration/#ROOM');
                            else $this->error('添加失败');
                            break;
                        case 'delete':
                            if($Model->delete($_POST['rid'])) $this->redirect('Home/Index/administration/#ROOM');
                            else $this->error('删除失败');
                            break;
                        default:
                            $this->error('无效的请求');
                    }
                    break;
                case 'TAGSMETA':
                    $Model = M('tags_meta');
                    switch($_POST['request']){
                        case 'insert':
                            $map['description'] = $_POST['description'];
                            if($Model->add($map)) $this->redirect('Home/Index/administration/#TAGSMETA');
                            else $this->error('添加失败');
                            break;
                        case 'delete':
                            if($Model->delete($_POST['tagid'])) $this->redirect('Home/Index/administration/#TAGSMETA');
                            else $this->error('删除失败');
                            break;
                        default:
                            $this->error('无效的请求');
                    }
                    break;
                default:
                    $this->error('无效的请求');
            }
        }else{
            $this->error('无效的请求');
        }
    }

    public function admin_dangerous_post(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
        }else{
            $session_auth_check = session('login_auth');
            if(($session_auth_check & 16) != 16){
                $this->error('无效的权限');
            }
        }
        if (IS_POST){
            switch ($_POST['request']) {
                case 'resetRemoteDatabase':
                    $Enroll = M('', 'enroll2015_application', '');
                    $mapEnroll['version'] = 0;
                    if($Enroll->where($mapEnroll)->setField('resultStatus', 1) === false){
                        $this->error('更新失败');
                    }else{
                        $this->success('已重置远程数据库状态', U('/Home/Index/administration'));
                    }
                    break;
                case 'dropInterviewee':
                    $Interviewee = M('interviewees');
                    if($Interviewee->execute("TRUNCATE table `interviewees`") === false){
                        $this->error('丢弃失败');
                    }else{
                        $this->success('已丢弃本地已检录数据', U('/Home/Index/administration'));
                    }
                    break;
                case 'dropWaitingList':
                    $T = M('television_waiting_list');
                    if($T->execute("TRUNCATE table `television_waiting_list`") === false){
                        $this->error('丢弃失败');
                    }else{
                        $R = M('radio_waiting_list');
                        if($R->execute("TRUNCATE table `radio_waiting_list`") === false){
                            $this->error('丢弃失败');
                        }else{
                            $N = M('newspaper_waiting_list');
                            if($N->execute("TRUNCATE table `newspaper_waiting_list`") === false){
                                $this->error('丢弃失败');
                            }else{
                                $this->success('已丢弃本地等候队列', U('/Home/Index/administration'));
                            }
                        }
                    }
                    break;
                case 'dropRoom':
                    $Room = M('rooms');
                    if($Room->execute("TRUNCATE table `rooms`") === false){
                        $this->error('丢弃失败');
                    }else{
                        $this->success('已丢弃本地考点信息', U('/Home/Index/administration'));
                    }
                    break;
                case 'dropGroup':
                    $Group = M('groups');
                    if($Group->execute("TRUNCATE table `groups`") === false){
                        $this->error('丢弃失败');
                    }else{
                        $this->success('已丢弃本地分组信息', U('/Home/Index/administration'));
                    }
                    break;
                case 'dropRTC':
                    $Rate = M('rates');
                    if($Rate->execute("TRUNCATE table `rates`") === false){
                        $this->error('丢弃评分失败');
                    }else{
                        $Tag = M('tags');
                        if($Tag->execute("TRUNCATE table `tags`") === false){
                            $this->error('丢弃标签记录失败');
                        }else{
                            $Comment = M('comments');
                            if($Comment->execute("TRUNCATE table `comments`") === false){
                                $this->error('丢弃备注信息失败');
                            }else{
                                $this->success('已丢弃全部考核数据', U('/Home/Index/administration'));
                            }
                        }
                    }
                    break;

                case 'resetRoom':
                    $Room = M('rooms');
                    if($Room->where('1=1')->setField('gid', 0) === false || $Room->where('1=1')->setField('roomstatus', 1) === false){
                        $this->error('重置失败');
                    }else{
                        $this->success('已重置本地考点分组', U('/Home/Index/administration'));
                    }
                    break;
                case 'simuEnroll':
                    $Round = M('status_round');
                    $fetchRound = $Round->find();
                    if($fetchRound['round'] != 254 && $fetchRound['round']!=255) {
                        $this->error('EVA尚未准备就绪');
                    }
                    $SimuEnroll = M('simu_enroll');
                    $Enroll = M('', 'enroll2015_application', '');
                    $fetchEnroll = $Enroll->select();
                    foreach ($fetchEnroll as $e) {
                        if(!$SimuEnroll->add($e)){ $this->error('神经连接失败'); }
                    }
                    $this->success('以中文为基准进行神经连接，同步率100%', U('/Home/Index/administration'));
                    break;
                case 'dropSimuEnroll':
                    $SimuEnroll = M('simu_enroll');
                    if($SimuEnroll->execute("TRUNCATE table `simu_enroll`") === false){
                        $this->error('丢弃失败');
                    }else{
                        $this->success('已丢弃安全装置信息', U('/Home/Index/administration'));
                    }
                    break;
                case 'confirmGrades':
                    $Round = M('status_round');
                    $fetchRound = $Round->find();

                    // Set Model Enroll
                    if($fetchRound['round'] == 2 || $fetchRound['round'] == 6) {
                        $Enroll = M('', 'enroll2015_application', '');
                    }
                    if($fetchRound['round'] == 254 || $fetchRound['round'] == 255) {
                        $Enroll = M('simu_enroll');
                    }

                    if($fetchRound['round'] == 2 || $fetchRound['round'] == 254) {
                        $newInStatus = 12;
                    }
                    // Simu Enroll
                    if($fetchRound['round'] == 6 || $fetchRound['round'] == 255) {
                        $newInStatus = 128;
                    }
                    $newOutStatus = 64;

                    $Interviewee = M('interviewees');
                    $fetchInterviewee = $Interviewee->select();
                    foreach ($fetchInterviewee as $intv) {
                        $map['studentid'] = $intv['studentid'];
                        $map['version'] = 0;
                        if($intv['multisector'] > 0) {
                            if($Interviewee->where($map)->setField('status', $newInStatus) === false){
                                $this->error('更新本地状态失败');
                            }else{
                                if($Enroll->where($map)->setField('resultstatus', $newInStatus) === false){
                                    $this->error('更新远程状态失败');
                                }
                            }
                        }else{
                            if($Interviewee->where($map)->setField('status', $newOutStatus) === false){
                                $this->error('更新本地状态失败');
                            }else{
                                if($Enroll->where($map)->setField('resultstatus', $newOutStatus) === false){
                                    $this->error('更新远程状态失败');
                                }
                            }
                        }
                    }
                    $this->success('成功更新成绩');
                    break;
                default:
                    $this->error('无效的请求');
                    break;
            }
        }else{
            $this->error('无效的请求');
        }
    }

    // PAGE
    public function checkin(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
        }else{
            $session_auth_check = session('login_auth');
            if(($session_auth_check & 1) != 1){
                $this->error('无效的权限');
            }
        }

        $this->profile_url = U('Home/Index/profile');
        $this->logout_url = U('Home/Index/logout_post');
        $this->checkin_url = U('Home/Index/checkin_post');
        $this->checkout_url = U('Home/Index/checkout_post');

        $Enroll = M('', 'enroll2015_application', '');
        $Interviewee = M('interviewees');
        $Round = M('status_round');
        $fetchRound = $Round->find();
        if($fetchRound['round'] == 2){
            $this->fetchEnroll = $Enroll->where('resultstatus = 1')->select();
            $this->fetchInterviewee = $Interviewee->where('status = 4')->order('checkintime desc')->select();
        }
        if($fetchRound['round'] == 6){
            $this->fetchEnroll = $Enroll->where('resultstatus = 12')->select();
            $this->fetchInterviewee = $Interviewee->where('status = 16')->order('checkintime desc')->select();
        }

        $simuEnroll = M('simu_enroll');
        // EVA - 00
        if($fetchRound['round'] == 254){
            $this->fetchEnroll = $simuEnroll->where('resultstatus = 1')->select();
            $this->fetchInterviewee = $Interviewee->where('status = 4')->order('checkintime desc')->select();
        }
        // EVA - 01
        if($fetchRound['round'] == 255){
            $this->fetchEnroll = $simuEnroll->where('resultstatus = 12')->select();
            $this->fetchInterviewee = $Interviewee->where('status = 16')->order('checkintime desc')->select();
        }

        $this->display();
    }

    public function checkin_post(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
        }else{
            $session_auth_check = session('login_auth');
            if(($session_auth_check & 1) != 1){
                $this->error('无效的权限');
            }
        }
        if (IS_POST){
            $stuId = $_POST['stuid'];
            $map['studentid'] = $stuId;

            $Round = M('status_round');
            $fetchRound = $Round->find();
            if($fetchRound['round'] == 2 || $fetchRound['round'] == 6) {
                $Enroll = M('', 'enroll2015_application', '');
            }else{
                // Simu Enroll
                if($fetchRound['round'] == 254 || $fetchRound['round'] == 255) {
                    $Enroll = M('simu_enroll');
                }else{
                    $this->error('面试进度逻辑号错误');
                }
            }

            $fetchEnroll = $Enroll->where($map)->find();
            $Interviewee = M('interviewees');
            $checkInterviewee = $Interviewee->where($map)->find();
            if($checkInterviewee['status'] == 4 || $checkInterviewee['status'] == 16) { $this->error('该人员已被检录'); }

            if(!$fetchEnroll) { $this->error('无效的请求'); }
            else{
                if($fetchEnroll['resultstatus'] != 1 && $fetchEnroll['resultstatus'] != 12) {
                    $this->error('该人员不能被检录，逻辑状态错误');
                }

                // First CheckIn / Transfer Data
                if($fetchRound['round'] == 2 || $fetchRound['round'] == 254){
                    // Genrerate SerialNumber
                    $serialDate = date('YmdHis');
                    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                    $random_str = '';
                    for ($i = 0; $i < 16; $i++){ $random_str .= $chars[mt_rand(0, strlen($chars)-1)]; }

                    $serialNumber = substr($serialDate, 0, 4) . substr($random_str, 0, 2);
                    $serialNumber .= substr($serialDate, 4, 1) . substr($random_str, 2, 3);
                    $serialNumber .= substr($stuId, 0, 2) . substr($serialDate, 5, 2);
                    $serialNumber .= substr($stuId, 2, 4) . substr($serialDate, 7, 2) . substr($random_str, 5, 2);
                    $serialNumber .= substr($serialDate, 9, 2) . substr($stuId, 6, 2) . substr($random_str, 7, 3);
                    $serialNumber .= substr($stuId, 8, 1) . substr($random_str, 10, 2);
                    $serialNumber .= substr($stuId, 9, 1) . substr($serialDate, 11, 3) . substr($random_str, 12, 4);

                    // Generate serialDigit
                    $chars = '0123456789';
                    $random_str = '';
                    for ($i = 0; $i < 5; $i++){ $random_str .= $chars[mt_rand(0, strlen($chars)-1)]; }
                    $serialDigit = $random_str;

                    // Transfer & insert data
                    $addInterviewee['studentid'] = $stuId;                          // Set studengId
                    $addInterviewee['serialnumber'] = $serialNumber;                // Set serialNumber
                    $addInterviewee['serialdigit'] = $serialDigit;                  // Set serialDigit
                    $addInterviewee['status'] = $fetchEnroll['resultstatus'];       // Set status
                    $Round = M('status_round');
                    $fetchRound = $Round->find();
                    if($fetchRound['round'] == 2 && $addInterviewee['status'] == 1) $addInterviewee['status'] = 4;
                    if($fetchRound['round'] == 6 && $addInterviewee['status'] == 12) $addInterviewee['status'] = 16;
                    // Simu Enroll
                    if($fetchRound['round'] == 254 && $addInterviewee['status'] == 1) $addInterviewee['status'] = 4;
                    if($fetchRound['round'] == 255 && $addInterviewee['status'] == 12) $addInterviewee['status'] = 16;
                    if($addInterviewee['status'] == $fetchEnroll['resultstatus']) {
                        $this->error('该人员检录逻辑状态错误，请联系管理员');
                    }
                    $addInterviewee['time1tid'] = $fetchEnroll['interviewtime'];    // Set time1
                    $addInterviewee['time2tid'] = $fetchEnroll['interviewtime2']+5; // Set time2
                    $addInterviewee['checkintime'] = date('Y-m-d H:i:s');           // Set checkinTime
                    $addInterviewee['multisector'] = 0;                             // Set multiSector
                    $job[1] = substr($fetchEnroll['job1'], 0, 1);
                    $job[2] = substr($fetchEnroll['job2'], 0, 1);
                    $job[3] = substr($fetchEnroll['job3'], 0, 1);
                    $job[4] = substr($fetchEnroll['job4'], 0, 1);
                    $job[5] = substr($fetchEnroll['job5'], 0, 1);
                    for($step = 1; $step <= 5; $step++){
                        if($job[$step] == 'A') { $addInterviewee['multisector'] |= 2; }
                        if($job[$step] == 'B') { $addInterviewee['multisector'] |= 1; }
                        if($job[$step] == 'C') { $addInterviewee['multisector'] |= 1; }
                        if($job[$step] == 'D') { $addInterviewee['multisector'] |= 4; }
                    }
                    if(!$Interviewee->add($addInterviewee)) {
                        $this->error('首次检录失败');
                    }else{
                        // Insert WaitingList
                        $T_WL = M('television_waiting_list');
                        $R_WL = M('radio_waiting_list');
                        $N_WL = M('newspaper_waiting_list');

                        $succ = 1;
                        $inQueueTime = date('Y-m-d H:i:s');
                        if(($addInterviewee['multisector'] & 1) == 1){
                            $waitingList['studentid'] = $stuId;
                            $waitingList['serialnumber'] = $serialNumber;
                            $waitingList['serialdigit'] = $serialDigit;
                            $waitingList['inqueuetime'] = $inQueueTime;
                            $waitingList['status'] = 1;
                            if(!$T_WL->add($waitingList)) $succ = 0;
                        }
                        if(($addInterviewee['multisector'] & 2) == 2){
                            $waitingList['studentid'] = $stuId;
                            $waitingList['serialnumber'] = $serialNumber;
                            $waitingList['serialdigit'] = $serialDigit;
                            $waitingList['inqueuetime'] = $inQueueTime;
                            $waitingList['status'] = 1;
                            if(!$R_WL->add($waitingList)) $succ = 0;
                        }
                        if(($addInterviewee['multisector'] & 4) == 4){
                            $waitingList['studentid'] = $stuId;
                            $waitingList['serialnumber'] = $serialNumber;
                            $waitingList['serialdigit'] = $serialDigit;
                            $waitingList['inqueuetime'] = $inQueueTime;
                            $waitingList['status'] = 1;
                            if(!$N_WL->add($waitingList)) $succ = 0;
                        }
                        if(!$succ) { $this->error('侯考列表更新失败'); }
                    }
                }

                // Second CheckIn / Transfer Data
                if($fetchRound['round'] == 6 || $fetchRound['round'] == 255){
                    if($Interviewee->where('studentid = ' . $stuId)->setField('status', 16) === false){
                        $this->error('二次检录失败');
                    }else{
                        // Insert WaitingList
                        $T_WL = M('television_waiting_list');
                        $R_WL = M('radio_waiting_list');
                        $N_WL = M('newspaper_waiting_list');

                        $Inter = M('interviewees');
                        $fetch = $Inter->where('studentid = ' . $stuId)->find();
                        $serialNumber = $fetch['serialnumber'];
                        $serialDigit = $fetch['serialdigit'];

                        $succ = 1;
                        $inQueueTime = date('Y-m-d H:i:s');
                        if(($fetch['multisector'] & 1) == 1){
                            $waitingList['studentid'] = $stuId;
                            $waitingList['serialnumber'] = $serialNumber;
                            $waitingList['serialdigit'] = $serialDigit;
                            $waitingList['inqueuetime'] = $inQueueTime;
                            $waitingList['status'] = 1;
                            if(!$T_WL->add($waitingList)) $succ = 0;
                        }
                        if(($fetch['multisector'] & 2) == 2){
                            $waitingList['studentid'] = $stuId;
                            $waitingList['serialnumber'] = $serialNumber;
                            $waitingList['serialdigit'] = $serialDigit;
                            $waitingList['inqueuetime'] = $inQueueTime;
                            $waitingList['status'] = 1;
                            if(!$R_WL->add($waitingList)) $succ = 0;
                        }
                        if(($fetch['multisector'] & 4) == 4){
                            $waitingList['studentid'] = $stuId;
                            $waitingList['serialnumber'] = $serialNumber;
                            $waitingList['serialdigit'] = $serialDigit;
                            $waitingList['inqueuetime'] = $inQueueTime;
                            $waitingList['status'] = 1;
                            if(!$N_WL->add($waitingList)) $succ = 0;
                        }
                        if(!$succ) { $this->error('侯考列表更新失败'); }
                    }
                }

                // Update remote database
                if($fetchRound['round'] == 2 || $fetchRound['round'] == 254){
                    $map['resultstatus'] = 1;
                    $newStatus = 4;
                }
                if($fetchRound['round'] == 6 || $fetchRound['round'] == 255){
                    $map['resultstatus'] = 12;
                    $newStatus = 16;
                    $Inter = M('interviewees');
                    $fetch = $Inter->where('studentid = ' . $stuId)->find();
                    $serialDigit = $fetch['serialdigit'];
                }
                if($fetchRound['round'] == 6 || $fetchRound['round'] == 255) $map['resultstatus'] = 12;
                // REMOTE SHUTDOWN
                if($Enroll->where($map)->setField('resultstatus', $newStatus) === false) {
                    $this->error('远程状态更新失败');
                }else{
                    $this->success('检录成功，' . $fetchEnroll['studentname'] . '的流水号为 ' . $serialDigit, U('Home/Index/checkin'), 5);
                    //$this->redirect('Home/Index/checkin');
                }

            }
        }else{
            $this->error('无效的请求');
        }
    }

    public function checkout_post(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
        }else{
            $session_auth_check = session('login_auth');
            if(($session_auth_check & 1) != 1){
                $this->error('无效的权限');
            }
        }
        if (IS_POST){
            $stuId = $_POST['stuid'];
            $map['studentid'] = $stuId;
            $Interviewee = M('interviewees');
            $fetchInterviewee = $Interviewee->where($map)->find();

            if(!$fetchInterviewee) { $this->error('无效的请求'); }
            else{
                if($_POST['access'] != 'thereisnospoon') { $this->error('该人员不能被检出'); }
                $SN = $fetchInterviewee['serialnumber'];

                if($Interviewee->delete($stuId)){
                    // Delete WaitingList
                    $T_WL = M('television_waiting_list');
                    $R_WL = M('radio_waiting_list');
                    $N_WL = M('newspaper_waiting_list');

                    $succ = 1;
                    if(($fetchInterviewee['multisector'] & 1) == 1){ if(!$T_WL->delete($SN)) $succ = 0; }
                    if(($fetchInterviewee['multisector'] & 2) == 2){ if(!$R_WL->delete($SN)) $succ = 0; }
                    if(($fetchInterviewee['multisector'] & 4) == 4){ if(!$N_WL->delete($SN)) $succ = 0; }
                    if(!$succ) { $this->error('侯考列表更新失败'); }

                    // Update remote database
                    $Round = M('status_round');
                    $fetchRound = $Round->find();
                    $newStatus = $fetchInterviewee['status'];
                    if($fetchRound['round'] == 2 && $fetchInterviewee['status'] == 4){
                        $map['resultstatus'] = 4;
                        $newStatus = 1;
                    }
                    if($fetchRound['round'] == 6 && $fetchInterviewee['status'] == 16){
                        $map['resultstatus'] = 16;
                        $newStatus = 12;
                    }
                    // Simu Enroll
                    if($fetchRound['round'] == 254 && $fetchInterviewee['status'] == 4){
                        $map['resultstatus'] = 4;
                        $newStatus = 1;
                    }
                    if($fetchRound['round'] == 255 && $fetchInterviewee['status'] == 16){
                        $map['resultstatus'] = 16;
                        $newStatus = 12;
                    }
                    if($newStatus == $fetchInterviewee['status']) {
                        $this->error('该人员检录逻辑状态错误，请联系管理员');
                    }

                    if($fetchRound['round'] == 2 || $fetchRound['round'] == 6) {
                        $Enroll = M('', 'enroll2015_application', '');
                    }
                    // Simu Enroll
                    if($fetchRound['round'] == 254 || $fetchRound['round'] == 255) {
                        $Enroll = M('simu_enroll');
                    }

                    if($Enroll->where($map)->setField('resultstatus', $newStatus) === false) {
                        $this->error('远程状态更新失败');
                    }else{
                        $this->redirect('Home/Index/checkin');
                    }
                }else{
                    $this->error('检出失败');
                }
            }
        }else{
            $this->error('无效的请求');
        }
    }

    // PAGE
    public function guide(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
        }else{
            $session_auth_check = session('login_auth');
            if(($session_auth_check & 2) != 2){
                $this->error('无效的权限');
            }
        }

        $this->profile_url = U('Home/Index/profile');
        $this->logout_url = U('Home/Index/logout_post');
        $this->setGroup_url = U('Home/Index/setGroup_post');

        // $User = M('users');
        // $fetchUser = $User->where('uid = ' . $_SESSION['login_uid']);
        // $targetSector = $fetchUser['sector'];

        $Room = M('rooms');
        $fetchRoom = $Room->order('sector')->select();
        $this->fetchRoom = $fetchRoom;
        $this->countRoom = count($fetchRoom);

        $T_WL = M('television_waiting_list');
        $this->twl = $T_WL->order('inqueuetime')->select();
        $R_WL = M('radio_waiting_list');
        $this->rwl = $R_WL->order('inqueuetime')->select();
        $N_WL = M('newspaper_waiting_list');
        $this->nwl = $N_WL->order('inqueuetime')->select();

        // $User = M('users');
        // $fetchUser = $User->where('uid = ' . $_SESSION['login_uid'])->find();
        // if($fetchUser['sector'] == 1){
        //     $this->twl = $twl;
        //     $this->maxrec = count($this->twl);
        // }
        // if($fetchUser['sector'] == 2){
        //     $this->rwl = $rwl;
        //     $this->maxrec = count($this->rwl);
        // }
        // if($fetchUser['sector'] == 3){
        //     $this->nwl = $nwl;
        //     $this->maxrec = count($this->nwl);
        // }

        $max = count($this->twl);
        if(count($this->rwl) > $max) $max = count($this->rwl);
        if(count($this->nwl) > $max) $max = count($this->nwl);
        $this->maxrec = $max;

        // Enroll Info Mapping
        $Round = M('status_round');
        $fetchRound = $Round->find();
        if($fetchRound['round'] == 2 || $fetchRound['round'] == 6) {
            $Enroll = M('', 'enroll2015_application', '');
        }
        // Simu Enroll
        if($fetchRound['round'] == 254 || $fetchRound['round'] == 255) {
            $Enroll = M('simu_enroll');
        }

        // Simu Enroll
        if($fetchRound['round'] == 2 || $fetchRound['round'] == 254) $fetchEnroll = $Enroll->where('resultstatus = 4')->select();
        if($fetchRound['round'] == 6 || $fetchRound['round'] == 255) $fetchEnroll = $Enroll->where('resultstatus = 16')->select();
        //$fetchEnroll = $Enroll->where('resultstatus = 1')->select();
        foreach($fetchEnroll as $e) $eMap[$e['studentid']] = $e['studentname'];
        $this->enrollMap = $eMap;
        $this->fetchRound = $fetchRound;

        $this->display();
    }

    public function setGroup_post(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
        }else{
            $session_auth_check = session('login_auth');
            if(($session_auth_check & 2) != 2){
                $this->error('无效的权限');
            }
        }
        if (IS_POST){
            $Interviewee = M('interviewees');

            // Verify POST data
            $postCount = 0;
            for($i = 1; $i <= 10; $i++){
                $map['serialnumber'] = $_POST['sn' . $i];
                $fetchInterviewee = $Interviewee->where($map)->find();
                if($fetchInterviewee){
                    if($fetchInterviewee['status'] != 4 && $fetchInterviewee['status'] != 16) {
                        $this->error('无效的分组信息', U('Home/Index/guide'));
                    }
                    $availableSN[$postCount] = $_POST['sn' . $i];
                    $postCount++;
                }
            }
            if($postCount == 0) $this->error('无效的分组信息', U('Home/Index/guide'));

            $Room = M('rooms');
            $fetchRoom = $Room->where('rid = "' . $_POST['rid'] . '"')->find();
            if(!$fetchRoom || $fetchRoom['roomstatus'] != 1) $this->error('房间状态不正常', U('Home/Index/guide'));

            $Round = M('status_round');
            $fetchRound = $Round->find();

            $Group = M('groups');
            $T_WL = M('television_waiting_list');
            $R_WL = M('radio_waiting_list');
            $N_WL = M('newspaper_waiting_list');

            for($i = 0; $i < $postCount; $i++){
                $step = $i + 1;
                $addGroup['member' . $step . '_sn'] = $availableSN[$i];
                $mapInterviewee['serialnumber'] = $availableSN[$i];
                $fetchInterviewee = $Interviewee->where($mapInterviewee)->find();

                // Lock WaitingList
                $T_WL->where($mapInterviewee)->setField('status', 2);
                $R_WL->where($mapInterviewee)->setField('status', 2);
                $N_WL->where($mapInterviewee)->setField('status', 2);

                // Update status
                if($fetchInterviewee['status'] == 4) $newStatus = 8;
                if($fetchInterviewee['status'] == 16) $newStatus = 32;
                if($Interviewee->where($mapInterviewee)->setField('status', $newStatus) === false) {
                    // Rolling back
                    for($j = 0; $j <= $i; $j++){
                        $rollingMap['serialnumber'] = $availableSN[$j];
                        $rolling = $Interviewee->where($rollingMap)->find();
                        if($fetchRound['round'] == 2) $rollingStatus = 4;
                        if($fetchRound['round'] == 6) $rollingStatus = 16;
                        if($Interviewee->where($rollingMap)->setField('status', $rollingStatus) === false) {
                            echo '<head><meta charset="utf-8"></head>';
                            echo '致命的数据库回滚错误，请联系管理员 <br />';
                            dump ($_POST);
                            echo '可用SerialNumber <br />';
                            dump ($availableSN);
                            echo '循环进行至' . $j . '/$i:' . $i . '<br />';
                            return ;
                        }
                        $T_WL->where($rollingMap)->setField('status', 1);
                        $R_WL->where($rollingMap)->setField('status', 1);
                        $N_WL->where($rollingMap)->setField('status', 1);
                    }
                    $this->error('更新面试者信息出错', U('Home/Index/guide'));
                }
            }

            // Fill the empty row
            for($i = ($postCount + 1) ; $i <= 12; $i++){
                $addGroup['member' . $i . '_sn'] = "N/A";
            }
            $addGroup['member_count'] = $postCount;
            $addGroup['round'] = $fetchRound['round'];

            $gid = $Group->add($addGroup);

            if($gid > 0){
                $Room = M('rooms');
                $mapRoom['rid'] = $_POST['rid'];

                $updateRoom['roomstatus'] = 2;
                $updateRoom['gid'] = $gid;
                if(!($Room->where($mapRoom)->save($updateRoom))){
                    echo '<head><meta charset="utf-8"></head>';
                    echo '致命的分组信息更新错误，请联系管理员 <br />';
                    dump($_POST);
                    echo '分组组号' . $gid;
                    break;
                }
                $this->redirect('Home/Index/guide');
            }else{
                $this->error('无法建立分组', U('Home/Index/guide'));
            }
        }else{
            $this->error('无效的请求', U('Home/Index/guide'));
        }
    }

    // PAGE
    public function screen(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
        }else{
            $session_auth_check = session('login_auth');
            if(($session_auth_check & 3) != 3){
                $this->error('无效的权限');
            }
        }

        $this->profile_url = U('Home/Index/profile');
        $this->logout_url = U('Home/Index/logout_post');

        $Room = M('rooms');
        $this->fetchRoom = $Room->order('sector')->select();

        $T_WL = M('television_waiting_list');
        $this->twl = $T_WL->order('inqueuetime')->select();
        $R_WL = M('radio_waiting_list');
        $this->rwl = $R_WL->order('inqueuetime')->select();
        $N_WL = M('newspaper_waiting_list');
        $this->nwl = $N_WL->order('inqueuetime')->select();

        $max = count($this->twl);
        if(count($this->rwl) > $max) $max = count($this->rwl);
        if(count($this->nwl) > $max) $max = count($this->nwl);
        $this->maxrec = $max;

        $this->display();
    }

    // PAGE
    public function interview(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
        }else{
            $session_auth_check = session('login_auth');
            if((($session_auth_check & 4) != 4) && (($session_auth_check & 8) != 8)){
                $this->error('无效的权限');
            }
        }
        // switch($session_auth_check){
        //     case 4:
        //         $Super = 0;
        //         break;
        //     case 8:
        //         $Super = 1;
        //         break;
        // }

        if(!IS_GET) { $this->error('无效的权限'); }

        $this->profile_url = U('Home/Index/profile');
        $this->logout_url = U('Home/Index/logout_post');
        //$this->allinfo_url = U('Home/Index/allinfo_get');
        $this->ratectl_url = U('Home/Index/grades');

        $this->setTags_url = U('Home/Index/admission_set_tags_post');
        $this->setMark_url = U('Home/Index/admission_set_marks_post');
        $this->setComm_url = U('Home/Index/admission_set_comments_post');
        $this->rmvComm_url = U('Home/Index/admission_remove_comments_post');
        $this->terminateInterview_url = U('Home/Index/admission_terminate_interview');

        $map['rid'] = $_GET['rid'];
        $Room = M('rooms');
        $fetchRoom = $Room->where($map)->find();

        $u = $_SESSION['login_uid'];
        $f = $fetchRoom;
        if($f['suid_core']!=$u && $f['suid1']!=$u && $f['suid2']!=$u && $f['suid3']!=$u && $f['suid4']!=$u && $f['suid5']!=$u && $f['suid6']!=$u && $f['suid7']!=$u && $f['suid8']!=$u && $f['suid9']!=$u && $f['suid10']!=$u){
            $this->error('无效的权限');
        }

        $Round = M('status_round');
        $fetchRound = $Round->find();
        switch ($fetchRound['round']) {
                case 0:
                    $this->fetchRound = "未知";
                    break;
                case 1:
                    $this->fetchRound = "等待面试开始";
                    break;
                case 2:
                    $this->fetchRound = "正在进行第一轮面试";
                    break;
                case 6:
                    $this->fetchRound = "正在进行第二轮面试";
                    break;
                case 14:
                    $this->fetchRound = "面试已经结束";
                    break;
                case 254:
                    $this->fetchRound = "EVA 00 / 仿真模拟 一面";
                    break;
                case 255:
                    $this->fetchRound = "EVA 01 / 仿真模拟 二面";
                    break;
                default:
                    $this->fetchRound = "未定义状态号";
                    break;
        }
        $Group = M('groups');
        $this->fetchGroup = $Group->where('gid = ' . $f['gid'])->find();

        $this->rid = $_GET['rid'];
        $this->fetchRoom = $f;

        $Tags_meta = M('tags_meta');
        $this->tagsmeta = $Tags_meta->select();

        $Interviewee = M('interviewees');
        if($fetchRound['round'] == 2 || $fetchRound['round'] == 6) {
            $Enroll = M('', 'enroll2015_application', '');
        }
        // Simu Enroll
        if($fetchRound['round'] == 254 || $fetchRound['round'] == 255) {
            $Enroll = M('simu_enroll');
        }

        // Data Mapping
        $JobMapping['A1'] = 'A1 - 记者主持  ';
        $JobMapping['A2'] = 'A2 - 新闻主播  ';
        $JobMapping['A3'] = 'A3 - 栏目主播  ';
        $JobMapping['A4'] = 'A4 - 广播剧组  ';
        $JobMapping['A5'] = 'A5 - 导播机务  ';
        $JobMapping['B1'] = 'B1 - 新闻记者  ';
        $JobMapping['B2'] = 'B2 - 新闻摄像  ';
        $JobMapping['B3'] = 'B3 - 记者责编  ';
        $JobMapping['B4'] = 'B4 - 摄像剪辑  ';
        $JobMapping['B5'] = 'B5 - 实况摄像  ';
        $JobMapping['B6'] = 'B6 - 剪辑制作  ';
        $JobMapping['B7'] = 'B7 - 电视美工  ';
        $JobMapping['B8'] = 'B8 - 新闻播报  ';
        $JobMapping['B9'] = 'B9 - 专栏主持  ';
        $JobMapping['B10'] ='B10 - 形宣配音 ';
        $JobMapping['C1'] = 'C1 - 通联专员  ';
        $JobMapping['C2'] = 'C2 - 推广专员  ';
        $JobMapping['C3'] = 'C3 - 文字编辑  ';
        $JobMapping['C4'] = 'C4 - 新闻摄影  ';
        $JobMapping['C5'] = 'C5 - 图文美编  ';
        $JobMapping['C6'] = 'C6 - 编辑人员  ';
        $JobMapping['C7'] = 'C7 - 开发工程师 ';
        $JobMapping['D1'] = 'D1 - 专题策划部 ';
        $JobMapping['D2'] = 'D2 - 综合管理部 ';
        $JobMapping['D3'] = 'D3 - 外宣推广部 ';
        $JobMapping['D4'] = 'D4 新媒体工作团队';

        // Join Table
        for($i = 1; $i <= $this->fetchGroup['member_count']; $i++){
            $sn = $this->fetchGroup['member' . $i . '_sn'];
            $inter[$i] = $Interviewee->where('serialNumber = "' . $sn . '"')->find();
            $inter[$i]['li'] = $i;
            $enroll[$i] = $Enroll->where('studentid = ' . $inter[$i]['studentid'])->order('version')->select();
            $enrollLast[$i] = $Enroll->where('studentid = ' . $inter[$i]['studentid'] . ' and version=0')->find();
            // Format data
            if($enroll[$i]['shortphone'] == '') $enroll[$i]['shortphone'] = '------';
            if($enrollLast[$i]['shortphone'] == '') $enrollLast[$i]['shortphone'] = '------';
            for($step = 1; $step <=5; $step++){
                if($enroll[$i]['job' . $step] == ''){
                    $enroll[$i]['job' . $step] = '-- - 未填志愿  ';
                }else{
                    $enroll[$i]['job' . $step] = $JobMapping[$enroll[$i]['job' . $step]];
                }
                if($enrollLast[$i]['job' . $step] == ''){
                    $enrollLast[$i]['job' . $step] = '-- - 未填志愿  ';
                }else{
                    $enrollLast[$i]['job' . $step] = $JobMapping[$enrollLast[$i]['job' . $step]];
                }
            }
        }
        $this->fetchInterviewee = $inter;
        $this->joinEnroll = $enroll;
        $this->jEL = $enrollLast;

        $this->display();
    }

    public function allinfo_get(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
        }else{
            $session_auth_check = session('login_auth');
            if((($session_auth_check & 4) != 4) && (($session_auth_check & 8) != 8)){
                $this->error('无效的权限');
            }
        }
        if (IS_GET){
            $Interviewee = M('interviewees');
            $Round = M('status_round');
            $fetchRound = $Round->find();
            if($fetchRound['round'] == 2 || $fetchRound['round'] == 6) {
                $Enroll = M('', 'enroll2015_application', '');
            }
            // Simu Enroll
            if($fetchRound['round'] == 254 || $fetchRound['round'] == 255) {
                $Enroll = M('simu_enroll');
            }

            $fetchInterviewee = $Interviewee->where('serialNumber = "' . $_GET['sn'] . '"')->find();
            $fetchEnroll = $Enroll->where('studentid = ' . $fetchInterviewee['studentid'])->order('version')->select();

            echo '<head><meta charset="utf-8"></head>';
            echo '从上至下依次为 最新版本到最老版本<br />';
            dump($fetchEnroll);
        }else{
            $this->error('无效的请求');
        }
    }

    public function admission_set_tags_post(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
        }else{
            $session_auth_check = session('login_auth');
            if((($session_auth_check & 4) != 4) && (($session_auth_check & 8) != 8)){
                $this->error('无效的权限');
            }
        }
        if (IS_POST){
            $Tag = M('tags');
            $map['studentid'] = $_POST['stuId'];
            $map['serialnumber'] = $_POST['sn'];
            $map['serialdigit'] = $_POST['sd'];
            $map['uid'] = $_SESSION['login_uid'];
            $Rounds = M('status_round');
            $fetchRound = $Rounds->find();
            $map['round'] = $fetchRound['round'];
            $User = M('users');
            $fetchUser = $User->where('uid = ' . $_SESSION['login_uid'])->find();
            $map['sector'] = $fetchUser['sector'];
            $map['tags'] = $_POST['tagid'];
            $tags = $Tag->where($map)->find();

            if(!$tags){
                if($Tag->add($map)) {
                    $this->redirect('Home/Index/interview', array('rid'=>$_POST['rid']));
                }else{
                    $this->error('添加标签失败');
                }
            }else{
                if($Tag->delete($tags['tagid'])){
                    $this->redirect('Home/Index/interview', array('rid'=>$_POST['rid']));
                }else{
                    $this->error('删除标签失败');
                }
            }
        }else{
            $this->error('无效的请求');
        }
    }

    public function admission_set_marks_post(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
        }else{
            $session_auth_check = session('login_auth');
            if((($session_auth_check & 4) != 4) && (($session_auth_check & 8) != 8)){
                $this->error('无效的权限');
            }
        }
        if (IS_POST){
            $Rate = M('rates');
            $map['studentid'] = $_POST['stuId'];
            $map['serialnumber'] = $_POST['sn'];
            $map['serialdigit'] = $_POST['sd'];
            $map['uid'] = $_SESSION['login_uid'];
            $Rounds = M('status_round');
            $fetchRound = $Rounds->find();
            $map['round'] = $fetchRound['round'];
            $User = M('users');
            $fetchUser = $User->where('uid = ' . $_SESSION['login_uid'])->find();
            $map['sector'] = $fetchUser['sector'];
            $rates = $Rate->where($map)->find();

            $r = $_POST['val'];
            if($r < 0) $r = 0;
            if($r > 5) $r = 5;
            $map['rating'] = $r;

            if(!$rates){
                if($Rate->add($map)){
                    $this->redirect('Home/Index/interview', array('rid'=>$_POST['rid']));
                }else{
                    $this->error('添加评分失败');
                }
            }else{
                $map['rateid'] = $rates['rateid'];
                if($Rate->save($map)){
                    $this->redirect('/Home/Index/interview', array('rid'=>$_POST['rid']));
                }else{
                    $this->error('更新评分失败');
                }
            }
        }else{
            $this->error('无效的请求');
        }
    }

    public function admission_set_comments_post(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
        }else{
            $session_auth_check = session('login_auth');
            if((($session_auth_check & 4) != 4) && (($session_auth_check & 8) != 8)){
                $this->error('无效的权限');
            }
        }
        if (IS_POST){
            $Comment = M('comments');
            $add['studentid'] = $_POST['stuId'];
            $add['serialnumber'] = $_POST['sn'];
            $add['serialdigit'] = $_POST['sd'];
            $add['uid'] = $_SESSION['login_uid'];
            $Rounds = M('status_round');
            $fetchRound = $Rounds->find();
            $add['round'] = $fetchRound['round'];
            $User = M('users');
            $fetchUser = $User->where('uid = ' . $_SESSION['login_uid'])->find();
            $add['sector'] = $fetchUser['sector'];
            $add['comment'] = $_POST['comment'];

            if($Comment->add($add)) {
                $this->redirect('Home/Index/interview', array('rid'=>$_POST['rid']));
            }else{
                $this->error('添加评论失败');
            }
        }else{
            $this->error('无效的请求');
        }
    }

    public function admission_remove_comments_post(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
        }else{
            $session_auth_check = session('login_auth');
            if((($session_auth_check & 4) != 4) && (($session_auth_check & 8) != 8)){
                $this->error('无效的权限');
            }
        }
        if (IS_POST){
            $Comment = M('comments');
            if($Comment->delete($_POST['commId'])) {
                $this->redirect('Home/Index/interview', array('rid'=>$_POST['rid']));
            }else{
                $this->error('删除评论失败');
            }
        }else{
            $this->error('无效的请求');
        }
    }

    public function admission_terminate_interview(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
        }else{
            $session_auth_check = session('login_auth');
            if(($session_auth_check & 8) != 8){
                $this->error('无效的权限');
            }
        }
        if (IS_POST){
            $Room = M('rooms');
            $fetchRoom = $Room->where('rid = ' . $_POST['rid'])->find();
            if(!$fetchRoom) $this->error('无效的请求');
            $InterviewSector = $fetchRoom['sector'];

            $Group = M('groups');
            $fetchGroup = $Group->where('gid = ' . $fetchRoom['gid'])->find();
            if(!$fetchGroup) $this->error('无效的请求');

            $Rounds = M('status_round');
            $fetchRound = $Rounds->find();

            $checkResult_count = 0;
            for($i = 1; $i <= $fetchGroup['member_count']; $i++){
                $sn = $fetchGroup['member' . $i . '_sn'];
                //dump($sn);
                $Rate = M('rates');
                $map['serialnumber'] = $sn;
                $map['uid'] = $_SESSION['login_uid'];
                $map['round'] = $fetchRound['round'];
                $fetchRate = $Rate->where($map)->find();
                if(!$fetchRate) $this->error('请完成所有评分');
            }

            // Update Waiting List
            $T_WL = M('television_waiting_list');
            $R_WL = M('radio_waiting_list');
            $N_WL = M('newspaper_waiting_list');
            $Interviewee = M('interviewees');
            for($i = 1; $i <= $fetchGroup['member_count']; $i++){
                $sn = $fetchGroup['member' . $i . '_sn'];
                $map['serialnumber'] = $sn;
                // Unlock Waiting list
                $T_WL->where($map)->setField('status', 1);
                $R_WL->where($map)->setField('status', 1);
                $N_WL->where($map)->setField('status', 1);
                switch($InterviewSector){
                    case 1: //television
                        if(!$T_WL->delete($sn)) $this->error('删除等待列表出错');
                        break;
                    case 2: //radio
                        if(!$R_WL->delete($sn)) $this->error('删除等待列表出错');
                        break;
                    case 3: //newspaper
                        if(!$N_WL->delete($sn)) $this->error('删除等待列表出错');
                        break;
                    default:
                        $this->error('无效的部门信息');
                }

                // Update User Status
                if($fetchRound['round'] == 2) $Interviewee->where($map)->setField('status', 4);
                if($fetchRound['round'] == 6) $Interviewee->where($map)->setField('status', 16);
                // Simu Enroll
                if($fetchRound['round'] == 254) $Interviewee->where($map)->setField('status', 4);
                if($fetchRound['round'] == 255) $Interviewee->where($map)->setField('status', 16);
            }

            // Update Room
            $Room->where('rid = ' . $_POST['rid'])->setField('roomstatus', 1);
            $Room->where('rid = ' . $_POST['rid'])->setField('gid', 0);

            $this->redirect('Home/Index/interview', array('rid'=>$_POST['rid']));
        }else{
            $this->error('无效的请求');
        }
    }

    // PAGE
    public function grades(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
            echo 'err';
        }else{
            $session_auth_check = session('login_auth');
            if((($session_auth_check & 4) != 4) && (($session_auth_check & 8) != 8)){
                $this->error('无效的权限');
            }
        }

        $this->profile_url = U('Home/Index/profile');
        $this->logout_url = U('Home/Index/logout_post');
        $this->admission_url = U('Home/Index/admission_post');
        //$this->updateSector_url = U('Home/Index/')

        $User = M('users');
        $fetchUser = $User->where('uid = ' . $_SESSION['login_uid'])->find();

        $Rate = M('rates');
        $Round = M('status_round');
        $fetchRound = $Round->find();
        $fetchRate = $Rate->join('users ON users.uid = rates.uid and users.authority >=8')->where('round = ' . $fetchRound['round'] . ' and rates.sector = ' . $fetchUser['sector'])->order('rating desc')->select();

        //$fetchRate = $Rate->where('round = ' . $fetchRound['round'] . ' and sector = ' . $fetchUser['sector'])->order('rating desc')->select();

        if($fetchRound['round'] == 2 || $fetchRound['round'] == 6) {
            $Enroll = M('', 'enroll2015_application', '');
        }
        // Simu Enroll
        if($fetchRound['round'] == 254 || $fetchRound['round'] == 255) {
            $Enroll = M('simu_enroll');
        }

        $Interviewee = M('interviewees');
        $Tag = M('tags');
        $Comment = M('comments');

        foreach($fetchRate as $r){
            $mapRateToEnroll[$r['serialnumber']] = $Enroll->where('studentid = ' . $r['studentid'])->find();
            $mapRateToInterviewee[$r['serialnumber']] = $Interviewee->where('studentid = ' . $r['studentid'])->find();
            $mapRateToTags[$r['serialnumber']] = $Tag->where('studentid = ' . $r['studentid'])->select();
            $mapRateToComments[$r['serialnumber']] = $Comment->where('studentid = ' . $r['studentid'])->select();
        }

        $this->fetchRate = $fetchRate;
        $this->mapToEnroll = $mapRateToEnroll;
        $this->mapToInterv = $mapRateToInterviewee;
        $this->mapToTag = $mapRateToTags;
        $this->mapToCommen = $mapRateToComments;
        $this->fetchRound = $fetchRound;

        $this->display();
    }

    public function admission_post(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
            echo 'err';
        }else{
            $session_auth_check = session('login_auth');
            if(($session_auth_check & 8) != 8){
                $this->error('无效的权限');
            }
        }

        if(IS_POST){
            $Round = M('status_round');
            $fetchRound = $Round->find();
            $curRound = $fetchRound['round'];
            // if($fetchRound['round'] == 2 || $fetchRound['round'] == 6) {
            //     $Enroll = M('', 'enroll2015_application', '');
            // }
            // if($fetchRound['round'] == 254 || $fetchRound['round'] == 255) {
            //     $Enroll = M('simu_enroll');
            // }
            $Interviewee = M('interviewees');
            $fetchInterviewee = $Interviewee->where('serialnumber = "' . $_POST['sn'] . '"')->find();
            if(!$fetchInterviewee) $this->error('无效的请求');
            $stuId = $fetchInterviewee['studentid'];
            if($curRound != 2 && $curRound != 6 && $curRound!= 254 && $curRound != 255){
                $this->error('当前时段不允许更改成绩');
            }
            if($fetchInterviewee['status'] != 4 && $fetchInterviewee['status'] != 16){
                $this->error('当前用户状态不允许更改成绩');
            }

            $User = M('users');
            $fetchUser = $User->where('uid = ' . $_SESSION['login_uid'])->find();
            $loginUserSector = $fetchUser['sector'];
            switch ($loginUserSector) {
                case '1':
                    $sectorFlag = 1;
                    $sectorNFlag = 6;
                    break;
                case '2':
                    $sectorFlag = 2;
                    $sectorNFlag = 5;
                    break;
                case '3':
                    $sectorFlag = 4;
                    $sectorNFlag = 3;
                    break;
                default:
                    $sectorFlag = 0;
                    $sectorNFlag = 7;
                    break;
            }

            $map['studentid'] = $stuId;
            $fetchInterviewee = $Interviewee->where($map)->find();
            $originSector = $fetchInterviewee['multisector'];

            switch ($_POST['request']) {
                case 'getIn':
                    $newSector = $originSector | $sectorFlag;
                    if($Interviewee->where($map)->setField('multisector', $newSector) === false) {
                        $this->error('更新本地状态失败');
                    }
                    break;
                case 'getOut':
                    $newSector = $originSector & $sectorNFlag;
                    if($Interviewee->where($map)->setField('multisector', $newSector) === false) {
                        $this->error('更新本地状态失败');
                    }
                    break;
                default:
                    $this->error('无效的请求');
            }
            $this->redirect('Home/Index/grades');
        }else{
            $this->error('无效的请求');
        }
    }
}
