<?php

namespace App\Http\Controllers\DocumentSystems;

use App\Models\User;
use App\Models\Leave;
use App\Models\Approver;
use App\Models\SearchField;
use App\Models\ApproverUser;
use Illuminate\Http\Request;
use App\Models\OverTimeDetail;
use App\Helpers\ActivityLogger;
use App\Models\CompanyDepartment;
use App\Http\Controllers\Controller;
use App\Services\UpdatedRoleGroupCollectionService;

class DocumentSystemSettingApproveDocumentAssignmentController extends Controller
{
    private $updatedRoleGroupCollectionService;
    private $activityLogger;

    public function __construct(UpdatedRoleGroupCollectionService $updatedRoleGroupCollectionService,ActivityLogger $activityLogger) 
    {
        $this->updatedRoleGroupCollectionService = $updatedRoleGroupCollectionService;
        $this->activityLogger = $activityLogger;
    }
    public function index($id)
    {
        // กำหนดค่าตัวแปร $action ให้เป็น 'show'
        $action = 'show';
        // ดึงค่า 'groupUrl' จาก session และแปลงเป็นข้อความ
        $groupUrl = strval(session('groupUrl'));

        // เรียกใช้งานเซอร์วิส updatedRoleGroupCollectionService เพื่อดึงข้อมูล updatedRoleGroupCollection, permission โดยใช้ค่า $action
        $roleGroupCollection = $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);
        $updatedRoleGroupCollection = $roleGroupCollection['updatedRoleGroupCollection'];
        $permission = $roleGroupCollection['permission'];
        $companyDepartments = CompanyDepartment::get();

