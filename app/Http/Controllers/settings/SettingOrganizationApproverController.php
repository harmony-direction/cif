<?php

namespace App\Http\Controllers\Settings;

use App\Models\User;
use App\Models\Approver;
use App\Models\SearchField;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use App\Models\CompanyDepartment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SettingOrganizationApproverController extends Controller
{
    private $activityLogger;

    public function __construct(ActivityLogger $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }
    /**
     * แสดงรายการผู้อนุมัติทั้งหมด
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $approvers = Approver::paginate(5000);

        return view('setting.organization.approver.index', [
            'approvers' => $approvers
        ]);
    }

    /**
     * สร้างรายการผู้อนุมัติใหม่
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $documentTypes = DocumentType::all();
        $companyDepartments = CompanyDepartment::all();
        $approvers = User::where('nationality_id', 1)->where('ethnicity_id', 1)->get();

        return view('setting.organization.approver.create', [
            'documentTypes' => $documentTypes,
            'companyDepartments' => $companyDepartments,
            'approvers' => $approvers
        ]);
    }

    /**
     * เพิ่มรายการผู้อนุมัติใหม่
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $validator = $this->validateFormData($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $approveName = $request->name;
        $companyDepartmentId = $request->company_department;
        $approverOneId = $request->approver_one;
        $approverTwoId = $request->approver_two ?? null;
        $documentType = $request->document_type;

        $approver = new Approver();
        $approver->name = $approveName;
        $approver->document_type_id = $documentType;
        $approver->company_department_id = $companyDepartmentId;
        $approver->approver_one_id = $approverOneId;
        $approver->approver_two_id = $approverTwoId;
        $approver->save();

        $this->activityLogger->log('เพิ่ม', $approver);

        return redirect()->route('setting.organization.approver.index')->with([
            'message' => 'นำเข้าข้อมูลเรียบร้อยแล้ว'
        ]);
    }

    /**
     * แสดงรายละเอียดของผู้อนุมัติ
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function view($id)
    {
        $approver = Approver::find($id);
        $documentTypes = DocumentType::all();
        $companyDepartments = CompanyDepartment::all();
        $approvers = User::where('nationality_id', 1)->where('ethnicity_id', 1)->get();

        return view('setting.organization.approver.view', [
            'approver' => $approver,
            'documentTypes' => $documentTypes,
            'companyDepartments' => $companyDepartments,
            'approvers' => $approvers
        ]);
    }

    /**
     * อัปเดตรายการผู้อนุมัติ
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $validator = $this->validateFormData($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $approveName = $request->name;
        $companyDepartmentId = $request->company_department;
        $approverOneId = $request->approver_one;
        $approverTwoId = $request->approver_two ?? null;
        $documentType = $request->document_type;

        $approver = Approver::findOrFail($id);

        $this->activityLogger->log('อัปเดต', $approver);

        $approver->update([
            'name' => $approveName,
            'document_type_id' => $documentType,
            'company_department_id' => $companyDepartmentId,
            'approver_one_id' => $approverOneId,
            'approver_two_id' => $approverTwoId
        ]);

        return redirect()->route('setting.organization.approver.index');
    }

    /**
     * ลบรายการผู้อนุมัติ
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $approver = Approver::findOrFail($id);
        $this->activityLogger->log('ลบ', $approver);
        $approver->delete();

        return response()->json(['message' => 'สายอนุมัติได้ถูกลบออกเรียบร้อยแล้ว']);
    }

    /**
     * ตรวจสอบความถูกต้องของข้อมูลที่ส่งมาจากฟอร์ม
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateFormData($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'company_department.*' => 'required',
            'document_type.*' => 'required',
            'approver_one.*' => 'required'
        ]);

        return $validator;
    }

}
