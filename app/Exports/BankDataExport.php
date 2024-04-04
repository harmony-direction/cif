<?php

namespace App\Exports;

use App\Models\BankData;
use App\Models\PaydayDetail;
use App\Models\User;
use App\Models\UserPayday;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BankDataExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    /* public function collection()
    {
        return BankData::all();
    } */
    use Exportable;

    protected $year;
    protected $month;
    protected $type;

    public function __construct($year, $month, $type)
    {
        $this->year = $year;
        $this->month = $month;
        $this->type = $type;
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

    public function collection()
    {
        $paydayDetail = PaydayDetail::whereYear('end_date', $this->year)->pluck('payday_id')->toArray();

        $userIds = [];
        $startDate = $this->year . '-' . $this->month . '-01';
        $endDate = $this->year . '-' . $this->month . '-31';
        $ids = $this->getUsersByWorkScheduleAssignment($startDate, $endDate)->pluck('id')->toArray();
        $userPaydayIds = UserPayday::whereIn('payday_id', $paydayDetail)->pluck('user_id')->toArray();
        $userIddiffs = array_intersect($ids, $userPaydayIds);

        //$userIds = array_merge($userIds, $ids);
        if($this->type=='day'){
            $data = User::whereIn('id', $userIddiffs)->where('employee_type_id', 2)->get();
        }elseif($this->type=='month'){
            $data = User::whereIn('id', $userIddiffs)->where('employee_type_id', 1)->get();
        }else{
            $data = User::whereIn('id', $userIddiffs)->get();
        }

        // Format the data to match the structure of the provided array
        $formattedData = $data->map(function ($item) {
        return [
                '006',
                $item->bank_account,
                $item->prefix_id.$item->name.' '.$item->lastname,
                '503.00',
                number_format($item->hid, 0, '.', ''),
                '0000',
                '0000',
                $item->email,
                $item->phone,
            ];
        });

        return $formattedData;
        /* return collect([
            [
                '006',
                '2080189468',
                'น.ส.มลฤดี  บุญลอย',
                '503.00',
                '0000000000000',
                'xxxx',
                '0000',
                'xxxx',
                '0000000000',
            ],
            [
                '006',
                '2080189069',
                'น.ส.เล็ก  วงษ์ลา',
                '1584.00',
                '0000000000000',
                'xxxx',
                '0000',
                'xxxx',
                '0000000000',
            ]
        ]); */
    }

    public function headings(): array
    {
        return [
            'Receiving Bank Code',
            'Receiving A/C No.',
            'Receiver Name',
            'Transfer Amount',
            'Citizen ID/Tax ID',
            'DDA Ref',
            'Reference No./ DDA Ref 2',
            'Email',
            'Mobile No.',
        ];
    }
}
