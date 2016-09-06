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
        if (IS_POST) {
            $login_account['username'] = $_POST['username'];
            $login_account['password'] = $_POST['password'];
            $login_account['authority'] = $_POST['authority'];
            $User = M('users');
            $fetchUser = $User->where($login_account)->find();
            if (!$fetchUser) {
                $this->error('账号或密码错误',U('Home/Index/index'));
            }
            session('login_uid', $fetchUser['uid']);
            session('login_auth', $fetchUser['authority']);


            switch ($fetchUser['authority']) {
                case 1:
                    $this->redirect('Home/Index/checkin');
                    break;
                case 3:
                    $this->redirect('Home/Index/screen');
                    break;
                case 4:
                case 8:
                    $this->redirect('Home/Index/interview');
                    break;
                case 16:
                    $this->redirect('Home/Index/administration');
                    break;
                default:
                    session('login_uid', null);
                    session('login_auth', null);
                    $this->error('权限未知',U('Home/Index/index'));
                    break;
            }

        }
        
    }

    public function logout_post(){
        session('login_uid', null);
        session('login_auth', null);
        $this->redirect('Home/Index/index');
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

        $this->logout_url = U('Home/Index/logout_post');
        $this->management_url = U('Home/Index/admin_management_post');
        $this->dangerctl_url = U('Home/Index/admin_dangerous_post');

        $this->assign('uid', session('login_uid'));

        $User = M('users');
        $this->fetchUser = $User->select();
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
                default:
                    $this->fetchRound = "未定义代码";
                    break;
        }
        $Time = M('times');
        $this->fetchTime = $Time->select();
        $Room = M('rooms');
        $this->fetchRoom = $Room->select();

        $Interviewee = M('interviewees');
        $this->fetchInterviewee = $Interviewee->select();
        $Enroll = M('enroll2016_application');
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
                case 'ROOM':
                    $Model = M('rooms');
                    switch($_POST['request']){
                        case 'insert':
                            $map['roomnumber'] = $_POST['roomnumber'];
                            $map['roomstatus'] = 1;
                            $map['gid'] = 0;
                            $map['suid_core'] = $_POST['suid_core'];
                            $map['suid1'] = $_POST['suid1'];
                            $map['suid2'] = $_POST['suid2'];
                            $map['suid3'] = $_POST['suid3'];
                            $map['suid4'] = $_POST['suid4'];
                            $count = 0;
                            for($i = 1; $i <= 4; $i++){
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
                    $Enroll = M('enroll2016_application');
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
                    $WL = M('waiting_list');
                    if($WL->execute("TRUNCATE table `waiting_list`") === false){
                        $this->error('丢弃失败');
                    }else{
                        $this->success('已丢弃本地等候队列', U('/Home/Index/administration'));
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
                case 'confirmGrades':
                    $Round = M('status_round');
                    $fetchRound = $Round->find();

                    // Set Model Enroll
                    if($fetchRound['round'] == 2 || $fetchRound['round'] == 6) {
                        $Enroll = M('enroll2016_application');
                    }

                    if($fetchRound['round'] == 2) {
                        $newInStatus = 12;
                    }
                    elseif ($fetchRound['round'] == 6) {
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

        $this->logout_url = U('Home/Index/logout_post');
        $this->checkin_url = U('Home/Index/checkin_post');

        $Enroll = M('enroll2016_application');
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
                $Enroll = M('enroll2016_application');
            }else{
                $this->error('现在不能进行检录');
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
                if($fetchRound['round'] == 2){
                    // Genrerate SerialNumber
                    $succ = 1;
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
                    if($addInterviewee['status'] == $fetchEnroll['resultstatus']) {
                        $this->error('该人员检录逻辑状态错误，请联系管理员');
                    }
                    $addInterviewee['time1tid'] = $fetchEnroll['interviewtime'];    // Set time1
                    $addInterviewee['time2tid'] = $fetchEnroll['interviewtime2']+5; // Set time2
                    $addInterviewee['studentname'] = $fetchEnroll['studentname'];    // Set name
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
                    }
                    $sector = $addInterviewee['multisector'];                       // Set sector for adding waitinglist
                    $studentname = $addInterviewee['studentname'];                  // Set stuname for adding waitinglist

                    if(!$Interviewee->add($addInterviewee)) {
                        $this->error('首次检录失败');
                    }             
                }  

                // Second CheckIn / Transfer Data
                if($fetchRound['round'] == 6){
                    $succ = 1;
                    if($Interviewee->where('studentid = ' . $stuId)->setField('status', 16) === false){
                        $this->error('二次检录失败');
                    }else{
                        $Inter = M('interviewees');
                        $fetch = $Inter->where('studentid = ' . $stuId)->find();
                        $serialNumber = $fetch['serialnumber'];
                        $serialDigit = $fetch['serialdigit'];
                        $sector = $fetch['multisector'];
                        $studentname = $fetch['studentname'];
                    }
                }

                // Insert WaitingList & AddInto Group
                if ($succ) {
                    $WL = M('waiting_list');
                    $Group = M('groups');
                    $fetchGroup = $Group->select();

                    $inQueueTime = date('Y-m-d H:i:s');
                    $waitingList['studentid'] = $stuId;
                    $waitingList['serialnumber'] = $serialNumber;
                    $waitingList['serialdigit'] = $serialDigit;
                    $waitingList['inqueuetime'] = $inQueueTime;
                    $waitingList['status'] = 1;
                    $waitingList['sector'] = $sector;
                    $waitingList['studentname'] = $studentname;
                    if(!$WL->add($waitingList)) {
                        if ($fetchRound['round'] == 2) {
                            if(!$Interviewee->where($addInterviewee)->delete()) {
                                $this->error('回滚失败：删除面试者列表出错');
                            }
                        }
                        else {
                            if($Interviewee->where('studentid = ' . $stuId)->setField('status', 12) === false){
                                $this->error('回滚失败：删除面试者列表出错');
                            }
                        }
                        $this->error('侯考列表更新失败');                    
                    }

                    $addsucc = 0;
                    $maxinter = ($fetchRound['round'] == 2) ? 5 : 10;             //inter_number_control

                    if ($fetchGroup[0]) {
                        foreach ($fetchGroup as $f) {
                            $newcnt = $f['member_count'] + 1;
                            if ($f['status'] == 1 && $f['member_count'] < $maxinter) {
                                $update_group = array('member_count'=>$newcnt, 'member'. $newcnt .'_sn'=>$serialNumber);
                                if (!$Group->where('gid = '. $f['gid'])->setField($update_group)) {
                                    // Enroll System
                                    if ($fetchRound['round'] == 2) {
                                        if(!$Interviewee->where($addInterviewee)->delete()) {
                                            $this->error('回滚失败：删除面试者列表出错');
                                        }
                                    }
                                    elseif($fetchRound['round'] == 6) {
                                        if($Interviewee->where('studentid = ' . $stuId)->setField('status', 12) === false){
                                            $this->error('回滚失败：更新面试者列表出错');
                                        }
                                    }
                                    if (!$WL->where($waitingList)->delete()) {
                                        $this->error('回滚失败：删除等待列表出错');
                                    }
                                    $this->error('a类分组失败');
                                }
                                else {
                                    $addsucc = 1;
                                    $is_group = 1;
                                    break;
                                }
                            }
                        }
                    }

                    if (!$addsucc) {
                        $addGroup['round'] = $fetchRound['round'];
                        $addGroup['member_count'] = 1;
                        $addGroup['member1_sn'] = $serialNumber;
                        $addGroup['status'] = 1;
                        if (!$Group->add($addGroup)) {
                            // Enroll System
                            if ($fetchRound['round'] == 2) {
                                if(!$Interviewee->where($addInterviewee)->delete()) {
                                    $this->error('回滚失败：删除面试者列表出错');
                                }
                            }
                            elseif($fetchRound['round'] == 6) {
                                if($Interviewee->where('studentid = ' . $stuId)->setField('status', 12) === false){
                                    $this->error('回滚失败：更新面试者列表出错');
                                }
                            }
                            if (!$WL->where($waitingList)->delete()) {
                                $this->error('回滚失败：删除等待列表出错');
                            }
                            $this->error('b类分组失败');                                
                        }
                        else {
                            $is_group = 2;
                        }
                    }
                }

                // Update remote database
                if($fetchRound['round'] == 2){
                    $map['resultstatus'] = 1;
                    $newStatus = 4;
                }
                if($fetchRound['round'] == 6){
                    $map['resultstatus'] = 12;
                    $newStatus = 16;
                    $Inter = M('interviewees');
                    $fetch = $Inter->where('studentid = ' . $stuId)->find();
                    $serialDigit = $fetch['serialdigit'];
                }

                // REMOTE SHUTDOWN
                if($Enroll->where($map)->setField('resultstatus', $newStatus) === false) {
                    // Enroll System
                    if ($fetchRound['round'] == 2) {
                        if(!$Interviewee->where($addInterviewee)->delete()) {
                            $this->error('回滚失败：删除面试者列表出错');
                        }
                    }
                    elseif($fetchRound['round'] == 6) {
                        if($Interviewee->where('studentid = ' . $stuId)->setField('status', 12) === false){
                            $this->error('回滚失败：更新面试者列表出错');
                        }
                    }
                    if (!$WL->where($waitingList)->delete()) {
                        $this->error('回滚失败：删除等待列表出错');
                    }

                    if ($is_group == 1) {
                        $rollback_group = array('member_count'=>$newcnt - 1, 'member'. $newcnt .'_sn'=>0);
                        if (!$Group->where('gid = '. $f['gid'])->setField($rollback_group)) {
                            $this->error('回滚失败：更新分组列表出错');
                        }
                    }
                    else {
                        if (!$Group->where($addGroup)->delete()) {
                            $this->error('回滚失败：删除分组列表出错');
                        }
                    }
                    $this->error('远程状态更新失败');
                }else{
                    $this->success('检录成功，' . $fetchEnroll['studentname'] . '的流水号为 ' . $serialDigit, U('Home/Index/checkin'), 5);
                }
            }
        }else{
            $this->error('无效的请求');
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

        $this->logout_url = U('Home/Index/logout_post');

        $Room = M('rooms');
        $this->fetchRoom = $Room->select();

        $Group = M('groups');
        $this->fetchGroup = $Group->select();

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

        if(!IS_GET) { $this->error('is_get_interview错误'); }

        $this->session_auth_check = $session_auth_check;

        $this->logout_url = U('Home/Index/logout_post');
        $this->ratectl_url = U('Home/Index/grades');

        $this->setTags_url = U('Home/Index/admission_set_tags_post');
        $this->setMark_url = U('Home/Index/admission_set_marks_post');
        $this->setComm_url = U('Home/Index/admission_set_comments_post');
        $this->rmvComm_url = U('Home/Index/admission_remove_comments_post');
        $this->startInterview_url = U('Home/Index/admission_start_interview');
        $this->terminateInterview_url = U('Home/Index/admission_terminate_interview');

        $Room = M('rooms');
        $fetchRoom = $Room->select();
        $rid = 0;
        $u = $_SESSION['login_uid'];
        foreach ($fetchRoom as $f) {
            if($f['suid_core']==$u || $f['suid1']==$u || $f['suid2']==$u || $f['suid3']==$u || $f['suid4']==$u){
                $rid = $f['rid'];
                break;
            }
        }
        if (!$rid) {
            $this->error('您非本场面试官');
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
                default:
                    $this->fetchRound = "未定义状态号";
                    break;
        }
        $Group = M('groups');
        $this->fetchGroup = $Group->where('gid = ' . $f['gid'])->find();

        $this->rid = 1;                                                 //only one room available
        $this->fetchRoom = $f;

        $Interviewee = M('interviewees');
        if($fetchRound['round'] == 2 || $fetchRound['round'] == 6) {
            $Enroll = M('enroll2016_application');
        }
        // Simu Enroll
        if($fetchRound['round'] == 254 || $fetchRound['round'] == 255) {
            $Enroll = M('simu_enroll');
        }

        // Data Mapping
        $JobMapping['A1'] = 'A1 - 广播记者编辑  ';
        $JobMapping['A2'] = 'A2 - 广播新闻主播  ';
        $JobMapping['A3'] = 'A3 - 广播栏目主播  ';
        $JobMapping['A4'] = 'A4 - 广播英语主播  ';
        $JobMapping['B1'] = 'B1 - 电视新闻记者  ';
        $JobMapping['B2'] = 'B2 - 电视英语记者  ';
        $JobMapping['B3'] = 'B3 - 电视摄像剪辑  ';
        $JobMapping['B4'] = 'B4 - 新闻摄影记者  ';
        $JobMapping['B5'] = 'B5 - 电视栏目主持  ';
        $JobMapping['B6'] = 'B6 - 图像美工制作  ';
        $JobMapping['C1'] = 'C1 - 通联员推广员  ';
        $JobMapping['C2'] = 'C2 - 文字简讯编辑  ';
        $JobMapping['C3'] = 'C3 - 图文美术编辑  ';
        $JobMapping['C4'] = 'C4 - 程序员技术员  ';

        // Join Table
        for($i = 1; $i <= $this->fetchGroup['member_count']; $i++){
            $sn = $this->fetchGroup['member' . $i . '_sn'];
            $inter[$i] = $Interviewee->where('serialnumber = "' . $sn . '"')->find();
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
                $Enroll = M('enroll2016_application');
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
            $Rounds = M('status_round');
            $fetchRound = $Rounds->find();
            $map['round'] = $fetchRound['round'];
            $rates = $Rate->where($map)->find();
            $Room = M('rooms');
            $fetchRoom = $Room->where('rid = '. $_POST['rid'])->find();
            $Interviewee = M('interviewees');
            $fetchInterviewee = $Interviewee->where('studentid='. $_POST['stuId'])->find();

            //*violence
            $r_status_sector = $fetchInterviewee['multisector'] & 2;
            $t_status_sector = $fetchInterviewee['multisector'] & 1;

            if ($r_status_sector) {
                $map['r_status'] |= 1;
            }
            if ($t_status_sector) {
                $map['t_status'] |= 1;
            }

            if ($fetchRoom['suid_core'] == $_SESSION['login_uid']) {
                $map['t_rate_0'] = $_POST['t_rates'] ? $_POST['t_rates'] : -1;
                $map['r_rate_0'] = $_POST['r_rates'] ? $_POST['r_rates'] : -1;
            }
            for ($i=1; $i < 5; $i++) {
                if ($fetchRoom['suid'. $i] == $_SESSION['login_uid']) {
                    $map['t_rate_'. $i] = $_POST['t_rates'] ? $_POST['t_rates'] : -1;
                    $map['r_rate_'. $i] = $_POST['r_rates'] ? $_POST['r_rates'] : -1;
                }
            }

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

    public function admission_start_interview(){
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
            if(!$fetchRoom) $this->error('该场地暂无面试');
            $Round = M('status_round');
            $fetchRound = $Round->find();
            if($fetchRound['round']!=2 && $fetchRound['round']!=6)
                $this->error('该时间段不可进行面试');

            $succ = 0;
            $Group = M('groups');
            $fetchGroup = $Group->select();
            if ($fetchGroup[0]) {
                foreach ($fetchGroup as $f) {
                    if ($f['status'] == 1) {
                        if (!$Group->where('gid='. $f['gid'])->setField('status',2)) {
                            $this->error('修改分组状态出错');
                        }
                        else{
                            $succ = 1;                            
                            break;
                        } 
                    }
                }
            }

            if (!$succ) {
                $this->error('无可分配面试小组');
            }

            // Update local & remote database
            $Enroll = M('enroll2016_application');
            $Interviewee = M('interviewees');

            $newStatus = ($fetchRound['round'] == 2) ? 8 : 32;

            $member_count = $f['member_count'];
            for ($i=1; $i <= $member_count; $i++) { 
                $fetchsn = $f['member'. $i .'_sn'];
                $fetchInterviewee = $Interviewee->where('serialnumber="'.$fetchsn.'"')->find();
                if (!$Interviewee->where('serialnumber = "'. $fetchsn .'"')->setField('status', $newStatus)) {
                    // Enroll System
                    if (!$Group->where('gid='. $f['gid'])->setField('status',1)) {
                            $this->error('回滚失败：修改分组状态出错');
                    }
                    $this->error('连续更新本地状态失败');
                }
                if (!$Enroll->where('studentid = "'. $fetchInterviewee['studentid'] .'"')->setField('resultstatus', $newStatus)) {
                    // Enroll System
                    if (!$Group->where('gid='. $f['gid'])->setField('status',1)) {
                            $this->error('回滚失败：修改分组状态出错');
                    }
                    $this->error('连续更新远端状态失败');
                }
            }

            // Update Room
            $Room->where('rid = ' . $_POST['rid'])->setField('roomstatus', 2);
            $Room->where('rid = ' . $_POST['rid'])->setField('gid',$f['gid']);

            $this->redirect('Home/Index/interview', array('rid'=>$_POST['rid']));
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
            if(!$fetchRoom) $this->error('无效请求：该场地尚无面试');

            $Group = M('groups');
            $fetchGroup = $Group->where('gid = ' . $fetchRoom['gid'])->find();
            if(!$fetchGroup) $this->error('无效的请求：该场地未分配小组');
            $gid = $fetchGroup['gid'];

            $Rounds = M('status_round');
            $fetchRound = $Rounds->find();

            $checkResult_count = 0;
            for($i = 1; $i <= $fetchGroup['member_count']; $i++){
                $sn = $fetchGroup['member' . $i . '_sn'];
                $Rate = M('rates');
                $map['serialnumber'] = $sn;
                $map['round'] = $fetchRound['round'];
                $fetchRate = $Rate->where($map)->find();
                if(!$fetchRate) $this->error('请完成所有评分');
            }

            // Update Waiting List
            $WL = M('waiting_list');
            $Interviewee = M('interviewees');
            $Enroll = M('enroll2016_application');
            for($i = 1; $i <= $fetchGroup['member_count']; $i++){
                $sn = $fetchGroup['member' . $i . '_sn'];
                $condition['serialnumber'] = $sn;
                if(!$WL->delete($sn)) $this->error('删除等待列表出错');                
            }

            // Update Group
            $Group->where('gid = ' . $gid)->setField('status', 3);

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

        $this->logout_url = U('Home/Index/logout_post');
        $this->admission_url = U('Home/Index/admission_post');

        $User = M('users');
        $fetchUser = $User->where('uid = ' . $_SESSION['login_uid'])->find();

        $Rate = M('rates');
        $Round = M('status_round');
        $fetchRound = $Round->find();
        if ($fetchRound['round'] == 2) {
            $interstatus = 8; 
        }
        if ($fetchRound['round'] == 6) {
            $interstatus = 32;
        }


        $fetchT_rate = $Rate->where('round = ' . $fetchRound['round'])->where('t_status = 1')->order('t_rate_0 desc')->select();
        $fetchR_rate = $Rate->where('round = ' . $fetchRound['round'])->where('r_status = 1')->order('r_rate_0 desc')->select();

        if($fetchRound['round'] == 2 || $fetchRound['round'] == 6) {
            $Enroll = M('enroll2016_application');
        }

        $Comment = M('comments');

        foreach($fetchT_rate as $r){
            $T_mapRateToEnroll[$r['serialnumber']] = $Enroll->where('studentid = ' . $r['studentid'])->find();
            $T_mapRateToComments[$r['serialnumber']] = $Comment->where('studentid = ' . $r['studentid'])->select();
        }

        foreach($fetchR_rate as $r){
            $R_mapRateToEnroll[$r['serialnumber']] = $Enroll->where('studentid = ' . $r['studentid'])->find();
            $R_mapRateToComments[$r['serialnumber']] = $Comment->where('studentid = ' . $r['studentid'])->select();
        }

        $this->fetchT_rate = $fetchT_rate;
        $this->fetchR_rate = $fetchR_rate;
        $this->T_mapToEnroll = $T_mapRateToEnroll;
        $this->T_mapToCommen = $T_mapRateToComments;
        $this->R_mapToEnroll = $R_mapRateToEnroll;
        $this->R_mapToCommen = $R_mapRateToComments;
        $this->fetchRound = $fetchRound;
        $this->session_auth_check = $session_auth_check;

        $this->display();
    }

    public function admission_post(){
        if (!isset($_SESSION['login_uid']) || !isset($_SESSION['login_auth'])) {
            $this->redirect('Home/Index/index');
            echo 'error';
        }else{
            $session_auth_check = session('login_auth');
            if(($session_auth_check & 8) != 8){
                $this->error('无效的权限');
            }
        }

        if(IS_POST){
            $Rate = M('rates');
            $Enroll = M('enroll2016_application');
            $Round = M('status_round');
            $fetchRound = $Round->find();
            $curRound = $fetchRound['round'];
            $Interviewee = M('interviewees');
            $fetchInterviewee = $Interviewee->where('serialnumber = "' . $_POST['sn'] . '"')->find();
            if(!$fetchInterviewee) $this->error('无效的请求');
            $stuId = $fetchInterviewee['studentid'];
            if($curRound != 2 && $curRound != 6){
                $this->error('当前时段不允许更改成绩');
            }

            switch ($_POST['sector']) {
                case 1:
                    $sectorFlag = 1;
                    $sectorNFlag = 6;
                    break;
                case 2:
                    $sectorFlag = 2;
                    $sectorNFlag = 5;
                    break;
                default:
                    $sectorFlag = 0;
                    $sectorNFlag = 7;
                    break;
            }

            $map['studentid'] = $stuId;
            $fetchInterviewee = $Interviewee->where($map)->find();
            $originSector = $fetchInterviewee['multisector'];

            // Update remote & local database
            if($fetchRound['round'] == 2){
                $newInStatus = 12;
            }
            if($fetchRound['round'] == 6){
                $newInStatus = 128;
            }
            $newOutStatus = 64;

            switch ($_POST['request']) {
                case 'getIn':
                    $newSector = $originSector | $sectorFlag;
                    $newInterv = array('multisector'=>$newSector,'status'=>$newInStatus);
                    if($Interviewee->where($map)->setField($newInterv) === false) {
                        $this->error('更新本地状态失败');
                    }
                    if($Enroll->where($map)->setField('resultstatus', $newInStatus) === false){
                        $this->error('更新远程状态失败');
                    }
                    break;
                case 'getOut':
                    $newSector = $originSector & $sectorNFlag;
                    if($Interviewee->where($map)->setField('multisector', $newSector) === false) {
                        $this->error('更新本地状态失败');
                    }
                    $interv = $Interviewee->where($map)->find();
                    if ($interv['multisector'] == 0) {
                        if($Enroll->where($map)->setField('resultstatus', $newOutStatus) === false){
                            $this->error('更新远程状态失败');
                        }
                        if($Interviewee->where($map)->setField('status', $newOutStatus) === false){
                            $this->error('更新本地状态失败');
                        }
                    }
                    break;
                default:
                    $this->error('无效的请求');
            }
            //*violence
            if ($_POST['sector'] == 1) {
                $Rate->where($map)->setField('t_status', 3);
            }
            else
                $Rate->where($map)->setField('r_status', 3); 
                                 
            $this->redirect('Home/Index/grades');
        }else{
            $this->error('无效的请求');
        }
    }
}

