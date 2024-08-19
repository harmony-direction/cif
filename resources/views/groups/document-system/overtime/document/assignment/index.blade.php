@extends('layouts.dashboard')

@section('content')
<div>
    <div>
        <div class="container-fluid">
            <div class="title-header">
                <div>
                    <h3 class="m-0">{{$overtime->name}}</h3>
                </div>
                <div aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a
                                href="{{route('groups.document-system.overtime.document')}}">รายการล่วงเวลา</a>
                        </li>
                        <li class="breadcrumb-item active">{{$overtime->name}}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div>
                <div class="d-flex align-items-center my-3 gap-3">
                    <div class="form-group">
                        <a class="btn btn-primary"
                            href="{{route('groups.document-system.overtime.approval.assignment.create',['id' => $overtime->id])}}">
                            <i class="fas fa-plus">
                            </i>
                            เพิ่มพนักงาน
                        </a>
                    </div>
                    <div>
                        <span>หรือนำเข้าจากรหัสพนักงาน</span>
                    </div>
                    <div class="form-group ml-2 mr-2">
                        <a class="btn btn-primary " href="" id="import-employee-code">
                            <i class="fas fa-plus mr-1"></i>
                            รหัสพนักงาน
                        </a>
                    </div>
                    <div>
                        <span>หรือนำเข้าจากกลุ่มพนักงาน</span>
                    </div>
                    <div class="form-group ml-2" style="width: 300px;">
                        <select name="userGroup" id="userGroup"
                            class="form-control select2 @error('userGroup') is-invalid @enderror" style="width: 100%;">
                            <option value="">==เลือกสายอนุมัติล่วงเวลา==</option>
                            @foreach ($approvers->where('document_type_id',2) as $approver)
                            <option value="{{ $approver->id }}">
                                {{ $approver->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">รายการพนักงาน</h3>
                        </div>
                        <div class="card-body">
                            <div class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <input name="overtimeId" id="overtimeId" value="{{$overtime->id}}" type="text"
                                        hidden>
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                                <thead class="border-bottom">
                                                    <tr>
                                                        <th>รหัสพนักงาน</th>
                                                        <th>ชื่อ-สกุล</th>
                                                        <th>แผนก</th>
                                                        <th style="width: 250px">จำนวนชั่วโมง</th>
                                                        <th class="text-end">เพิ่มเติม</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($users as $key => $user)
                                                    <tr>
                                                        <td>{{$user->employee_no}}</td>
                                                        <td>{{$user->name}}
                                                            {{$user->lastname}}</td>
                                                        <td>{{$user->company_department->name}}</td>
                                                        <td>
                                                            <div class="form-group mb-0">
                                                                <input type="text" name="hour" id="hour"
                                                                    value="{{$user->getOvertimeHour($overtime->id)}}"
                                                                    class="form-control" data-user="{{$user->id}}">
                                                            </div>
                                                        </td>
                                                        <td class="text-end">
                                                            <form
                                                                action="{{ route('groups.document-system.overtime.approval.assignment.delete', ['overtime_id' => $overtime->id, 'user_id' => $user->id]) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="btn btn-action btn-delete btn-sm" type="submit"><i
                                                                        class="fas fa-trash"></i></button>
                                                            </form>
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
    </div>
    <div class="modal fade" id="modal-import-employee-code">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="employee-code">รหัสพนักงานแถวละ 1 รายการ</label>
                                <textarea class="form-control" id="employee-code" rows="10"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-primary"
                                id="btn-import-employee-code">เพิ่มรายการ</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')

<script type="module" src="{{asset('assets/js/helpers/document-system/overtime/assignment/overtime.js?v=1')}}"></script>
<script src="{{asset('assets/js/helpers/helper.js?v=1')}}"></script>
<script>
    $('.select2').select2()
    window.params = {
        importUserGroupRoute: '{{ route('groups.document-system.overtime.approval.assignment.import-user-group') }}',
        updateHourRoute: '{{ route('groups.document-system.overtime.approval.assignment.update-hour') }}',
        importEmployeeNoRoute: '{{ route('groups.document-system.overtime.approval.assignment.import-employee-no') }}',
        url: '{{ url('/') }}',
        token: $('meta[name="csrf-token"]').attr('content')
    };
</script>
@endpush
@endsection