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
                    <h3 class="m-0">นำเข้าพนักงาน</h3>
                </div>
                <div aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a
                                href="{{ route('groups.time-recording-system.schedulework.schedule.assignment.user', ['workScheduleId' => $workSchedule->id, 'year' => $year, 'month' => $monthId]) }}">ตารางทำงาน</a>
                        </li>
                        <li class="breadcrumb-item active">{{$workSchedule->name}}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    @if($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5> ผิดพลาด</h5>
                        <ul>
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">รายชื่อพนักงาน</h4>
                            <div class="card-tools search">
                                <input type="text" name="search_query" id="search_query"
                                    class="form-control" placeholder="ค้นหา...">
                                <label for="search_query">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="23" viewBox="0 0 22 23" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0833 4.39585C6.66608 4.39585 3.89584 7.16609 3.89584 10.5834C3.89584 14.0006 6.66608 16.7709 10.0833 16.7709C11.7446 16.7709 13.2529 16.1162 14.3644 15.0507C14.3915 15.0167 14.4208 14.9838 14.4523 14.9523C14.4838 14.9208 14.5167 14.8915 14.5507 14.8644C15.6162 13.7529 16.2708 12.2446 16.2708 10.5834C16.2708 7.16609 13.5006 4.39585 10.0833 4.39585ZM16.8346 15.7141C17.9188 14.2897 18.5625 12.5117 18.5625 10.5834C18.5625 5.90044 14.7663 2.10419 10.0833 2.10419C5.40042 2.10419 1.60417 5.90044 1.60417 10.5834C1.60417 15.2663 5.40042 19.0625 10.0833 19.0625C12.0117 19.0625 13.7896 18.4188 15.2141 17.3346L18.4398 20.5602C18.8873 21.0077 19.6128 21.0077 20.0602 20.5602C20.5077 20.1128 20.5077 19.3873 20.0602 18.9398L16.8346 15.7141Z" fill="#475467"/>
                                        </svg>
                                </label>
                            </div>
                        </div>
                        <div>
                            <form
                                action="{{route('groups.time-recording-system.schedulework.schedule.assignment.user.store')}}"
                                method="POST">
                                @csrf
                                <input type="text" name="month" value="{{$monthId}}" hidden>
                                <input type="text" name="year" value="{{$year}}" hidden>
                                <input type="text" name="workScheduleId" value="{{$workSchedule->id}}" hidden>
                                <div class="dataTables_wrapper dt-bootstrap4">
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
                                                            <th>ตำแหน่ง</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="employee_tbody">
                                                        @foreach ($users as $user)
                                                        <tr>
                                                            <td>
                                                                <div class="icheck-primary d-inline">
                                                                    <input name="users[]" type="checkbox"
                                                                        class="user-checkbox"
                                                                        id="checkboxPrimary{{$user->id}}"
                                                                        value="{{$user->id}}">
                                                                    <label for="checkboxPrimary{{$user->id}}"></label>
                                                                </div>
                                                            </td>
                                                            <td>{{$user->employee_no}}</td>
                                                            <td>{{$user->prefix->name}}{{$user->name}} {{$user->lastname}}
                                                            </td>
                                                            <td>{{$user->company_department->name}}</td>
                                                            <td>{{$user->user_position->name}}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                {{ $users->links() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="cif-modal-footer mt-2">
                                        @if ($permission->create)
                                        <button type="submit"
                                            class="btn btn-primary">บันทึก</button>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script type="module" src="{{asset('assets/js/helpers/time-recording-system/schedule/assignment/assignment.js?v=1')}}">
</script>
<script src="{{asset('assets/js/helpers/helper.js?v=1')}}"></script>
<script>
    window.params = {
        searchRoute: '{{ route('groups.time-recording-system.schedulework.schedule.assignment.user.search') }}',
        url: '{{ url('/') }}',
        token: $('meta[name="csrf-token"]').attr('content')
    };

</script>
@endpush
@endsection