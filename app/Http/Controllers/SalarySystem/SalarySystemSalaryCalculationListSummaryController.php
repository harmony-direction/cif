<?php

namespace App\Http\Controllers\SalarySystem;
use PDF;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserPayday;
use App\Models\SearchField;
use App\Models\IncomeDeduct;
use App\Models\PaydayDetail;
use Illuminate\Http\Request;
use App\Models\SalarySummary;
use App\Models\OverTimeDetail;
use App\Helpers\ActivityLogger;
use App\Models\IncomeDeductUser;
use App\Models\CompanyDepartment;
use App\Http\Controllers\Controller;
use App\Models\UserDiligenceAllowance;
use App\Models\DiligenceAllowanceClassify;
use App\Helpers\AddDefaultWorkScheduleAssignment;
use App\Services\UpdatedRoleGroupCollectionService;

class SalarySystemSalaryCalculationListSummaryController extends Controller
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
        $userIds = array_unique($userIddiffs);
        $users = User::whereIn('id', $userIds)->paginate(5000);

        $incomeDeducts = IncomeDeduct::all();
        $salarySummaries = SalarySummary::where('payday_detail_id',$id)->get();
        return view('groups.salary-system.salary.calculation-list.summary.index', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'users' => $users,
            'paydayDetail' => $paydayDetail,
            'incomeDeducts' => $incomeDeducts,
            'salarySummaries' => $salarySummaries
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

    public function search(Request $request)
    {
        $queryInput = $request->data['searchInput'];
        $paydayDetailId = $request->data['paydayDetailId'];
        $paydayDetail = PaydayDetail::find($paydayDetailId);
   
        // ค้นหา searchFields จากตาราง SearchField ที่เกี่ยวข้องกับตาราง users และมีสถานะเป็น 1
        $searchFields = SearchField::where('table', 'users')->where('status', 1)->get();
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
        $userIds = array_unique($userIddiffs);
        $commonUserIds = array_intersect($userIds, $searchUserIds);

        $users = User::whereIn('id',$commonUserIds)->paginate(5000);

         return view('groups.salary-system.salary.calculation-list.summary.table-render.user-table',[
            'users' => $users,
            'paydayDetail' => $paydayDetail,
            ])->render();
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

    public function downloadSingle($userId,$paydayDetailId)
    {
        $user = User::find($userId);
        $paydayDetail = PaydayDetail::find($paydayDetailId);
        $salarySummary = $user->salarySummary($paydayDetailId);
        $incomes = $user->getSummaryIncomeDeductByUsers(1,$paydayDetailId);
        
        $deducts = $user->getSummaryIncomeDeductByUsers(2,$paydayDetailId);
        $data = ['user'=>$user,'paydayDetail' => $paydayDetail, 'salarySummary' => $salarySummary, 'incomes' => $incomes, 'deducts'=> $deducts];
        $pdf = PDF::loadView('groups.salary-system.salary.calculation-list.summary.single', $data);
        return $pdf->stream('document.pdf');
    }

    public function downloadAll($paydayDetailId)
    {
        $paydayDetail = PaydayDetail::find($paydayDetailId);
        $userIds = [];
        $startDate = $paydayDetail->start_date;
        $endDate = $paydayDetail->end_date;
        $ids = $this->getUsersByWorkScheduleAssignment($startDate, $endDate)->pluck('id')->toArray();
        $userPaydayIds = UserPayday::where('payday_id',$paydayDetail->payday_id)->pluck('user_id')->toArray();
        $userIddiffs = array_intersect($ids, $userPaydayIds);

        // $userIds = array_merge($userIds, $ids);
        $userIds = array_unique($userIddiffs);
        // $userIds = array_unique($userIds);
        $users = User::whereIn('id', $userIds)->get();
        $companyDepartmentIds = array_unique($users->pluck('company_department_id')->toArray());
        $companyDepartments = CompanyDepartment::whereIn('id',$companyDepartmentIds)->get();
       
        $data = ['users'=>$users,'companyDepartments' => $companyDepartments,'paydayDetail' => $paydayDetail];
        $pdf = PDF::loadView('groups.salary-system.salary.calculation-list.summary.all', $data,[],[ 
          'title' => 'Certificate', 
          'format' => 'A4-L',
          'orientation' => 'L'
        ]);
        return $pdf->stream('document.pdf');
    }

    public function finish($paydayDetailId)
    {
        $paydayDetail = PaydayDetail::find($paydayDetailId);
        $userIds = [];
        $startDate = $paydayDetail->start_date;
        $endDate = $paydayDetail->end_date;
        $ids = $this->getUsersByWorkScheduleAssignment($startDate, $endDate)->pluck('id')->toArray();     
        $userPaydayIds = UserPayday::where('payday_id',$paydayDetail->payday_id)->pluck('user_id')->toArray();
        $userIddiffs = array_intersect($ids, $userPaydayIds);      

        // $userIds = array_merge($userIds, $ids);
        $userIds = array_unique($userIddiffs);
        $users = User::whereIn('id', $userIds)->get();

        $companyDepartments = CompanyDepartment::all();

        foreach($companyDepartments as $key => $companyDepartment){
            $totalSalary = 0;
            $totalsocialSecurityFivePercent = 0;
            $totaloverTimeCost = 0;
            $totaldeligenceAllowance = 0;
            $totalLeave = 0;
            $totalAbsence = 0;
            $netDeduct =0;
            $netIncome =0;
            
            foreach($users->where('company_department_id',$companyDepartment->id) as $user){
                $userSummary = $user->salarySummary($paydayDetail->id);
                $totalSalary += round(str_replace(',', '',$userSummary['salary']));
                $totalLeave += $userSummary['leaveCountSum'];
                $totalAbsence += $userSummary['absentCountSum'];
                $totaloverTimeCost += round(str_replace(',', '',$userSummary['overTimeCost']));
                $totaldeligenceAllowance += round(str_replace(',', '',$userSummary['deligenceAllowance']));
                $totalsocialSecurityFivePercent += round(str_replace(',', '',$userSummary['socialSecurityFivePercent']));
                foreach ($user->getSummaryIncomeDeductByUsers(2,$paydayDetail->id) as $getIncomeDeductByUser) {
                    $netDeduct += $getIncomeDeductByUser->value;
                }

                foreach ($user->getSummaryIncomeDeductByUsers(1,$paydayDetail->id) as $getIncomeDeductByUser) {
                    $netIncome += $getIncomeDeductByUser->value;
                }
            }
            $salarySummary = SalarySummary::where('payday_detail_id',$paydayDetail->id)->where('company_department_id',$companyDepartment->id)->first();
            if ($salarySummary == null){
                SalarySummary::create([
                    'employee' => count($users->where('company_department_id',$companyDepartment->id)),
                    'payday_detail_id' => $paydayDetail->id,
                    'company_department_id' => $companyDepartment->id,
                    'sum_salary' => $totalSalary,
                    'sum_overtime' => $totaloverTimeCost,
                    'sum_allowance_diligence' => $totaldeligenceAllowance,
                    'sum_income' => $netIncome,
                    'sum_deduct' => $netDeduct,
                    'sum_leave' => $totalLeave,
                    'sum_absence' => $totalAbsence,
                    'sum_social_security' => $totalsocialSecurityFivePercent
                ]);
            }else{
                $salarySummary->update([
                    'employee' => count($users->where('company_department_id',$companyDepartment->id)),
                    'payday_detail_id' => $paydayDetail->id,
                    'company_department_id' => $companyDepartment->id,
                    'sum_salary' => $totalSalary,
                    'sum_overtime' => $totaloverTimeCost,
                    'sum_allowance_diligence' => $totaldeligenceAllowance,
                    'sum_income' => $netIncome,
                    'sum_deduct' => $netDeduct,
                    'sum_leave' => $totalLeave,
                    'sum_absence' => $totalAbsence,
                    'sum_social_security' => $totalsocialSecurityFivePercent
                ]);
            }
        }
        return redirect()->back();
    }
}
