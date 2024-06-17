<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyDepartment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CompanyDepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createDefaultCompanyDepatments();
    }
     public function createDefaultCompanyDepatments()
    {
        $departments = [
            [
                'name' => 'วิศวกรรม',
                'eng_name' => 'Engineering',
                'color' => '#FEAF71',
                'code' => 'ENG'
            ],
            [
                'name' => 'เทคนิค',
                'eng_name' => 'Technical',
                'color' => '#FD6F8E',
                'code' => 'TD'
            ],
            [
                'name' => 'ธุรการ',
                'eng_name' => 'Administrative',
                'color' => '#F63D68',
                'code' => 'AD'
            ],
            [
                'name' => 'บัญชี',
                'eng_name' => 'Account',
                'color' => '#F670C7',
                'code' => 'ACC'
            ],
            [
                'name' => 'บุคคล',
                'eng_name' => 'Personal',
                'color' => '#F670C7',
                'code' => 'PS'
            ],
            [
                'name' => 'ควบคุมคุณภาพ',
                'eng_name' => 'Quality Control',
                'color' => '#C295DE',
                'code' => 'QC'
            ],
            [
                'name' => 'ผลิตสุก',
                'eng_name' => 'Production cooked Plant',
                'color' => '#9B8AFB',
                'code' => 'PDC'
            ],
            [
                'name' => 'วิจัยและพัฒนาผลิตภัณฑ์',
                'eng_name' => 'Research and Development',
                'color' => '#7C7AD6',
                'code' => 'RD'
            ],
            [
                'name' => 'จัดซื้อ',
                'eng_name' => 'Purchase',
                'color' => '#444376',
                'code' => 'PC'
            ],
            [
                'name' => 'คลังสินค้า',
                'eng_name' => 'Warehouse',
                'color' => '#48E6FE',
                'code' => 'WH'
            ],
            [
                'name' => 'Export',
                'eng_name' => 'Export',
                'color' => '#53B1FD',
                'code' => 'EX'
            ],
            [
                'name' => 'ความปลอดภัย',
                'eng_name' => 'Health and Safety',
                'color' => '#1E616B',
                'code' => 'HS'
            ],
            [
                'name' => 'โรงงานแปรรูป',
                'eng_name' => 'Production Raw Plant',
                'color' => '#175CD3',
                'code' => 'PDR'
            ],
            [
                'name' => 'อนามัย',
                'eng_name' => 'Sanitation',
                'color' => '#41B87C',
                'code' => 'CS'
            ],
            [
                'name' => 'สิ่งแวดล้อม',
                'eng_name' => 'Environment',
                'color' => '#F9F58F',
                'code' => 'ENV'
            ],
            [
                'name' => 'การตลาด',
                'eng_name' => 'Marketing',
                'color' => '#F5D489',
                'code' => 'MK'
            ],
            [
                'name' => 'บริหาร',
                'eng_name' => 'Management',
                'color' => '#FE9F55',
                'code' => 'MG'
            ],
            [
                'name' => 'โรงงานอาหารสุก',
                'eng_name' => 'Production cooked Plant',
                'color' => '#FE9F55',
                'code' => 'PCP'
            ]
        ];

        foreach ($departments as $department) {
            $companyDepartment = new CompanyDepartment();
            $companyDepartment->name = $department['name'];
            $companyDepartment->eng_name = $department['eng_name'];
            $companyDepartment->color = $department['color'];
            $companyDepartment->code = $department['code'];
            $companyDepartment->save();
        }
    }
}
