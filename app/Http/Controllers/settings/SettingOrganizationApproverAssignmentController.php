<?php

namespace App\Http\Controllers\Settings;

use App\Models\User;
use App\Models\Approver;
use App\Models\SearchField;
use App\Models\ApproverUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingOrganizationApproverAssignmentController extends Controller
{
    /**
     * แสดงหน้ารายการผู้อนุมัติสำหรับการมอบหมายงาน โดยใช้ ID ของผู้อนุมัติที่ระบุ
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function index($id)
    {
        $approver = Approver::find($id);
        return view('setting.organization.approver.assignment.index', [
            'approver' => $approver
        ]);
    }

    /**
     * แสดงหน้าสร้างการมอบหมายผู้ใช้งานให้กับผู้อนุมัติ โดยใช้ ID ของผู้อนุมัติที่ระบุ
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function create($id)
    {
        $approver = Approver::find($id);

        $users = User::whereDoesntHave('approvers', function ($query) use ($id) {
            $query->where('approver_id', $id);
        })->paginate(5000);

        return view('setting.organization.approver.assignment.create', [
            'users' => $users,
            'approver' => $approver
        ]);
    }

    /**
     * บันทึกการมอบหมายผู้ใช้งานให้กับผู้อนุมัติ
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $selectedUsers = $request->users;
        $approverId = $request->approverId;

        $approver = Approver::find($approverId);
        $approver->users()->attach($selectedUsers);

        return redirect()->to('setting/organization/approver/assignment/' . $approverId);
    }

    /**
     * ลบการมอบหมายผู้ใช้งานจากผู้อนุมัติ
     *
     * @param \Illuminate\Http\Request $request
     * @param int $approverId
     * @param int $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, $approverId, $userId)
    {
        $approver = Approver::findOrFail($approverId);
        $user = User::findOrFail($userId);

        $approver->users()->detach($user);

        return redirect()->to('setting/organization/approver/assignment/' . $approverId);
    }

    /**
     * ค้นหาพนักงงาน
     *
     * @return \Illuminate\Http\JsonResponse
     */
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

        return view('setting.organization.approver.assignment.table-render.employee-table', ['users' => $users])->render();
    }



}
