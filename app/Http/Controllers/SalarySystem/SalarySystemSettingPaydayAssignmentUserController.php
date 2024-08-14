<?php

namespace App\Http\Controllers\SalarySystem;

use App\Models\User;
use App\Models\Payday;
use App\Models\UserPayday;
use App\Models\SearchField;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\UserDiligenceAllowance;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AddDefaultWorkScheduleAssignment;
use App\Models\CompanyDepartment;
use App\Services\UpdatedRoleGroupCollectionService;

class SalarySystemSettingPaydayAssignmentUserController extends Controller
{
    private $updatedRoleGroupCollectionService;
    private $addDefaultWorkScheduleAssignment;
    private $activityLogger;

    public function __construct(UpdatedRoleGroupCollectionService $updatedRoleGroupCollectionService, AddDefaultWorkScheduleAssignment $addDefaultWorkScheduleAssignment,ActivityLogger $activityLogger) 
    {
        $this->updatedRoleGroupCollectionService = $updatedRoleGroupCollectionService;
        $this->addDefaultWorkScheduleAssignment = $addDefaultWorkScheduleAssignment;
        $this->activityLogger = $activityLogger;
    }
    public function index($id)
    {
        // กำหนดค่าตัวแปร $action ให้เป็น 'show'
        $action = 'show';
        // ดึงค่า 'groupUrl' จาก session และแปลงเป็นข้อความ
        $groupUrl = strval(session('groupUrl'));

        // เรียกใช้งานเซอร์วิส updatedRoleGroupCollectionService เพื่อดึงข้อมูล updatedRoleGroupCollection, permission, viewName โดยใช้ค่า $action
        $roleGroupCollection = $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);
        $updatedRoleGroupCollection = $roleGroupCollection['updatedRoleGroupCollection'];
        $permission = $roleGroupCollection['permission'];
        $payday = Payday::find($id);
        $userIds = $payday->users->pluck('id')->toArray();
        $users = User::whereIn('id',$userIds)->paginate(5000);
        $companyDepartments = CompanyDepartment::get();

