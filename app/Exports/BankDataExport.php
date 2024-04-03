<?php

namespace App\Exports;

use App\Models\BankData;
use App\Models\User;
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

    public function __construct($year, $month, $payDetailIds)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public function collection()
    {
         // Fetch data from your model and format it
         $data = User::/* whereYear('date_column', $this->year)
         ->whereMonth('date_column', $this->month)
         ->get */all();

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
