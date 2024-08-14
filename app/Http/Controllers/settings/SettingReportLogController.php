<?php

namespace App\Http\Controllers\Settings;

use Carbon\Carbon;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingReportLogController extends Controller
{
    /**
     * แสดงหน้าตาราง Log
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $logActivities = LogActivity::orderBy('created_at', 'desc')->paginate(5000);
        return view('setting.report.log-activity.index',[
            'logActivities' => $logActivities
        ]);
    }

    public function search(Request $request)
    {
        $queryInput = $request->data;

        $logActivities = LogActivity::where(function ($query) use ($queryInput) {
            $query->where('action', 'like', '%' . $queryInput . '%')
                ->orWhere(function ($query) use ($queryInput) {
                    $query->whereRaw("DATE_FORMAT(created_at, '%d-%m') LIKE ?", ['%' . $queryInput . '%'])
                        ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') LIKE ?", ['%' . $queryInput . '%']);
                })
                ->orWhereHas('user', function ($query) use ($queryInput) {
                    $query->where('name', 'like', '%' . $queryInput . '%')
                        ->orWhere('lastname', 'like', '%' . $queryInput . '%');
                });
        })->paginate(5000);

        return view('setting.report.log-activity.table-render.log-activity-table',['logActivities' => $logActivities])->render();
    }
}
