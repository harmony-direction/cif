@extends('layouts.dashboard')
@push('scripts')
<style>
    #calendar .fc-header-toolbar,
    #calendar .fc-toolbar {
        display: none;
    }
</style>
@endpush
@section('content')
<div>
    <div>
        <div class="container-fluid">
            <div class="title-header">
                <div>
                    <h3 class="m-0">เพิ่มตารางทำงาน</h3>
                </div>
                <div aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a
                                href="{{route('groups.time-recording-system.schedulework.schedule')}}">ตารางทำงาน</a>
                        </li>
                        <li class="breadcrumb-item active">เพิ่มตารางทำงาน</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4 class="card-title">รายละเอียดตารางทำงาน</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{route('groups.time-recording-system.schedulework.schedule.store')}}"
                                method="POST">
                                @csrf
                                <div class="row gy-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>ตารางทำงาน<span class="small text-danger">*</span></label>
                                            <input type="text" name="name" value="{{old('name')}}"
                                                class="form-control @error('name') is-invalid @enderror">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>คำอธิบาย</label>
                                            <input type="text" name="description" value="{{old('description')}}"
                                                class="form-control ">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>กะการทำงานที่ใช้<span class="small text-danger">*</span></label>
                                            <select name="shift[]" id="shift"
                                                class="form-control select2 @error('shift') is-invalid @enderror"
                                                style="width: 100%;" multiple>
                                                @foreach ($shifts->where('base_shift', 1) as $shift)
                                                <option value="{{ $shift->id }}" @if(in_array($shift->id, old('shift',
                                                    []))) selected @endif>
                                                    {{ $shift->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('shift')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>กรุณาเลือกกะการทำงาน</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>ประเภทตารางทำงาน</label>
                                            <select name="schedule_type"
                                                class="form-control select2 @error('schedule_type') is-invalid @enderror"
                                                style="width: 100%;">
                                                @foreach ($scheduleTypes as $scheduleType)
                                                <option value="{{ $scheduleType->id }}" {{
                                                    old('schedule_type')==$scheduleType->id ? 'selected' : '' }}>
                                                    {{ $scheduleType->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>ปี</label>
                                            <select name="year"
                                                class="form-control select2 @error('year') is-invalid @enderror"
                                                style="width: 100%;">
                                                @foreach ($years as $year)
                                                <option value="{{ $year }}" {{ old('year')==$year ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label>ล่วงเวลาอัตโนมัติ</label>
                                            <select name="auto_overtime" id="auto_overtime"
                                                class="form-control select2 @error('auto_overtime') is-invalid @enderror"
                                                style="width: 100%;">
                                                <option value="1">ไม่</option>
                                                <option value="2">ใช่</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6" id="cantain_wrapper" style="display: none;">
                                        <div class="form-group">
                                            <label>จำนวนชั่วโมงล่วงเวลา</label>
                                            <input type="text" name="duration" value="3" class="form-control ">
                                        </div>
                                    </div> --}}
                                </div>
                                    <div class="cit-modal-footer mt-2">
                                        @if ($permission->create)
                                        <button type="submit"
                                            class="btn btn-primary">บันทึก</button>
                                        @endif
                                    </div>
                            </form>
                            <!-- Display validation errors -->

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@push('scripts')

<script>
    $('.select2').select2()
    // $(document).on('change', '#auto_overtime', function (e) {
    //     var selectedValue = $(this).val();
    //     if (selectedValue === '2') {
    //     $('#cantain_wrapper').show();
    //     } else if (selectedValue === '1') {
    //     $('#cantain_wrapper').hide();
    //     }
    //     });
</script>
@endpush
@endsection
