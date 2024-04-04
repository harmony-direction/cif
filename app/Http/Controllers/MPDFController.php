<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Job;
use App\Models\User;
use App\Models\Month;
use App\Models\Shift;
use App\Models\Module;
use App\Models\Payday;
use App\Models\UserPayday;
use App\Models\PaydayDetail;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use App\Models\RoleGroupJson;
use App\Models\SalarySummary;
use App\Exports\BankDataExport;
use App\Helpers\ActivityLogger;

use App\Exports\CustomPndExport;
use App\Models\CompanyDepartment;
use PhpOffice\PhpWord\Writer\PDF;
use App\Exports\EmployeeSsoExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Excel as ExcelType;
use App\Helpers\AddDefaultWorkScheduleAssignment;
use App\Services\UpdatedRoleGroupCollectionService;

class MPDFController extends Controller
{
    public function monthThai($month, $type)
    {
        if ($type == 'FULL') {
            $thai_months = [
                1 => 'มกราคม',
                2 => 'กุมภาพันธ์',
                3 => 'มีนาคม',
                4 => 'เมษยน.',
                5 => 'พฤษภาคม',
                6 => 'มิถุนายน',
                7 => 'กรกฏาคม',
                8 => 'สิงหาคม',
                9 => 'กันยายน',
                10 => 'ตุลาคม',
                11 => 'พฤศจิกายน',
                12 => 'ธันวาคม',
            ];
        } else {
            $thai_months = [
                1 => 'ม.ค.',
                2 => 'ก.พ.',
                3 => 'มี.ค.',
                4 => 'เม.ย.',
                5 => 'พ.ค.',
                6 => 'มิ.ย.',
                7 => 'ก.ค.',
                8 => 'ส.ค.',
                9 => 'ก.ย.',
                10 => 'ต.ค.',
                11 => 'พ.ย.',
                12 => 'ธ.ค.',
            ];
        }


        return $thai_months[$month];
    }

    public function index()
    {
        // กำหนดค่าตัวแปร $action ให้เป็น 'show'
        $action = 'show';
        // ดึงค่า 'groupUrl' จาก session และแปลงเป็นข้อความ
        $groupUrl = strval(session('groupUrl'));


        $salarySummaries = SalarySummary::all();
        $year = Carbon::now()->year;;
        $paydayDetails = PaydayDetail::whereHas('payday', function ($query) use ($year) {
            $query->where('year', $year);
        })->get();

        $previousMonth = Carbon::now()->month - 1;
        $currentPaydayDetailIds = PaydayDetail::where(function ($query) use ($previousMonth, $year) {
            $query->whereHas('payday', function ($subQuery) use ($year) {
                $subQuery->where('cross_month', 2)
                    ->where('year', $year);
            })->whereMonth('start_date', $previousMonth);

            $query->orWhere(function ($query) use ($previousMonth, $year) {
                $query->whereHas('payday', function ($subQuery) use ($year) {
                    $subQuery->where('cross_month', 1)
                        ->where('year', $year);
                })->whereMonth('start_date', $previousMonth - 1);
            });
        })
            ->pluck('id')
            ->toArray();
        $previousPaydayDetails = PaydayDetail::whereIn('id', $currentPaydayDetailIds)->get();
        $month = Month::find($previousMonth);

        $paydays = Payday::where('year', $year)->get();

        return view('report.index', []);
    }
    //
    public function generate()
    {
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $html = view('mpdf/pdf1')->render();
        $mpdf = new \Mpdf\Mpdf([
            'margin_top' => 10,
            'margin_left' => 8,
            'margin_right' => 8,
            'fontDir' => array_merge($fontDirs, [
                public_path('font'),
            ]),
            'fontdata' => $fontData + [
                'thsarabun' => [
                    'R' => 'THSarabunNew.ttf',
                    'I' => 'THSarabunNew Italic.ttf',
                    'B' => 'THSarabunNew Bold.ttf',
                ]
            ],
            'default_font' => 'thsarabun'
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
        return $mpdf->Output();
    }

    public function getPage($year, $month)
    {
        $id = $year;
        include '../vendor/autoload.php';
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [
                storage_path('fonts/'),
            ]),
            'fontdata' => $fontData + [
                'sarabun' => [
                    'R' => 'THSarabunNew.ttf',
                    'I' => 'THSarabunNew Italic.ttf',
                    'B' => 'THSarabunNew Bold.ttf',
                ]
            ],
            'default_font' => 'sarabun',
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0
        ]);

        ob_start();
        $data = DB::table('users')->whereId($id)->first();
        if (isset($data) && $data) {
            $html = view('report.rd1', compact('data', 'id'))->render();

            $stylesheet = file_get_contents(public_path('css/report/report-5.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->WriteHTML($html, 2);

            /* $pdfFilePath = "report_1.pdf";
            $pdfFile = file_get_contents($pdfFilePath); */
            $mpdf->Output("rd1.pdf", 'F');
            ob_end_clean();


            $pdfFilePath = "rd1.pdf";
            $pdfFile = file_get_contents($pdfFilePath);

            return Response::make($pdfFile, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="rd1.pdf"'
            ]);
        } else {
            echo 'not found data';
        }
    }

