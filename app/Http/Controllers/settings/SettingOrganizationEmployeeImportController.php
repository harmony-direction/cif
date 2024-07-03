<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Imports\EmployeeImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class SettingOrganizationEmployeeImportController extends Controller
{
    /**
     * แสดงหน้าตารางสำหรับการนำเข้าข้อมูลพนักงาน
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('setting.organization.employee.import.index');
    }

    /**
     * เก็บข้อมูลการนำเข้าพนักงาน
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $file = $request->file('file');
        if(!$file){
            return redirect()->back();
        }
        $import = new EmployeeImport;
        Excel::import($import, $file);

        $successCount = $import->getSuccessCount();
        $errorCount = $import->getErrorCount();
        $errorRows = $import->getErrorRows();
        $errorMessages = $import->getErrorMessages();
        $errorUsers = $import->getErrorUsers();

        if ($errorCount > 0) {
            return redirect()->back()->withErrors([
                'message' => 'การนำเข้าข้อมูลล้มเหลว',
                'error_count' => $errorCount,
                'error_rows' => $errorRows,
                'error_messages' => $errorMessages,
                'error_users' => $errorUsers,
            ]);
        } else {
            return redirect()->route('setting.organization.employee.index', [
                'message' => 'นำเข้าข้อมูลเรียบร้อยแล้ว',
                'success_count' => $successCount,
            ]);
        }
    }

}
