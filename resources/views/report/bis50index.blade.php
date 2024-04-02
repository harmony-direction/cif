@extends('layouts.dashboard')

@section('content')
<div>
    <div>
        <div class="container-fluid">
            <div class="title-header">
                <div>
                    <h3 class="m-0">ข้อมูลพนักงาน {{ isset($year) ? 'ประจำปี '.$year:'' }}</h3>
                </div>
                <div aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active">รายการพนักงาน</li>
                    </ol>
                </div>
            </div>

            <div class="card card-info card-outline">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>ปี</label>
                                <select name="year" id="year"
                                    class="form-control select2 @error('year') is-invalid @enderror"
                                    style="width: 100%;">
                                    @if (count($years) >= 1)
                                        @foreach ($years as $year)
                                        <option value="{{$year}}" {{ $year==date('Y') ? 'selected' : '' }}>{{$year}}
                                        </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled selected>ยังไม่มีข้อมูล</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                    </div>
                        <div class="mt-2 d-flex justify-content-end">
                            <button class="btn btn-primary d-flex gap-2 align-items-center" id="search_work_schedule">
                                <i class="fas fa-search"></i>ค้นหา</button>
                        </div>

                </div>
            </div>
        </div>
    </div>
    <div class="content mt-3">
        <div class="container-fluid">
            @if ($permission->show)
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title m-0">รายชื่อพนักงาน</h4>
                            <div class="card-tools search">
                                <input type="text" name="search_query" id="search_query"
                                    class="form-control" placeholder="ค้นหา">
                                    <label for="search_query">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="23" viewBox="0 0 22 23" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0833 4.39585C6.66608 4.39585 3.89584 7.16609 3.89584 10.5834C3.89584 14.0006 6.66608 16.7709 10.0833 16.7709C11.7446 16.7709 13.2529 16.1162 14.3644 15.0507C14.3915 15.0167 14.4208 14.9838 14.4523 14.9523C14.4838 14.9208 14.5167 14.8915 14.5507 14.8644C15.6162 13.7529 16.2708 12.2446 16.2708 10.5834C16.2708 7.16609 13.5006 4.39585 10.0833 4.39585ZM16.8346 15.7141C17.9188 14.2897 18.5625 12.5117 18.5625 10.5834C18.5625 5.90044 14.7663 2.10419 10.0833 2.10419C5.40042 2.10419 1.60417 5.90044 1.60417 10.5834C1.60417 15.2663 5.40042 19.0625 10.0833 19.0625C12.0117 19.0625 13.7896 18.4188 15.2141 17.3346L18.4398 20.5602C18.8873 21.0077 19.6128 21.0077 20.0602 20.5602C20.5077 20.1128 20.5077 19.3873 20.0602 18.9398L16.8346 15.7141Z" fill="#475467"/>
                                          </svg>
                                    </label>
                            </div>
                        </div>
                        <div>
                            <div class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12" id="table_container">
                                        <div class="table-responsive">
                                            <table class="table table-borderless text-nowrap dataTable dtr-inline"
                                                id="userTable">
                                                <thead class="border-bottom">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>รหัสพนักงาน</th>
                                                        <th>ชื่อ-สกุล</th>
                                                        <th>แผนก</th>
                                                        <th>ประเภท</th>
                                                        <th>ตำแหน่ง</th>
                                                        <th class="text-end">พิมพ์ ทวิ 50</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="employee_tbody">
                                                    @foreach ($users as $key => $user)
                                                    <tr>
                                                        <td>{{($key + 1 + $users->perPage() * ($users->currentPage() - 1))}}
                                                        </td>
                                                        <td>{{$user->employee_no}}</td>
                                                        <td>{{$user->prefix->name}}{{$user->name}} {{$user->lastname}}</td>
                                                        <td>{{$user->company_department->name}}</td>
                                                        <td>{{$user->employee_type->name}}</td>
                                                        <td>{{$user->user_position->name}}</td>
                                                        <td class="text-end">
                                                            <a class="btn btn-action btn-edit btn-sm"
                                                                href="{{route('bis50',['id' => $user->id, 'year' => $year])}}">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            {{-- <a class="btn btn-danger btn-sm"
                                                                data-confirm='ลบพนักงาน "{{$user->name}} {{$user->lastname}}" หรือไม่?'
                                                                href="#" data-id="{{$user->id}}"
                                                                data-delete-route="{{ route('setting.organization.employee.delete', ['id' => '__id__']) }}"
                                                                data-message="ผู้ใช้งาน">
                                                                <i class="fas fa-trash"></i>
                                                            </a> --}}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            {{ $users->links() }}
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
<script type="module" src="{{asset('assets/js/helpers/user-management-system/setting/userinfo.js?v=1')}}">
</script>
<script src="{{asset('assets/js/helpers/helper.js?v=1')}}"></script>

<script>
    $(document).ready(function(){
            $('#search_work_schedule').click(function(){
                var year = $('#year').val();
                var url = "{{ route('bis50.list.search', ['year' => ':year']) }}".replace(':year', year);

                window.location.href = url;
            });
        });
</script>

@endpush
@endsection