        return view('groups.salary-system.setting.payday.assignment-user.index', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'payday' => $payday,
            'users' => $users,
            'companyDepartments' => $companyDepartments
        ]);
    }
    public function create($id)
    {
        // กำหนดค่าตัวแปร $action ให้เป็น 'create'
        $action = 'create';
        // ดึงค่า 'groupUrl' จาก session และแปลงเป็นข้อความ
        $groupUrl = strval(session('groupUrl'));

        // เรียกใช้งานเซอร์วิส updatedRoleGroupCollectionService เพื่อดึงข้อมูล updatedRoleGroupCollection, permission, viewName โดยใช้ค่า $action
        $roleGroupCollection = $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);
        $updatedRoleGroupCollection = $roleGroupCollection['updatedRoleGroupCollection'];
        $permission = $roleGroupCollection['permission'];
        $payday = Payday::find($id);
        $users = User::paginate(30);
        
        

        return view('groups.salary-system.setting.payday.assignment-user.create', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'payday' => $payday,
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        // กำหนดเงื่อนไขการตรวจสอบข้อมูล
        $validator = Validator::make($request->all(), [
            'paydayId' => 'required',
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ], [
            'paydayId.required' => 'กรุณากรอกข้อมูล Payday ID',
            'users.required' => 'กรุณาเลือกพนักงาน',
            'users.array' => 'ข้อมูลผู้ใช้ต้องเป็นรูปแบบของอาร์เรย์',
            'users.*.exists' => 'ผู้ใช้บางรายที่เลือกไม่มีอยู่ในระบบ',
        ]);
        // ถ้าการตรวจสอบไม่ผ่าน
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $paydayId = $request->paydayId; 
            $selectedUsers = $request->users;

            $users = User::whereIn('id', $selectedUsers)->get();
            Payday::find($paydayId)->users()->detach();
            foreach ($users as $user) {
                $user->paydays()->attach($paydayId);
            }
            // $userPaydays = UserPayday::where('payday_id',$paydayId)->get();
            // foreach ($userPaydays as $userPayday) {
            //     UserDiligenceAllowance::create([
            //         'user_payday_id' => $userPayday->id
            //     ]);
            // }
        }
        // กำหนด URL สำหรับ redirect
        $url = "groups/salary-system/setting/payday/assignment-user/{$paydayId}";
            
        // ทำการ redirect ไปยัง URL ที่กำหนด
        return redirect()->to($url);
    }

     public function delete($paydayId,$userId)
    {
        // ค้นหาข้อมูลผู้ใช้จาก userId
        $user = User::find($userId);
        // ถ้าพบข้อมูลผู้ใช้
        if ($user) {
            $user->paydays()->detach($paydayId);
        }

        // กำหนด URL สำหรับ redirect
        $url = "groups/salary-system/setting/payday/assignment-user/{$paydayId}";
            
        // ทำการ redirect ไปยัง URL ที่กำหนด
        return redirect()->to($url);

    }

    public function importEmployeeNo(Request $request)
    {
        $paydayId = $request->data['paydayId'];
        $employeeNos = $request->data['employeeNos'];
        
        $users = User::whereIn('employee_no', $employeeNos)->get();
        
        Payday::find($paydayId)->users()->detach();
        foreach ($users as $user) {
            $user->paydays()->attach($paydayId);
        }
        return;
    }

    public function importEmployeeNoFromDept(Request $request){
        $paydayId = $request->data['paydayId'];
        $companyDepartmentId = $request->data['companyDepartmentId'];
        $users = User::where('company_department_id',$companyDepartmentId)->get();
        Payday::find($paydayId)->users()->detach();
        foreach ($users as $user) {
            $user->paydays()->attach($paydayId);
        }
        return;
    }

    public function importEmployeeNoFromUserType(Request $request){
        $paydayId = $request->data['paydayId'];
        $employeeTypeId = $request->data['employeeTypeId'];
        $users = User::where('employee_type_id',$employeeTypeId)->get();
        Payday::find($paydayId)->users()->detach();
        foreach ($users as $user) {
            $user->paydays()->attach($paydayId);
        }
        return;
    }

    public function search(Request $request)
    {
        $action = 'show';
        $roleGroupCollection = $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);
        $permission = $roleGroupCollection['permission'];

        $queryInput = $request->data['searchInput'];
        $paydayId = $request->data['paydayId'];
        // ค้นหา searchFields จากตาราง SearchField ที่เกี่ยวข้องกับตาราง users และมีสถานะเป็น 1
        $searchFields = SearchField::where('table', 'users')->where('status', 1)->get();

        // สร้าง query ของตาราง users
        $query = User::query();

        foreach ($searchFields as $field) {
            $fieldName = $field['field'];
            $fieldType = $field['type'];

            if ($fieldType === 'foreign') {
                $query->orWhereHas($fieldName, function ($query) use ($fieldName, $queryInput) {
                    $query->where('name', 'like', "%{$queryInput}%");
                });
            } else {
                // ค้นหาข้อมูลในฟิลด์ของตาราง users และตรวจสอบว่ามีค่าตรงกับ queryInput หรือไม่
                $query->orWhere($fieldName, 'like', "%{$queryInput}%");
            }
        }
        $searchUserIds = $query->pluck('id')->toArray();

        $payday = Payday::find($paydayId);
        $paydayUserIds = $payday->users->pluck('id')->toArray();

        // Get common user IDs
        $commonUserIds = array_intersect($searchUserIds, $paydayUserIds);

        // If you need the details of the common users (e.g., user objects)
        $commonUsers = User::whereIn('id', $commonUserIds)->paginate(5000);

        return view('groups.salary-system.setting.payday.assignment-user.table-render.user-table', [
            'users' => $commonUsers,
            'permission' => $permission,
            'payday' => $payday
            ])->render();
    }
}
