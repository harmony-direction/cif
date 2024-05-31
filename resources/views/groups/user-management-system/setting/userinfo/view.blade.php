@extends('layouts.dashboard')

@push('styles')

@endpush

@section('content')
<div>
    <div>
        <div class="container-fluid">
            <div class="title-header">
                <div>
                    <h3 class="m-0">{{$user->name}} {{$user->lastname}}</h3>
                </div>
                <div aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active">{{$user->name}} {{$user->lastname}}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            @if ($permission->show)
            <div class="row">
                <input type="text" id="userId" value="{{$user->id}}" hidden>
                <div class="col-12">
                    <div class="card card-tabs">
                        <div class="p-0 pt-1">
                            <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="custom-tabs-one-home-tab" data-bs-toggle="tab"
                                        href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home"
                                        aria-selected="true">ข้อมูลทั่วไป</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-one-workschedule-tab"
                                        href="#custom-tabs-one-workschedule" role="tab"
                                        aria-controls="custom-tabs-one-workschedule"
                                        data-bs-toggle="tab"
                                        aria-selected="false">ตารางทำงาน</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-one-leave-tab" data-bs-toggle="tab"
                                        href="#custom-tabs-one-leave" role="tab" aria-controls="custom-tabs-one-leave"
                                        aria-selected="false">วันลา</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-one-diligence-allowance-tab" data-bs-toggle="tab"
                                        href="#custom-tabs-one-diligence-allowance" role="tab"
                                        aria-controls="custom-tabs-one-diligence-allowance"
                                        aria-selected="false">เบี้ยขยัน</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-one-education-tab" data-bs-toggle="tab"
                                        href="#custom-tabs-one-education" role="tab"
                                        aria-controls="custom-tabs-one-education" aria-selected="false">การศึกษา</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-one-trainings-tab" data-bs-toggle="tab"
                                        href="#custom-tabs-one-trainings" role="tab"
                                        aria-controls="custom-tabs-one-trainings" aria-selected="false">การฝึกอบรม</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-one-position-adjustment-tab" data-bs-toggle="tab"
                                        href="#custom-tabs-one-position-adjustment" role="tab"
                                        aria-controls="custom-tabs-one-position-adjustment"
                                        aria-selected="false">การปรับตำแหน่ง</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-one-salary-adjustment-tab" data-bs-toggle="tab"
                                        href="#custom-tabs-one-salary-adjustment" role="tab"
                                        aria-controls="custom-tabs-one-salary-adjustment"
                                        aria-selected="false">การปรับเงินเดือน</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-one-punishments-tab" data-bs-toggle="tab"
                                        href="#custom-tabs-one-punishments" role="tab"
                                        aria-controls="custom-tabs-one-punishments"
                                        aria-selected="false">ความผิดและโทษ</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="custom-tabs-one-documents-tab" data-bs-toggle="tab"
                                        href="#custom-tabs-one-documents" role="tab"
                                        aria-controls="custom-tabs-one-documents" aria-selected="false">เอกสารสำคัญ</a>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <div class="tab-content" id="custom-tabs-one-tabContent">
                                <div class="card-body tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-home-tab">
                                    <div class="row gy-3">
                                        <div class="col-12 position-relative mt-6">
                                            <div class="form-group" style="width: 300px;">
                                                <label>รหัสพนักงาน <span class="fw-bold text-danger">*</span></label>
                                                <input type="text" name="employee_no" value="{{old('employee_code') ?? $user->employee_no}}" class="form-control numericInputInt @error('employee_no') is-invalid @enderror" disabled>
                                            </div>
                                            <label for="avatar-input">
                                                <div class="d-flex flex-column rounded-4 overflow-hidden position-absolute bottom-0 end-0" style="width: 124px; height: 124px;">
                                                    <div class="d-flex justify-content-center align-items-center " style="background: #667085; flex: 1;">
                                                        <img src="{{ Auth::user()->avatar ? route('storage.avatar', ['image'=> Auth::user()->avatar]) : asset('icon _user_.png') }}" alt="avatar-preview" style="{{ Auth::user()->avatar ? "width: 100%; height: 100%; object-fit: cover;" : "" }}">
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>คำนำหน้าชื่อ <span class="fw-bold text-danger">*</span></label>
                                                <select name="prefix"
                                                class="form-control select2 @error('prefix') is-invalid @enderror"
                                                style="width: 100%;" disabled>
                                                @foreach ($prefixes as $prefix)
                                                <option value="{{ $prefix->id }}" @if ($prefix->id ==
                                                    $user->prefix_id) selected @endif>
                                                    {{ $prefix->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>ชื่อ <span class="fw-bold text-danger">*</span></label>
                                                <input type="text" name="name" value="{{old('name') ?? $user->name}}"
                                                    class="form-control @error('name') is-invalid @enderror" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>นามสกุล <span class="fw-bold text-danger">*</span></label>
                                                <input type="text" name="lastname" value="{{old('lastname') ?? $user->lastname}}"
                                                    class="form-control @error('lastname') is-invalid @enderror" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>วันเดือนปี เกิด</label>
                                                <div class="date-box date" id="birth_date" data-target-input="nearest">
                                                    <input name="birthDate" value="{{old('birthDate') ?? $user->birth_date}}" type="text" placeholder="ดด/วว/ปป"
                                                        class="form-control datetimepicker-input" data-target="#birth_date" disabled>
                                                    <div class="date-icon" data-target="#birth_date"
                                                        data-toggle="datetimepicker">
                                                        <span class="material-symbols-outlined">
                                                            calendar_today
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>สัญชาติ <span class="fw-bold text-danger">*</span></label>
                                                <select name="nationality"
                                                    class="form-control select2 @error('nationality') is-invalid @enderror"
                                                    style="width: 100%;" disabled>
                                                    @foreach ($nationalities as $nationality)
                                                    <option value="{{ $nationality->id }}" @if ($nationality->id == $user->nationality_id) selected @endif >
                                                        {{ $nationality->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>เชื้อชาติ <span class="fw-bold text-danger">*</span></label>
                                                <select name="ethnicity"
                                                    class="form-control select2 @error('ethnicity') is-invalid @enderror"
                                                    style="width: 100%;" disabled>
                                                    @foreach ($ethnicities as $ethnicity)
                                                    <option value="{{ $ethnicity->id }}" @if ($ethnicity->id == $user->ethnicity_id) selected @endif>
                                                        {{ $ethnicity->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>ระดับการศึกษาสูงสุด</label>
                                                <input type="text" name="education" value="{{old('education') ?? $user->education}}" class="form-control" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>สาขาวิชา</label>
                                                <input type="text" name="edu_department" value="{{old('edu_department') ?? $user->edu_department }}" class="form-control " disabled>
                                            </div>
                                        </div>
                                        {{-- ยังไม่มีข้อมูล --}}
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>สถานภาพสมรส</label>
                                                <select name="relationship"
                                                    class="form-control select2"
                                                    style="width: 100%;" disabled>
                                                    @foreach ($relationships as $relationship)
                                                        <option value="{{ $relationship->id }}" {{ $relationship->id === $user->relationship ? 'selected' : '' }}>{{ $relationship->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>เลขที่บัตรประชาชน</label>
                                                <input type="text" name="hid" value="{{old('hid') ?? $user->hid}}" disabled
                                                    class="form-control numericInputHid">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>เลขประจำตัวผู้เสียภาษีอากร</label>
                                                <input type="text" name="tax" value="{{old('tax') ?? $user->tax }}" disabled
                                                    class="form-control numericInputInt">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-end h-100">
                                                <div class="form-check form-check-inline py-2">
                                                    <input class="form-check-input" type="checkbox" id="is_foreigner" checked="{{$user->is_foreigner ? "true" : "false"}}" disabled>
                                                    <label class="form-check-label" for="is_foreigner">พนักงานต่างชาติ</label>
                                                  </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="form-group">
                                                    <label>พาสพอร์ต</label>
                                                    <input type="text" name="passport" value="{{old('passport') ?? $user->passport}}" disabled
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>วันหมดอายุวีซ่า</label>
                                                <div class="date-box date" id="visa_expire_date"
                                                    data-target-input="nearest">
                                                    <input name="visaExpireDate" value="{{old('visaExpireDate') ?? $user->visa_expiry_date}}" disabled
                                                        type="text"
                                                        class="form-control datetimepicker-input @error('visaExpireDate') is-invalid @enderror"
                                                        data-target="#visa_expire_date">
                                                    <div class="date-icon" data-target="#visa_expire_date"
                                                        data-toggle="datetimepicker">
                                                        <span class="material-symbols-outlined">
                                                            calendar_today
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="form-group">
                                                    <label>เลขที่ใบอนุญาตทำงาน</label>
                                                    <input type="text" name="work_permit" value="{{old('work_permit') ?? $user->work_permit}}" disabled
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div>
                                        <h4 class="m-0" style="padding-bottom: 32px;">ข้อมูลติดต่อ</h4>
                                        <div class="row gy-3">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>ที่อยู่ <span class="fw-bold text-danger">*</span></label>
                                                    <input type="text" name="address" value="{{old('address') ?? $user->address}}" disabled
                                                        class="form-control @error('address') is-invalid @enderror">
                                                </div>
                                            </div>
                                            {{-- เพิ่มใหม่ --}}
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>เขต/อำเภอ <span class="fw-bold text-danger">*</span></label>
                                                    <select name="district"
                                                        class="form-control select2 @error('district') is-invalid @enderror"
                                                        style="width: 100%;" disabled>
                                                        <option value="{{ $user->district }}" selected>{{ $user->district }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>แขวง/ตำบล <span class="fw-bold text-danger">*</span></label>
                                                    <select name="subdistrict"
                                                        class="form-control select2 @error('subdistrict') is-invalid @enderror"
                                                        style="width: 100%;" disabled>
                                                        <option value="{{ $user->subdistrict }}" selected>{{ $user->subdistrict }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>รหัสไปรษณีย์ <span class="fw-bold text-danger">*</span></label>
                                                    <input type="text" name="zip" id="zip" value="{{ $user->zip }}" class="form-control @error('zip') is-invalid @enderror" style="width: 100%;" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>จังหวัด <span class="fw-bold text-danger">*</span></label>
                                                    <select name="city"
                                                        class="form-control select2 @error('city') is-invalid @enderror"
                                                        style="width: 100%;" disabled>
                                                        <option value="{{ $user->city }}" selected>{{ $user->city }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>ประเทศ <span class="fw-bold text-danger">*</span></label>
                                                    <select name="country"
                                                        class="form-control select2 @error('country') is-invalid @enderror"
                                                        style="width: 100%;" disabled>
                                                        <option value="{{ $user->country }}" selected>{{ $user->country }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            {{-- \\\\ --}}
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>เบอร์โทรศัพท์มือถือ</label>
                                                    <input type="text" name="phone" value="{{old('phone') ?? $user->phone}}" disabled
                                                        class="form-control numericInputPhone">
                                                </div>
                                            </div>
                                            {{-- เพิ่มใหม่ --}}
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>อีเมล</label>
                                                    <input type="email" name="email" value="{{old('email') ?? $user->email}}" disabled
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div>
                                        <h4 class="m-0" style="padding-bottom: 32px;">ข้อมูลการทำงาน</h4>
                                        <div class="row gy-3">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>ประเภทพนักงาน <span class="fw-bold text-danger">*</span></label>
                                                    <select name="employeeType" disabled
                                                    class="form-control select2 @error('employeeType') is-invalid @enderror"
                                                    style="width: 100%;">
                                                    @foreach ($employeeTypes as $employeeType)
                                                    <option value="{{ $employeeType->id }}" {{
                                                        $user->employee_type_id === $employeeType->id
                                                        ?
                                                        'selected' : '' }}>
                                                        {{ $employeeType->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>ตำแหน่ง <span class="fw-bold text-danger">*</span></label>
                                                    <select name="userPosition" disabled
                                                    class="form-control select2 @error('userPosition') is-invalid @enderror"
                                                    style="width: 100%;">
                                                    @foreach ($userPositions as $userPosition)
                                                    <option value="{{ $userPosition->id }}" {{
                                                        $user->user_position_id === $userPosition->id
                                                        ?
                                                        'selected' : '' }}>
                                                        {{ $userPosition->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>แผนก <span class="fw-bold text-danger">*</span></label>
                                                    <select name="companyDepartment" disabled
                                                    class="form-control select2 @error('companyDepartment') is-invalid @enderror"
                                                    style="width: 100%;">
                                                    @foreach ($companyDepartments as $companyDepartment)
                                                    <option value="{{ $companyDepartment->id }}" {{
                                                        $user->company_department_id === $companyDepartment->id
                                                        ?
                                                        'selected' : '' }}>
                                                        {{ $companyDepartment->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>เริ่มทำงาน <span class="fw-bold text-danger">*</span></label>
                                                    <div class="date-box date" id="start_work_date"
                                                        data-target-input="nearest">
                                                        <input name="startWorkDate" value="{{old('startWorkDate') ?? $user->start_work_date}}" type="text" disabled
                                                            class="form-control datetimepicker-input @error('startWorkDate') is-invalid @enderror"
                                                            data-target="#start_work_date">
                                                        <div class="date-icon" data-target="#start_work_date"
                                                            data-toggle="datetimepicker">
                                                            <span class="material-symbols-outlined">
                                                                calendar_today
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>วันหมดอายุใบอนุญาตทำงาน</label>
                                                    <div class="date-box date" id="work_permit_expire_date"
                                                        data-target-input="nearest">
                                                        <input type="text" name="workPermitExpireDate" disabled
                                                            value="{{old('workPermitExpireDate') ?? $user->permit_expiry_date}}"
                                                            class="form-control datetimepicker-input @error('workPermitExpireDate') is-invalid @enderror"
                                                            data-target="#work_permit_expire_date">
                                                        <div class="date-icon" data-target="#work_permit_expire_date"
                                                            data-toggle="datetimepicker">
                                                            <span class="material-symbols-outlined">
                                                                calendar_today
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <label>เลขที่บัญชี</label>
                                                        <input type="text" name="bank" value="{{old('bank') ?? $user->bank}}" disabled
                                                            class="form-control numericInputInt">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>บัญชีธนาคาร</label>
                                                    <input type="text" name="bankAccount" value="{{old('bankAccount') ?? $user->bank_account}}" disabled
                                                        class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>เลขที่ประกันสังคม</label>
                                                    <input type="text" name="social_security_number"
                                                        value="{{old('social_security_number') ?? $user->social_security_number}}"
                                                        class="form-control numericInputInt" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>การสแกนเวลาเข้าออก <span class="fw-bold text-danger">*</span></label>
                                                    <select name="timeRecordRequire"
                                                        class="form-control select2 @error('timeRecordRequire') is-invalid @enderror"
                                                        style="width: 100%;" disabled>

                                                        <option value="1" {{ $user->timeRecordRequire == "1" ? 'selected': '' }}>
                                                            ต้องสแกนเวลา
                                                        </option>
                                                        <option value="0" {{ $user->timeRecordRequire == "0" ? 'selected': '' }}>
                                                            ไม่ต้องสแกนเวลา
                                                        </option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-one-workschedule" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-workschedule-tab">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                                <thead class="border-bottom">
                                                    <tr>
                                                        <th>รายการ</th>
                                                        <th>รายละเอียด</th>
                                                        <th class="text-end">เพิ่มเติม</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <tr>
                                                        <td>ตารางการทำงานปัจจุบัน</td>
                                                        <td>
                                                            <div class="form-group">
                                                                @php
                                                                $workScheduleByCurrentMonth =
                                                                $user->getWorkScheduleByCurrentMonth();
                                                                $workScheduleId = $workScheduleByCurrentMonth ?
                                                                $workScheduleByCurrentMonth->id : null;
                                                                @endphp

                                                                <select id="workScheduleId" class="form-control select2"
                                                                    style="width: 100%;">
                                                                    <option value="">==ไม่พบรายการ==</option>
                                                                    @foreach ($workSchedules as $workSchedule)
                                                                    <option value="{{ $workSchedule->id }}"
                                                                        @if($workScheduleId !==null && $workSchedule->id ==
                                                                        $workScheduleId)
                                                                        selected @endif>
                                                                        {{ $workSchedule->name }}
                                                                    </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td class="text-end">
                                                            <a class="btn btn-action btn-links btn-sm" id="update-workschedule">
                                                                <i class="fas fa-save"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>รอบคำนวนเงินเดือน</td>
                                                        <td>
                                                            <select id="payday" class="form-control select2"
                                                                style="width: 100%;" multiple>
                                                                @foreach ($paydays as $payday)
                                                                <option value="{{ $payday->id }}" {{ in_array($payday->id,
                                                                    $user->paydays->pluck('id')->toArray()) ? 'selected' :
                                                                    '' }}>
                                                                    {{ $payday->name }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td class="text-end">
                                                            <a class="btn btn-action btn-links btn-sm" id="update-payday">
                                                                <i class="fas fa-save"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>ผู้อนุมัติล่วงเวลา</td>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    @php
                                                                    $selectedApproverId = null;
                                                                    $matchingApprover =
                                                                    $user->approvers->where('document_type_id', 2)->first();
                                                                    if ($matchingApprover) {
                                                                    $selectedApproverId = $matchingApprover->id;
                                                                    }
                                                                    @endphp

                                                                    <select id="overtime-approver"
                                                                        class="form-control select2" style="width: 100%;">
                                                                        <option value="">==ไม่พบรายการ==</option>
                                                                        @foreach ($approvers->where('document_type_id', 2)
                                                                        as $approver)
                                                                        <option value="{{ $approver->id }}"
                                                                            @if($selectedApproverId !==null &&
                                                                            $selectedApproverId==$approver->id)
                                                                            selected
                                                                            @endif >
                                                                            {{ $approver->name }}
                                                                        </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="mt-2" for="">ผู้อนุมัติ</label>
                                                                    <ul id="overtime_authorized_container">
                                                                        @foreach ($user->getAuthorizedUsers(2)
                                                                        as $authoUser)
                                                                        <li>{{$authoUser->name}} {{$authoUser->lastname}}
                                                                        </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-end">
                                                            <a class="btn btn-action btn-links btn-sm" id="update-overtime-approver">
                                                                <i class="fas fa-save"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>เอกสารอนุมัติการลา</td>
                                                        <td>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    @php
                                                                    $selectedApproverId = null;
                                                                    $matchingApprover =
                                                                    $user->approvers->where('document_type_id', 1)->first();
                                                                    if ($matchingApprover) {
                                                                    $selectedApproverId = $matchingApprover->id;
                                                                    }
                                                                    @endphp

                                                                    <select id="leave-approver" class="form-control select2"
                                                                        style="width: 100%;">
                                                                        <option value="">==ไม่พบรายการ==</option>
                                                                        @foreach ($approvers->where('document_type_id', 1)
                                                                        as $approver)
                                                                        <option value="{{ $approver->id }}"
                                                                            @if($selectedApproverId !==null &&
                                                                            $selectedApproverId==$approver->id) selected
                                                                            @endif >
                                                                            {{ $approver->name }}
                                                                        </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-12">
                                                                    <label class="mt-2" for="">ผู้อนุมัติ</label>
                                                                    <ul id="leave_authorized_container">
                                                                        @foreach ($user->getAuthorizedUsers(1)
                                                                        as $authoUser)
                                                                        <li>{{$authoUser->name}} {{$authoUser->lastname}}
                                                                        </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            </div>


                                                        </td>
                                                        <td class="text-end">
                                                            <a class="btn btn-action btn-links btn-sm" id="update-leave-approver">
                                                                <i class="fas fa-save"></i>
                                                            </a>

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>

                                <div class="tab-pane fade" id="custom-tabs-one-leave" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-leave-tab">
                                    <div class="col-12 my-2" id="user-leave-container">
                                        <h5 class="py-2 px-3 d-flex justify-content-between align-items-center">วันลาคงเหลือ <a
                                                class="btn btn-primary btn-sm btn-leave-increment-setting ml-2">
                                                <i class="fas fa-cog"></i> ตั้งการเพิ่มวันลา
                                            </a>
                                        </h5>
                                        <div class="table-responsive">
                                            <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                                <thead class="border-bottom">
                                                    <tr>
                                                        <th style="width: 50%">ประเภท</th>
                                                        <th>คงเหลือ</th>
                                                        <th style="width: 100px" class="text-end">เพิ่มเติม</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($userLeaves as $key =>$userLeave)
                                                    <tr>

                                                        <td>{{$userLeave->leaveType->name}}</td>
                                                        <td>{{$userLeave->count}}</td>
                                                        <td class="text-end">

                                                            <a class="btn btn-action btn-edit btn-sm btn-update-leave"
                                                                data-id="{{$userLeave->id}}"
                                                                data-count="{{$userLeave->count}}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>

                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>

                                    <div class="col-12" id="leave-container">
                                        <h5 class="mx-3 mb-0">รายการลา</h5>
                                        <div class="table-responsive">
                                            <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                                <thead class="border-bottom">
                                                    <tr>
                                                        <th style="width: 50%">วันที่</th>
                                                        <th>ประเภท</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($leaves as $key =>$leave)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',
                                                            $leave->from_date)->format('d/m/Y H:i')
                                                            }} -
                                                            {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',
                                                            $leave->to_date)->format('d/m/Y H:i')
                                                            }}</td>
                                                        <td>{{$leave->leaveType->name}}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-one-diligence-allowance" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-diligence-allowance-tab">
                                    {{-- เบี้ยขยัน --}}
                                    {{-- $userDiligenceAllowances --}}
                                    <div class="col-12" id="dilegence-allowance-container">
                                        <div class="table-responsive">
                                            <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                                <thead class="border-bottom">
                                                    <tr>
                                                        {{-- <th>ระดับ</th> --}}
                                                        <th>รอบจ่ายเงินเดือน</th>
                                                        <th>เบี้ยขยัน</th>
                                                        <th class="text-end">เพิ่มเติม</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($userDiligenceAllowances as $key
                                                    =>$userDiligenceAllowance)
                                                    <tr>
                                                        <td>
                                                            @if ($userDiligenceAllowance->paydayDetail->start_date != null
                                                            && $userDiligenceAllowance->paydayDetail->end_date != null)
                                                            {{ \Carbon\Carbon::createFromFormat('Y-m-d',
                                                            $userDiligenceAllowance->paydayDetail->start_date)->format('d/m/Y')
                                                            }} -
                                                            {{ \Carbon\Carbon::createFromFormat('Y-m-d',
                                                            $userDiligenceAllowance->paydayDetail->end_date)->format('d/m/Y')
                                                            }}
                                                            @endif
                                                        </td>
                                                        <td>{{$userDiligenceAllowance->diligenceAllowanceClassify->cost}}
                                                        </td>
                                                        <td class="text-end">
                                                            @if ($loop->iteration == 2)
                                                            <a class="btn btn-action btn-edit btn-sm btn-update-user-diligence-allowance"
                                                                data-id="{{$userDiligenceAllowance->id}}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            @endif

                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-one-education" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-education-tab">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="col-12 p-3">
                                                <a class="btn btn-header" href="" id="btn-add-education">
                                                    <i class="fas fa-plus">
                                                    </i>
                                                    เพิ่มการศึกษา
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-12" id="education-container">
                                            <div class="table-responsive">
                                                <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                                    <thead class="border-bottom">
                                                        <tr>
                                                            <th>ระดับ</th>
                                                            <th>สาขาวิชา</th>
                                                            <th>ปีที่จบ</th>
                                                            <th class="text-end">เพิ่มเติม</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($user->educations->sortBy('year') as $key =>$education)
                                                        <tr>
                                                            <td>{{$education->level}}</td>
                                                            <td>{{$education->branch}}</td>
                                                            <td>{{$education->year}}</td>
                                                            <td class="text-end">
                                                                <a class="btn btn-action btn-edit btn-sm btn-update-education"
                                                                    data-id="{{$education->id}}">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </a>
                                                                <a class="btn btn-action btn-delete btn-sm btn-delete-education"
                                                                    data-id="{{$education->id}}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-one-trainings" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-trainings-tab">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="col-12 p-3">
                                                <a class="btn btn-header" href="" id="btn-add-training">
                                                    <i class="fas fa-plus">
                                                    </i>
                                                    เพิ่มการฝึกอบรม
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-12" id="training-container">
                                            <div class="table-responsive">
                                                <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                                    <thead class="border-bottom">
                                                        <tr>
                                                            <th>หัวข้อ</th>
                                                            <th>หน่วยงาน</th>
                                                            <th>ปีที่ฝึกอบรม</th>
                                                            <th class="text-end">เพิ่มเติม</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($user->trainings->sortBy('year') as $key =>$training)
                                                        <tr>
                                                            <td>{{$training->course}}</td>
                                                            <td>{{$training->organizer}}</td>
                                                            <td>{{$training->year}}</td>
                                                            <td class="text-end">
                                                                <a class="btn btn-action btn-edit btn-sm btn-update-training"
                                                                    data-id="{{$training->id}}">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </a>
                                                                <a class="btn btn-action btn-delete btn-sm btn-delete-training"
                                                                    data-id="{{$training->id}}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-one-position-adjustment" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-position-adjustment-tab">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="col-12 p-3">
                                                <a class="btn btn-header" href="" id="btn-add-position">
                                                    <i class="fas fa-plus">
                                                    </i>
                                                    เพิ่มตำแหน่ง
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-12" id="position-histories-container">
                                            <div class="table-responsive">
                                                <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                                    <thead class="border-bottom">
                                                        <tr>
                                                            <th>วันที่ปรับ</th>
                                                            <th>ตำแหน่ง</th>
                                                            <th class="text-end">เพิ่มเติม</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($user->positionHistories->sortBy('adjust_date') as $key =>
                                                        $positionHistory)
                                                        <tr>
                                                            <td>
                                                                @if ($positionHistory->adjust_date != null)
                                                                {{
                                                                \Carbon\Carbon::createFromFormat('Y-m-d',$positionHistory->adjust_date)->format('d/m/Y')
                                                                }}
                                                                @endif
                                                            </td>
                                                            <td>{{$positionHistory->user_position->name}}</td>
                                                            <td class="text-end">
                                                                <a class="btn btn-edit btn-action btn-sm btn-update-position-history"
                                                                    data-id="{{$positionHistory->id}}">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </a>
                                                                <a class="btn btn-action btn-delete btn-sm btn-delete-position-history"
                                                                    data-id="{{$positionHistory->id}}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="tab-pane fade" id="custom-tabs-one-salary-adjustment" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-salary-adjustment-tab">
                                    <div class="row">
                                        <div class="col-12">
                                            <a class="btn btn-header p-3" href="" id="btn-add-salary">
                                                <i class="fas fa-plus">
                                                </i>
                                                เพิ่มเงินเดือน
                                            </a>
                                        </div>
                                        <div class="col-12" id="salary_table_container">
                                            <div class="table-responsive">
                                                <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                                    <thead class="border-bottom">
                                                        <tr>
                                                            <th>วันที่ปรับ</th>
                                                            <th>เงินเดือน</th>
                                                            <th class="text-end">เพิ่มเติม</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($user->salaryRecords->sortBy('record_date') as $key =>
                                                        $salaryRecord)
                                                        <tr>
                                                            <td>
                                                                @if ($salaryRecord->record_date != null)
                                                                {{ \Carbon\Carbon::createFromFormat('Y-m-d',
                                                                $salaryRecord->record_date)->format('d/m/Y') }}
                                                                @endif
                                                            </td>
                                                            <td>{{$salaryRecord->salary}}</td>
                                                            <td class="text-end">
                                                                <a class="btn btn-action btn-edit btn-sm btn-update-salary"
                                                                    data-id="{{$salaryRecord->id}}">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </a>
                                                                <a class="btn btn-action btn-delete btn-sm btn-delete-salary"
                                                                    data-id="{{$salaryRecord->id}}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="tab-pane fade" id="custom-tabs-one-punishments" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-punishments-tab">
                                    <div class="row">
                                        <div class="col-12">
                                            <a class="btn btn-header p-3" href="" id="btn-add-punishment">
                                                <i class="fas fa-plus">
                                                </i>
                                                เพิ่มความผิดและโทษ
                                            </a>
                                        </div>
                                        <div class="col-12" id="punishment-container">
                                            <div class="table-responsive">
                                                <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                                    <thead class="border-bottom">
                                                        <tr>
                                                            <th>ความผิด / โทษ</th>
                                                            <th>วันที่บันทึก</th>
                                                            <th class="text-end">เพิ่มเติม</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($user->punishments as $key => $punishment)
                                                        <tr>
                                                            <td>
                                                                @if ($punishment->record_date != null)
                                                                {{ \Carbon\Carbon::createFromFormat('Y-m-d',
                                                                $punishment->record_date)->format('d/m/Y') }}
                                                                @endif


                                                            </td>
                                                            <td>{{$punishment->punishment}}</td>
                                                            <td class="text-end">
                                                                <a class="btn btn-action btn-edit btn-sm btn-update-punishment"
                                                                    data-id="{{$punishment->id}}">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </a>
                                                                <a class="btn btn-action btn-delete btn-sm btn-delete-punishment"
                                                                    data-id="{{$punishment->id}}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="custom-tabs-one-documents" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-documents-tab">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="col-12 p-3">
                                                <a class="btn btn-header" href="" id="btn-add-user-attachment">
                                                    <i class="fas fa-plus">
                                                    </i>
                                                    เพิ่มเอกสารสำคัญ
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-12" id="user-attachment-container">
                                            <div class="table-responsive">
                                                <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                                    <thead class="border-bottom">
                                                        <tr>
                                                            <th>เอกสาร</th>
                                                            {{-- <th>ไฟล์</th> --}}
                                                            <th class="text-end">เพิ่มเติม</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($user->user_attachments as $key =>$user_attachment)
                                                        <tr>
                                                            <td>{{$user_attachment->name}}</td>
                                                            {{-- <td>{{$user_attachment->file}}</td> --}}
                                                            <td class="text-end">
                                                                @php
                                                                $path = $user_attachment->file;
                                                                if ($user_attachment->type == 1){
                                                                $path = url('/storage/uploads/attachment') .'/'.
                                                                $user_attachment->file;
                                                                }
                                                                @endphp
                                                                <a class="btn btn-edit btn-action btn-sm" href="{{$path}}">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                                <a class="btn btn-action btn-delete btn-sm btn-delete-user-attachment"
                                                                    data-id="{{$user_attachment->id}}">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
    <div class="modal fade" id="modal-add-salary">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>วันที่ปรับ (วดป. คศ) <span class="fw-bold text-danger">*</span></label>
                                <input type="text" class="form-control input-date-format" id="salaray-adjustment-date">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>เงินเดือน <span class="fw-bold text-danger">*</span></label>
                                <input type="text" id="salary" class="form-control numericInputInt">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="cif-modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="save-add-salary">เพิ่ม</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-update-salary">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <input type="text" id="salary-record-id" hidden>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>วันที่ปรับ (วดป. คศ) <span class="fw-bold text-danger">*</span></label>
                                <input type="text" class="form-control input-date-format"
                                    id="update-salaray-adjustment-date">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>เงินเดือน <span class="fw-bold text-danger">*</span></label>
                                <input type="text" id="update-salary" class="form-control numericInputInt">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="cif-modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="save-update-salary">แก้ไข</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-workschedule-month">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เลือกเดือน</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @foreach ($months as $month)
                        <div class="col-3 mb-2 icheck-primary d-inline ">
                            <input type="checkbox" id="month_{{$month->id}}" value="">
                            <label for="month_{{$month->id}}">{{$month->name}}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="cif-modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="save-update-workschedule">แก้ไข</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-add-position">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>วันที่ปรับ (วดป. คศ) <span class="fw-bold text-danger">*</span></label>
                                <input type="text" class="form-control input-date-format" id="position-adjustment-date">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>ตำแหน่ง <span class="fw-bold text-danger">*</span></label>
                                <select id="position" class="form-control select2" style="width: 100%;">
                                    @foreach ($userPositions as $position)
                                    <option value="{{ $position->id }}">
                                        {{ $position->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="cif-modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="save-add-position">เพิ่ม</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-update-position">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row" id="update-position-modal-container">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-add-education">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>ระดับ <span class="fw-bold text-danger">*</span></label>
                                <input type="text" class="form-control" id="education-level">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>สาขาวิชา <span class="fw-bold text-danger">*</span></label>
                                <input type="text" id="education-branch" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>ปีที่จบ <span class="fw-bold text-danger">*</span></label>
                                <input type="text" id="graduated-year" class="form-control input-date-format">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="cif-modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="save-add-education">เพิ่ม</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-update-education">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <input type="text" id="educationId" hidden>
                        <div class="col-12">
                            <div class="form-group">
                                <label>ระดับ <span class="fw-bold text-danger">*</span></label>
                                <input type="text" class="form-control" id="update-education-level">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>สาขาวิชา <span class="fw-bold text-danger">*</span></label>
                                <input type="text" id="update-education-branch" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>ปีที่จบ <span class="fw-bold text-danger">*</span></label>
                                <input type="text" id="update-graduated-year" class="form-control numericInputInt">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="cif-modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="save-update-education">แก้ไข</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-add-training">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>หัวข้อ <span class="fw-bold text-danger">*</span></label>
                                <input type="text" class="form-control" id="training-course">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>หน่วยงาน <span class="fw-bold text-danger">*</span></label>
                                <input type="text" id="training-organizer" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>ปีที่ฝึกอบรม <span class="fw-bold text-danger">*</span></label>
                                <input type="text" id="training-year" class="form-control numericInputInt">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="cif-modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="save-add-training">เพิ่ม</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-update-training">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <input type="text" id="trainingId" hidden>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>หัวข้อ <span class="fw-bold text-danger">*</span></label>
                                <input type="text" class="form-control" id="update-training-course">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>หน่วยงาน <span class="fw-bold text-danger">*</span></label>
                                <input type="text" id="update-training-organizer" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>ปีที่ฝึกอบรม <span class="fw-bold text-danger">*</span></label>
                                <input type="text" id="update-training-year" class="form-control numericInputInt">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="cif-modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="save-update-training">แก้ไข</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-add-punishment">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>ความผิด / โทษ <span class="fw-bold text-danger">*</span></label>
                                <input type="text" class="form-control" id="punishment">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>วันที่บันทึก (วดป. คศ) <span class="fw-bold text-danger">*</span></label>
                                <input type="text" class="form-control input-date-format" id="punishment-record-date">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="cif-modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="save-add-punishment">เพิ่ม</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-update-punishment">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <input type="text" id="punishmentId" hidden>
                        <div class="col-12">
                            <div class="form-group">
                                <label>ความผิด / โทษ <span class="fw-bold text-danger">*</span></label>
                                <input type="text" class="form-control" id="update-punishment">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>วันที่บันทึก (วดป. คศ) <span class="fw-bold text-danger">*</span></label>
                                <input type="text" class="form-control input-date-format"
                                    id="update-punishment-record-date">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="cif-modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="save-update-punishment">แก้ไข</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-add-attachment">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>ชื่อเอกสาร <span class="fw-bold text-danger">*</span></label>
                                <input type="text" class="form-control" id="attachment">
                            </div>
                        </div>
                        <div class="col-12 my-3">
                            <div class="form-group clearfix">

                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="radFile" name="r1" checked>
                                    <label for="radFile">แนบไฟล์
                                    </label>
                                </div>

                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="radLink" name="r1">
                                    <label for="radLink">ลิงก์ไฟล์
                                    </label>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-12" id="file_wrapper">
                            <div class="form-group">
                                <button type="button" class="btn btn-header" id="btn-add-attachment"><i class="fas fa-file"></i>เพิ่มไฟล์แนบ
                                    <span id="attachment-file" class="text-dark"></span></button>
                                <div class="form-group">
                                    <input type="file" accept="" id="file-input" style="display: none;">
                                </div>
                            </div>
                        </div>
                        <div class="col-12" id="link_wrapper" style="display: none">
                            <div class="form-group">
                                <label>ลิงก์ไฟล์ <span class="fw-bold text-danger">*</span></label>
                                <input type="text" class="form-control" id="link">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="cif-modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="save-add-attachment">เพิ่ม</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-update-user-diligence-allowance">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <input type="text" name="" id="user-diligence-allowance-id" hidden>
                    <div class="row" id="update-user-diligence-allowance-modal-container">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-update-user-leave">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <input type="text" id="user-leave-id" hidden>
                        <input type="text" id="user-leave-count" hidden>
                        <div class="col-12">
                            <div class="form-group">
                                <label>จำนวนวันลา <span class="fw-bold text-danger">*</span></label>
                                <input type="text" class="form-control numericInputInt" id="update-user-leave">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cif-modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="save-update-user-leave">แก้ไข</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-leave-increment-setting">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table id="module_modal_table" class="table table-borderless text-nowrap">
                                <thead class="border-bottom">
                                    <tr>
                                        <th style="width:220px">การลา</th>
                                        <th>ประเภท</th>
                                        <th>ม.ค.</th>
                                        <th>ก.พ.</th>
                                        <th>มี.ค.</th>
                                        <th>เม.ษ.</th>
                                        <th>พ.ค.</th>
                                        <th>มิ.ย.</th>
                                        <th>ก.ค.</th>
                                        <th>ส.ค.</th>
                                        <th>ก.ย.</th>
                                        <th>ต.ค.</th>
                                        <th>พ.ย.</th>
                                        <th>ธ.ค.</th>
                                        <th style="width:80px">เพิ่ม</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($leaveIncrements as $leaveIncrement)
                                    <tr style="padding-bottom:0px; margin-bottom:0px">
                                        <td style="padding-left: 10px;" class="mb-0"
                                            data-id="{{$leaveIncrement->leaveType->id}}">
                                            {{$leaveIncrement->leaveType->name}}
                                        </td>
                                        <td>
                                            <select id="type_{{$leaveIncrement->leaveType->id}}"
                                                class="form-control select2" style="width: 100%;">
                                                <option value="1" {{$leaveIncrement->type == 1 ? 'selected' :
                                                    ''}}>เริ่มใหม่</option>
                                                <option value="2" {{$leaveIncrement->type == 2 ? 'selected' : ''}}>สะสม
                                                </option>
                                            </select>
                                        </td>
                                        @foreach ($months as $month)
                                        <td style="padding-bottom:0px; margin-bottom:0px">
                                            <div class="icheck-primary d-inline">
                                                <input class="align-middle" type="checkbox"
                                                    id="{{$leaveIncrement->leaveType->id}}_{{$month->id}}"
                                                    data-leave="{{$leaveIncrement->leaveType->id}}"
                                                    data-month="{{$month->id}}" {{$leaveIncrement->isChecked($month->id)
                                                ? 'checked' : ''}}>
                                                <label for="{{$leaveIncrement->leaveType->id}}_{{$month->id}}"></label>
                                            </div>
                                        </td>
                                        @endforeach
                                        <td style="padding-bottom:0px; margin-bottom:0px">
                                            <div class="form-group" >
                                                <input type="text" class="form-control sm integer" style="width: 70px"
                                                    id="val_{{$leaveIncrement->leaveType->id}}"
                                                    value="{{$leaveIncrement->quantity}}">
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="cif-modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="button" class="btn btn-primary"
                            id="save-update-leave-increment">บันทึก</button>
                </div>

            </div>
        </div>
    </div>
</div>
@push('scripts')
<script type="module" src="{{asset('assets/js/helpers/user-management-system/setting/userinfo.js?v=2')}}"></script>
<script src="{{asset('assets/js/helpers/helper.js?v=1')}}"></script>
<script>
    $('.select2').select2()
    window.params = {
        storeSalaryRoute: '{{ route('groups.user-management-system.setting.userinfo.salary.store') }}',
        getSalaryRoute: '{{ route('groups.user-management-system.setting.userinfo.get-salary') }}',
        updateSalaryRoute: '{{ route('groups.user-management-system.setting.userinfo.salary.update') }}',
        deleteSalaryRoute: '{{ route('groups.user-management-system.setting.userinfo.salary.delete') }}',
        updateWorkScheduleRoute: '{{ route('groups.user-management-system.setting.userinfo.workschedule.update-workschedule') }}',
        updatePaydayRoute: '{{ route('groups.user-management-system.setting.userinfo.workschedule.update-payday') }}',
        updateApproverRoute: '{{ route('groups.user-management-system.setting.userinfo.workschedule.update-approver') }}',
        getApproverRoute: '{{ route('groups.user-management-system.setting.userinfo.workschedule.get-approver') }}',
        storePositionRoute: '{{ route('groups.user-management-system.setting.userinfo.position.store') }}',
        getPositionRoute: '{{ route('groups.user-management-system.setting.userinfo.position.get-position') }}',
        updatePositionRoute: '{{ route('groups.user-management-system.setting.userinfo.position.update-position') }}',
        deletePositionRoute: '{{ route('groups.user-management-system.setting.userinfo.position.delete') }}',
        storeEducationRoute: '{{ route('groups.user-management-system.setting.userinfo.education.store') }}',
        getEducationRoute: '{{ route('groups.user-management-system.setting.userinfo.education.get-education') }}',
        updateEducationRoute: '{{ route('groups.user-management-system.setting.userinfo.education.update-education') }}',
        deleteEducationRoute: '{{ route('groups.user-management-system.setting.userinfo.education.delete') }}',
        storeTrainingRoute: '{{ route('groups.user-management-system.setting.userinfo.training.store') }}',
        getTrainingRoute: '{{ route('groups.user-management-system.setting.userinfo.training.get-training') }}',
        updateTrainingRoute: '{{ route('groups.user-management-system.setting.userinfo.training.update-training') }}',
        deleteTrainingRoute: '{{ route('groups.user-management-system.setting.userinfo.training.delete') }}',
        storePunishmentRoute: '{{ route('groups.user-management-system.setting.userinfo.punishment.store') }}',
        getPunishmentRoute: '{{ route('groups.user-management-system.setting.userinfo.punishment.get-punishment') }}',
        updatePunishmentRoute: '{{ route('groups.user-management-system.setting.userinfo.punishment.update-punishment') }}',
        deletePunishmentRoute: '{{ route('groups.user-management-system.setting.userinfo.punishment.delete') }}',
        storeAttachmentRoute: '{{ route('groups.user-management-system.setting.userinfo.attachment.store') }}',
        deleteAttachmentRoute: '{{ route('groups.user-management-system.setting.userinfo.attachment.delete') }}',
        getDiligenceAllowanceClassifyRoute: '{{ route('groups.user-management-system.setting.userinfo.get-diligence-allowance-classify') }}',
        updateDiligenceAllowanceClassifyRoute: '{{ route('groups.user-management-system.setting.userinfo.update-diligence-allowance-classify') }}',
        updateUserLeaveRoute: '{{ route('groups.user-management-system.setting.userinfo.update-user-leave') }}',
        updateLeaveIncrementRoute: '{{ route('groups.user-management-system.setting.userinfo.update-leave-increment') }}',




        url: '{{ url('/') }}',
        token: $('meta[name="csrf-token"]').attr('content')
    };



</script>

@endpush
@endsection
