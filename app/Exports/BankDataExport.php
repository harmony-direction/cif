<?php

namespace App\Exports;

use App\Models\User;
use App\Models\BankData;
use App\Models\PaydayDetail;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

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

    public function __construct($year, $month, $type)
    {
        $this->year = $year;
        $this->month = $month;
        $this->type = $type;
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
                $item->hid,
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
