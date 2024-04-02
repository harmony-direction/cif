<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\Bonus;
use App\Models\Group;
use App\Models\Leave;
use App\Models\Gender;
use App\Models\Payday;
use App\Models\Prefix;
use App\Models\Approver;
use App\Models\Training;
use App\Models\BonusUser;
use App\Models\Education;
use App\Models\Ethnicity;
use App\Models\LeaveType;
use App\Models\UserGroup;
use App\Models\UserLeave;
use App\Models\Punishment;
use App\Models\LeaveDetail;
use App\Models\Nationality;
use App\Models\EmployeeType;
use App\Models\SalaryRecord;
use App\Models\UserPosition;
use App\Models\WorkSchedule;
use App\Models\UserAttachment;
use App\Models\PositionHistory;
use App\Models\ApproveAuthority;
use App\Models\IncomeDeductUser;
use App\Models\CompanyDepartment;
use Laravel\Sanctum\HasApiTokens;
use App\Models\DiligenceAllowance;
use App\Models\AssessmentGroupUser;
use App\Models\UserDiligenceAllowance;
use App\Models\WorkScheduleAssignment;
use Illuminate\Notifications\Notifiable;
use App\Models\DiligenceAllowanceClassify;
use App\Models\WorkScheduleAssignmentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'prefix_id',
        'nationality_id',
        'ethnicity_id',
        'user_position_id',
        'employee_type_id',
        'company_department_id',
        'employee_no',
        'username',
        'name',
        'lastname',
        'address',
        'phone',
        'hid',
        'passport',
        'start_work_date',
        'birth_date',
        'visa_expiry_date',
        'permit_expiry_date',
        'email',
        'password',
        'tax',
        'social_security_number',
        'diligence_allowance_id',
        'time_record_require',
        'bank',
        'bank_account',
        'is_admin',
        'work_permit',
        // Add New Field
        'avatar',
        'education',
        'edu_department',
        'district',
        'subdistrict',
        'zip',
        'city',
        'country',
        'is_foreigner'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * ตรวจสอบว่าผู้ใช้งานเป็นผู้ดูแลระบบหรือไม่
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->is_admin === '1';
    }

    /**
     * ความสัมพันธ์กับโมเดล WorkSchedule (กำหนดตารางงาน)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function work_schedule()
    {
        return $this->belongsTo(WorkSchedule::class);
    }

    /**
     * ความสัมพันธ์กับโมเดล Gender (เพศ)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    /**
     * ความสัมพันธ์กับโมเดล Prefix (คำนำหน้าชื่อ)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prefix()
    {
        return $this->belongsTo(Prefix::class);
    }

    /**
     * ความสัมพันธ์กับโมเดล Nationality (สัญชาติ)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }

    /**
     * ความสัมพันธ์กับโมเดล Ethnicity (เชื้อชาติ)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ethnicity()
    {
        return $this->belongsTo(Ethnicity::class);
    }

    /**
     * ความสัมพันธ์กับโมเดล EmployeeType (ประเภทพนักงาน)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee_type()
    {
        return $this->belongsTo(EmployeeType::class);
    }

    /**
     * ความสัมพันธ์กับโมเดล UserPosition (ตำแหน่งงานผู้ใช้งาน)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user_position()
    {
        return $this->belongsTo(UserPosition::class);
    }

    /**
     * ความสัมพันธ์กับโมเดล CompanyDepartment (แผนกของบริษัท)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company_department()
    {
        return $this->belongsTo(CompanyDepartment::class);
    }

    /**
     * ความสัมพันธ์กับโมเดล WorkScheduleAssignment (การมอบหมายตารางงาน)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function workScheduleAssignments()
    {
        return $this->belongsToMany(WorkScheduleAssignment::class, 'work_schedule_assignment_users')
            ->withPivot('user_id', 'work_schedule_assignment_id')
            ->withTimestamps();
    }

    /**
     * ความสัมพันธ์กับโมเดล Role (บทบาทผู้ใช้งาน)
     * ผ่านตารางกลาง role_users (บทบาทของผู้ใช้งาน)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users', 'user_id', 'role_id')
            ->where('role_users.user_id', $this->id);
    }

    /**
     * ความสัมพันธ์กับโมเดล Group (กลุ่ม)
     * ผ่านตารางกลาง group_roles (บทบาทของกลุ่ม)
     * เฉพาะกลุ่มที่มีบทบาทที่ผู้ใช้งานมีอยู่
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_roles', 'role_id', 'group_id')
            ->whereIn('role_id', $this->roles()->pluck('id'));
    }

    /**
     * แปลงค่าวันเกิดเป็นรูปแบบ 'm/d/Y'
     *
     * @param  mixed  $value
     * @return string|null
     */
    public function getBirthDateAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->format('m/d/Y');
        }
        return null;
    }

    /**
     * แปลงค่าวันเริ่มงานเป็นรูปแบบ 'm/d/Y'
     *
     * @param  mixed  $value
     * @return string|null
     */
    public function getStartWorkDateAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->format('m/d/Y');
        }
        return null;
    }

    /**
     * แปลงค่าวันหมดอายุวีซ่าเป็นรูปแบบ 'm/d/Y'
     *
     * @param  mixed  $value
     * @return string|null
     */
    public function getVisaExpiryDateAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->format('m/d/Y');
        }
        return null;
    }

    /**
     * แปลงค่าวันหมดอายุใบอนุญาตเป็นรูปแบบ 'm/d/Y'
     *
     * @param  mixed  $value
     * @return string|null
     */
    public function getPermitExpiryDateAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->format('m/d/Y');
        }
        return null;
    }

    /**
     * ความสัมพันธ์กับโมเดล Approver (ผู้อนุมัติ)
     * ผ่านตารางกลาง approver_users (ผู้ใช้งานที่มีสิทธิ์ในสายอนุมัติ)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function approvers()
    {
        return $this->belongsToMany(Approver::class, 'approver_users', 'user_id', 'approver_id');
    }

    public function approveAuthorities()
    {
        return $this->belongsToMany(ApproveAuthority::class, 'approve_authorities', 'user_id', 'approver_id');
    }

    public function isApprover($approverId,$userId){
        return ApproveAuthority::where('approver_id',$approverId)->where('user_id',$userId)->first();
    }

    /**
     * ความสัมพันธ์กับโมเดล WorkScheduleAssignmentUser (ผู้ใช้งานที่ได้รับการกำหนดตารางเวลางาน)
     * ผ่านการเชื่อมโยงกับโมเดล WorkScheduleAssignmentUser (ผู้ใช้งานที่ได้รับการกำหนดตารางเวลางาน)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workScheduleAssignmentUsers()
    {
        return $this->hasMany(WorkScheduleAssignmentUser::class);
    }

    // public function getWorkScheduleUserByMonthYear($month, $year)
    // {
    //     $workScheduleUser = $this->workScheduleAssignmentUsers()
    //         ->whereHas('workScheduleAssignment', function ($query) use ($month, $year) {
    //             $query->where('month_id', $month)
    //                 ->where('year', $year);
    //         })
    //         ->get();

    //     return $workScheduleUser;
    // }

    public function getWorkScheduleUserByMonthYear($month, $year)
    {
        $workScheduleUser = $this->workScheduleAssignmentUsers()
            ->whereHas('workScheduleAssignment', function ($query) use ($month, $year) {
                $query->where('month_id', $month)
                    ->where('year', $year);
            })
            ->with('workScheduleAssignment') // Eager load the 'workScheduleAssignment' relationship
            ->get();

        return $workScheduleUser;
    }


    // public function getWorkScheduleAssignmentUserByConditions($day, $month, $year)
    // {
    //     $workScheduleAssignmentUser = $this->workScheduleAssignmentUsers()
    //         ->whereHas('workScheduleAssignment', function ($query) use ($day, $month, $year) {
    //             $query->where('day', $day)
    //                 ->where('month_id', $month)
    //                 ->where('year', $year);
    //         })
    //         ->get();

    //     return $workScheduleAssignmentUser;
    // }

    public function getWorkScheduleAssignmentUserByConditions($day, $month, $year)
    {
        $workScheduleAssignmentUser = $this->workScheduleAssignmentUsers()
            ->whereHas('workScheduleAssignment', function ($query) use ($day, $month, $year) {
                $query->where('day', $day)
                    ->where('month_id', $month)
                    ->where('year', $year);
            })
            ->with('workScheduleAssignment') // Eager load the 'workScheduleAssignment' relationship
            ->get();

        return $workScheduleAssignmentUser;
    }

    // public function getWorkScheduleAssignmentUsersByConditions($startDate, $endDate, $year)
    // {
    //     $startDate = date('Y-m-d', strtotime($startDate));
    //     $endDate = date('Y-m-d', strtotime($endDate));
    //     $workScheduleAssignmentUsers = $this->workScheduleAssignmentUsers()
    //         ->whereHas('workScheduleAssignment', function ($query) use ($year) {
    //             $query->where('year', $year);
    //         })
    //         ->whereBetween('date_in', [$startDate, $endDate])
    //         ->orderBy('date_in')
    //         ->get();

    //     return $workScheduleAssignmentUsers;
    // }

    public function getWorkScheduleAssignmentUsersByConditions($startDate, $endDate, $year)
    {
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = date('Y-m-d', strtotime($endDate));

        $workScheduleAssignmentUsers = $this->workScheduleAssignmentUsers()
            ->whereHas('workScheduleAssignment', function ($query) use ($year) {
                $query->where('year', $year);
            })
            ->whereBetween('date_in', [$startDate, $endDate])
            ->orderBy('date_in')
            ->with('workScheduleAssignment') // Eager load the 'workScheduleAssignment' relationship
            ->get();

        return $workScheduleAssignmentUsers;
    }


    public function getWorkScheduleAssignmentUsersInformation($startDate, $endDate, $year)
    {
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = date('Y-m-d', strtotime($endDate));

        $notNullworkScheduleAssignmentUserDateIns = $this->workScheduleAssignmentUsers()
        ->whereHas('workScheduleAssignment', function ($query) use ($year) {
            $query->where('year', $year)
                ->whereHas('shift', function ($subQuery) {
                    $subQuery->where('code', 'NOT LIKE', '%_H')
                            ->where('code', 'NOT LIKE', '%_TH');
                });
        })
        ->whereBetween('date_in', [$startDate, $endDate])
        ->where(function ($query) {
            $query->whereNull('time_in')
                ->orWhereNull('time_out');
        })
        ->orderBy('date_in')
        ->pluck('date_in')->toArray();

        $shiftId = $this->isShiftAssignment($startDate);

        $workShift = Shift::find($shiftId)->first();

        // dd($startDate,$shiftId);

        $shiftStartDate = "$startDate $workShift->start";
        $shiftEndDate = "$endDate $workShift->end";

        $leaveDetails = LeaveDetail::join('leaves', 'leave_details.leave_id', '=', 'leaves.id')
            ->where('leaves.user_id', $this->id)
            ->where(function ($query) use ($shiftStartDate, $shiftEndDate) {
                $query->whereBetween('leave_details.from_date', [$shiftStartDate, $shiftEndDate])
                    ->orWhereBetween('leave_details.to_date', [$shiftStartDate, $shiftEndDate])
                    ->orWhere(function ($subquery) use ($shiftStartDate, $shiftEndDate) {
                        $subquery->where('leave_details.from_date', '<=', $shiftStartDate)
                                ->where('leave_details.to_date', '>=', $shiftEndDate);
                    });
            })
            ->get(['leave_details.*'])
            ->pluck('from_date')
            ->toArray();

        $dateArray = array_map(function ($datetime) {
            return Carbon::parse($datetime)->toDateString();
        }, $leaveDetails);


        $datesNotInWorkSchedule = array_diff($notNullworkScheduleAssignmentUserDateIns, $dateArray);

        $workScheduleAssignmentUsers = WorkScheduleAssignmentUser::whereIn('date_in',$datesNotInWorkSchedule)
            ->where('user_id',$this->id)
            ->get();

        return $workScheduleAssignmentUsers;
    }

    public function moreThanOneHourLate($startDate,$endDate,$year)
    {

        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = date('Y-m-d', strtotime($endDate));

        $workScheduleAssignmentUsers = $this->workScheduleAssignmentUsers()
        ->whereHas('workScheduleAssignment', function ($query) use ($year) {
            $query->where('year', $year)
                ->whereHas('shift', function ($subQuery) {
                    $subQuery->where('code', 'NOT LIKE', '%_H')
                            ->where('code', 'NOT LIKE', '%_TH');
                });
        })
        ->whereBetween('date_in', [$startDate, $endDate])
        ->where(function ($query) {
            $query->whereNotNull('time_in');
        })
        ->get();


        $lateList = [];

        foreach($workScheduleAssignmentUsers as $workScheduleAssignmentUser){
                $shiftId = $this->isShiftAssignment($workScheduleAssignmentUser->date_in);

                $workShift = Shift::find($shiftId)->first();
                $shiftStartDate = "$workScheduleAssignmentUser->date_in $workShift->start";
                $workStartDate = "$workScheduleAssignmentUser->date_in $workScheduleAssignmentUser->time_in";

                $start = Carbon::parse($shiftStartDate);
                $end = Carbon::parse($workStartDate);

                $differenceInMinutes = $start->diffInMinutes($end);

                if ($differenceInMinutes > 55){
                    $lateList[] = $workScheduleAssignmentUser->date_in;

                }


        }
        return $lateList;

    }

    public function getWorkScheduleAssignmentUsersInformationWithHolidayCheck($startDate, $endDate, $year)
    {
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = date('Y-m-d', strtotime($endDate));

        $notNullworkScheduleAssignmentUserDateIns = $this->workScheduleAssignmentUsers()
        ->whereHas('workScheduleAssignment', function ($query) use ($year) {
            $query->where('year', $year)
                ->whereHas('shift', function ($subQuery) {
                    $subQuery->where('code', 'NOT LIKE', '%_H')
                            ->where('code', 'NOT LIKE', '%_TH');
                });
        })
        ->whereBetween('date_in', [$startDate, $endDate])
        ->where(function ($query) {
            $query->whereNull('time_in')
                ->orWhereNull('time_out');
        })
        ->orderBy('date_in')
        ->pluck('date_in')->toArray();

        $shiftId = $this->isShiftAssignment($startDate);
        $workShift = Shift::find($shiftId)->first();
        $shiftStartDate = "$startDate $workShift->start";
        $shiftEndDate = "$endDate $workShift->end";

        $leaveDetails = LeaveDetail::join('leaves', 'leave_details.leave_id', '=', 'leaves.id')
            ->where('leaves.user_id', $this->id)
            ->where(function ($query) use ($shiftStartDate, $shiftEndDate) {
                $query->whereBetween('leave_details.from_date', [$shiftStartDate, $shiftEndDate])
                    ->orWhereBetween('leave_details.to_date', [$shiftStartDate, $shiftEndDate])
                    ->orWhere(function ($subquery) use ($shiftStartDate, $shiftEndDate) {
                        $subquery->where('leave_details.from_date', '<=', $shiftStartDate)
                                ->where('leave_details.to_date', '>=', $shiftEndDate);
                    });
            })
            ->get(['leave_details.*'])
            ->pluck('from_date')
            ->toArray();

        $dateArray = array_map(function ($datetime) {
            return Carbon::parse($datetime)->toDateString();
        }, $leaveDetails);

        $datesNotInWorkSchedule = array_diff($notNullworkScheduleAssignmentUserDateIns, $dateArray);

        $workScheduleAssignmentUserIds = WorkScheduleAssignmentUser::whereIn('date_in',$datesNotInWorkSchedule)
            ->where('user_id',$this->id)
            ->pluck('id')->toArray();

        $notNullHolidayworkScheduleAssignmentUserDateInIds = $this->workScheduleAssignmentUsers()
        ->whereHas('workScheduleAssignment', function ($query) use ($year) {
            $query->where('year', $year)
                ->whereHas('shift', function ($subQuery) {
                    $subQuery->where('code', 'LIKE', '%_H')
                            ->orWhere('code', 'LIKE', '%_TH');
                });
        })
        ->whereBetween('date_in', [$startDate, $endDate])
        ->where(function ($query) {
            $query->where(function ($subquery) {
                $subquery->whereNull('time_in')
                        ->whereNotNull('time_out');
            })
            ->orWhere(function ($subquery) {
                $subquery->whereNotNull('time_in')
                        ->whereNull('time_out');
            });
        })
        ->orderBy('date_in')
        ->pluck('id')->toArray();

        $combinedArray = array_merge($workScheduleAssignmentUserIds, $notNullHolidayworkScheduleAssignmentUserDateInIds);

        $workScheduleAssignmentUsers = WorkScheduleAssignmentUser::whereIn('id',$combinedArray)->get();

        return $workScheduleAssignmentUsers;
    }

    public function getHolidayDateByType($startDate, $endDate, $holidayType)
    {
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = date('Y-m-d', strtotime($endDate));

        $holidayDatesQuery = $this->workScheduleAssignmentUsers()
            ->with('workScheduleAssignment.shift')
            ->whereHas('workScheduleAssignment', function ($query) use ($startDate, $endDate, $holidayType) {
                $query->whereBetween('short_date', [$startDate, $endDate]);
                if ($holidayType === 1) {
                    $query->whereHas('shift', function ($subQuery) {
                        $subQuery->where('code', 'LIKE', '%_H');
                    });
                }else if ($holidayType === 2){
                    $query->whereHas('shift', function ($subQuery) {
                        $subQuery->where('code', 'LIKE', '%_TH');
                    });
                }else if($holidayType === 3){
                    $query->whereHas('shift', function ($subQuery) {
                        $subQuery->where('code', 'LIKE', '%_H')
                        ->orWhere('code', 'LIKE', '%_TH');
                    });
                }
            });

        $holidayDates = $holidayDatesQuery->get()->pluck('workScheduleAssignment.short_date');
        return $holidayDates;
    }

    public function getHolidayDates($startDate, $endDate)
    {
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = date('Y-m-d', strtotime($endDate));
        $holidayDates = $this->workScheduleAssignmentUsers()
            ->whereHas('workScheduleAssignment', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('short_date', [$startDate, $endDate])
                    ->whereHas('shift', function ($subQuery) {
                        $subQuery->where('code', 'LIKE', '%_H')
                                ->orWhere('code', 'LIKE', '%_TH');
                    });
            })
            ->get()
            ->pluck('workScheduleAssignment.short_date');
        return $holidayDates;
    }


    public function getWorkScheduleByCurrentMonth()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $workSchedule = $this->workScheduleAssignmentUsers()
            ->whereHas('workScheduleAssignment', function ($query) use ($currentMonth, $currentYear) {
                $query->where('month_id', $currentMonth)
                    ->where('year', $currentYear);
            })
            ->orderBy('date_in')
            ->with('workScheduleAssignment.workSchedule')
            ->get()
            ->pluck('workScheduleAssignment.workSchedule')
            ->unique('work_schedule_id')
            ->first();

        return $workSchedule;
    }

    public function isShiftAssignment($date)
    {
        $shiftId = $this->workScheduleAssignmentUsers()
            ->whereHas('workScheduleAssignment', function ($query) use ($date) {
                $query->where('short_date', $date);
            })
            ->get()
            ->pluck('workScheduleAssignment.shift_id');
        return $shiftId;
    }

    public function getWorkScheduleAssignmentUsers($startDate, $endDate)
    {
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = date('Y-m-d', strtotime($endDate));



        $workScheduleAssignmentUsers = $this->workScheduleAssignmentUsers()
            ->whereBetween('date_in', [$startDate, $endDate])
            ->orderBy('date_in')
            ->get();

        return $workScheduleAssignmentUsers;
    }


    public function detachWorkScheduleAssignments($workScheduleId, $month, $year)
    {
        $detachIds = $this->workScheduleAssignments()
            // ->where('work_schedule_id', $workScheduleId)
            ->where('month_id', $month)
            ->where('year', $year)
            ->pluck('work_schedule_assignments.id')
            ->toArray();

        $this->workScheduleAssignments()->detach($detachIds);
    }

    public function attachWorkScheduleAssignments($workScheduleAssignments)
    {
        $this->workScheduleAssignments()->attach($workScheduleAssignments);
    }

    public function workSchedules()
    {
        return $this->belongsToMany(WorkSchedule::class, 'work_schedule_users');
    }

    public function userGroups()
    {
        return $this->belongsToMany(UserGroup::class, 'user_group_users', 'user_id', 'user_group_id');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function leaveDetails()
    {
        return $this->hasManyThrough(LeaveDetail::class, Leave::class, 'user_id', 'leave_id', 'user_id', 'id');
    }
    public function overTimeDetails()
    {
        return $this->hasMany(OverTimeDetail::class);
    }

    public function paydays()
    {
        return $this->belongsToMany(Payday::class, 'user_paydays', 'user_id', 'payday_id');
    }

    public function salaryRecords()
    {
        return $this->hasMany(SalaryRecord::class);
    }

    public function getAuthorizedUsers($documentType)
    {
        $firstApprover = $this->approvers->where('document_type_id', $documentType)->first();

        if ($firstApprover) {
            return $firstApprover->authorizedUsers;
        } else {
            return collect();
        }
    }

    public function positionHistories()
    {
        return $this->hasMany(PositionHistory::class);
    }

    public function trainings()
    {
        return $this->hasMany(Training::class);
    }

    public function educations()
    {
        return $this->hasMany(Education::class);
    }

    public function punishments()
    {
        return $this->hasMany(Punishment::class);
    }

    public function user_attachments()
    {
        return $this->hasMany(UserAttachment::class);
    }

    public function diligenceAllowance()
    {
        return $this->belongsTo(DiligenceAllowance::class);
    }

    public function getErrorDate()
    {
        $today = Carbon::today();
        $year = $today->year;
        $paydayDetail = $this->getPaydayDetailWithToday();

        if($paydayDetail)
        {
            $startDate = $paydayDetail->start_date;
            $endDate = $paydayDetail->end_date;

            $workScheduleAssignmentUsers = $this->getWorkScheduleAssignmentUsersInformationWithHolidayCheck($startDate, $endDate, $year);

            $dateInList = $workScheduleAssignmentUsers->pluck('date_in')->toArray();
            return $dateInList;
        }

    }

    public function getMissingDate($id)
    {
        $today = Carbon::today();
        $year = $today->year;
        $paydayDetail = PaydayDetail::find($id);

        if($paydayDetail)
        {
            $startDate = $paydayDetail->start_date;
            $endDate = $paydayDetail->end_date;

            $workScheduleAssignmentUsers = $this->getWorkScheduleAssignmentUsersInformation($startDate, $endDate, $year);

            $dateInList = $workScheduleAssignmentUsers->pluck('date_in')->toArray();
            return $dateInList;
        }

    }

    public function getPaydayDetailFromId($id)
    {
        return PaydayDetail::find($id);
    }

    public function getPaydayDetailWithToday()
    {
        $today = Carbon::today();
        $paydayDetail = PaydayDetail::whereDate('end_date', '<=', $today)
            ->whereDate('payment_date', '>=', $today)
            ->whereHas('payday', function ($query) {
                $query->whereHas('users', function ($subQuery) {
                    $subQuery->where('user_id', $this->id);
                });
            })
            ->first();

        return $paydayDetail;
    }

    // public function getPaydayDetailWithTodays()
    // {
    //     $today = Carbon::today();
    //     $paydayDetail = PaydayDetail::whereDate('end_date', '<=', $today)
    //         ->whereDate('payment_date', '>=', $today)
    //         ->whereHas('payday', function ($query) {
    //             $query->whereHas('users', function ($subQuery) {
    //                 $subQuery->where('user_id', $this->id);
    //             })
    //             ->where('type',1);
    //         })
    //         ->get();

    //     return $paydayDetail;
    // }
    public function getPaydayDetailWithTodays()
    {
        $today = Carbon::today();
        $paydayDetail = PaydayDetail::whereDate('end_date', '<=', $today)
            ->whereDate('payment_date', '>=', $today)
            ->whereHas('payday', function ($query) {
                $query->whereHas('users', function ($subQuery) {
                    $subQuery->where('user_id', $this->id);
                })
                ->where('type', 1);
            })
            ->with('payday.users') // Eager load the 'payday' and 'users' relationships
            ->get();

        return $paydayDetail;
    }


    public function getPaydayWithToday()
    {
        $today = Carbon::today();
        $paydayWithToday = $this->paydays()->where('type',1)->whereHas('paydayDetails', function ($query) use ($today) {
            $query->whereDate('end_date', '<=', $today)
                ->whereDate('payment_date', '>=', $today);
        })->first();
        return $paydayWithToday;
    }

    public function getPaydayWithTodays()
    {
        $today = Carbon::today();
        $paydayWithToday = $this->paydays()->where('type',1)->whereHas('paydayDetails', function ($query) use ($today) {
            $query->whereDate('end_date', '<=', $today)
                ->whereDate('payment_date', '>=', $today);
        })->get();
        return $paydayWithToday;
    }

    public function incomeDeductUsers()
    {
        return $this->hasMany(IncomeDeductUser::class, 'user_id');
    }

    public function getIncomeDeductByUsers()
    {
        $paydayDetail = $this->getPaydayDetailWithToday();
        if ($paydayDetail != null)
        {
            $incomeDeductUsers = IncomeDeductUser::where('user_id',$this->id)->where('payday_detail_id',$paydayDetail->id)->get();
            if ($incomeDeductUsers != null)
            {
                return $incomeDeductUsers;
            }else{
                return null;
            }
        }
        return null;
    }

    public function getIncomeDeductUsers($id)
    {
        $paydayDetail = PaydayDetail::find($id);
        // dd($paydayDetail);
        if ($paydayDetail != null)
        {

            $incomeDeductUsers = IncomeDeductUser::where('user_id',$this->id)->where('payday_detail_id',$paydayDetail->id)->get();
            //  dd($incomeDeductUsers);
            if ($incomeDeductUsers != null)
            {
                return $incomeDeductUsers;
            }else{
                return null;
            }
        }
        return null;
    }

    // public function getSummaryIncomeDeductByUsers($type)
    // {
    //     $paydayDetail = $this->getPaydayDetailWithToday();
    //     if ($paydayDetail != null)
    //     {
    //         $incomeDeductUsers = IncomeDeductUser::where('user_id', $this->id)
    //             ->where('payday_detail_id', $paydayDetail->id)
    //             ->whereHas('incomeDeduct', function ($query) use ($type) {
    //                 $query->where('assessable_type_id', $type);
    //             })
    //             ->get();
    //         if ($incomeDeductUsers != null)
    //         {
    //             return $incomeDeductUsers;
    //         }else{
    //             return null;
    //         }
    //     }
    //     return null;
    // }

    public function getSummaryIncomeDeductByUsers($type,$paydayDetailId)
    {
        $paydayDetail = PaydayDetail::find($paydayDetailId);
        if ($paydayDetail != null) {
            $incomeDeductUsers = IncomeDeductUser::where('user_id', $this->id)
                ->where('payday_detail_id', $paydayDetail->id)
                ->whereHas('incomeDeduct', function ($query) use ($type) {
                    $query->where('assessable_type_id', $type);
                })
                ->with('incomeDeduct') // Eager load the 'incomeDeduct' relationship
                ->get();

            if ($incomeDeductUsers != null) {
                return $incomeDeductUsers;
            } else {
                return null;
            }
        }
        return null;
    }


    public function IsvalidTimeInOut($startDate,$endDate)
    {
        $workScheduleAssignmentUsers = WorkScheduleAssignmentUser::where('user_id', $this->id)
            ->where(function ($query) {
                $query->whereNull('time_in')
                    ->orWhereNull('time_out');
            })
            ->where(function ($query) {
                $query->whereNotNull('time_in')
                    ->orWhereNotNull('time_out');
            })
            ->whereBetween('date_in', [$startDate, $endDate])
            ->get();

            return $workScheduleAssignmentUsers;

    }

    public function getTimeRecordInfo($startDate,$endDate)
    {
        $result = collect([
            'workHour' => null,
            'leaveCount' => null,
            'absentCount' => null,
            'lateMinute' => null,
            'earlyMinute' => null,
        ]);
        if(count($this->IsvalidTimeInOut($startDate,$endDate)) ==0 ){
            $workScheduleAssignmentUsers = WorkScheduleAssignmentUser::where('user_id', $this->id)
                    ->whereBetween('date_in', [$startDate, $endDate])
                    ->get();

            foreach($workScheduleAssignmentUsers as $workScheduleAssignmentUser)
            {
                $dateTimeIn = $workScheduleAssignmentUser->date_in . ' ' . $workScheduleAssignmentUser->time_in;
                $dateTimeOut = $workScheduleAssignmentUser->date_out . ' ' . $workScheduleAssignmentUser->time_out;
                $shift = $workScheduleAssignmentUser->workScheduleAssignment->shift;
                echo($dateTimeIn . ' ' . $dateTimeOut . ' <br>');
                // echo($shift->start . ' ' . $shift->end . ' <br>');
            }
        }

    }
    public function diligenceAllowances()
    {
        return $this->hasMany(UserDiligenceAllowance::class, 'user_id');
    }

    public function userSummary()
    {
        $paydayDetail = $this->getPaydayDetailWithToday();
        $startDate = $paydayDetail->start_date;
        $endDate = $paydayDetail->end_date;

        $workScheduleAssignmentUsers = $this->workScheduleAssignmentUsers()
            ->whereBetween('date_in', [$startDate, $endDate])
            ->get();

        $absentCountSum = 0;
        $leaveCountSum = 0;
        $earlyMinuteCountSum = 0;
        $lateMinuteCountSum = 0;
        $workHourCountSum = 0;
        $overTimeCountSum = 0;
        $leaveType = [];
        $noDeductLeaveType = LeaveType::where('diligence_allowance_deduct',0)->pluck('id')->toArray();
        $commonLeaveTypes = [];
        $sumTraditionalHoliday = 0;
        $overTime = [];
        $workHourCountSum_Hour = 0;
        $workHourCountSum_Minute = 0;
        foreach($workScheduleAssignmentUsers as $workScheduleAssignmentUser){

            $traditionalHoliday = $this->getHolidayDateByType($workScheduleAssignmentUser->date_in, $workScheduleAssignmentUser->date_out,2)->toArray();

            if(count($traditionalHoliday) != 0)
            {
                $sumTraditionalHoliday++;
            }

            $workHour = $workScheduleAssignmentUser->getWorkHour();

            if ($workHour['workHour'] !== null) {
                // $workHourCountSum += $workHour['workHour'];
                $workHourCountSum_Hour += floor($workHour['workHour']);
                $workHourCountSum_Minute += ($workHour['workHour'] -floor($workHour['workHour'])) * 100;
            }
            if ($workHour['absentCount'] !== null) {
                $absentCountSum += $workHour['absentCount'];
            }

            if ($workHour['leaveCount'] !== null) {
                $leaveCountSum += $workHour['leaveCount']['count'];
                $leaveType[] = $workHour['leaveCount']['leaveType'];
            }

            if ($workHour['earlyMinute'] !== null) {
                $earlyMinuteCountSum += $workHour['earlyMinute'];
            }

            if ($workHour['lateMinute'] !== null) {
                $lateMinuteCountSum += $workHour['lateMinute'];

            }

            if ($workHour['overTime'] !== null) {
                $overTimeCountSum += $workHour['overTime']['hourDifference'];
                 $overTime[] = [
                    "hourDifference"=> $workHour['overTime']['hourDifference'],
                    "isHoliday"=> $workHour['overTime']['isHoliday']
                 ];
            }
        }
        if(count($leaveType) > 0)
        {
            $commonLeaveTypes = array_intersect($leaveType, $noDeductLeaveType);
        }
        $allowance = 1;
        $earlyHour = $this->minutesToHoursAndMinutes($earlyMinuteCountSum);
        $lateHour = $this->minutesToHoursAndMinutes($lateMinuteCountSum);
        $workHourCountSum  = $workHourCountSum_Hour*60 + $workHourCountSum_Minute;


        $earlyHourHour = floor($earlyHour);
        $earlyHourMinute = ($earlyHour - $earlyHourHour) * 100;

        $lateHourHour = floor($lateHour);
        $lateHourMinute = ($lateHour - $lateHourHour) * 100;

        $totalWorkMinute = ($earlyHourHour + $lateHourHour)*60 + $workHourCountSum + $earlyHourMinute + $lateHourMinute;

        $salaryRecord = SalaryRecord::where('user_id',$this->id)
                        ->latest('id')
                        ->first();

        $sumWorkingHour = $this->minutesToHoursAndMinutes($totalWorkMinute);


        $totalWorkDay = $sumWorkingHour/8 +  $sumTraditionalHoliday + $leaveCountSum ;

        $totalOvertime = 0;

        foreach ($overTime as $overtimeItem) {
            $hourDifference = $overtimeItem['hourDifference'];
            $isHoliday = $overtimeItem['isHoliday'];

            // Apply the multiplication based on isHoliday
            if ($isHoliday) {
                $totalOvertime += ($hourDifference * 3);
            } else {
                $totalOvertime += ($hourDifference * 1.5);
            }
        }

        if ($absentCountSum !=0 || $earlyMinuteCountSum > 60 || $lateMinuteCountSum > 60 || (count($leaveType) > 0 && count($commonLeaveTypes) == 0)){
            $allowance = 0;
        }

        $workHourCountSum = intVal($workHourCountSum/60) + intVal($workHourCountSum % 60)/100 ;

        return collect([
            'workHour' => $workHourCountSum !== 0 ? number_format($workHourCountSum, 2) : null,
            'absentCountSum' => $absentCountSum !== 0 ? $absentCountSum : null,
            'leaveCountSum' => $leaveCountSum !== 0 ? $leaveCountSum : null,
            'earlyHour' => $earlyHour !== 0 ? number_format($earlyHour, 2) : null,
            'lateHour' => $lateHour !== 0 ? number_format($lateHour, 2) : null,
            'overTime' => $overTimeCountSum !== 0 ?  $overTimeCountSum : null,
            'deligenceAllowance' => number_format($this->getdiligenceAllowance($allowance,null), 2) ,
            'salary' => number_format(round($totalWorkDay*$salaryRecord->salary, 0), 2),
            'overTimeCost' => number_format(round($totalOvertime*$salaryRecord->salary/8, 0), 2),
        ]);
    }

    public function salarySummary($id)
    {
        $paydayDetail = PaydayDetail::find($id);
        $startDate = $paydayDetail->start_date;
        $endDate = $paydayDetail->end_date;

        $workScheduleAssignmentUsers = $this->workScheduleAssignmentUsers()
            ->whereBetween('date_in', [$startDate, $endDate])
            ->get();

        $absentCountSum = 0;
        $leaveCountSum = 0;
        $earlyMinuteCountSum = 0;
        $lateMinuteCountSum = 0;
        $workHourCountSum = 0;
        $overTimeCountSum = 0;
        $leaveType = [];
        $noDeductLeaveType = LeaveType::where('diligence_allowance_deduct',0)->pluck('id')->toArray();
        $commonLeaveTypes = [];
        $sumTraditionalHoliday = 0;
        $overTime = [];
        $workHourCountSum_Hour = 0;
        $workHourCountSum_Minute = 0;
        foreach($workScheduleAssignmentUsers as $workScheduleAssignmentUser){
            $traditionalHoliday = $this->getHolidayDateByType($workScheduleAssignmentUser->date_in, $workScheduleAssignmentUser->date_out,2)->toArray();

            if(count($traditionalHoliday) != 0)
            {
                $sumTraditionalHoliday++;
            }

            $workHour = $workScheduleAssignmentUser->getWorkHour();

            if ($workHour['workHour'] !== null) {
                $workHourCountSum_Hour += floor($workHour['workHour']);
                $workHourCountSum_Minute += ($workHour['workHour'] -floor($workHour['workHour'])) * 100;
            }
            if ($workHour['absentCount'] !== null) {
                $absentCountSum += $workHour['absentCount'];
            }

            if ($workHour['leaveCount'] !== null) {
                $leaveCountSum += $workHour['leaveCount']['count'];
                $leaveType[] = $workHour['leaveCount']['leaveType'];
            }

            if ($workHour['earlyMinute'] !== null) {
                $earlyMinuteCountSum += $workHour['earlyMinute'];
            }

            if ($workHour['lateMinute'] !== null) {
                $lateMinuteCountSum += $workHour['lateMinute'];

            }

            if ($workHour['overTime'] !== null) {
                $overTimeCountSum += $workHour['overTime']['hourDifference'];
                 $overTime[] = [
                    "hourDifference"=> $workHour['overTime']['hourDifference'],
                    "isHoliday"=> $workHour['overTime']['isHoliday']
                 ];
            }
        }

        if(count($leaveType) > 0)
        {
            $commonLeaveTypes = array_intersect($leaveType, $noDeductLeaveType);
        }
        $allowance = 1;
        $earlyHour = $this->minutesToHoursAndMinutes($earlyMinuteCountSum);
        $lateHour = $this->minutesToHoursAndMinutes($lateMinuteCountSum);
        $workHourCountSum  = $workHourCountSum_Hour*60 + $workHourCountSum_Minute;

        $earlyHourHour = floor($earlyHour);
        $earlyHourMinute = ($earlyHour - $earlyHourHour) * 100;

        $lateHourHour = floor($lateHour);
        $lateHourMinute = ($lateHour - $lateHourHour) * 100;

        $totalWorkMinute = ($earlyHourHour + $lateHourHour)*60 + $workHourCountSum + $earlyHourMinute + $lateHourMinute;

        $salaryRecord = SalaryRecord::where('user_id',$this->id)
                        ->latest('id')
                        ->first();

        $sumWorkingHour = $this->minutesToHoursAndMinutes($totalWorkMinute);


        $totalWorkDay = $sumWorkingHour/8 +  $sumTraditionalHoliday + $leaveCountSum ;

        if ($absentCountSum !=0 || $earlyMinuteCountSum > 60 || $lateMinuteCountSum > 60 || (count($leaveType) > 0 && count($commonLeaveTypes) == 0)){
            $allowance = 0;
        }
        //
        $workHourCountSum = intVal($workHourCountSum/60) + intVal($workHourCountSum % 60)/100 ;
        $socialSecurity = 0.00;

        $totalSalary = SalaryRecord::where('user_id',$this->id)->latest()->first()->salary;

        if ($this->employee_type_id == 2){
            $totalSalary= round($totalWorkDay*$salaryRecord->salary, 0);
        }

        $socialSecurity = round($salaryRecord->salary, 0);
        $exceedOvertime = 0;
        $taxSetting = TaxSetting::first();

        if ($socialSecurity > $taxSetting->social_contribution_salary){
            $socialSecurityFivePercent = number_format(round($taxSetting->social_contribution_salary * $taxSetting->social_contribution_percent * 0.01), 2);
        }else{
            if ($this->employee_type_id != 1){
                $socialSecurity = round($totalWorkDay*$salaryRecord->salary, 0);
            }


            $incomes = $this->getSummaryIncomeDeductByUsers(1,$id);

            if(count($incomes) > 0)
            {
                $incomeDeductIds = $incomes->whereIn('income_deduct_id',[1,2])->pluck('income_deduct_id')->toArray();
                $sum = IncomeDeductUser::where('user_id',$this->id)->where('payday_detail_id',$id)->whereIn('income_deduct_id',$incomeDeductIds)->sum('value');
                $socialSecurity += $sum;
            }

            $socialSecurityFivePercent = number_format(round($socialSecurity * ($taxSetting->social_contribution_percent * 0.01)), 2);
        }

        if ($socialSecurityFivePercent > $taxSetting->social_contribution_max){
            $socialSecurityFivePercent = $taxSetting->social_contribution_max;
        }

        $exceedlimit = 24;
        if($this->employee_type_id == 1){
            $exceedlimit = 48;
        }
        $exceedOverTimeCost = 0;
        if($overTimeCountSum > $exceedlimit){
            $exceedOvertime = $overTimeCountSum - $exceedlimit;
            $overTimeCountSum = $exceedlimit;

            $exceedOverTimeCost = round($exceedOvertime*1.5*$salaryRecord->salary/8/30, 0);
            if ($this->employee_type_id != 1){
                $exceedOverTimeCost = round($exceedOvertime*1.5*$salaryRecord->salary/8, 0);
            }

        }

        $overTimeCost = number_format(round($overTimeCountSum*1.5*$salaryRecord->salary/8/30, 0), 2);
        if ($this->employee_type_id != 1){
            $overTimeCost = number_format(round($overTimeCountSum*1.5*$salaryRecord->salary/8, 0), 2);
        }

        $diligene_allowance_cost = null;
        if($this->diligence_allowance_id != null){

            $diligene_allowance_cost = number_format($this->getdiligenceAllowance($allowance,$id), 2) ;
        }

        return collect([
            'workHour' => $workHourCountSum !== 0 ? number_format($workHourCountSum, 2) : null,
            'absentCountSum' => $absentCountSum !== 0 ? $absentCountSum : null,
            'leaveCountSum' => $leaveCountSum !== 0 ? $leaveCountSum : null,
            'earlyHour' => $earlyHour !== 0 ? number_format($earlyHour, 2) : null,
            'lateHour' => $lateHour !== 0 ? number_format($lateHour, 2) : null,
            'overTime' => $overTimeCountSum !== 0 ? $overTimeCountSum  : null,
            'deligenceAllowance' => $diligene_allowance_cost ,
            'salary' => number_format($totalSalary, 2),
            'overTimeCost' => $overTimeCost,
            'socialSecurityFivePercent' => $socialSecurityFivePercent,
            'exceedOvertime' => $exceedOvertime,
            'exceedOverTimeCost' => $exceedOverTimeCost,
        ]);
    }



    public function getBonus($bonusId)
    {
        return BonusUser::where('user_id',$this->id)->where('bonus_id',$bonusId)->first();
    }

    public function getExtraOvertime($id)
    {
        $paydayDetail = PaydayDetail::find($id);

        $payday = $paydayDetail->payday;
        $firstPaydayId = $payday->first_payday_id;
        $secondPaydayId = $payday->second_payday_id;

        $startDate = $paydayDetail->start_date;
        $endDate = $paydayDetail->end_date;

        $firstPadayDetail = PaydayDetail::where('payday_id',$firstPaydayId)->where('start_date',$paydayDetail->start_date)->first();
        $secondPadayDetail = PaydayDetail::where('payday_id',$secondPaydayId)->where('end_date',$paydayDetail->end_date)->first();


        if($firstPadayDetail == null || $secondPadayDetail == null ){
            return;
        }

        $exceedOvertimeFirstPaydayDetail = 0;

        $workScheduleAssignmentUsersForFirstPaydayDetails = $this->workScheduleAssignmentUsers()
            ->whereBetween('date_in', [$firstPadayDetail->start_date, $firstPadayDetail->end_date])
            ->get();

        foreach($workScheduleAssignmentUsersForFirstPaydayDetails as $workScheduleAssignmentUser){
            $hourDifference = $workScheduleAssignmentUser->getOvertimeFromManual();
            if($hourDifference != null){
                $exceedOvertimeFirstPaydayDetail += $hourDifference['hourDifference'];
            }
        }
        $exceedlimit = 24;
        if($this->employee_type_id == 1){
            $exceedlimit = 48;
        }
        $exceedOvertimeFirstPaydayDetail = ($exceedOvertimeFirstPaydayDetail > $exceedlimit) ? ($exceedOvertimeFirstPaydayDetail - $exceedlimit) : 0;

        $exceedOvertimeSecondPaydayDetail = 0;
        // dd($secondPadayDetail);
        $workScheduleAssignmentUsersForSecondPaydayDetails = $this->workScheduleAssignmentUsers()
            ->whereBetween('date_in', [$secondPadayDetail->start_date, $secondPadayDetail->end_date])
            ->get();

        foreach($workScheduleAssignmentUsersForSecondPaydayDetails as $workScheduleAssignmentUser){
            $hourDifference = $workScheduleAssignmentUser->getOvertimeFromManual();
            if($hourDifference != null){
                $exceedOvertimeSecondPaydayDetail += $hourDifference['hourDifference'];
            }
        }

        $exceedOvertimeSecondPaydayDetail = ($exceedOvertimeSecondPaydayDetail > $exceedlimit) ? ($exceedOvertimeSecondPaydayDetail - $exceedlimit) : 0;
        $exceedOvertime = $exceedOvertimeFirstPaydayDetail + $exceedOvertimeSecondPaydayDetail;

        $holidayWorkScheduleAssignmentUsers = $this->workScheduleAssignmentUsers()
            ->whereBetween('date_in', [$startDate, $endDate])
            ->whereHas('workScheduleAssignment', function ($query) {
                    $query->whereHas('shift', function ($subQuery) {
                            $subQuery->where('code', 'LIKE', '%_H')
                            ->where('code', 'NOT LIKE', '%_TH');
                        });
                })
            ->whereNotNull('time_in')
            ->whereNotNull('time_out')
            ->get();

        $traditionalHolidayWorkScheduleAssignmentUsers = $this->workScheduleAssignmentUsers()
            ->whereBetween('date_in', [$startDate, $endDate])
            ->whereHas('workScheduleAssignment', function ($query) {
                    $query->whereHas('shift', function ($subQuery) {
                            $subQuery->where('code', 'LIKE', '%_TH');
                        });
                })
            ->whereNotNull('time_in')
            ->whereNotNull('time_out')
            ->get();

        $workHoureHoliday = 0;
        $workHourTraditionalHoliday = 0;

        foreach($holidayWorkScheduleAssignmentUsers as $holidayWorkScheduleAssignmentUser)
        {
            $workHoureHoliday += $holidayWorkScheduleAssignmentUser->getWorkHourHolidayFromManual();
        }
        foreach($traditionalHolidayWorkScheduleAssignmentUsers as $traditionalHolidayWorkScheduleAssignmentUser)
        {
           $workHourTraditionalHoliday += $traditionalHolidayWorkScheduleAssignmentUser->getWorkHourHolidayFromManual();
        }

        $exceedOvertimeCost = 0;
        $holidayOvertimeCost = 0;
        $traditionalHolidayOvertimeCost=0;

        if($this->employee_type_id == 1){
            $hourSalary = ((SalaryRecord::where('user_id',$this->id)->latest()->first()->salary)/30)/8;
            $exceedOvertimeCost = 1.5*$exceedOvertime*$hourSalary;
            if($workHoureHoliday <= 8){
                $holidayOvertimeCost = 1*$workHoureHoliday*$hourSalary;
            }elseif($workHoureHoliday > 8)
            {
                $holidayOvertimeCost = 1*8*$hourSalary + 3*($workHoureHoliday-8)*$hourSalary;
            }
            if($workHourTraditionalHoliday <= 8){
                $traditionalHolidayOvertimeCost = 1*$workHourTraditionalHoliday*$hourSalary;
            }elseif($workHourTraditionalHoliday > 8)
            {
                $traditionalHolidayOvertimeCost = 1*8*$hourSalary + 3*($workHourTraditionalHoliday-8)*$hourSalary;
            }
        }else if($this->employee_type_id == 2){
            $hourSalary = (SalaryRecord::where('user_id',$this->id)->latest()->first()->salary)/8;
            $exceedOvertimeCost = 1.5*$exceedOvertime*$hourSalary;
            $holidayOvertimeCost = 1.5*$workHoureHoliday*$hourSalary;
            $traditionalHolidayOvertimeCost = 2*$workHourTraditionalHoliday*$hourSalary;
        }
        $totalOvertimeCost = $exceedOvertimeCost+$holidayOvertimeCost+$traditionalHolidayOvertimeCost;

        return collect([
            'exceedOvertime' => $exceedOvertime,
            'holidayOvertime' => $workHoureHoliday,
            'traditionalHolidayOvertime' => $workHourTraditionalHoliday,
            'exceedOvertimeCost' => $exceedOvertimeCost,
            'holidayOvertimeCost' => $holidayOvertimeCost,
            'traditionalHolidayOvertimeCost' => number_format(round($traditionalHolidayOvertimeCost, 0), 2),
            'totalOvertimeCost' => number_format(round($totalOvertimeCost, 0), 2),
        ]);
    }

    function minutesToHoursAndMinutes($minutes) {
        $hours = intval($minutes / 60);
        $minutes %= 60;
        return $hours + ($minutes / 100);
    }

    public function getdiligenceAllowance($allowance,$id)
    {
        // $paydayDetail = $this->getPaydayDetailWithToday();
        $diligenceAllowanceClassifyId = DiligenceAllowanceClassify::where('diligence_allowance_id',$this->diligence_allowance_id)->first()->id;


        $paydayDetail = PaydayDetail::find($id);

        $diligenceAllowances = $this->diligenceAllowances;



        $userDiligenceAllowance =  $diligenceAllowances->where('payday_detail_id', $paydayDetail->id);

        $previousUserDiligenceAllowanceId =  $diligenceAllowances->max('id');
        $previousUserDiligenceAllowance = $diligenceAllowances->where('id', $previousUserDiligenceAllowanceId)->first();
        if($previousUserDiligenceAllowance == null){
            return null;
        }
        $diligenceAllowanceId= $previousUserDiligenceAllowance->diligenceAllowanceClassify->diligence_allowance_id;
        $maxDiligenceAllowanceClassifyLevel = DiligenceAllowanceClassify::where('diligence_allowance_id',$diligenceAllowanceId)->max('id');



        if(count($userDiligenceAllowance) == 0){
            $diligenceAllowanceClassifyLevel = UserDiligenceAllowance::where('user_id',$this->id)
                                    ->latest('id')
                                    ->first()
                                    ->diligence_allowance_classify_id;
            if($allowance == 1)
            {
                $diligenceAllowanceClassifyLevel ++;
                if($diligenceAllowanceClassifyLevel > $maxDiligenceAllowanceClassifyLevel)
                {
                    $diligenceAllowanceClassifyLevel = $maxDiligenceAllowanceClassifyLevel;
                }
            }else if($allowance == 0){
                $diligenceAllowanceClassifyLevel = 1;
            }
            UserDiligenceAllowance::create([
                    'user_id' => $this->id,
                    'payday_detail_id' => $paydayDetail->id,
                    'diligence_allowance_classify_id' => $diligenceAllowanceClassifyLevel,
                ]);
            $userDiligenceAllowance = UserDiligenceAllowance::where('user_id', $this->id)
                                    ->where('payday_detail_id', $paydayDetail->id)
                                    ->orderBy('id', 'desc') // Change 'asc' to 'desc' if you want to order in descending order
                                    ->first();
            return $userDiligenceAllowance->diligenceAllowanceClassify->cost;
        }else{

            if($allowance == 0)
            {
                $diligenceAllowanceClassifyId = DiligenceAllowanceClassify::where('diligence_allowance_id',$diligenceAllowanceId)->min('id');
                UserDiligenceAllowance::where('user_id', $this->id)
                                    ->where('payday_detail_id', $paydayDetail->id)
                                    ->orderBy('id', 'desc') // Change 'asc' to 'desc' if you want to order in descending order
                                    ->first()->update(['diligence_allowance_classify_id' => $diligenceAllowanceClassifyId]);
            }elseif($allowance == 1){

                $currentUserDiligenceAllowance = UserDiligenceAllowance::where('user_id', $this->id)
                                ->where('payday_detail_id', $paydayDetail->id)
                                ->latest()
                                ->first();

                $previousMaxUserDiligenceAllowanceId = UserDiligenceAllowance::where('user_id', $this->id)
                ->where('id','<', $currentUserDiligenceAllowance->id)->max('id');

                if(DiligenceAllowanceClassify::find(UserDiligenceAllowance::where('user_id', $this->id)
                ->where('id','<', $currentUserDiligenceAllowance->id)->first() == null)){
                    return null;
                };

                $diligenceAllowanceId = DiligenceAllowanceClassify::find(UserDiligenceAllowance::where('user_id', $this->id)
                ->where('id','<', $currentUserDiligenceAllowance->id)->first()->diligence_allowance_classify_id)->diligence_allowance_id;

                $diligenceAllowanceClassifyId = DiligenceAllowanceClassify::where('diligence_allowance_id',$diligenceAllowanceId)->max('id');

                $previousMaxUserDiligenceAllowanceValue = UserDiligenceAllowance::find($previousMaxUserDiligenceAllowanceId)->diligence_allowance_classify_id;

                if ($previousMaxUserDiligenceAllowanceValue < $diligenceAllowanceClassifyId ){
                    $previousMaxUserDiligenceAllowanceValue ++;
                }else{
                    $previousMaxUserDiligenceAllowanceValue = $diligenceAllowanceClassifyId;
                }

                UserDiligenceAllowance::find($currentUserDiligenceAllowance->id)->update([
                    'diligence_allowance_classify_id' => $previousMaxUserDiligenceAllowanceValue
                ]);
            }

            $userDiligenceAllowance = UserDiligenceAllowance::where('user_id', $this->id)
                        ->where('payday_detail_id', $paydayDetail->id)
                        ->orderBy('id', 'desc')
                        ->first();
            return $userDiligenceAllowance->diligenceAllowanceClassify->cost;
        }

        return $userDiligenceAllowance;
    }
    public function leave()
    {
        return $this->hasMany(UserLeave::class);
    }
    public function assessmentGroupUsers()
    {
        return $this->hasMany(AssessmentGroupUser::class, 'user_id');
    }

    public function getOvertimeHour($overtimeId){
        return OvertimeDetail::where('over_time_id',$overtimeId)->where('user_id',$this->id)->first()->hour;
    }

    public function bonus()
    {
        return $this->belongsTo(Bonus::class);
    }

}





