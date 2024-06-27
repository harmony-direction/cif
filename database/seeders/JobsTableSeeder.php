<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JobsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Job::create([
            'code' => 'SHIFT-MANAGEMENT',
            'name' => 'กะการทำงาน',
            'route' => 'groups.time-recording-system.shift.timeattendance',    
            'view' => 'groups.time-recording-system.shift.timeattendance.index',
        ]);
        Job::create([
            'code' => 'YEARLY-HOLIDAY',
            'name' => 'วันหยุดประจำปี',
            'route' => 'groups.time-recording-system.shift.yearlyholiday',
            'view' => 'groups.time-recording-system.shift.yearlyholiday.index',
        ]);
        Job::create([
            'code' => 'WORK-SCHEDULE',
            'name' => 'ตารางทำงาน',
            'route' => 'groups.time-recording-system.schedulework.schedule',  
            'view' => 'groups.time-recording-system.schedulework.schedule.index',
        ]);
        Job::create([
            'code' => 'TIME-RECORDING',
            'name' => 'บันทึกเวลา',
            'route' => 'groups.time-recording-system.schedulework.time-recording',  
            'view' => 'groups.time-recording-system.schedulework.time-recording.index',
        ]);
        Job::create([
            'code' => 'TIME-RECORDING-CHECK',
            'name' => 'ตรวจสอบเวลา',
            'route' => 'groups.time-recording-system.schedulework.time-recording-check',  
            'view' => 'groups.time-recording-system.schedulework.time-recording-check.index',
        ]);
        // Job::create([
        //     'code' => 'TIME-RECORDING-CURRENT-PAYDAY',
        //     'name' => 'บันทึกเวลารอบปัจจุบัน',
        //     'route' => 'groups.time-recording-system.schedulework.time-recording-current-payday',  
        //     'view' => 'groups.time-recording-system.schedulework.time-recording-current-payday.index',
        // ]);
        // Job::create([
        //     'code' => 'TIME-RECORDING-CHECK-CURRENT-PAYDAY',
        //     'name' => 'ตรวจสอบเวลารอบปัจจุบัน',
        //     'route' => 'groups.time-recording-system.schedulework.time-recording-check-current-payday',  
        //     'view' => 'groups.time-recording-system.schedulework.time-recording-check-current-payday.index',
        // ]);
        Job::create([
            'code' => 'EMPLOYEE-GROUP',
            'name' => 'กลุ่มพนักงาน',
            'route' => 'groups.time-recording-system.setting.employee-group',  
            'view' => 'groups.time-recording-system.setting.employee-group.index',
        ]);
        Job::create([
            'code' => 'WORK-SCHEDULR-VISIBILITY',
            'name' => 'การมองเห็น',
            'route' => 'groups.time-recording-system.setting.work-schedule-visibility',  
            'view' => 'groups.time-recording-system.setting.work-schedule-visibility.index',
        ]);
        // Job::create([
        //     'code' => 'WORK-SCHEDULR-REPORT',
        //     'name' => 'รายงาน',
        //     'route' => 'groups.time-recording-system.report',  
        //     'view' => 'groups.time-recording-system.report.index',
        // ]);
        Job::create([
            'code' => 'CALCULATION-LIST',
            'name' => 'เงินเดือนงวดปกติ',
            'route' => 'groups.salary-system.salary.calculation-list',
            'view' => 'groups.salary-system.salary.calculation-list.index'
        ]);
        Job::create([
            'code' => 'CALCULATION-EXTRA-LIST',
            'name' => 'เงินเดือนงวดพิเศษ',
            'route' => 'groups.salary-system.salary.calculation-extra-list',
            'view' => 'groups.salary-system.salary.calculation-extra-list.index'
        ]);
        Job::create([
            'code' => 'CALCULATION-BONUS-LIST',
            'name' => 'เงินโบนัสประจำปี',
            'route' => 'groups.salary-system.salary.calculation-bonus-list',
            'view' => 'groups.salary-system.salary.calculation-bonus-list.index'
        ]);
        Job::create([
            'code' => 'SALARY-PAYDAY',
            'name' => 'รอบคำนวนเงินเดือน',
            'route' => 'groups.salary-system.setting.payday',
            'view' => 'groups.salary-system.setting.payday.index'
        ]);
        Job::create([
            'code' => 'SALARY-SKILL-BASED-COST',
            'name' => 'ค่าทักษะ',
            'route' => 'groups.salary-system.setting.skill-based-cost',
            'view' => 'groups.salary-system.setting.skill-based-cost.index'
        ]);
        Job::create([
            'code' => 'INCOME-DEDUCT',
            'name' => 'เงินได้ / เงินหัก',
            'route' => 'groups.salary-system.setting.income-deduct',
            'view' => 'groups.salary-system.setting.income-deduct.index'
        ]);
        Job::create([
            'code' => 'DILIGENCE-ALLOWANCE',
            'name' => 'เบี้ยขยัน',
            'route' => 'groups.salary-system.setting.diligence-allowance',
            'view' => 'groups.salary-system.setting.diligence-allowance.index'
        ]);
        Job::create([
            'code' => 'DOCUMENT-LEAVE',
            'name' => 'รายการลา',
            'route' => 'groups.document-system.leave.document',
            'view' => 'groups.document-system.leave.document.index'
        ]);
        Job::create([
            'code' => 'DOCUMENT-LEAVE-APPROVAL',
            'name' => 'อนุมัติการลา',
            'route' => 'groups.document-system.leave.approval',
            'view' => 'groups.document-system.leave.approval.index'
        ]);
        Job::create([
            'code' => 'DOCUMENT-OVERTIME',
            'name' => 'รายการล่วงเวลา',
            'route' => 'groups.document-system.overtime.document',
            'view' => 'groups.document-system.overtime.document.index'
        ]);
        Job::create([
            'code' => 'DOCUMENT-OVERTIME-APPROVAL',
            'name' => 'อนุมัติล่วงเวลา',
            'route' => 'groups.document-system.overtime.approval',
            'view' => 'groups.document-system.overtime.approval.index'
        ]);
        Job::create([
            'code' => 'DOCUMENT-APPROVE',
            'name' => 'การอนุมัติ',
            'route' => 'groups.document-system.setting.approve-document',
            'view' => 'groups.document-system.setting.approve-document.index'
        ]);
        Job::create([
            'code' => 'USER-INFO',
            'name' => 'ข้อมูลพนักงาน',
            'route' => 'groups.user-management-system.setting.userinfo',
            'view' => 'groups.user-management-system.setting.userinfo.index'
        ]);
        // Job::create([
        //     'code' => 'USER-LEAVE',
        //     'name' => 'วันลา',
        //     'route' => 'groups.user-management-system.setting.userleave',
        //     'view' => 'groups.user-management-system.setting.userleave.index'
        // ]);
        Job::create([
            'code' => 'ASSESSMENT',
            'name' => 'การประเมิน',
            'route' => 'groups.assessment-system.assessment.assessment',
            'view' => 'groups.assessment-system.assessment.assessment.index'
        ]);
        Job::create([
            'code' => 'ASSESSMENT-GROUP',
            'name' => 'กลุ่มการประเมิน',
            'route' => 'groups.assessment-system.setting.assessment-group',
            'view' => 'groups.assessment-system.setting.assessment-group.index'
        ]);
        Job::create([
            'code' => 'ASSESSMENT-CRITERIA',
            'name' => 'เกณฑ์การประเมิน',
            'route' => 'groups.assessment-system.setting.criteria',
            'view' => 'groups.assessment-system.setting.criteria.index'
        ]);
        Job::create([
            'code' => 'ASSESSMENT-SCORE',
            'name' => 'คะแนนเกณฑ์การประเมิน',
            'route' => 'groups.assessment-system.setting.score',
            'view' => 'groups.assessment-system.setting.score.index'
        ]);
        Job::create([
            'code' => 'ASSESSMENT-SCORE-MULTIPLICATION',
            'name' => 'ตัวคูณคะแนน',
            'route' => 'groups.assessment-system.setting.multiplication',
            'view' => 'groups.assessment-system.setting.multiplication.index'
        ]);
        Job::create([
            'code' => 'ANNOUNCEMENT-LIST',
            'name' => 'รายการประกาศ',
            'route' => 'groups.announcement-system.announcement.list',
            'view' => 'groups.announcement-system.announcement.list.index'
        ]);
        Job::create([
            'code' => 'JOB-APPLICATION-LIST',
            'name' => 'รายการข่าวสมัครงาน',
            'route' => 'groups.job-application-system.job-application.list',
            'view' => 'groups.job-application-system.job-application.list.index'
        ]);
        Job::create([
            'code' => 'LEARNING-LIST',
            'name' => 'รายการเรียนรู้',
            'route' => 'groups.learning-system.learning.learning-list',
            'view' => 'groups.learning-system.learning.learning-list.index'
        ]);
        Job::create([
            'code' => 'LEARNING-SETTING',
            'name' => 'รายการตั้งค่า',
            'route' => 'groups.learning-system.setting.learning-list',
            'view' => 'groups.learning-system.setting.learning-list.index'
        ]);
        Job::create([
            'code' => 'SALARY-REPORT',
            'name' => 'รายงานระบบเงินเดือน',
            'route' => 'groups.report-system.report.salary',
            'view' => 'groups.report-system.report.salary.index'
        ]);

         Job::create([
            'code' => 'EMPLOYEE-INFO',
            'name' => 'ข้อมูลส่วนตัว',
            'route' => 'groups.employee-system.employee.info',
            'view' => 'groups.employee-system.employee.info.index'
        ]);
         Job::create([
            'code' => 'EMPLOYEE-LEAVE',
            'name' => 'การลา',
            'route' => 'groups.employee-system.employee.leave',
            'view' => 'groups.employee-system.employee.leave.index'
        ]);
         Job::create([
            'code' => 'EMPLOYEE-OVERTIME',
            'name' => 'ล่วงเวลา',
            'route' => 'groups.employee-system.employee.overtime',
            'view' => 'groups.employee-system.employee.overtime.index'
        ]);
    }
}
