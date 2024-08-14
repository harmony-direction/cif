<?php

namespace App\Http\Controllers\TimeRecordingSystems;

use App\Models\User;
use App\Models\UserGroup;
use App\Models\SearchField;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Helpers\AddDefaultWorkScheduleAssignment;
use App\Services\UpdatedRoleGroupCollectionService;

class TimeRecordingSystemSettingEmployeeGroupAssignmentController extends Controller
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
        // กำหนดค่าตัวแปร $action ให้เป็น 'create'
        $action = 'show';
        // ดึงค่า 'groupUrl' จาก session และแปลงเป็นข้อความ
        $groupUrl = strval(session('groupUrl'));

        // เรียกใช้งานเซอร์วิส updatedRoleGroupCollectionService เพื่อดึงข้อมูล updatedRoleGroupCollection, permission, viewName โดยใช้ค่า $action
        $roleGroupCollection = $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);
        $updatedRoleGroupCollection = $roleGroupCollection['updatedRoleGroupCollection'];
        $permission = $roleGroupCollection['permission'];

        $userGroup = UserGroup::find($id);
        
        $users = $userGroup->users()->paginate(5000);
        
        
        return view('groups.time-recording-system.setting.employee-group.assignment.index', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'userGroup' => $userGroup,
            'users' => $users,
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
        $users = User::paginate(5000);
        $userGroup = UserGroup::find($id);
        
        return view('groups.time-recording-system.setting.employee-group.assignment.create', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'userGroup' => $userGroup,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $selectedUsers = $request->users;
        $userGroupId = $request->userGroupId;
        $userGroup = UserGroup::find($userGroupId);

        $currentUsers = $userGroup->users()->pluck('users.id')->toArray();

        // Detach users that exist in the database but are not in the selectedUsers array
        // $usersToDetach = array_diff($currentUsers, $selectedUsers);
        // $userGroup->users()->detach($usersToDetach);

        // Attach the selected users to the user group
        $userGroup->users()->syncWithoutDetaching($selectedUsers);

        return redirect()->to('groups/time-recording-system/setting/employee-group/assignment/' . $userGroupId);
    }

    public function delete(Request $request, $userGroupId, $userId)
    {
        
        $userGroup = UserGroup::findOrFail($userGroupId);
        $user = User::findOrFail($userId);

        $userGroup->users()->detach($user);

        return redirect()->to('groups/time-recording-system/setting/employee-group/assignment/' . $userGroupId);
    }

    public function search(Request $request)
    {
        $queryInput = $request->data;
      
        $searchFields = SearchField::where('table','users')->where('status',1)->get();

        $query = User::query();
        
        foreach ($searchFields as $field) {
            $fieldName = $field['field'];
            $fieldType = $field['type'];
            
            if ($fieldType === 'foreign') {
                $query->orWhereHas($fieldName, function ($query) use ($fieldName, $queryInput) {
                    $query->where('name', 'like', "%{$queryInput}%");
                });
            } else {
                $query->orWhere($fieldName, 'like', "%{$queryInput}%");
            }
        }

        $users = $query->paginate(5000);
        return view('groups.time-recording-system.setting.employee-group.assignment.table-render.user-table',['users' => $users])->render();
    }
}
