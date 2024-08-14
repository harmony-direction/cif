<?php

namespace App\Http\Controllers\SalarySystem;

use App\Models\User;
use App\Models\Bonus;
use App\Models\BonusUser;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Helpers\AddDefaultWorkScheduleAssignment;
use App\Services\UpdatedRoleGroupCollectionService;

class SalarySystemSalaryCalculationBonusListAssignmentController extends Controller
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
        // ตัวอย่างการเข้าถึงข้อมูลของ User ที่เกี่ยวข้องกับ BonusUser
        $bonusUsers = BonusUser::where('bonus_id',$id)->paginate(5000); // ให้นำเลข id ที่ต้องการมาใส่ใน find()
        $bonus = Bonus::find($id);
 
        return view('groups.salary-system.salary.calculation-bonus-list.assignment.index', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'bonusUsers' => $bonusUsers, 
            'bonus' => $bonus,
        ]);
    }

    public function import(Request $request)
    {
        $bonusId = $request->data['bonusId'];
        foreach ($request->data['employeeNos'] as $employeeNo) {
            [$userId, $value] = explode('-', $employeeNo, 2);
            $userIds[] = $userId;
            $values[] = $value;
        }

        $users = User::whereIn('employee_no',$userIds)->get();

        foreach($users as $index => $user)
        {
            $bonusUser = BonusUser::where('user_id',$user->id)->where('bonus_id',$bonusId)->first();
            if ($bonusUser == null)
            {
                BonusUser::create([
                    'user_id' => $user->id,
                    'bonus_id' => $bonusId,
                    'cost' => $values[$index]
                ]);
            }else{
                $bonusUser->update([
                    'cost' => $values[$index]
                ]);
            }
        }

        $bonusUsers = BonusUser::where('bonus_id',$bonusId)->paginate(5000); // ให้นำเลข id ที่ต้องการมาใส่ใน find()
     
        return view('groups.salary-system.salary.calculation-bonus-list.assignment.table-render.user-table',[
            'bonusUsers' => $bonusUsers,
            ])->render();
    }

    public function updateBonus(Request $request)
    {
        $bonusUserId = $request->data['bonusUserId'];
        $cost = $request->data['cost'];

        BonusUser::find($bonusUserId)->update([
            'cost' => $cost
        ]);

        $bonusUser = BonusUser::find($bonusUserId);

        $bonusUsers = BonusUser::where('bonus_id',$bonusUser->bonus_id)->paginate(5000); // ให้นำเลข id ที่ต้องการมาใส่ใน find()

        return view('groups.salary-system.salary.calculation-bonus-list.assignment.table-render.user-table',[
            'bonusUsers' => $bonusUsers,
            ])->render();
    }
    public function search(Request $request)
    {
        $searchString =$request->data['searchInput'];
        
        $bonusUsers = BonusUser::whereHas('user', function ($query) use ($searchString) {
            $query->where('employee_no', 'like', '%' . $searchString . '%')
                ->orWhere('name', 'like', '%' . $searchString . '%')
                ->orWhere('lastname', 'like', '%' . $searchString . '%')
                ->orWhereHas('company_department', function ($subQuery) use ($searchString) {
                    $subQuery->where('name', 'like', '%' . $searchString . '%');
                });
        })
        ->paginate(5000);

        return view('groups.salary-system.salary.calculation-bonus-list.assignment.table-render.user-table',[
            'bonusUsers' => $bonusUsers,
            ])->render();
    }

    public function delete(Request $request)
    {
        $bonusUserId = $request->data['bonusUserId'];
        
        $bonusUser = BonusUser::find($bonusUserId);

        $bonusUser->delete();
        $bonusUsers = BonusUser::where('bonus_id',$bonusUser->bonus_id)->paginate(5000);
        return view('groups.salary-system.salary.calculation-bonus-list.assignment.table-render.user-table',[
            'bonusUsers' => $bonusUsers,
            ])->render();
    }
}
