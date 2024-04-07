<?php

namespace App\Exports;

use App\Models\User;
use App\Models\UserPayday;
use App\Models\PaydayDetail;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class EmployeeSsoExport implements FromCollection, WithHeadings
{
    use Exportable;

    protected $year;
    protected $month;

    public function __construct($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
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
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $paydayDetail = PaydayDetail::whereYear('end_date', $this->year)->pluck('payday_id')->toArray();

        $userIds = [];
        $startDate = $this->year . '-' . $this->month . '-01';
        $endDate = $this->year . '-' . $this->month . '-31';
        $ids = $this->getUsersByWorkScheduleAssignment($startDate, $endDate)->pluck('id')->toArray();
        $userPaydayIds = UserPayday::whereIn('payday_id', $paydayDetail)->pluck('user_id')->toArray();
        $userIddiffs = array_intersect($ids, $userPaydayIds);
        $data = User::whereIn('id', $userIddiffs)->get();
        $returndata = [];

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
        }

        return collect($returndata);
    }

    public function headings(): array
    {
        return [
            'เลขประจำตัวประชาชน',
            'คำนำหน้าชื่อ',
            'ชื่อผู้ประกันตน',
            'นามสกุลผู้ประกันตน',
            'ค่าจ้าง',
            'จำนวนเงินสมทบ',
        ];
    }

    public function encoding(): string
    {
        return 'UTF-8';
    }

    public function title(): string
    {
        return '000000';
    }
}
