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
                'name' => 'แผนกบริหาร',
                'eng_name' => 'แผนกบริหาร',
                'color' => '#FEAF71',
                'code' => 'MG'
            ],
            [
                'name' => 'แผนกวิศวกรรม',
                'eng_name' => 'แผนกวิศวกรรม',
                'color' => '#FD6F8E',
                'code' => 'ENG'
            ],
            [
                'name' => 'แผนกเทคนิค',
                'eng_name' => 'แผนกเทคนิค',
                'color' => '#F63D68',
                'code' => 'TD'
            ],
            [
                'name' => 'แผนกธุรการ',
                'eng_name' => 'แผนกธุรการ',
                'color' => '#F670C7',
                'code' => 'AD'
            ],
            [
                'name' => 'แผนกบัญชี',
                'eng_name' => 'แผนกบัญชี',
                'color' => '#F670C7',
                'code' => 'ACC'
            ],
            [
                'name' => 'แผนกบุคคล',
                'eng_name' => 'แผนกบุคคล',
                'color' => '#C295DE',
                'code' => 'PS'
            ],
            [
                'name' => 'แผนกควบคุมคุณภาพ',
                'eng_name' => 'แผนกควบคุมคุณภาพ',
                'color' => '#9B8AFB',
                'code' => 'QC'
            ],
            [
                'name' => 'แผนกผลิตสุก',
                'eng_name' => 'แผนกผลิตสุก',
                'color' => '#7C7AD6',
                'code' => 'PDC'
            ],
            [
                'name' => 'แผนกวิจัยและพัฒนาผลิตภัณฑ์',
                'eng_name' => 'แผนกวิจัยและพัฒนาผลิตภัณฑ์',
                'color' => '#444376',
                'code' => 'RD'
            ],
            [
                'name' => 'แผนกคลังสินค้า',
                'eng_name' => 'แผนกคลังสินค้า',
                'color' => '#48E6FE',
                'code' => 'WH'
            ],
            [
                'name' => 'แผนกจัดซื้อ',
                'eng_name' => 'แผนกจัดซื้อ',
                'color' => '#53B1FD',
                'code' => 'PC'
            ],
            [
                'name' => 'แผนก Export',
                'eng_name' => 'แผนก Export',
                'color' => '#1E616B',
                'code' => 'EX'
            ],
            [
                'name' => 'แผนกความปลอดภัย',
                'eng_name' => 'แผนกความปลอดภัย',
                'color' => '#175CD3',
                'code' => 'HS'
            ],
            [
                'name' => 'แผนกโรงงานแปรรูป',
                'eng_name' => 'แผนกโรงงานแปรรูป',
                'color' => '#41B87C',
                'code' => 'PDR'
            ],
            [
                'name' => 'แผนกอนามัย',
                'eng_name' => 'แผนกอนามัย',
                'color' => '#F9F58F',
                'code' => 'CS'
            ],
            [
                'name' => 'แผนกสิ่งแวดล้อม',
                'eng_name' => 'แผนกสิ่งแวดล้อม',
                'color' => '#F5D489',
                'code' => 'ENV'
            ],
            [
                'name' => 'แผนกการตลาด',
                'eng_name' => 'แผนกการตลาด',
                'color' => '#FE9F55',
                'code' => 'MK'
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