    public function bis50list(Request $request, $year = null)
    {
        $action = 'show';
        $groupUrl = strval(session('groupUrl'));
        $permission = (object)[
            'show' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ];
        $viewName = 'report.bis50index';
        if ($year != null) {
            $users = User::paginate(50);
            $years = WorkSchedule::distinct()->pluck('year');
            return view($viewName, [
                'groupUrl' => $groupUrl,
                'permission' => $permission,
                'users' => $users,
                'years' => $years,
                'year' => $year,
            ]);
        }
        $users = User::paginate(50);
        $years = WorkSchedule::distinct()->pluck('year');
        return view($viewName, [
            'groupUrl' => $groupUrl,
            'permission' => $permission,
            'users' => $users,
            'years' => $years,
        ]);
    }
    public function bis50($id, $year)
    {
        include '../vendor/autoload.php';
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [
                storage_path('fonts/'),
            ]),
            'fontdata' => $fontData + [
                'sarabun' => [
                    'R' => 'THSarabunNew.ttf',
                    'I' => 'THSarabunNew Italic.ttf',
                    'B' => 'THSarabunNew Bold.ttf',
                ]
            ],
            'default_font' => 'sarabun',
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0
        ]);

        ob_start();
        /* $data = User::whereId($id)->first();
        $paydayDetail = PaydayDetail::whereYear('end_date', $year)->pluck('payday_id')->toArray();

        $userIds = [];
        $startDate = $year . '-01-01';
        $endDate = $year . '-12-31';
        $ids = $this->getUsersByWorkScheduleAssignment($startDate, $endDate)->pluck('id')->toArray(); */
        //$userPaydayIds = UserPayday::whereIn('payday_id', $paydayDetail)->pluck('user_id')->toArray();
        $data = User::where('id', $id)->first();
        $paydayDetail = PaydayDetail::whereYear('end_date', $year)->pluck('payday_id')->toArray();
        $salarySummary = array(
            'workHour' => 0,
            'absentCountSum' => 0,
            'leaveCountSum' => 0,
            'earlyHour' => 0,
            'lateHour' => 0,
            'overTime' => 0,
            'deligenceAllowance' => 0,
            'salary' => 0,
            'overTimeCost' => 0,
            'socialSecurityFivePercent' => 0,
            'exceedOvertime' => 0,
            'exceedOverTimeCost' => 0
        );
        $incomes = 0;
        $deducts = 0;


        foreach($paydayDetail as $item){
            $salarySummary['workHour'] += $data->salarySummaryYear($item)['workHour'];
            $salarySummary['absentCountSum'] += $data->salarySummaryYear($item)['absentCountSum'];
            $salarySummary['leaveCountSum'] += $data->salarySummaryYear($item)['leaveCountSum'];
            $salarySummary['earlyHour'] += $data->salarySummaryYear($item)['earlyHour'];
            $salarySummary['lateHour'] += $data->salarySummaryYear($item)['lateHour'];
            $salarySummary['overTime'] += $data->salarySummaryYear($item)['overTime'];
            $salarySummary['deligenceAllowance'] += $data->salarySummaryYear($item)['deligenceAllowance'];
            $salarySummary['salary'] += str_replace(',', '', isset($data->salarySummaryYear($item)['salary']) ? $data->salarySummaryYear($item)['salary']:0);
            $salarySummary['overTimeCost'] += $data->salarySummaryYear($item)['overTimeCost'];
            $salarySummary['socialSecurityFivePercent'] += $data->salarySummaryYear($item)['socialSecurityFivePercent'];
            $salarySummary['exceedOvertime'] += $data->salarySummaryYear($item)['exceedOvertime'];
            $salarySummary['exceedOverTimeCost'] += $data->salarySummaryYear($item)['exceedOverTimeCost'];
            $incomes = $data->getSummaryIncomeDeductByUsers(1,$item);
            $deducts = $data->getSummaryIncomeDeductByUsers(2,$item);
        }

        $data = ['data'=>$data,'paydayDetail' => $paydayDetail, 'salarySummary' => $salarySummary, 'incomes' => $incomes, 'deducts'=> $deducts];
        /* $returndata = [];

        foreach ($data as $item) {
            $rowData = [
                'passport' => $item->nationality_id,
                'prefix' => $item->prefix->name,
                'name' => $item->name,
                'lastname' => $item->name, // This might be incorrect, should it be $item->lastname?
                'bank_account' => isset($item->salarySummary($this->month)['salary']) ? $item->salarySummary($this->month)['salary']:0,
                'bank' => isset($item->salarySummary($this->month)['socialSecurityFivePercent']) ? $item->salarySummary($this->month)['socialSecurityFivePercent']:0,
            ];

            $returndata[] = $rowData;
        } */
        if (isset($data) && $data) {
            $html = view('report.bis50-2', compact('data', 'year'))->render();

            $stylesheet = file_get_contents(public_path('css/report/bis50.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->WriteHTML($html, 2);

            /* $pdfFilePath = "report_1.pdf";
            $pdfFile = file_get_contents($pdfFilePath); */
            $mpdf->Output("report_1.pdf", 'F');
            ob_end_clean();


            $pdfFilePath = "report_1.pdf";
            $pdfFile = file_get_contents($pdfFilePath);

            return Response::make($pdfFile, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="report_1.pdf"'
            ]);
        } else {
            echo 'not found data';
        }
    }

    public function pndindexyear()
    {

        $action = 'show';
        $groupUrl = strval(session('groupUrl'));
        $permission = (object)[
            'show' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ];
        $viewName = 'report.pndindexyear';
        $users = User::paginate(50);

        // ค้นหาปีที่มีการกำหนดงานเรียกงานอย่างน้อยหนึ่งรอบ
        $years = WorkSchedule::distinct()->pluck('year');

        // ค้นหาเดือนทั้งหมด
        $months = Month::all();

        // ค้นหาปีปัจจุบัน
        $currentYear = Carbon::now()->year;

        // ค้นหาเดือนปัจจุบัน
        $currentMonth = Carbon::now()->month;

        // ค้นหา workSchedules ที่มีการกำหนดงานเรียกงานในปีและเดือนปัจจุบัน
        $workSchedules = WorkSchedule::whereHas('assignments', function ($query) use ($currentYear, $currentMonth) {
            $query->where('year', $currentYear)
                ->where('month_id', $currentMonth)
                ->whereNotNull('shift_id');
        })->get();



        return view($viewName, [
            'groupUrl' => $groupUrl,
            'permission' => $permission,
            'users' => $users,
            'years' => $years,
            'months' => $months,
            'workSchedules' => $workSchedules,
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth
        ]);
    }

    public function pndindex()
    {

        $action = 'show';
        $groupUrl = strval(session('groupUrl'));
        $permission = (object)[
            'show' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ];
        $viewName = 'report.pndindex';
        $users = User::paginate(50);

        // ค้นหาปีที่มีการกำหนดงานเรียกงานอย่างน้อยหนึ่งรอบ
        $years = WorkSchedule::distinct()->pluck('year');

        // ค้นหาเดือนทั้งหมด
        $months = Month::all();

        // ค้นหาปีปัจจุบัน
        $currentYear = Carbon::now()->year;

        // ค้นหาเดือนปัจจุบัน
        $currentMonth = Carbon::now()->month;

        // ค้นหา workSchedules ที่มีการกำหนดงานเรียกงานในปีและเดือนปัจจุบัน
        $workSchedules = WorkSchedule::whereHas('assignments', function ($query) use ($currentYear, $currentMonth) {
            $query->where('year', $currentYear)
                ->where('month_id', $currentMonth)
                ->whereNotNull('shift_id');
        })->get();



        return view($viewName, [
            'groupUrl' => $groupUrl,
            'permission' => $permission,
            'users' => $users,
            'years' => $years,
            'months' => $months,
            'workSchedules' => $workSchedules,
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth
        ]);
        /* return view('report.rd1', compact('id')); */
    }

    public function pnd($year, $month)
    {
        $data = User::all();

        $filePath = public_path($year . $month . 'pnd');
        $file = fopen($filePath, 'w');
        foreach ($data as $item) {
            $line = implode('|', $item->toArray());
            fwrite($file, $line . PHP_EOL);
        }

        fclose($file);
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function pndyear($year)
    {
        $data = User::all();

        $filePath = public_path($year . 'pnd');
        $file = fopen($filePath, 'w');
        foreach ($data as $item) {
            $line = implode('|', $item->toArray());
            fwrite($file, $line . PHP_EOL);
        }

        fclose($file);
        return response()->download($filePath)->deleteFileAfterSend(true);
    }



    public function rd1index()
    {

        $action = 'show';
        $groupUrl = strval(session('groupUrl'));
        $permission = (object)[
            'show' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ];
        $viewName = 'report.rd1index';
        $users = User::paginate(50);

        // ค้นหาปีที่มีการกำหนดงานเรียกงานอย่างน้อยหนึ่งรอบ
        $years = WorkSchedule::distinct()->pluck('year');

        // ค้นหาเดือนทั้งหมด
        $months = Month::all();

        // ค้นหาปีปัจจุบัน
        $currentYear = Carbon::now()->year;

        // ค้นหาเดือนปัจจุบัน
        $currentMonth = Carbon::now()->month;

        // ค้นหา workSchedules ที่มีการกำหนดงานเรียกงานในปีและเดือนปัจจุบัน
        $workSchedules = WorkSchedule::whereHas('assignments', function ($query) use ($currentYear, $currentMonth) {
            $query->where('year', $currentYear)
                ->where('month_id', $currentMonth)
                ->whereNotNull('shift_id');
        })->get();

        $currentYear = Carbon::now()->year;
        $payDays = Payday::where('year', $currentYear)->get();
        $distinctYears = Payday::distinct('year')->pluck('year');

        return view($viewName, [
            'groupUrl' => $groupUrl,
            'permission' => $permission,
            'users' => $users,
            'years' => $years,
            'months' => $months,
            'workSchedules' => $workSchedules,
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth,
            'distinctYears' => $distinctYears,
            'paydays' => $payDays,
            'years' => $distinctYears,
            'selectedYear' => $currentYear,
        ]);
        /* return view('report.rd1', compact('id')); */
    }

    public function rd1indexyear()
    {

        $action = 'show';
        $groupUrl = strval(session('groupUrl'));
        $permission = (object)[
            'show' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ];
        $viewName = 'report.rd1indexyear';
        $users = User::paginate(50);

        // ค้นหาปีที่มีการกำหนดงานเรียกงานอย่างน้อยหนึ่งรอบ
        $years = WorkSchedule::distinct()->pluck('year');

        // ค้นหาเดือนทั้งหมด
        $months = Month::all();

        // ค้นหาปีปัจจุบัน
        $currentYear = Carbon::now()->year;

        // ค้นหาเดือนปัจจุบัน
        $currentMonth = Carbon::now()->month;

        // ค้นหา workSchedules ที่มีการกำหนดงานเรียกงานในปีและเดือนปัจจุบัน
        $workSchedules = WorkSchedule::whereHas('assignments', function ($query) use ($currentYear, $currentMonth) {
            $query->where('year', $currentYear)
                ->where('month_id', $currentMonth)
                ->whereNotNull('shift_id');
        })->get();



        return view($viewName, [
            'groupUrl' => $groupUrl,
            'permission' => $permission,
            'users' => $users,
            'years' => $years,
            'months' => $months,
            'workSchedules' => $workSchedules,
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth
        ]);
        /* return view('report.rd1', compact('id')); */
    }

    public function rd1($year, $month, $paydayDetailIds)
    {
        $id = $year;
        include '../vendor/autoload.php';
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [
                storage_path('fonts/'),
            ]),
            'fontdata' => $fontData + [
                'sarabun' => [
                    'R' => 'THSarabunNew.ttf',
                    'I' => 'THSarabunNew Italic.ttf',
                    'B' => 'THSarabunNew Bold.ttf',
                ]
            ],
            'default_font' => 'sarabun',
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0
        ]);

        ob_start();
        $paydayDetail = PaydayDetail::find($paydayDetailIds);

        $userIds = [];
        $startDate = $paydayDetail->start_date;
        $endDate = $paydayDetail->end_date;
        $ids = $this->getUsersByWorkScheduleAssignment($startDate, $endDate)->pluck('id')->toArray();
        $userPaydayIds = UserPayday::where('payday_id', $paydayDetail->payday_id)->pluck('user_id')->toArray();
        $userIddiffs = array_intersect($ids, $userPaydayIds);

        // $userIds = array_merge($userIds, $ids);
        $userIds = array_unique($userIddiffs);
        // $userIds = array_unique($userIds);
        $users = User::whereIn('id', $userIds)->get();
        $companyDepartmentIds = array_unique($users->pluck('company_department_id')->toArray());
        $companyDepartments = CompanyDepartment::whereIn('id', $companyDepartmentIds)->get();

        if (isset($users) && isset($companyDepartments) && isset($paydayDetail)) {
            $html = view('report.rd1', compact('users', 'companyDepartments', 'paydayDetail'))->render();

            $stylesheet = file_get_contents(public_path('css/report/report-5.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->WriteHTML($html, 2);

            /* $pdfFilePath = "report_1.pdf";
            $pdfFile = file_get_contents($pdfFilePath); */
            $mpdf->Output("rd1.pdf", 'F');
            ob_end_clean();


            $pdfFilePath = "rd1.pdf";
            $pdfFile = file_get_contents($pdfFilePath);

            return Response::make($pdfFile, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="rd1.pdf"'
            ]);
        } else {
            echo 'not found data';
        }
    }

    public function rd1year($year)
    {
        $id = $year;
        include '../vendor/autoload.php';
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [
                storage_path('fonts/'),
            ]),
            'fontdata' => $fontData + [
                'sarabun' => [
                    'R' => 'THSarabunNew.ttf',
                    'I' => 'THSarabunNew Italic.ttf',
                    'B' => 'THSarabunNew Bold.ttf',
                ]
            ],
            'default_font' => 'sarabun',
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0
        ]);

        ob_start();
        $paydayDetail = PaydayDetail::whereYear('end_date', $year)->first();

        $userIds = [];
        $startDate = $year.'-01-01';
        $endDate = $year.'-12-31';
        $ids = $this->getUsersByWorkScheduleAssignment($startDate, $endDate)->pluck('id')->toArray();
        $userPaydayIds = UserPayday::where('payday_id', $paydayDetail->payday_id)->pluck('user_id')->toArray();
        $userIddiffs = array_intersect($ids, $userPaydayIds);

        // $userIds = array_merge($userIds, $ids);
        $userIds = array_unique($userIddiffs);
        // $userIds = array_unique($userIds);
        $users = User::whereIn('id', $userIds)->get();
        $companyDepartmentIds = array_unique($users->pluck('company_department_id')->toArray());
        $companyDepartments = CompanyDepartment::whereIn('id', $companyDepartmentIds)->get();

        if (isset($users) && isset($companyDepartments) && isset($paydayDetail)) {
            $html = view('report.rd1', compact('users', 'companyDepartments', 'paydayDetail'))->render();

            $stylesheet = file_get_contents(public_path('css/report/report-5.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->WriteHTML($html, 2);

            /* $pdfFilePath = "report_1.pdf";
            $pdfFile = file_get_contents($pdfFilePath); */
            $mpdf->Output("rd1.pdf", 'F');
            ob_end_clean();


            $pdfFilePath = "rd1.pdf";
            $pdfFile = file_get_contents($pdfFilePath);

            return Response::make($pdfFile, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="rd1.pdf"'
            ]);
        } else {
            echo 'not found data';
        }
    }

    public function rd2index()
    {

        $action = 'show';
        $groupUrl = strval(session('groupUrl'));
        $permission = (object)[
            'show' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ];
        $viewName = 'report.rd2index';
        $users = User::paginate(50);

        // ค้นหาปีที่มีการกำหนดงานเรียกงานอย่างน้อยหนึ่งรอบ
        $years = WorkSchedule::distinct()->pluck('year');

        // ค้นหาเดือนทั้งหมด
        $months = Month::all();

        // ค้นหาปีปัจจุบัน
        $currentYear = Carbon::now()->year;

        // ค้นหาเดือนปัจจุบัน
        $currentMonth = Carbon::now()->month;

        // ค้นหา workSchedules ที่มีการกำหนดงานเรียกงานในปีและเดือนปัจจุบัน
        $workSchedules = WorkSchedule::whereHas('assignments', function ($query) use ($currentYear, $currentMonth) {
            $query->where('year', $currentYear)
                ->where('month_id', $currentMonth)
                ->whereNotNull('shift_id');
        })->get();



        return view($viewName, [
            'groupUrl' => $groupUrl,
            'permission' => $permission,
            'users' => $users,
            'years' => $years,
            'months' => $months,
            'workSchedules' => $workSchedules,
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth
        ]);
        /* return view('report.rd1', compact('id')); */
    }

    public function rd2indexyear()
    {

        $action = 'show';
        $groupUrl = strval(session('groupUrl'));
        $permission = (object)[
            'show' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ];
        $viewName = 'report.rd2indexyear';
        $users = User::paginate(50);

        // ค้นหาปีที่มีการกำหนดงานเรียกงานอย่างน้อยหนึ่งรอบ
        $years = WorkSchedule::distinct()->pluck('year');

        // ค้นหาเดือนทั้งหมด
        $months = Month::all();

        // ค้นหาปีปัจจุบัน
        $currentYear = Carbon::now()->year;

        // ค้นหาเดือนปัจจุบัน
        $currentMonth = Carbon::now()->month;

        // ค้นหา workSchedules ที่มีการกำหนดงานเรียกงานในปีและเดือนปัจจุบัน
        $workSchedules = WorkSchedule::whereHas('assignments', function ($query) use ($currentYear, $currentMonth) {
            $query->where('year', $currentYear)
                ->where('month_id', $currentMonth)
                ->whereNotNull('shift_id');
        })->get();



        return view($viewName, [
            'groupUrl' => $groupUrl,
            'permission' => $permission,
            'users' => $users,
            'years' => $years,
            'months' => $months,
            'workSchedules' => $workSchedules,
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth
        ]);
        /* return view('report.rd1', compact('id')); */
    }

    public function rd2($year, $month)
    {
        $id = $year;
        include '../vendor/autoload.php';
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [
                storage_path('fonts/'),
            ]),
            'fontdata' => $fontData + [
                'sarabun' => [
                    'R' => 'THSarabunNew.ttf',
                    'I' => 'THSarabunNew Italic.ttf',
                    'B' => 'THSarabunNew Bold.ttf',
                ]
            ],
            'default_font' => 'sarabun',
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0
        ]);

        ob_start();
        $paydayIds = PaydayDetail::whereYear('end_date', $year)->where('month_id', $month)->pluck('id')->toArray();
        $data = SalarySummary::whereIn('payday_detail_id', $paydayIds)->get();
        $data = [
            'employee' => $data->sum('employee'),
            'sum_salary' => $data->sum('sum_salary'),
            'sum_social_security' => $data->sum('sum_social_security'),
            'sum_leave' => $data->sum('sum_leave'),
        ];

        if (isset($data) && $data) {
            $month = $this->monthThai($month, 'FULL');
            $html = view('report.rd2', compact('data', 'id', 'year', 'month'))->render();

            $stylesheet = file_get_contents(public_path('css/report/report-4.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->WriteHTML($html, 2);

            /* $pdfFilePath = "report_1.pdf";
            $pdfFile = file_get_contents($pdfFilePath); */
            $mpdf->Output("rd2.pdf", 'F');
            ob_end_clean();


            $pdfFilePath = "rd2.pdf";
            $pdfFile = file_get_contents($pdfFilePath);

            return Response::make($pdfFile, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="rd1.pdf"'
            ]);
        } else {
            echo 'not found data';
        }
        /* return view('report.rd2', compact('id')); */
    }

    public function rd2year($year)
    {
        $id = $year;
        include '../vendor/autoload.php';
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [
                storage_path('fonts/'),
            ]),
            'fontdata' => $fontData + [
                'sarabun' => [
                    'R' => 'THSarabunNew.ttf',
                    'I' => 'THSarabunNew Italic.ttf',
                    'B' => 'THSarabunNew Bold.ttf',
                ]
            ],
            'default_font' => 'sarabun',
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0
        ]);

        ob_start();
        $paydayIds = PaydayDetail::whereYear('end_date', $year)->pluck('id')->toArray();
        $data = SalarySummary::whereIn('payday_detail_id', $paydayIds)->get();
        $data = [
            'employee' => $data->sum('employee'),
            'sum_salary' => $data->sum('sum_salary'),
            'sum_social_security' => $data->sum('sum_social_security'),
            'sum_leave' => $data->sum('sum_leave'),
        ];

        if (isset($data) && $data) {
            $html = view('report.rd2', compact('data', 'id'))->render();

            $stylesheet = file_get_contents(public_path('css/report/report-4.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->WriteHTML($html, 2);

            /* $pdfFilePath = "report_1.pdf";
            $pdfFile = file_get_contents($pdfFilePath); */
            $mpdf->Output("rd2.pdf", 'F');
            ob_end_clean();


            $pdfFilePath = "rd2.pdf";
            $pdfFile = file_get_contents($pdfFilePath);

            return Response::make($pdfFile, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="rd1.pdf"'
            ]);
        } else {
            echo 'not found data';
        }
        /* return view('report.rd2', compact('id')); */
    }


    ########################################## sso ##############################################

    public function ssoPaymentIndex($list, $type)
    {

        $action = 'show';
        $groupUrl = strval(session('groupUrl'));
        $permission = (object)[
            'show' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ];
        $viewName = 'report.sso_' . $list;
        $users = User::paginate(50);

        // ค้นหาปีที่มีการกำหนดงานเรียกงานอย่างน้อยหนึ่งรอบ
        $years = WorkSchedule::distinct()->pluck('year');

        // ค้นหาเดือนทั้งหมด
        $months = Month::all();

        // ค้นหาปีปัจจุบัน
        $currentYear = Carbon::now()->year;

        // ค้นหาเดือนปัจจุบัน
        $currentMonth = Carbon::now()->month;

        // ค้นหา workSchedules ที่มีการกำหนดงานเรียกงานในปีและเดือนปัจจุบัน
        $workSchedules = WorkSchedule::whereHas('assignments', function ($query) use ($currentYear, $currentMonth) {
            $query->where('year', $currentYear)
                ->where('month_id', $currentMonth)
                ->whereNotNull('shift_id');
        })->get();

        if ($type == 'day') {
            $typeData = 'รายวัน';
        } elseif ($type == 'month') {
            $typeData = 'รายเดือน';
        } elseif ($type == 'all') {
            $typeData = 'รวม';
        }

        return view($viewName, [
            'groupUrl' => $groupUrl,
            'permission' => $permission,
            'users' => $users,
            'years' => $years,
            'type' => $type,
            'typeData' => $typeData,
            'months' => $months,
            'workSchedules' => $workSchedules,
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth
        ]);
        /* return view('report.rd1', compact('id')); */
    }

    public function ssoPaymentList($list, $type)
    {

        $action = 'show';
        $groupUrl = strval(session('groupUrl'));
        $permission = (object)[
            'show' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ];
        $viewName = 'report.sso_' . $list;
        $users = User::paginate(50);

        // ค้นหาปีที่มีการกำหนดงานเรียกงานอย่างน้อยหนึ่งรอบ
        $years = WorkSchedule::distinct()->pluck('year');

        // ค้นหาเดือนทั้งหมด
        $months = Month::all();

        // ค้นหาปีปัจจุบัน
        $currentYear = Carbon::now()->year;

        // ค้นหาเดือนปัจจุบัน
        $currentMonth = Carbon::now()->month;

        // ค้นหา workSchedules ที่มีการกำหนดงานเรียกงานในปีและเดือนปัจจุบัน
        $workSchedules = WorkSchedule::whereHas('assignments', function ($query) use ($currentYear, $currentMonth) {
            $query->where('year', $currentYear)
                ->where('month_id', $currentMonth)
                ->whereNotNull('shift_id');
        })->get();

        if ($type == 'day') {
            $typeData = 'รายวัน';
        } elseif ($type == 'month') {
            $typeData = 'รายเดือน';
        } elseif ($type == 'all') {
            $typeData = 'รวม';
        }

        return view($viewName, [
            'groupUrl' => $groupUrl,
            'permission' => $permission,
            'users' => $users,
            'years' => $years,
            'type' => $type,
            'typeData' => $typeData,
            'months' => $months,
            'workSchedules' => $workSchedules,
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth
        ]);
        /* return view('report.rd1', compact('id')); */
    }

    public function ssoPayment_list($year, $month, $type)
    {

        $id = $year;
        include '../vendor/autoload.php';
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [
                storage_path('fonts/'),
            ]),
            'fontdata' => $fontData + [
                'sarabun' => [
                    'R' => 'THSarabunNew.ttf',
                    'I' => 'THSarabunNew Italic.ttf',
                    'B' => 'THSarabunNew Bold.ttf',
                ]
            ],
            'default_font' => 'sarabun',
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0
        ]);

        ob_start();
        /* $paydayIds = PaydayDetail::whereYear('end_date', $year)->where('month_id', $month)->pluck('id')->toArray();
        $userPayDay = UserPayday::whereIn('payday_id', $paydayIds)->pluck('user_id')->toArray(); */
        $paydayDetail = PaydayDetail::whereYear('end_date', $year)->pluck('payday_id')->toArray();

        $userIds = [];
        $startDate = $year . '-' . $month . '-01';
        $endDate = $year . '-' . $month . '-31';
        $ids = $this->getUsersByWorkScheduleAssignment($startDate, $endDate)->pluck('id')->toArray();
        $userPaydayIds = UserPayday::whereIn('payday_id', $paydayDetail)->pluck('user_id')->toArray();
        $userIddiffs = array_intersect($ids, $userPaydayIds);

        if($type=='day'){
            $userData = User::whereIn('id', $userIddiffs)->where('employee_type_id', 2)->get();
        }elseif($type=='month'){
            $userData = User::whereIn('id', $userIddiffs)->where('employee_type_id', 1)->get();
        }else{
            $userData = User::whereIn('id', $userIddiffs)->get();
        }

        /* $data = SalarySummary::whereIn('payday_detail_id', $paydayIds)->get(); */
        $sum_salary_total = 0;
        $sum_social_security_total = 0;
        $sum_leave_total = 0;

        foreach ($userData as $item) {
            $sum_salary_total += $item->salarySummary($item->id)['salary'];
            $sum_social_security_total += $item->salarySummary($item->id)['socialSecurityFivePercent'];
            $sum_leave_total += $item->salarySummary($item->id)['leaveCountSum'];
        }

        $data = [
            'employee' => count($userData),
            'sum_salary' => $sum_salary_total,
            'sum_social_security' => $sum_social_security_total,
            'sum_leave' => $sum_leave_total,
        ];


        if (isset($data) && $data) {
            $month = $this->monthThai($month, 'FULL');
            $html = view('report.sso1', compact('data', 'id', 'year', 'month'))->render();

            $stylesheet = file_get_contents(public_path('/css/report/sso1.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->WriteHTML($html, 2);

            /* $pdfFilePath = "report_1.pdf";
            $pdfFile = file_get_contents($pdfFilePath); */
            $mpdf->Output("rd2.pdf", 'F');
            ob_end_clean();


            $pdfFilePath = "rd2.pdf";
            $pdfFile = file_get_contents($pdfFilePath);

            return Response::make($pdfFile, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="rd1.pdf"'
            ]);
        } else {
            echo 'not found data';
        }
        /*  return view('report.sso1', compact('id')); */
    }

    public function ssoPayment($year, $month, $type)
    {
        $paydayDetail = PaydayDetail::whereYear('end_date', $year)->pluck('payday_id')->toArray();

        $userIds = [];
        $startDate = $year . '-' . $month . '-01';
        $endDate = $year . '-' . $month . '-31';
        $ids = $this->getUsersByWorkScheduleAssignment($startDate, $endDate)->pluck('id')->toArray();
        $userPaydayIds = UserPayday::whereIn('payday_id', $paydayDetail)->pluck('user_id')->toArray();
        $userIddiffs = array_intersect($ids, $userPaydayIds);

        //$userIds = array_merge($userIds, $ids);
        if($type=='day'){
            $data = User::whereIn('id', $userIddiffs)->where('employee_type_id', 2)->get();
        }elseif($type=='month'){
            $data = User::whereIn('id', $userIddiffs)->where('employee_type_id', 1)->get();
        }else{
            $data = User::whereIn('id', $userIddiffs)->get();
        }


        $summany = [
            'workHour' => 0,
            'absentCountSum' => 0,
            'leaveCountSum' => 0,
            'earlyHour' => 0,
            'lateHour' => 0,
            'overTime' => 0,
            'deligenceAllowance' => 0,
            'salary' => 0,
            'overTimeCost' => 0,
            'socialSecurityFivePercent' => 0,
            'exceedOvertime' => 0,
            'exceedOverTimeCost' => 0,
        ];

        foreach ($data as $employee) {
            $dataSummary = $employee->salarySummary($employee->id);
            $summany['workHour'] += isset($dataSummary['workHour']) ? $dataSummary['workHour'] : 0;
            $summany['absentCountSum'] += isset($dataSummary['absentCountSum']) ? $dataSummary['absentCountSum'] : 0;
            $summany['leaveCountSum'] += isset($dataSummary['leaveCountSum']) ? $dataSummary['leaveCountSum'] : 0;
            $summany['earlyHour'] += isset($dataSummary['earlyHour']) ? $dataSummary['earlyHour'] : 0;
            $summany['lateHour'] += isset($dataSummary['lateHour']) ? $dataSummary['lateHour'] : 0;
            $summany['overTime'] += isset($dataSummary['overTime']) ? $dataSummary['overTime'] : 0;
            $summany['deligenceAllowance'] += isset($dataSummary['deligenceAllowance']) ? $dataSummary['deligenceAllowance'] : 0;
            $summany['salary'] += isset($dataSummary['salary']) ? $dataSummary['salary'] : 0;
            $summany['overTimeCost'] += isset($dataSummary['overTimeCost']) ? $dataSummary['overTimeCost'] : 0;
            $summany['socialSecurityFivePercent'] += isset($dataSummary['socialSecurityFivePercent']) ? $dataSummary['socialSecurityFivePercent'] : 0;
            $summany['exceedOvertime'] += isset($dataSummary['exceedOvertime']) ? $dataSummary['exceedOvertime'] : 0;
            $summany['exceedOverTimeCost'] += isset($dataSummary['exceedOverTimeCost']) ? $dataSummary['exceedOverTimeCost'] : 0;
        }
        $id = $year;
        include '../vendor/autoload.php';
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [
                storage_path('fonts/'),
            ]),
            'fontdata' => $fontData + [
                'sarabun' => [
                    'R' => 'THSarabunNew.ttf',
                    'I' => 'THSarabunNew Italic.ttf',
                    'B' => 'THSarabunNew Bold.ttf',
                ]
            ],
            'default_font' => 'sarabun',
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0
        ]);

        ob_start();
        if (isset($data) && $data) {
            $month = $this->monthThai($month, 'FULL');
            $html = view('report.sso2', compact('data', 'id', 'month', 'year'))->render();

            $stylesheet = file_get_contents(public_path('/css/report/sso2.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->WriteHTML($html, 2);

            /* $pdfFilePath = "report_1.pdf";
            $pdfFile = file_get_contents($pdfFilePath); */
            $mpdf->Output("sso2.pdf", 'F');
            ob_end_clean();


            $pdfFilePath = "sso2.pdf";
            $pdfFile = file_get_contents($pdfFilePath);

            return Response::make($pdfFile, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="rd1.pdf"'
            ]);
        } else {
            echo 'not found data';
        }
        return view('report.sso2', compact('id'));
    }

    public function ssoPaymentMonth($id)
    {
        return view('report.sso2', compact('id'));
    }

    public function ssofile($year, $month)
    {
        return Excel::download(new EmployeeSsoExport($year, $month), 'sso.xlsx');
    }

    #######################################################################################################endregion

    public function getReport($list, $type)
    {

        $action = 'show';
        $groupUrl = strval(session('groupUrl'));
        $permission = (object)[
            'show' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ];
        $viewName = 'report.' . $list;
        $users = User::paginate(50);

        // ค้นหาปีที่มีการกำหนดงานเรียกงานอย่าง+น้อยหนึ่งรอบ
        $years = WorkSchedule::distinct()->pluck('year');

        // ค้นหาเดือนทั้งหมด
        $months = Month::all();

        // ค้นหาปีปัจจุบัน
        $currentYear = Carbon::now()->year;

        // ค้นหาเดือนปัจจุบัน
        $currentMonth = Carbon::now()->month;

        // ค้นหา workSchedules ที่มีการกำหนดงานเรียกงานในปีและเดือนปัจจุบัน
        $workSchedules = WorkSchedule::whereHas('assignments', function ($query) use ($currentYear, $currentMonth) {
            $query->where('year', $currentYear)
                ->where('month_id', $currentMonth)
                ->whereNotNull('shift_id');
        })->get();

        if ($list == 'ipay') {
            $listData = 'IPAY';
        } elseif ($list == 'cashBank') {
            $listData = 'รายงานโอนเงินเข้าธนาคาร';
        }

        return view($viewName, [
            'groupUrl' => $groupUrl,
            'permission' => $permission,
            'users' => $users,
            'years' => $years,
            'type' => $type,
            'typeData' => $listData,
            'months' => $months,
            'workSchedules' => $workSchedules,
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth
        ]);
    }

    public function cashBank($id)
    {
        include '../vendor/autoload.php';
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [
                storage_path('fonts/'),
            ]),
            'fontdata' => $fontData + [
                'sarabun' => [
                    'R' => 'THSarabunNew.ttf',
                    'I' => 'THSarabunNew Italic.ttf',
                    'B' => 'THSarabunNew Bold.ttf',
                ]
            ],
            'default_font' => 'sarabun',
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 5,
            'margin_bottom' => 5,
            'margin_header' => 0,
            'margin_footer' => 3
        ]);
        $data = User::all();
        $html = View::make('report.cashbank_file', compact('data'))->render();

        // Create an instance of mPDF

        // Set headers and footers
        /* $mpdf->SetHeader('บริษัท ฉวีวรรณ อินเตอร์เนชั่นแนลฟู๊ดส์ จำกัด'); */
        $mpdf->SetHTMLFooter('<div style="margin:20px 30px 20px 20px; font-size: 14px; text-align: left; border: 0;"><b>พิมพ์วันที่</b> &nbsp;&nbsp;&nbsp;&nbsp;  {DATE j/m/Y H:i} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>รายงานโดย</b> &nbsp;&nbsp; business &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>แฟ้มรายงาน</b> file path</div>');


        // Write PDF content
        $mpdf->WriteHTML($html);

        // Output the PDF
        $mpdf->Output('bank.pdf', 'I');
        /* return view('report.sso2', compact('id')); */
        ob_end_clean();
        $pdfFilePath = "bank.pdf";
        $pdfFile = file_get_contents($pdfFilePath);
        return Response::make($pdfFile, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="report_1.pdf"'
        ]);
    }

    public function ipay($year, $month, $type)
    {
        return Excel::download(new BankDataExport($year, $month, $type), 'bank_data.xlsx');
    }

    public function getUsersByWorkScheduleAssignment($startDate, $endDate)
    {
        // Convert the start and end date to the correct format
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = date('Y-m-d', strtotime($endDate));

        // ค้นหาผู้ใช้ที่มีการกำหนดงานเรียกงานใน workScheduleId และ date_in อยู่ในช่วง startDate ถึง endDate
        $users = User::whereHas('workScheduleAssignmentUsers', function ($query) use ($startDate, $endDate) {
            $query->whereNotNull('date_in')
                ->whereBetween('date_in', [$startDate, $endDate]);
        })->get();

        return $users;
    }
}
