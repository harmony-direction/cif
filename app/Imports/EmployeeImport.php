<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Prefix;
use App\Models\Ethnicity;
use App\Models\UserLeave;
use App\Models\Nationality;
use App\Models\EmployeeType;
use App\Models\SalaryRecord;
use App\Models\UserPosition;
use App\Models\LeaveIncrement;
use App\Models\PositionHistory;
use Illuminate\Validation\Rule;
use App\Models\CompanyDepartment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\UserDiligenceAllowance;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeeImport implements ToCollection, WithHeadingRow
{
    private $successCount = 0;
    private $errorCount = 0;
    private $errorRows = [];
    private $errorMessages = [];
    private $errorUsers = [];

    public function collection(Collection $rows)
    {
        // ตรวจสอบส่วนหัวของไฟล์
        $requiredHeaders = ['code','department','prefix','name','lastname','nationality','ethnicity','address','province','amphur','tambol','phone','birth','position','adjust_position_date','salary','adjust_salary_date','p_leave','sp_leave','sick_leave','sick_leave_op','a_leave','m_leave','o_leave','employee_type','hid','work_permit','passport','visa_expire','work_permitted_expire','start_work_date','tax','bank_account','bank'];
        $fileHeaders = $rows->first();
        // dd($rows);
        if (!$fileHeaders || !$this->validateHeaders($fileHeaders, $requiredHeaders)) {
            // จัดการกรณีที่ไฟล์ไม่มีส่วนหัวที่จำเป็น
            $this->errorCount++;
            $this->errorMessages[] = 'รูปแบบไฟล์ไม่ถูกต้อง';
            return;
        }

        $customMessages = [
            'code.required' => 'กรุณาระบุรหัสพนักงาน',
            'department.required' => 'กรุณาระบุแผนก',
            'department.exists' => 'แผนกที่ระบุไม่มีอยู่ในระบบ',
            'prefix.required' => 'กรุณาระบุคำนำหน้า',
            'prefix.exists' => 'คำนำหน้าที่ระบุไม่มีอยู่ในระบบ',
            'name.required' => 'กรุณาระบุชื่อ',
            'nationality.required' => 'กรุณาระบุสัญชาติ',
            'nationality.exists' => 'สัญชาติที่ระบุไม่มีอยู่ในระบบ',
            'ethnicity.required' => 'กรุณาระบุชาติพันธุ์',
            'ethnicity.exists' => 'ชาติพันธุ์ที่ระบุไม่มีอยู่ในระบบ',
            'address.required' => 'กรุณาระบุที่อยู่',
            'province.required' => 'กรุณาระบุจังหวัด',
            'amphur.required' => 'กรุณาระบุอำเภอ',
            'tambol.required' => 'กรุณาระบุตำบล',
            'position.required' => 'กรุณาระบุตำแหน่ง',
            'position.exists' => 'ตำแหน่งที่ระบุไม่มีอยู่ในระบบ',
            'adjust_position_date.required' => 'กรุณาระบุวันที่ปรับตำแหน่ง',
            'salary.required' => 'กรุณาระบุเงินเดือน',
            'adjust_salary_date.required' => 'กรุณาระบุวันที่ปรับเงินเดือน',
            'p_leave.required' => 'กรุณาระบุวันลาพักร้อน',
            'sp_leave.required' => 'กรุณาระบุวันลาพิเศษ',
            'sick_leave.required' => 'กรุณาระบุวันลาป่วย',
            'sick_leave_op.required' => 'กรุณาระบุวันลาป่วยสะสม',
            'a_leave.required' => 'กรุณาระบุวันลากิจ',
            'm_leave.required' => 'กรุณาระบุวันลาคลอด',
            'o_leave.required' => 'กรุณาระบุวันลาลาหยุดอื่นๆ',
            'employee_type.required' => 'กรุณาระบุประเภทพนักงาน',
            'employee_type.exists' => 'ประเภทพนักงานที่ระบุไม่มีอยู่ในระบบ',
            'start_work_date.required' => 'กรุณาระบุวันที่เริ่มงาน',
        ];

        // ดำเนินการตรวจสอบข้อมูล
        foreach ($rows as $index => $row) {
            if ($row->filter(function ($value) {
                return $value !== null && $value !== '';
            })->isEmpty()) {
                continue; // ข้ามการทำซ้ำถ้าแถวว่างเปล่า
            }
            $validator = Validator::make($row->toArray(), [
                'code' => 'required',
                'department' => [
                    'required',
                    Rule::exists(CompanyDepartment::class, 'code')
                ],
                'prefix' => [
                    'required',
                    Rule::exists(Prefix::class, 'name')
                ],
                'name' => 'required',
                'nationality' => [
                    'required',
                    Rule::exists(Nationality::class, 'name')
                ],
                'ethnicity' => [
                    'required',
                    Rule::exists(Ethnicity::class, 'name')
                ],
                'address' => 'required',
                'province' => 'required',
                'amphur' => 'required',
                'tambol' => 'required',
                'position' => [
                    'required',
                    Rule::exists(UserPosition::class, 'name')
                ],
                'adjust_position_date' => 'required',
                'salary' => 'required',
                'adjust_salary_date' => 'required',
                'p_leave' => 'required',
                'sp_leave' => 'required',
                'sick_leave' => 'required',
                'sick_leave_op' => 'required',
                'a_leave' => 'required',
                'm_leave' => 'required',
                'o_leave' => 'required',
                'employee_type' => [
                    'required',
                    Rule::exists(EmployeeType::class, 'name')
                ],
                'start_work_date' => 'required',
            ], $customMessages);

            if ($validator->fails()) {
                $this->errorCount++;
                $this->errorRows[] = $row;
                // $this->errorMessages[] = $validator->error();
                $errors = $validator->errors()->all();
                // $errorMessages = implode(', ', $errors);
                $this->errorMessages[] =  implode(', ', $errors);
                if ($row['lastname'] !== '-') {
                    $this->errorUsers[] = $row['name'] . ' ' .  $row['lastname'];
                } else {
                    $this->errorUsers[] = $row['name'];
                }
            } else {
                $this->successCount++;
            }
        }

        if ($this->errorCount === 0) {
            // บันทึกแถวที่ถูกต้องลงในโมเดล
            $users = [];

            foreach ($rows as $row) {
                if ($row->filter(function ($value) {
                    return $value !== null && $value !== '';
                })->isEmpty()) {
                    continue;
                }

                $row = $row->map(function ($value) {
                    return $value === '-' ? null : $value;
                });

                $employeeNo = $row['code'];
                $companyDepartmentCode = $row['department'];
                $prefixName = $row['prefix'];
                $nationalityName = $row['nationality'];
                $ethnicityName = $row['ethnicity'];
                $positionName = $row['position'];
                $employeeTypeName = $row['employee_type'];

                $companyDepartmentId = CompanyDepartment::where('code', $companyDepartmentCode)->value('id');
                // dd($companyDepartmentId);
                $prefixId = Prefix::where('name', $prefixName)->value('id');
                $nationalityId = Nationality::where('name', $nationalityName)->value('id');
                $ethnicityId = Ethnicity::where('name', $ethnicityName)->value('id');
                $positionId = UserPosition::where('name', $positionName)->value('id');
                $employeeTypeId = EmployeeType::where('name', $employeeTypeName)->value('id');
                /* . ' ตำบล' . $row['tambol'] . ' อำเภอ' . $row['amphur'] . ' จังหวัด' . $row['province']  */
                $address = $row['address'];
                $subdistrict =$row['tambol'];
                $district = $row['amphur'];
                $city = $row['province'];
                $visaExpire = $row['visa_expire'] != '' ? Carbon::createFromTimestamp((($row['visa_expire'] - 25569) * 86400))->format('Y-m-d') : null;
                $workPermittedExpire = $row['work_permitted_expire'] != '' ? Carbon::createFromTimestamp((($row['work_permitted_expire'] - 25569) * 86400))->format('Y-m-d') : null;
                $startWorkDate = $row['start_work_date'] != '' ? Carbon::createFromTimestamp((($row['start_work_date'] - 25569) * 86400))->format('Y-m-d') : null;

                $positionAdjustDate = $row['adjust_position_date'] != '' ? Carbon::createFromTimestamp((($row['adjust_position_date'] - 25569) * 86400))->format('Y-m-d') : null;
                $salary = $row['salary'];
                $salaryRecordDate = $row['adjust_salary_date'] != '' ? Carbon::createFromTimestamp((($row['adjust_salary_date'] - 25569) * 86400))->format('Y-m-d') : null;

                $p_leave = $row['p_leave'];
                $sp_leave = $row['sp_leave'];
                $sick_leave = $row['sick_leave'];
                $sick_leave_op = $row['sick_leave_op'];
                $a_leave = $row['a_leave'];
                $m_leave = $row['m_leave'];
                $o_leave = $row['o_leave'];

                $user = User::where('employee_no', $employeeNo)->first();
                if (!$user) {
                    $userData = [
                        'prefix_id' => $prefixId,
                        'nationality_id' => $nationalityId,
                        'ethnicity_id' => $ethnicityId,
                        'user_position_id' => $positionId,
                        'employee_type_id' => $employeeTypeId,
                        'company_department_id' => $companyDepartmentId,
                        'employee_no' => $employeeNo,
                        'username' => $employeeNo,
                        'name' => $row['name'],
                        'lastname' => $row['lastname'] ?? null,
                        'address' => $address,
                        'subdistrict' => $subdistrict,
                        'district' => $district,
                        'city' => $city,

                        'phone' => $row['phone'] ?? null,
                        'hid' => $row['hid'] ?? null,
                        'passport' => $row['passport'] ?? null,
                        'work_permit' => $row['work_permit'] ?? null,
                        'birth_date' => Carbon::createFromTimestamp((($row['birth'] - 25569) * 86400))->format('Y-m-d'),
                        'visa_expiry_date' => $visaExpire,
                        'permit_expiry_date' => $workPermittedExpire,
                        'start_work_date' => $startWorkDate,
                        'tax' => $row['tax'] ?? null,
                        'bank_account' => $row['bank_account'] ?? null,
                        'bank' => $row['bank'] ?? null,
                        'email' => $employeeNo . '@cif.com',
                        'password' => bcrypt('11111111'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $user = User::create($userData);

                    PositionHistory::create([
                            "user_id" => $user->id,
                            "user_position_id" => $positionId,
                            'adjust_date' => $positionAdjustDate
                        ]);
                    SalaryRecord::create([
                            'user_id' => $user->id,
                            'salary' => $salary,
                            'record_date' => $salaryRecordDate,
                        ]);

                    UserLeave::create([
                        'user_id' => $user->id,
                        'leave_type_id' => 1,
                        'count' => $p_leave,
                    ]);
                    UserLeave::create([
                        'user_id' => $user->id,
                        'leave_type_id' => 2,
                        'count' => $sp_leave,
                    ]);
                    UserLeave::create([
                        'user_id' => $user->id,
                        'leave_type_id' => 3,
                        'count' => $sick_leave_op,
                    ]);
                    UserLeave::create([
                        'user_id' => $user->id,
                        'leave_type_id' => 4,
                        'count' => $sick_leave,
                    ]);
                    UserLeave::create([
                        'user_id' => $user->id,
                        'leave_type_id' => 5,
                        'count' => $a_leave,
                    ]);
                    UserLeave::create([
                        'user_id' => $user->id,
                        'leave_type_id' => 6,
                        'count' => $m_leave,
                    ]);
                    UserLeave::create([
                        'user_id' => $user->id,
                        'leave_type_id' => 7,
                        'count' => $o_leave,
                    ]);

                    $leaveIncrements = LeaveIncrement::where('user_id',$user->id)->get();
                    if($leaveIncrements->count() == 0){
                        $this->createLeaveTypesForUser($user);
                    }

                    // Add the user record to the array
                    // $userDatas[] = $userData;
                }
            }
            // DB::table('users')->insert($userDatas);
            // foreach (User::all() as $user) {
            //     UserDiligenceAllowance::create([
            //         'user_id' => $user->id,
            //         'level' => 1,
            //         'diligence_allowance_id' => 1,
            //     ]);
            // }
        }
    }

        /**
     * Create leave types data for a specific user.
     *
     * @param \App\User $user
     * @return void
     */
    private function createLeaveTypesForUser($user)
    {
        $leaveTypes = [
            [
                'user_id' => $user->id,
                'leave_type_id' => 1,
                'type' => 1,
                'months' => $this->generateMonthsData([1]),
                'quantity' => 10,
            ],
            [
                'user_id' => $user->id,
                'leave_type_id' => 2,
                'type' => 1,
                'months' => $this->generateMonthsData([1]),
                'quantity' => 10,
            ],
            [
                'user_id' => $user->id,
                'leave_type_id' => 3,
                'type' => 1,
                'months' => $this->generateMonthsData([1]),
                'quantity' => 30,
            ],
            [
                'user_id' => $user->id,
                'leave_type_id' => 4,
                'type' => 1,
                'months' => $this->generateMonthsData([1]),
                'quantity' => 30,
            ],
            [
                'user_id' => $user->id,
                'leave_type_id' => 5,
                'type' => 2,
                'months' => $this->generateMonthsData([1, 3, 5, 7, 9, 11]),
                'quantity' => 1,
            ],
            [
                'user_id' => $user->id,
                'leave_type_id' => 6,
                'type' => 1,
                'months' => $this->generateMonthsData([1]),
                'quantity' => 90,
            ],
            [
                'user_id' => $user->id,
                'leave_type_id' => 7,
                'type' => 1,
                'months' => $this->generateMonthsData([1]),
                'quantity' => 120,
            ],
        ];

        // Insert data into the table
        DB::table('leave_increments')->insert($leaveTypes);
    }

    /**
     * Generate months data with initial values.
     *
     * @param array $checkedMonths
     * @return array
     */
    private function generateMonthsData($checkedMonths)
    {
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $isChecked = in_array($i, $checkedMonths) ? 1 : 0;
            $months[] = ['monthId' => $i, 'isChecked' => $isChecked];
        }
        return json_encode($months);
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getErrorCount()
    {
        return $this->errorCount;
    }

    public function getErrorRows()
    {
        return $this->errorRows;
    }

    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    public function getErrorUsers()
    {
        return $this->errorUsers;
    }

    // ฟังก์ชันสำหรับตรวจสอบส่วนหัว
    private function validateHeaders($headers, $requiredHeaders)
    {
        $fileHeaders = $headers->keys()->toArray();
        sort($fileHeaders);
        sort($requiredHeaders);
        return $fileHeaders === $requiredHeaders;
    }

}
