<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class CustomPndExport implements FromCollection/* , WithMapping, ShouldAutoSize, WithCustomCsvSettings */
{
    protected $data;

    public function __construct()
    {
        $this->data = User::all();
    }

    public function collection()
    {

        return $this->data->map(function ($row) {
            $rowArray = $row->toArray();
            return array_map(function ($value) {
                return str_replace(['","', '"', ',', '\"', '/["]+/'], '|', trim($value)) ?: '|';
            }, $rowArray);
        });
    }
    public function map($row): array
    {
        $mappedRow = [];

        foreach ($row as $value) {
            $mappedRow[] = str_replace(['","', '"', ',', '\"', '/["]+/'], '|', trim($value)) ?: '|';
        }

        return $mappedRow;
    }
    public function mapCollection($collection): array
    {
        $mappedRow = [];
        foreach ($row as $value) {
            $mappedRow[] = str_replace(['","', '"', ',', '\"', '/["]+/'], '|', trim($value)) ?: '|';
        }
        return $mappedRow;
    }

}
