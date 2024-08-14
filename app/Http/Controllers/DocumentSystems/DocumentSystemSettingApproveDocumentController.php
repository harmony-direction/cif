<?php

namespace App\Http\Controllers\DocumentSystems;

use App\Models\User;
use App\Models\Leave;
use App\Models\Approver;
use App\Models\ApproverUser;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use App\Models\OverTimeDetail;
use App\Helpers\ActivityLogger;
use App\Models\CompanyDepartment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\UpdatedRoleGroupCollectionService;

class DocumentSystemSettingApproveDocumentController extends Controller
{
    private $updatedRoleGroupCollectionService;
    private $activityLogger;

    public function __construct(UpdatedRoleGroupCollectionService $updatedRoleGroupCollectionService,ActivityLogger $activityLogger) 
    {
        $this->updatedRoleGroupCollectionService = $updatedRoleGroupCollectionService;
        $this->activityLogger = $activityLogger;
    }
    public function index()
    {
        // กำหนดค่าตัวแปร $action ให้เป็น 'show'
        $action = 'show';
        // ดึงค่า 'groupUrl' จาก session และแปลงเป็นข้อความ
        $groupUrl = strval(session('groupUrl'));

        // เรียกใช้งานเซอร์วิส updatedRoleGroupCollectionService เพื่อดึงข้อมูล updatedRoleGroupCollection, permission โดยใช้ค่า $action
        $roleGroupCollection = $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);
        $updatedRoleGroupCollection = $roleGroupCollection['updatedRoleGroupCollection'];
        $permission = $roleGroupCollection['permission'];
        $viewName = $roleGroupCollection['viewName'];
        $approvers = Approver::paginate(5000);

