<?php

namespace App\Http\Controllers\SalarySystem;

use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use App\Models\DiligenceAllowance;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\DiligenceAllowanceClassify;
use App\Helpers\AddDefaultWorkScheduleAssignment;
use App\Services\UpdatedRoleGroupCollectionService;

class SalarySystemSettingDiligenceAllowanceController extends Controller
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
        $diligenceAllowances = DiligenceAllowance::all();


        return view($viewName, [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'diligenceAllowances' => $diligenceAllowances
        ]);
    }

    public function view($levelId,$id)
    {
        
        // กำหนดค่าตัวแปร $action ให้เป็น 'update'
        $action = 'update';
        // ดึงค่า 'groupUrl' จาก session และแปลงเป็นข้อความ
        $groupUrl = strval(session('groupUrl'));

        // เรียกใช้งานเซอร์วิส updatedRoleGroupCollectionService เพื่อดึงข้อมูล updatedRoleGroupCollection, permission โดยใช้ค่า $action
        $roleGroupCollection = $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);
        $updatedRoleGroupCollection = $roleGroupCollection['updatedRoleGroupCollection'];
        $permission = $roleGroupCollection['permission'];

        $diligenceAllowanceClassify = DiligenceAllowanceClassify::where('diligence_allowance_id',$id)->where('level',$levelId)->first();

        return view('groups.salary-system.setting.diligence-allowance.assignment.view', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'diligenceAllowanceClassify' => $diligenceAllowanceClassify
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = $this->validateFormData($request);
        $cost = $request->cost;

        $diligenceAllowanceClassify = DiligenceAllowanceClassify::find($id);

        // dd($cost);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $diligenceAllowanceClassify->update([
            'cost' => $cost,
        ]);
        return redirect()->route('groups.salary-system.setting.diligence-allowance.assignment', ['id' => $diligenceAllowanceClassify->diligence_allowance_id]);

    }

    function validateFormData($request)
    {
        $validator = Validator::make($request->all(), [
            'cost' => 'required',
        ]);
        return $validator;
    }
}