        $approver = Approver::find($id);
        return view('groups.document-system.setting.approve-document.assignment.index', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'approver' => $approver,
            'companyDepartments' => $companyDepartments,
        ]);
    }

    public function create($id)
    {
        // กำหนดค่าตัวแปร $action ให้เป็น 'show'
        $action = 'create';
        // ดึงค่า 'groupUrl' จาก session และแปลงเป็นข้อความ
        $groupUrl = strval(session('groupUrl'));

        // เรียกใช้งานเซอร์วิส updatedRoleGroupCollectionService เพื่อดึงข้อมูล updatedRoleGroupCollection, permission โดยใช้ค่า $action
        $roleGroupCollection = $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);
        $updatedRoleGroupCollection = $roleGroupCollection['updatedRoleGroupCollection'];
        $permission = $roleGroupCollection['permission'];

        $approver = Approver::find($id);

        $users = User::whereDoesntHave('approvers', function ($query) use ($id) {
            $query->where('approver_id', $id);
        })->paginate(5000);
        
        return view('groups.document-system.setting.approve-document.assignment.create', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'users' => $users,
            'approver' => $approver
        ]);
    }

    public function store(Request $request)
    {
        $selectedUsers = $request->users;
        $approverId = $request->approverId;

        $approver = Approver::find($approverId);
        $authorizedUserIds = $approver->authorizedUsers->pluck('id')->toArray();
        // dd($selectedUsers);
        foreach ($selectedUsers as $userId) {
            $approverUsers = ApproverUser::all();
            $existingUser = ApproverUser::where('user_id', $userId)
            ->whereHas('approver', function ($query) use ($approver) {
                $query->where('document_type_id', $approver->document_type_id);
            })
            ->first();
            if (!$existingUser) {
                $approver->users()->attach($userId);
            } else {
                $existApprover = Approver::find($existingUser->approver_id); 
                if ($existApprover->document_type_id === $approver->document_type_id) {
                    $existApprover->users()->detach($userId);
                } 
                $approver->users()->attach($userId);
            }
        }

        if ($approver->document_type_id == '1')
        {
            $leaves = Leave::all();
            $approverUsers = ApproverUser::where('approver_id',$approverId)->get();
            
            if (!$leaves->isEmpty()) {
                $leaveUserIds = $leaves->pluck('user_id')->toArray();
                $approverUserIds = $approverUsers->pluck('user_id')->toArray();

                $usersToUpdates = array_intersect($leaveUserIds, $approverUserIds);
                // dd($usersToUpdates);
                foreach ($usersToUpdates as $usersToUpdate) {
                    $currentLeave = Leave::where('user_id', $usersToUpdate)->first();
                    if ($currentLeave) {
                        $currentApprovedList = json_decode($currentLeave->approved_list, true);

                        // Remove entries not in $authorizedUserIds
                        $currentApprovedList = array_filter($currentApprovedList, function ($entry) use ($authorizedUserIds) {
                            return in_array($entry['user_id'], $authorizedUserIds);
                        });

                        // Add missing entries from $authorizedUserIds
                        foreach ($authorizedUserIds as $authorizedUserId) {
                            $found = false;
                            foreach ($currentApprovedList as $entry) {
                                if ($entry['user_id'] == $authorizedUserId) {
                                    $found = true;
                                    break;
                                }
                            }
                            if (!$found) {
                                $currentApprovedList[] = ['user_id' => $authorizedUserId, 'status' => 0];
                            }
                        }

                        // Update the approved_list field
                        $currentLeave->update([
                            'approved_list' => json_encode($currentApprovedList)
                        ]);
                    }
                }
            }
        }else if($approver->document_type_id == '2')
        {
            $overtimeDetails = OverTimeDetail::all();
            $approverUsers = ApproverUser::where('approver_id',$approverId)->get();
            if (!$overtimeDetails->isEmpty()) {
                $overtimeDetailUserIds = $overtimeDetails->pluck('user_id')->toArray();
                $approverUserIds = $approverUsers->pluck('user_id')->toArray();

                $usersToUpdates = array_intersect($overtimeDetailUserIds, $approverUserIds);

                foreach ($usersToUpdates as $usersToUpdate) {
                    $currentOvertimeDetail = OverTimeDetail::where('user_id', $usersToUpdate)->first();
                    if ($currentOvertimeDetail) {
                        $currentApprovedList = json_decode($currentOvertimeDetail->approved_list, true);

                        $currentApprovedList = array_filter($currentApprovedList, function ($entry) use ($authorizedUserIds) {
                            return in_array($entry['user_id'], $authorizedUserIds);
                        });

                        foreach ($authorizedUserIds as $authorizedUserId) {
                            $found = false;
                            foreach ($currentApprovedList as $entry) {
                                if ($entry['user_id'] == $authorizedUserId) {
                                    $found = true;
                                    break;
                                }
                            }
                            if (!$found) {
                                $currentApprovedList[] = ['user_id' => $authorizedUserId, 'status' => 0];
                            }
                        }
                        // Update the approved_list field
                        $currentOvertimeDetail->update([
                            'approved_list' => json_encode($currentApprovedList)
                        ]);
                    }

                }
            }

        }

        return redirect()->to('groups/document-system/setting/approve-document/assignment/' . $approverId);
    }

    public function search(Request $request)
    {
        $queryInput = $request->data['searchInput'];
        $approverId = $request->data['approverId'];
        
        $searchFields = SearchField::where('table', 'users')->where('status', 1)->get();

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

        $userIds = $query->pluck('id');
        $approversUserIds = ApproverUser::where('approver_id', $approverId)->pluck('user_id');

        $filteredUserIds = $userIds->diff($approversUserIds);

        $users = User::whereIn('id', $filteredUserIds)->paginate(5000);

        return view('groups.document-system.setting.approve-document.assignment.table-render.employee-table', ['users' => $users])->render();
    }

    public function delete(Request $request, $approverId, $userId)
    {
        $approver = Approver::findOrFail($approverId);
        $user = User::findOrFail($userId);

        $approver->users()->detach($user);

        return redirect()->to('groups/document-system/setting/approve-document/assignment/' . $approverId);
    }

    public function importEmployeeNo(Request $request)
    {
        $employeeNos = $request->data['employeeNos'];
        $approverId = $request->data['approverId'];
        
        $selectedUsers = User::whereIn('employee_no',$employeeNos)->pluck('id');
  
        $approver = Approver::find($approverId);
        $authorizedUserIds = $approver->authorizedUsers->pluck('id')->toArray();
        // dd($selectedUsers);
        foreach ($selectedUsers as $userId) {
            $approverUsers = ApproverUser::all();
            $existingUser = ApproverUser::where('user_id', $userId)
            ->whereHas('approver', function ($query) use ($approver) {
                $query->where('document_type_id', $approver->document_type_id);
            })
            ->first();
            if (!$existingUser) {
                $approver->users()->attach($userId);
            } else {
                $existApprover = Approver::find($existingUser->approver_id); 
                if ($existApprover->document_type_id === $approver->document_type_id) {
                    $existApprover->users()->detach($userId);
                } 
                $approver->users()->attach($userId);
            }
        }

        if ($approver->document_type_id == '1')
        {
            $leaves = Leave::all();
            $approverUsers = ApproverUser::where('approver_id',$approverId)->get();
            
            if (!$leaves->isEmpty()) {
                $leaveUserIds = $leaves->pluck('user_id')->toArray();
                $approverUserIds = $approverUsers->pluck('user_id')->toArray();

                $usersToUpdates = array_intersect($leaveUserIds, $approverUserIds);
                // dd($usersToUpdates);
                foreach ($usersToUpdates as $usersToUpdate) {
                    $currentLeave = Leave::where('user_id', $usersToUpdate)->first();
                    if ($currentLeave) {
                        $currentApprovedList = json_decode($currentLeave->approved_list, true);

                        // Remove entries not in $authorizedUserIds
                        $currentApprovedList = array_filter($currentApprovedList, function ($entry) use ($authorizedUserIds) {
                            return in_array($entry['user_id'], $authorizedUserIds);
                        });

                        // Add missing entries from $authorizedUserIds
                        foreach ($authorizedUserIds as $authorizedUserId) {
                            $found = false;
                            foreach ($currentApprovedList as $entry) {
                                if ($entry['user_id'] == $authorizedUserId) {
                                    $found = true;
                                    break;
                                }
                            }
                            if (!$found) {
                                $currentApprovedList[] = ['user_id' => $authorizedUserId, 'status' => 0];
                            }
                        }

                        // Update the approved_list field
                        $currentLeave->update([
                            'approved_list' => json_encode($currentApprovedList)
                        ]);
                    }
                }
            }
        }else if($approver->document_type_id == '2')
        {
            $overtimeDetails = OverTimeDetail::all();
            $approverUsers = ApproverUser::where('approver_id',$approverId)->get();
            if (!$overtimeDetails->isEmpty()) {
                $overtimeDetailUserIds = $overtimeDetails->pluck('user_id')->toArray();
                $approverUserIds = $approverUsers->pluck('user_id')->toArray();

                $usersToUpdates = array_intersect($overtimeDetailUserIds, $approverUserIds);

                foreach ($usersToUpdates as $usersToUpdate) {
                    $currentOvertimeDetail = OverTimeDetail::where('user_id', $usersToUpdate)->first();
                    if ($currentOvertimeDetail) {
                        $currentApprovedList = json_decode($currentOvertimeDetail->approved_list, true);

                        $currentApprovedList = array_filter($currentApprovedList, function ($entry) use ($authorizedUserIds) {
                            return in_array($entry['user_id'], $authorizedUserIds);
                        });

                        foreach ($authorizedUserIds as $authorizedUserId) {
                            $found = false;
                            foreach ($currentApprovedList as $entry) {
                                if ($entry['user_id'] == $authorizedUserId) {
                                    $found = true;
                                    break;
                                }
                            }
                            if (!$found) {
                                $currentApprovedList[] = ['user_id' => $authorizedUserId, 'status' => 0];
                            }
                        }
                        // Update the approved_list field
                        $currentOvertimeDetail->update([
                            'approved_list' => json_encode($currentApprovedList)
                        ]);
                    }

                }
            }
        }
        return ;
    }

    public function importEmployeeNoFromDept(Request $request)
    {
        // $employeeNos = $request->data['employeeNos'];
        $companyDepartmentId = $request->data['companyDepartmentId'];
        $approverId = $request->data['approverId'];
        
        $selectedUsers = User::where('company_department_id',$companyDepartmentId)->pluck('id');
  
        $approver = Approver::find($approverId);
        $authorizedUserIds = $approver->authorizedUsers->pluck('id')->toArray();
        // dd($selectedUsers);
        foreach ($selectedUsers as $userId) {
            $approverUsers = ApproverUser::all();
            $existingUser = ApproverUser::where('user_id', $userId)
            ->whereHas('approver', function ($query) use ($approver) {
                $query->where('document_type_id', $approver->document_type_id);
            })
            ->first();
            if (!$existingUser) {
                $approver->users()->attach($userId);
            } else {
                $existApprover = Approver::find($existingUser->approver_id); 
                if ($existApprover->document_type_id === $approver->document_type_id) {
                    $existApprover->users()->detach($userId);
                } 
                $approver->users()->attach($userId);
            }
        }

        if ($approver->document_type_id == '1')
        {
            $leaves = Leave::all();
            $approverUsers = ApproverUser::where('approver_id',$approverId)->get();
            
            if (!$leaves->isEmpty()) {
                $leaveUserIds = $leaves->pluck('user_id')->toArray();
                $approverUserIds = $approverUsers->pluck('user_id')->toArray();

                $usersToUpdates = array_intersect($leaveUserIds, $approverUserIds);
                // dd($usersToUpdates);
                foreach ($usersToUpdates as $usersToUpdate) {
                    $currentLeave = Leave::where('user_id', $usersToUpdate)->first();
                    if ($currentLeave) {
                        $currentApprovedList = json_decode($currentLeave->approved_list, true);

                        // Remove entries not in $authorizedUserIds
                        $currentApprovedList = array_filter($currentApprovedList, function ($entry) use ($authorizedUserIds) {
                            return in_array($entry['user_id'], $authorizedUserIds);
                        });

                        // Add missing entries from $authorizedUserIds
                        foreach ($authorizedUserIds as $authorizedUserId) {
                            $found = false;
                            foreach ($currentApprovedList as $entry) {
                                if ($entry['user_id'] == $authorizedUserId) {
                                    $found = true;
                                    break;
                                }
                            }
                            if (!$found) {
                                $currentApprovedList[] = ['user_id' => $authorizedUserId, 'status' => 0];
                            }
                        }

                        // Update the approved_list field
                        $currentLeave->update([
                            'approved_list' => json_encode($currentApprovedList)
                        ]);
                    }
                }
            }
        }else if($approver->document_type_id == '2')
        {
            $overtimeDetails = OverTimeDetail::all();
            $approverUsers = ApproverUser::where('approver_id',$approverId)->get();
            if (!$overtimeDetails->isEmpty()) {
                $overtimeDetailUserIds = $overtimeDetails->pluck('user_id')->toArray();
                $approverUserIds = $approverUsers->pluck('user_id')->toArray();

                $usersToUpdates = array_intersect($overtimeDetailUserIds, $approverUserIds);

                foreach ($usersToUpdates as $usersToUpdate) {
                    $currentOvertimeDetail = OverTimeDetail::where('user_id', $usersToUpdate)->first();
                    if ($currentOvertimeDetail) {
                        $currentApprovedList = json_decode($currentOvertimeDetail->approved_list, true);

                        $currentApprovedList = array_filter($currentApprovedList, function ($entry) use ($authorizedUserIds) {
                            return in_array($entry['user_id'], $authorizedUserIds);
                        });

                        foreach ($authorizedUserIds as $authorizedUserId) {
                            $found = false;
                            foreach ($currentApprovedList as $entry) {
                                if ($entry['user_id'] == $authorizedUserId) {
                                    $found = true;
                                    break;
                                }
                            }
                            if (!$found) {
                                $currentApprovedList[] = ['user_id' => $authorizedUserId, 'status' => 0];
                            }
                        }
                        // Update the approved_list field
                        $currentOvertimeDetail->update([
                            'approved_list' => json_encode($currentApprovedList)
                        ]);
                    }

                }
            }
        }
        return ;
    }

}