        return view($viewName, [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'approvers' => $approvers
        ]);
    }
    public function create()
    {
        // กำหนดค่าตัวแปร $action ให้เป็น 'create'
        $action = 'show';
        // ดึงค่า 'groupUrl' จาก session และแปลงเป็นข้อความ
        $groupUrl = strval(session('groupUrl'));

        // เรียกใช้งานเซอร์วิส updatedRoleGroupCollectionService เพื่อดึงข้อมูล updatedRoleGroupCollection, permission, viewName โดยใช้ค่า $action
        $roleGroupCollection = $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);
        $updatedRoleGroupCollection = $roleGroupCollection['updatedRoleGroupCollection'];
        $permission = $roleGroupCollection['permission'];
        
        $documentTypes = DocumentType::all();
        $companyDepartments = CompanyDepartment::all();
        $users = User::where('employee_type_id',1)->get();
       

        return view('groups.document-system.setting.approve-document.create', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'documentTypes' => $documentTypes,
            'companyDepartments' => $companyDepartments,
            'users' => $users
        ]);
    }
    public function store(Request $request)
    {
        // dd($request->import_employee_from_dept);
        $validator = $this->validateFormData($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $userIds = $request->leader;
        $managerId = $request->manager;
        // dd($managerId,$userIds);
        $approveName = $request->name;
        $companyDepartmentId = $request->company_department;
        $code = $request->code;
        $documentType = $request->document_type;

        $approver = new Approver();
        $approver->name = $approveName;
        $approver->user_id = $managerId;
        $approver->code = $code;
        $approver->document_type_id = $documentType;
        $approver->company_department_id = $companyDepartmentId;
        $approver->save();

        // Attach users to the newly created Approver
        if ($userIds && is_array($userIds)) {
            $approver->authorizedUsers()->attach($userIds);
        }

        $this->activityLogger->log('เพิ่ม', $approver);
        if($request->import_employee_from_dept == 1){
            $this->importUser($companyDepartmentId,$approver->id);
        }
        

        return redirect()->route('groups.document-system.setting.approve-document')->with([
            'message' => 'นำเข้าข้อมูลเรียบร้อยแล้ว'
        ]);
    }

    public function importUser($companyDepartmentId,$approverId){

        $approver = Approver::find($approverId);
        $authorizedUserIds = $approver->authorizedUsers->pluck('id')->toArray();
        // dd($selectedUsers);
        $selectedUsers = User::where('company_department_id',$companyDepartmentId)->pluck('id')->toArray();
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
    }

    public function view($id)
    {
        // กำหนดค่าตัวแปร $action ให้เป็น 'update'
        $action = 'update';
        // ดึงค่า 'groupUrl' จาก session และแปลงเป็นข้อความ
        $groupUrl = strval(session('groupUrl'));

        // เรียกใช้งานเซอร์วิส updatedRoleGroupCollectionService เพื่อดึงข้อมูล updatedRoleGroupCollection, permission, viewName โดยใช้ค่า $action
        $roleGroupCollection = $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);
        $updatedRoleGroupCollection = $roleGroupCollection['updatedRoleGroupCollection'];
        $permission = $roleGroupCollection['permission'];
        $approver = Approver::find($id);
        $documentTypes = DocumentType::all();
        $companyDepartments = CompanyDepartment::all();
        $approvers = User::where('nationality_id', 1)->where('ethnicity_id', 1)->get();
        $users = User::where('employee_type_id',1)->get();

        return view('groups.document-system.setting.approve-document.view', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'approver' => $approver,
            'documentTypes' => $documentTypes,
            'companyDepartments' => $companyDepartments,
            'approvers' => $approvers,
            'users' => $users
        ]);
    }


    public function update(Request $request, $id)
    {
        $validator = $this->validateFormData($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $userIds = $request->leader;
        $managerId = $request->manager;
        $approveName = $request->name;
        $companyDepartmentId = $request->company_department;
        $code = $request->code;
        $documentType = $request->document_type;

        $approver = Approver::findOrFail($id);
        $currentAuthorizedUserIds = $approver->authorizedUsers->pluck('id')->toArray();

        $this->activityLogger->log('อัปเดต', $approver);

        $approver->update([
            'name' => $approveName,
            'code' => $code,
            'user_id' => $managerId,
            'document_type_id' => $documentType,
            'company_department_id' => $companyDepartmentId,
        ]);

        if ($documentType == 1)
        {
            $leaves = Leave::all();
            $approverUsers = ApproverUser::where('approver_id',$id)->get();
            if (!$leaves->isEmpty()) {
                $leaveUserIds = $leaves->pluck('user_id')->toArray();
                $approverUserIds = $approverUsers->pluck('user_id')->toArray();
                $usersToUpdates = array_intersect($leaveUserIds, $approverUserIds);
                foreach ($usersToUpdates as $usersToUpdate) {
                    $currentLeave = Leave::where('user_id', $usersToUpdate)->first();
                    if ($currentLeave) {
                        $currentApprovedList = json_decode($currentLeave->approved_list, true);

                        // Remove entries not in $authorizedUserIds
                        $currentApprovedList = array_filter($currentApprovedList, function ($entry) use ($currentAuthorizedUserIds) {
                            return in_array($entry['user_id'], $currentAuthorizedUserIds);
                        });

                        // Add missing entries from $authorizedUserIds
                        foreach ($currentAuthorizedUserIds as $authorizedUserId) {
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
        }else if ($documentType == 2){
            $overtimeDetails = OverTimeDetail::all();
            $approverUsers = ApproverUser::where('approver_id',$id)->get();
            if (!$overtimeDetails->isEmpty()) {
                $overtimeDetailUserIds = $overtimeDetails->pluck('user_id')->toArray();
                $approverUserIds = $approverUsers->pluck('user_id')->toArray();

                $usersToUpdates = array_intersect($overtimeDetailUserIds, $approverUserIds);

                foreach ($usersToUpdates as $usersToUpdate) {
                    $currentOvertimeDetail = OverTimeDetail::where('user_id', $usersToUpdate)->first();
                    if ($currentOvertimeDetail) {
                        $currentApprovedList = json_decode($currentOvertimeDetail->approved_list, true);

                        $currentApprovedList = array_filter($currentApprovedList, function ($entry) use ($currentAuthorizedUserIds) {
                            return in_array($entry['user_id'], $currentAuthorizedUserIds);
                        });

                        foreach ($currentAuthorizedUserIds as $authorizedUserId) {
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

        // Detach nonexistent users from the authorizedUsers relationship
        
        $detachUserIds = array_diff($currentAuthorizedUserIds, $userIds);
        if (!empty($detachUserIds)) {
            $approver->authorizedUsers()->detach($detachUserIds);
        }

        // Attach incoming unique users to the authorizedUsers relationship (without detaching existing ones)
        $uniqueUserIds = array_unique($userIds);
        $approver->authorizedUsers()->syncWithoutDetaching($uniqueUserIds);

        return redirect()->route('groups.document-system.setting.approve-document');
    }
    public function delete($id)
    {
        $approver = Approver::findOrFail($id);
        $this->activityLogger->log('ลบ', $approver);
        $approver->delete();

        return response()->json(['message' => 'สายอนุมัติได้ถูกลบออกเรียบร้อยแล้ว']);
    }

    public function getUsers(Request $request)
    {
        $users = User::where('nationality_id', 1)->where('ethnicity_id', 1)->get();
        return view('groups.document-system.setting.approve-document.modal-user-render.modal-user',['users' => $users])->render();
    }


    public function validateFormData($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'code' => 'required',
            'company_department.*' => 'required',
            'document_type.*' => 'required',
            'leader' => 'required|array|min:1',
        ]);

        return $validator;
    }
}
