<?php

namespace App\Http\Controllers\SalarySystem;

use Carbon\Carbon;
use App\Models\User;
use App\Models\IncomeDeduct;
use App\Models\PaydayDetail;
use Illuminate\Http\Request;
use App\Models\OverTimeDetail;
use App\Helpers\ActivityLogger;
use App\Models\IncomeDeductUser;
use App\Http\Controllers\Controller;
use App\Models\UserDiligenceAllowance;
use App\Models\DiligenceAllowanceClassify;
use App\Helpers\AddDefaultWorkScheduleAssignment;
use App\Models\WorkScheduleAssignmentUser;
use App\Services\UpdatedRoleGroupCollectionService;

class SalarySystemSalaryCalculationExtraListCalculationController extends Controller
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
        $viewName = $roleGroupCollection['viewName'];
        // dd('ok');
        $date = Carbon::now();
        $monthId = intval(Carbon::now()->month);
        $currentDate = $date->format('Y-m-d');
        $type = 1;
        $paydayDetail = PaydayDetail::find($id);
        $startDate = $paydayDetail->start_date;
        $endDate = $paydayDetail->end_date;

        $userIds = OverTimeDetail::where(function ($query) use ($startDate, $endDate) {
            $query->whereDate('from_date', '>=', $startDate)
                ->whereDate('from_date', '<=', $endDate);
        })
        ->whereHas('overtime', function ($query) use ($type) {
            $query->where('status', 1)
            ->where('type', '=', $type);
        })
        ->pluck('user_id')
        ->unique()
        ->toArray();

        $userPaydayIds = $paydayDetail->payday->users->pluck('id')->toarray();
        $userIds = array_intersect($userIds, $userPaydayIds);

        $users = User::whereIn('id', $userIds)->paginate(5000);
        $incomeDeducts = IncomeDeduct::all();

        return view('groups.salary-system.salary.calculation-extra-list.calculation.index', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'users' => $users,
            'paydayDetail' => $paydayDetail,
            'incomeDeducts' => $incomeDeducts
        ]);
    }
    public function importIncomeDeduct(Request $request)
    {
        $paydayDetailId = $request->data['paydayDetailId'];
        $incomeDeductId = $request->data['incomeDeductId'];

        foreach ($request->data['employeeNos'] as $employeeNo) {
            [$userId, $value] = explode('-', $employeeNo, 2);
            $userIds[] = $userId;
            $values[] = $value;
        }

        $users = User::whereIn('employee_no',$userIds)->get();
        foreach($users as $index => $user)
        {
            $paydayDetail = PaydayDetail::find($paydayDetailId);
            if ($paydayDetail != null)
            {
                IncomeDeductUser::create([
                    'user_id' => $user->id,
                    'payday_detail_id' => $paydayDetail->id,
                    'income_deduct_id' => $incomeDeductId,
                    'value' => $values[$index],
                ]);
            }
        }
    }
    public function getUsersByWorkScheduleAssignment($startDate,$endDate)
    {
        // Convert the start and end date to the correct format
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = date('Y-m-d', strtotime($endDate));

        // ค้นหาผู้ใช้ที่มีการกำหนดงานเรียกงานใน workScheduleId และ date_in อยู่ในช่วง startDate ถึง endDate
        $users = User::whereHas('workScheduleAssignmentUsers', function ($query) use ($startDate, $endDate) {
            $query->whereNotNull('date_in')
                ->whereBetween('date_in', [$startDate, $endDate]);
        })->get();

        return $users;

    }

}
