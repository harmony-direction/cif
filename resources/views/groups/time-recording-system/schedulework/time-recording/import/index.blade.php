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
                    <h3 class="m-0">นำเข้าไฟล์เวลา: {{$month->name}} {{$year}} ({{$workSchedule->name}})
                    </h3>
                    <input type="text" id="schedule_type_id" value="{{$workSchedule->schedule_type_id}}" hidden>
                </div>
                <div aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a
                                href="{{route('groups.time-recording-system.schedulework.time-recording')}}">ตารางทำงาน</a>
                        </li>
                        <li class="breadcrumb-item active">นำเข้าไฟล์เวลา</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            @if ($permission->show)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">ตารางทำงาน</h4>
                        </div>
                        <div>
                            <div class="row">
                                <div class="col-sm-12" id="table_container">
                                    <div class="table-responsive">
                                        <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                            <thead class="border-bottom">
                                                <tr>
                                                    <th>
                                                        <div class="icheck-primary d-inline">
                                                            <input type="checkbox" id="select_all">
                                                            <label for="select_all"></label>
                                                        </div>
                                                    </th>
                                                    <th>รหัสพนักงาน</th>
                                                    <th>ชื่อ-สกุล</th>
                                                    <th>แผนก</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($users as $user)
                                                <tr>
                                                    <td>
                                                        <div class="icheck-primary d-inline">
                                                            <input name="users[]" type="checkbox" class="user-checkbox"
                                                                id="checkboxPrimary{{$user->id}}" value="{{$user->id}}">
                                                            <label for="checkboxPrimary{{$user->id}}"></label>
                                                        </div>
                                                    </td>
                                                    <td>{{$user->employee_no}}</td>
                                                    <td>{{$user->prefix->name}}{{$user->name}} {{$user->lastname}}
                                                    </td>
                                                    <td>{{$user->company_department->name}}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        {{ $users->links() }}

                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer card-create">
                            <button type="submit" class="btn btn-primary"
                                id="show_modal">นำเข้า</button>
                        </div>
                    </div>
                </div>

            </div>

            @endif

        </div>
    </div>
    <div class="modal fade" id="modal-date-range">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="bs-stepper linear">
                                <div class="bs-stepper-header" role="tablist">
                                    <div class="step active" data-target="#logins-part">
                                        <button type="button" class="step-trigger" role="tab"
                                            aria-controls="logins-part" id="logins-part-trigger" aria-selected="true">
                                            <span class="bs-stepper-circle">1</span>
                                            <span class="bs-stepper-label">รอบวันที่</span>
                                        </button>
                                    </div>
                                    <div class="line"></div>
                                    <div class="step" data-target="#information-part">
                                        <button type="button" class="step-trigger" role="tab"
                                            aria-controls="information-part" id="information-part-trigger"
                                            aria-selected="false" disabled="disabled">
                                            <span class="bs-stepper-circle">2</span>
                                            <span class="bs-stepper-label">เพิ่มไฟล์สแกน</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="bs-stepper-content">
                                    <div id="logins-part" class="content active dstepper-block" role="tabpanel"
                                        aria-labelledby="logins-part-trigger">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="startDate">เริ่มวันที่ (วดป. คศ)<span
                                                            class="small text-danger">*</span></label>
                                                    <input type="text" class="form-control input-date-format"
                                                        id="startDate">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="endDate">ถึงวันที่ (วดป. คศ)<span
                                                            class="small text-danger">*</span></label>
                                                    <input type="text" class="form-control input-date-format"
                                                        id="endDate">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cif-modal-footer pb-0">
                                            <button class="btn btn-primary" id="nextButton">ต่อไป<i
                                                class="fas fa-angle-double-right ml-1"></i></button>
                                        </div>
                                    </div>
                                    <div id="information-part" class="content" role="tabpanel"
                                        aria-labelledby="information-part-trigger">
                                        <div class="form-group">
                                            <input type="file" id="file-inputs" style="display: none;" multiple>
                                        </div>
                                        <div class="cif-modal-footer pb-0">
                                            <button class="btn btn-primary" onclick="stepper.previous()"><i
                                                class="fas fa-angle-double-left mr-1"></i>กลับ</button>
                                            <button type="button" class="btn btn-success"
                                                id="import_file_inputs">เลือกไฟล์และนำเข้า</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- <div class="modal-footer justify-content-between">
    {{-- <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
    <button type="button" class="btn btn-primary" id="bntUpdateReportField">ต่อไป</button>
</div>
</div>
</div>
</div>
</div> --}}
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"
    integrity="sha512-dfX5uYVXzyU8+KHqj8bjo7UkOdg18PaOtpa48djpNbZHwExddghZ+ZmzWT06R5v6NSk3ZUfsH6FNEDepLx9hPQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="module"
    src="{{ asset('assets/js/helpers/time-recording-system/schedule/assignment/import-finger-print.js?v=1') }}">
</script>
<script src="{{asset('assets/js/helpers/helper.js?v=1')}}"></script>
<script>
    window.stepper = new Stepper(document.querySelector('.bs-stepper'))

    window.params = {
        searchRoute: '{{ route('groups.time-recording-system.shift.timeattendance.search') }}',        
        batchImportRoute: '{{ route('groups.time-recording-system.schedulework.time-recording.import.batch') }}',
        singleImportRoute: '{{ route('groups.time-recording-system.schedulework.time-recording.import.single') }}',
        url: '{{ url('/') }}',
        token: $('meta[name="csrf-token"]').attr('content')
    };
</script>

@endpush
@endsection