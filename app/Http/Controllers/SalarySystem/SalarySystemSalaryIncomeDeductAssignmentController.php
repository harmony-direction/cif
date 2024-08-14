<?php

namespace App\Http\Controllers\SalarySystem;

use App\Models\User;
use App\Models\Payday;
use App\Models\SearchField;
use App\Models\IncomeDeduct;
use App\Models\PaydayDetail;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use App\Models\IncomeDeductUser;
use App\Http\Controllers\Controller;
use App\Helpers\AddDefaultWorkScheduleAssignment;
use App\Services\UpdatedRoleGroupCollectionService;

class SalarySystemSalaryIncomeDeductAssignmentController extends Controller
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

        $today = now()->toDateString();
        $paydayDetails = PaydayDetail::whereDate('end_date', '<=', $today)
            ->whereDate('payment_date', '>=', $today)
            ->get();

        $paydayDetailIds = $paydayDetails->pluck('id');

        $incomeDeductUsers = IncomeDeductUser::whereIn('payday_detail_id', $paydayDetailIds)
            ->whereIn('id', function ($query) use ($paydayDetailIds) {
                $query->selectRaw('min(id)')
                    ->from('income_deduct_users')
                    ->whereIn('payday_detail_id', $paydayDetailIds)
                    ->groupBy('user_id');
            })
            ->select('user_id')
            ->distinct()
            ->paginate(5000);

        $incomeDeducts = IncomeDeduct::all();

        return view($viewName, [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'incomeDeductUsers' => $incomeDeductUsers,
            'incomeDeducts' => $incomeDeducts
        ]);
    }

    public function store(Request $request)
    {
        foreach ($request->data['employeeNos'] as $employeeNo) {
            [$userId, $value] = explode('-', $employeeNo, 2);
            $userIds[] = $userId;
            $values[] = $value;
        }
        $incomeDeductId = $request->data['incomeDeductId'];

        $users = User::whereIn('employee_no',$userIds)->get();
        foreach($users as $index => $user)
        {
            $paydayDetail = $user->getPaydayDetailWithToday();
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

        $today = now()->toDateString();
        $paydayDetails = PaydayDetail::whereDate('end_date', '<=', $today)
            ->whereDate('payment_date', '>=', $today)
            ->get();

        $paydayDetailIds = $paydayDetails->pluck('id');

        $incomeDeductUsers = IncomeDeductUser::whereIn('payday_detail_id', $paydayDetailIds)
            ->whereIn('id', function ($query) use ($paydayDetailIds) {
                $query->selectRaw('min(id)')
                    ->from('income_deduct_users')
                    ->whereIn('payday_detail_id', $paydayDetailIds)
                    ->groupBy('user_id');
            })
            ->select('user_id')
            ->distinct()
            ->paginate(5000);
        return view('groups.salary-system.salary.income-deduct-assignment.table-render.income-deduct-assignment-render', [
            'incomeDeductUsers' => $incomeDeductUsers,
            ])->render();
    }

    public function delete(Request $request)
    {
        $userId = $request->data['userId'];
        $incomeDeductUserId = User::find($userId)->getIncomeDeductByUsers()->pluck('id');
        IncomeDeductUser::whereIn('id',$incomeDeductUserId)->delete();

        $today = now()->toDateString();
        $paydayDetails = PaydayDetail::whereDate('end_date', '<=', $today)
            ->whereDate('payment_date', '>=', $today)
            ->get();

        $paydayDetailIds = $paydayDetails->pluck('id');

        $incomeDeductUsers = IncomeDeductUser::whereIn('payday_detail_id', $paydayDetailIds)
            ->whereIn('id', function ($query) use ($paydayDetailIds) {
                $query->selectRaw('min(id)')
                    ->from('income_deduct_users')
                    ->whereIn('payday_detail_id', $paydayDetailIds)
                    ->groupBy('user_id');
            })
            ->select('user_id')
            ->distinct()
            ->paginate(5000);

        return view('groups.salary-system.salary.income-deduct-assignment.table-render.income-deduct-assignment-render', [
            'incomeDeductUsers' => $incomeDeductUsers,
            ])->render();
    }
    public function search(Request $request)
    {
        $searchInput = $request->data['searchInput'];

        
        $searchFields = SearchField::where('table', 'users')->where('status', 1)->get();

        $query = User::query();

        foreach ($searchFields as $field) {
            $fieldName = $field['field'];
            $fieldType = $field['type'];

            if ($fieldType === 'foreign') {
                $query->orWhereHas($fieldName, function ($query) use ($fieldName, $searchInput) {
                    $query->where('name', 'like', "%{$searchInput}%");
                });
            } else {
                $query->orWhere($fieldName, 'like', "%{$searchInput}%");
            }
        }

        $userIds = $query->pluck('id');
        
        $userIds = $query->pluck('id');

        // Search in income_deducts table through income_deduct_users
        $userIds = User::whereIn('id', $userIds)
            ->orWhere(function ($query) use ($searchInput) {
                $query->whereIn('id', function ($subquery) use ($searchInput) {
                    $subquery->select('user_id')
                        ->from('income_deduct_users')
                        ->join('income_deducts', 'income_deduct_users.income_deduct_id', '=', 'income_deducts.id')
                        ->where('income_deducts.name', 'like', "%{$searchInput}%");
                });
            })
            ->pluck('id');

        $today = now()->toDateString();
        $paydayDetails = PaydayDetail::whereDate('end_date', '<=', $today)
            ->whereDate('payment_date', '>=', $today)
            ->get();

        $paydayDetailIds = $paydayDetails->pluck('id');
        $incomeDeductUsers = IncomeDeductUser::whereIn('user_id', $userIds)
            ->whereIn('payday_detail_id', $paydayDetailIds)
            ->whereIn('id', function ($query) use ($userIds, $paydayDetailIds) {
                $query->selectRaw('min(id)')
                    ->from('income_deduct_users')
                    ->whereIn('user_id', $userIds)
                    ->whereIn('payday_detail_id', $paydayDetailIds)
                    ->groupBy('user_id');
            })
            ->paginate(5000);

        return view('groups.salary-system.salary.income-deduct-assignment.table-render.income-deduct-assignment-render', [
            'incomeDeductUsers' => $incomeDeductUsers,
            ])->render();

    }
}
