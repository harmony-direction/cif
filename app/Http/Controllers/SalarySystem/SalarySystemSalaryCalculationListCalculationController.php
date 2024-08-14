<?php

namespace App\Http\Controllers\SalarySystem;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserPayday;
use App\Models\SearchField;
use App\Models\IncomeDeduct;
use App\Models\PaydayDetail;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use App\Models\IncomeDeductUser;
use App\Http\Controllers\Controller;
use App\Models\UserDiligenceAllowance;
use App\Models\DiligenceAllowanceClassify;
use App\Helpers\AddDefaultWorkScheduleAssignment;
use App\Services\UpdatedRoleGroupCollectionService;

class SalarySystemSalaryCalculationListCalculationController extends Controller
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
        
        $paydayDetail = PaydayDetail::find($id);
        $userPaydays = UserPayday::where('payday_id',$paydayDetail->payday_id)->pluck('user_id')->toArray();

        $userIds = [];

        $startDate = $paydayDetail->start_date;
        $endDate = $paydayDetail->end_date;
        $ids = $this->getUsersByWorkScheduleAssignment($startDate, $endDate)->pluck('id')->toArray();

        
        $userIddiffs = array_intersect($ids, $userPaydays);

        // dd($ids, $userPaydays,$userIddiffs);
        
        $paydayDetailWithMaxEndDate = PaydayDetail::where('end_date', '<', $startDate)
            ->where('end_date', function ($query) use ($startDate) {
                $query->selectRaw('MAX(end_date)')
                    ->from('payday_details')
                    ->where('end_date', '<', $startDate);
            })
            ->first();
            
        if ($paydayDetailWithMaxEndDate) {
            $paydayDetailWithMaxEndDateId= $paydayDetailWithMaxEndDate->id;
            foreach ($userIddiffs as $userId)
            {
                $userDiligenceAllowance = UserDiligenceAllowance::where('user_id',$userId)->where('payday_detail_id',$paydayDetailWithMaxEndDateId)->first();
                if(!$userDiligenceAllowance)
                {
                    $user = User::find($userId);
                    $diligenceAllowanceId = $user->diligence_allowance_id;
                    if($diligenceAllowanceId != null){
                        $diligenceAllowanceClassifyId = DiligenceAllowanceClassify::where('diligence_allowance_id',$diligenceAllowanceId)->min('id');
                        UserDiligenceAllowance::create([
                            'user_id' => $userId,
                            'payday_detail_id' => $paydayDetailWithMaxEndDateId,
                            'diligence_allowance_classify_id' => $diligenceAllowanceClassifyId,
                        ]);
                    }
                }
            }
            
        } 

        $userIds = array_unique($userIddiffs);
        $users = User::whereIn('id', $userIds)->paginate(5000);

        $incomeDeducts = IncomeDeduct::all();
        return view('groups.salary-system.salary.calculation-list.calculation.index', [
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
        
        foreach ($request->data['employeeNos'] as $pair) {
            [$employeeNo, $value] = explode('-', $pair, 2);

            $user = User::where('employee_no',$employeeNo)->first();
            $incomeDeductUser = IncomeDeductUser::where('user_id',$user->id)
                    ->where('payday_detail_id',$paydayDetailId)
                    ->where('income_deduct_id',$incomeDeductId)
                    ->first();

            if($incomeDeductUser == null){
                IncomeDeductUser::create([
                    'user_id' => $user->id,
                    'payday_detail_id' => $paydayDetailId,
                    'income_deduct_id' => $incomeDeductId,
                    'value' => $value,
                ]);
            }else{
                $incomeDeductUser::update([
                    'user_id' => $user->id,
                    'payday_detail_id' => $paydayDetailId,
                    'income_deduct_id' => $incomeDeductId,
                    'value' => $value,
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
    public function search(Request $request)
    {
        $queryInput = $request->data['searchInput'];
        $paydayDetailId = $request->data['paydayDetailId'];
   
        // ค้นหา searchFields จากตาราง SearchField ที่เกี่ยวข้องกับตาราง users และมีสถานะเป็น 1
        $searchFields = SearchField::where('table', 'users')->where('status', 1)->get();
        $paydayDetail = PaydayDetail::find($paydayDetailId);
        $userPaydayIds = UserPayday::where('payday_id',$paydayDetail->payday_id)->pluck('user_id')->toArray();

        // สร้าง query ของตาราง users
        $query = User::query();

        foreach ($searchFields as $field) {
            $fieldName = $field['field'];
            $fieldType = $field['type'];

            if ($fieldType === 'foreign') {
                // ค้นหาข้อมูลที่เกี่ยวข้องจากตาราง foreign และตรวจสอบว่ามีชื่อตรงกับ queryInput หรือไม่
                $query->orWhereHas($fieldName, function ($query) use ($fieldName, $queryInput) {
                    $query->where('name', 'like', "%{$queryInput}%");
                });
            } else {
                // ค้นหาข้อมูลในฟิลด์ของตาราง users และตรวจสอบว่ามีค่าตรงกับ queryInput หรือไม่
                $query->orWhere($fieldName, 'like', "%{$queryInput}%");
            }
        }
        $searchUserIds = $query->pluck('id')->toArray();

        $userIds = [];
        
        $startDate = $paydayDetail->start_date;
        $endDate = $paydayDetail->end_date;
        $ids = $this->getUsersByWorkScheduleAssignment($startDate, $endDate)->pluck('id')->toArray();

        $userIddiffs = array_intersect($ids, $userPaydayIds);
        
        $paydayDetailWithMaxEndDate = PaydayDetail::where('end_date', '<', $startDate)
            ->where('end_date', function ($query) use ($startDate) {
                $query->selectRaw('MAX(end_date)')
                    ->from('payday_details')
                    ->where('end_date', '<', $startDate);
            })
            ->first();
            
        if ($paydayDetailWithMaxEndDate) {
            $paydayDetailWithMaxEndDateId= $paydayDetailWithMaxEndDate->id;
            foreach ($userIddiffs as $userId)
            {
                $userDiligenceAllowance = UserDiligenceAllowance::where('user_id',$userId)->where('payday_detail_id',$paydayDetailWithMaxEndDateId)->first();
                
                if(!$userDiligenceAllowance)
                {
                    $user = User::find($userId);
                    $diligenceAllowanceId = $user->diligence_allowance_id;
                    if($diligenceAllowanceId != null){
                        
                        $diligenceAllowanceClassifyId = DiligenceAllowanceClassify::where('diligence_allowance_id',$diligenceAllowanceId)->min('id');
                        UserDiligenceAllowance::create([
                            'user_id' => $userId,
                            'payday_detail_id' => $paydayDetailWithMaxEndDateId,
                            'diligence_allowance_classify_id' => $diligenceAllowanceClassifyId,
                        ]);
                    }
                }
            }
            
        } 
        // $userIds = array_merge($userIds, $ids);
        $userIds = array_unique($$userIddiffs);
        $commonUserIds = array_intersect($userIds, $searchUserIds);

        $users = User::whereIn('id',$commonUserIds)->paginate(5000);

        return view('groups.salary-system.salary.calculation-list.calculation.table-render.user-table',[
            'users' => $users,
            'paydayDetail' => $paydayDetail,
            ])->render();

    }

}
