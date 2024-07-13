@extends('layouts.dashboard')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/loading.css?v=1.0') }}">
@endpush
<div>
    @include('layouts.partial.loading')
    <div>
        <div class="container-fluid">
            <div class="title-header">
                <div>
                    <h3 class="m-0">ข้อมูลส่วนตัว {{$user->name}} {{$user->lastname}}</h3>
                </div>
                <div aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">หน้าหลัก</a>
                        </li>
                        <li class="breadcrumb-item active">ข้อมูลส่วนตัว</li>
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
                                    <a class="nav-link" id="custom-tabs-one-workschedule-tab" data-bs-toggle="tab"
                                        href="#custom-tabs-one-workschedule" role="tab"
                                        aria-controls="custom-tabs-one-workschedule"
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
                                    <a class="nav-link" id="custom-tabs-one-punishments-tab" data-bs-toggle="tab"
                                        href="#custom-tabs-one-punishments" role="tab"
                                        aria-controls="custom-tabs-one-punishments"
                                        aria-selected="false">ความผิดและโทษ</a>
                                </li>

                            </ul>
                        </div>
                        <div>
                            <div class="tab-content" id="custom-tabs-one-tabContent">
                                <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-home-tab">
                                    <div class="card-body">
                                        <div class="pb-4 py-3">
                                            <h4 class="m-0">ข้อมูลส่วนบุคคล</h4>
                                        </div>
                                        <div class="row gy-3">
                                            <div class="col-12 position-relative">
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
                                                    <input type="text" name="education" value="{{old('education') ?? $user->education}}" class="form-control" placeholder="ระดับการศึกษาสูงสุด" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>สาขาวิชา</label>
                                                    <input type="text" name="edu_department" value="{{old('edu_department') ?? $user->edu_department }}" class="form-control" placeholder="สาขาวิชา" disabled>
                                                </div>
                                            </div>
                                            {{-- ยังไม่มีข้อมูล --}}
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>สถานภาพสมรส</label>
                                                    <select name="relationship"
                                                        class="form-control select2"
                                                        style="width: 100%;" disabled>
                                                        @if (!is_null($user->relationship))
                                                            @foreach ($relationships as $relationship)
                                                                <option value="{{ $relationship->id }}" {{ $relationship->id === $user->relationship ? 'selected' : '' }}>{{ $relationship->name }}</option>
                                                            @endforeach
                                                        @else
                                                            <option value="">ไม่ได้เลือก</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
        
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>เลขที่บัตรประชาชน</label>
                                                    <input type="text" name="hid" value="{{old('hid') ?? $user->hid}}" disabled
                                                        class="form-control numericInputHid" placeholder="เลขที่บัตรประชาชน">
                                                </div>
                                            </div>
        
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>เลขประจำตัวผู้เสียภาษีอากร</label>
                                                    <input type="text" name="tax" value="{{old('tax') ?? $user->tax }}" disabled
                                                        class="form-control numericInputInt" placeholder="เลขประจำตัวผู้เสียภาษีอากร">
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
                                                        <input type="text" name="passport" value="{{old('passport') ?? $user->passport}}" placeholder="passport" disabled
                                                            class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>วันหมดอายุวีซ่า</label>
                                                    <div class="date-box date" id="visa_expire_date"
                                                        data-target-input="nearest">
                                                        <input name="visaExpireDate" value="{{old('visaExpireDate') ?? $user->visa_expiry_date}}" disabled placeholder="วันหมดอายุวีซ่า"
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
                                                        <input type="text" name="work_permit" value="{{old('work_permit') ?? $user->work_permit}}" disabled placeholder="เลขที่ใบอนุญาตทำงาน"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="card-body">
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
                                                    <select name="zip"
                                                        class="form-control select2 @error('zip') is-invalid @enderror"
                                                        style="width: 100%;" disabled>
                                                        <option value="{{ $user->zip }}" selected>{{ $user->zip }}</option>
                                                    </select>
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
                                                        class="form-control numericInputPhone" placeholder="เบอร์โทรศัพท์มือถือ">
                                                </div>
                                            </div>
                                            {{-- เพิ่มใหม่ --}}
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>อีเมล</label>
                                                    <input type="email" name="email" value="{{old('email') ?? $user->email}}" disabled
                                                        class="form-control" placeholder="อีเมล">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="card-body">
                                        <h4 class="m-0" style="padding-bottom: 32px;">ข้อมูลการทำงาน</h4>
                                        <div class="row gy-3">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>ประเภทพนักงาน <span class="fw-bold text-danger">*</span></label>
                                                    <select name="employeeType"
                                                        class="form-control select2 @error('employeeType') is-invalid @enderror"
                                                        style="width: 100%;" disabled>
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
                                                    <select name="userPosition"
                                                        class="form-control select2 @error('userPosition') is-invalid @enderror"
                                                        style="width: 100%;" disabled>
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
                                                    <select name="companyDepartment"
                                                        class="form-control select2 @error('companyDepartment') is-invalid @enderror"
                                                        style="width: 100%;" disabled>
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
                                                            value="{{old('workPermitExpireDate') ?? $user->permit_expiry_date}}" placeholder="วันหมดอายุใบอนุญาตทำงาน"
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
                                                        <label>ธนาคาร</label>
                                                        <input type="text" name="bank" value="{{old('bank') ?? $user->bank}}" disabled placeholder="เลขที่บัญชี"
                                                            class="form-control numericInputInt">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>เลขที่บัญชี</label>
                                                    <input type="text" name="bankAccount" value="{{old('bankAccount') ?? $user->bank_account}}" disabled placeholder="บัญชีธนาคาร"
                                                        class="form-control">
                                                </div>
                                            </div>
                                          
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>เลขที่ประกันสังคม</label>
                                                    <input type="text" name="social_security_number"
                                                        value="{{old('social_security_number') ?? $user->social_security_number}}" placeholder="เลขที่ประกันสังคม"
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
                                    <hr>

                                    <div class="col-md-4">
                                        <form action="{{route('groups.employee-system.employee.info.change-password')}}"
                                            method="POST">
                                            @csrf
                                            <label for="" class="text-danger">***เปลี่ยนรหัสผ่าน***</label>
                                            <div class="form-group">
                                                <label>รหัสผ่านใหม่</label>
                                                <input type="text" name="changePassword" value="" class="form-control" placeholder="รหัสผ่านใหม่"
                                                    required>
                                            </div>
                                            <div class="form-group mt-2">
                                                <button type="submit" class="btn btn-primary">เปลี่ยนรหัสผ่าน</button>
                                            </div>
                                        </form>

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
                                                                    style="width: 100%;" disabled>
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
                                                    </tr>
                                                    <tr>
                                                        <td>รอบคำนวนเงินเดือน</td>
                                                        <td>
                                                            <select id="payday" class="form-control select2"
                                                                style="width: 100%;" multiple disabled>
                                                                @foreach ($paydays as $payday)
                                                                <option value="{{ $payday->id }}" {{ in_array($payday->id,
                                                                    $user->paydays->pluck('id')->toArray()) ? 'selected' :
                                                                    '' }}>
                                                                    {{ $payday->name }}
                                                                </option>
                                                                @endforeach
                                                            </select>
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
                                                                        class="form-control select2" style="width: 100%;"
                                                                        disabled>
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
                                                                        style="width: 100%;" disabled>
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
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>

                                <div class="tab-pane fade" id="custom-tabs-one-leave" role="tabpanel"
                                    aria-labelledby="custom-tabs-one-leave-tab">
                                    <div class="col-12" id="user-leave-container">
                                        <h5 class="mx-3 mb-0">วันลาคงเหลือ</h5>
                                        <div class="table-responsive">
                                            <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                                <thead class="border-bottom">
                                                    <tr>
                                                        <th style="width: 50%">ประเภท</th>
                                                        <th>คงเหลือ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($userLeaves as $key =>$userLeave)
                                                    <tr>
                                                        <td>{{$userLeave->leaveType->name}}</td>
                                                        <td>{{$userLeave->count}}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-12" id="leave-container">
                                        <h5 class="p-3 pb-1 m-0 ">รายการลา</h5>
                                        <div class="table-responsive">
                                            <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                                <thead class="border-bottom">
                                                    <tr>
                                                        <th style="width: 50%">วันที่</th>
                                                        <th>ประเภท</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($leaves->reverse() as $key =>$leave)
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
                                        <div class="col-12" id="education-container">
                                            <div class="table-responsive">
                                                <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                                    <thead class="border-bottom">
                                                        <tr>
                                                            <th>ระดับ</th>
                                                            <th>สาขาวิชา</th>
                                                            <th>ปีที่จบ</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($user->educations->sortBy('year') as $key =>$education)
                                                        <tr>
                                                            <td>{{$education->level}}</td>
                                                            <td>{{$education->branch}}</td>
                                                            <td>{{$education->year}}</td>
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
                                        <div class="col-12" id="training-container">
                                            <div class="table-responsive">
                                                <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                                    <thead class="border-bottom">
                                                        <tr>
                                                            <th>หัวข้อ</th>
                                                            <th>หน่วยงาน</th>
                                                            <th>ปีที่ฝึกอบรม</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($user->trainings->sortBy('year') as $key =>$training)
                                                        <tr>
                                                            <td>{{$training->course}}</td>
                                                            <td>{{$training->organizer}}</td>
                                                            <td>{{$training->year}}</td>
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
                                        <div class="col-12" id="position-histories-container">
                                            <div class="table-responsive">
                                                <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                                    <thead class="border-bottom">
                                                        <tr>
                                                            <th>วันที่ปรับ</th>
                                                            <th>ตำแหน่ง</th>
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
                                        <div class="col-12" id="punishment-container">
                                            <div class="table-responsive">
                                                <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                                    <thead class="border-bottom">
                                                        <tr>
                                                            <th>ความผิด / โทษ</th>
                                                            <th>วันที่บันทึก</th>
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
</div>

@push('scripts')



<script>
    $('.select2').select2()
</script>
@endpush
@endsection