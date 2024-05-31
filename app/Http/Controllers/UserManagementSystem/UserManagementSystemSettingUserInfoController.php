<?php

namespace App\Http\Controllers\UserManagementSystem;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Leave;
use App\Models\Month;
use App\Models\Payday;
use App\Models\Prefix;
use App\Models\Approver;
use App\Models\Ethnicity;
use App\Models\UserLeave;
use App\Models\Nationality;
use App\Models\EmployeeType;
use App\Models\UserPosition;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use App\Models\CompanyDepartment;
use App\Http\Controllers\Controller;
use App\Models\UserDiligenceAllowance;
use App\Helpers\AddDefaultWorkScheduleAssignment;
use App\Models\LeaveIncrement;
use App\Models\LeaveType;
use App\Services\UpdatedRoleGroupCollectionService;

class UserManagementSystemSettingUserInfoController extends Controller
{
    private $updatedRoleGroupCollectionService;
    private $addDefaultWorkScheduleAssignment;
    private $activityLogger;
    private $relationships;
    public function __construct(UpdatedRoleGroupCollectionService $updatedRoleGroupCollectionService, AddDefaultWorkScheduleAssignment $addDefaultWorkScheduleAssignment,ActivityLogger $activityLogger)
    {
        $this->updatedRoleGroupCollectionService = $updatedRoleGroupCollectionService;
        $this->addDefaultWorkScheduleAssignment = $addDefaultWorkScheduleAssignment;
        $this->activityLogger = $activityLogger;
        $this->relationships = [
            (object)[
                "id" => "1",
                "name" => "โสด"
            ],
            (object)[
                "id" => "2",
                "name" => "แต่งงาน"
            ],
            (object)[
                "id" => "3",
                "name" => "หย่าร้าง"
            ],
            (object)[
                "id" => "4",
                "name" => "ไม่ระบุ"
            ],
        ];
    }
    public function index()
    {
        // กำหนดค่าตัวแปร $action ให้เป็น 'show'
        $action = 'show';
        // ดึงค่า 'groupUrl' จาก session และแปลงเป็นข้อความ
        $groupUrl = strval(session('groupUrl'));

        // เรียกใช้งานเซอร์วิส updatedRoleGroupCollectionService เพื่อดึงข้อมูล updatedRoleGroupCollection, permission, viewName โดยใช้ค่า $action
        $roleGroupCollection = $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);
        $updatedRoleGroupCollection = $roleGroupCollection['updatedRoleGroupCollection'];
        $permission = $roleGroupCollection['permission'];
        $viewName = $roleGroupCollection['viewName'];
        $users = User::paginate(50);



        return view($viewName, [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'users' => $users,
        ]);
    }
    public function view($id)
    {

        // กำหนดค่าตัวแปร $action ให้เป็น 'create'
        $action = 'create';
        // ดึงค่า 'groupUrl' จาก session และแปลงเป็นข้อความ
        $groupUrl = strval(session('groupUrl'));

        // เรียกใช้งานเซอร์วิส updatedRoleGroupCollectionService เพื่อดึงข้อมูล updatedRoleGroupCollection, permission, viewName โดยใช้ค่า $action
        $roleGroupCollection = $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);
        $updatedRoleGroupCollection = $roleGroupCollection['updatedRoleGroupCollection'];
        $permission = $roleGroupCollection['permission'];

        $prefixes = Prefix::all();  // เรียกข้อมูลคำนำหน้าชื่อทั้งหมดจากตาราง prefixes
        $nationalities = Nationality::all();  // เรียกข้อมูลสัญชาติทั้งหมดจากตาราง nationalities
        $ethnicities = Ethnicity::all();  // เรียกข้อมูลเชื้อชาติทั้งหมดจากตาราง ethnicities
        $employeeTypes = EmployeeType::all();  // เรียกข้อมูลประเภทพนักงานทั้งหมดจากตาราง employee_types
        $userPositions = UserPosition::all();  // เรียกข้อมูลตำแหน่งงานทั้งหมดจากตาราง user_positions
        $companyDepartments = CompanyDepartment::all();
        $paydays = Payday::all();
        $currentYear = Carbon::now()->year;
        $workSchedules = WorkSchedule::where('year', $currentYear)->get();
        $users = User::all();
        $user = User::find($id);
        $approvers = Approver::all();
        $months = Month::all();
        $userDiligenceAllowances = UserDiligenceAllowance::where('user_id', $id)->orderBy('id', 'desc')->get();
        dd($userDiligenceAllowances);
        $leaves = Leave::where('user_id',$id)->whereYear('from_date',$currentYear)->get();
        $userLeaves = UserLeave::where('user_id',$id)->get();
        $leaveTypes = LeaveType::all();

        $leaveIncrements = LeaveIncrement::where('user_id',$id)->get();

        return view('groups.user-management-system.setting.userinfo.view', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'user' => $user,
            'prefixes' => $prefixes,
            'nationalities' => $nationalities,
            'ethnicities' => $ethnicities,
            'employeeTypes' => $employeeTypes,
            'userPositions' => $userPositions,
            'companyDepartments' => $companyDepartments,
            'paydays' => $paydays,
            'workSchedules' => $workSchedules,
            'users' => $users,
            'approvers' => $approvers,
            'months' => $months,
            'userDiligenceAllowances' => $userDiligenceAllowances,
            'leaves' => $leaves,
            'userLeaves' => $userLeaves,
            'leaveTypes' => $leaveTypes,
            'leaveIncrements' => $leaveIncrements,
            'relationships' => $this->relationships
        ]);
    }

    public function search(Request $request)
    {
        $searchInput = $request->data['searchInput'];
        $users = User::where(function ($query) use ($searchInput) {
            $query->where('employee_no', 'like', '%' . $searchInput . '%')
                ->orWhere('name', 'like', '%' . $searchInput . '%')
                ->orWhere('lastname', 'like', '%' . $searchInput . '%')
                ->orWhere('passport', 'like', '%' . $searchInput . '%')
                ->orWhere('hid', 'like', '%' . $searchInput . '%')
                ->orWhereHas('user_position', function ($query) use ($searchInput) {
                    $query->where('name', 'like', '%' . $searchInput . '%');
                })
                ->orWhereHas('ethnicity', function ($query) use ($searchInput) {
                    $query->where('name', 'like', '%' . $searchInput . '%');
                })
                ->orWhereHas('nationality', function ($query) use ($searchInput) {
                    $query->where('name', 'like', '%' . $searchInput . '%');
                })
                ->orWhereHas('company_department', function ($query) use ($searchInput) {
                    $query->where('name', 'like', '%' . $searchInput . '%');
                });
            })->paginate(50);
        return view('groups.user-management-system.setting.userinfo.table-render.users-table-render',[
            'users' => $users
            ])->render();
    }


}
