@extends('layouts.dashboard')

@section('content')
<div>
    <div>
        <div class="container-fluid">
            <div class="title-header">
                <div>
                    <h3 class="m-0">รายการลา</h3>
                </div>
                <div  aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active">รายการลา</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">รายการลาล่าสุด</h4>
                           <div class="d-flex gap-2">
                                <a class="btn btn-header" href="{{route('groups.document-system.leave.document.create')}}">
                                    <i class="fas fa-plus">
                                    </i>
                                    เพิ่มการลา
                                </a>
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
                        </div>
                        <div>
                            <div class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12" id="table_container">
                                        <div class="table-responsive">
                                            <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                                <thead class="border-bottom">
                                                    <tr>
                                                        <th>สายอนุมัติ</th>
                                                        <th>รหัสพนักงาน</th>
                                                        <th>ชื่อสกุล</th>
                                                        <th>แผนก</th>
                                                        <th>ประเภทการลา</th>
                                                        <th>ช่วงวันที่</th>
                                                        <th>หัวหน้างาน</th>
                                                        <th>สถานะ</th>

                                                        <th class="text-end">เพิ่มเติม</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($leaves as $key=> $leave)
                                                    @php
                                                    $approver =
                                                    $leave->user->approvers->where('document_type_id',1)->first()
                                                    @endphp
@if($approver !== null)
                                                    <tr>
                                                        <td>{{$approver->code}}</td>
                                                        <td>{{$leave->user->employee_no}}</td>
                                                        <td>{{$leave->user->name}} {{$leave->user->lastname}}</td>
                                                        <td>{{$leave->user->company_department->name}}</td>
                                                        <td>{{$leave->leaveType->name}}</td>
                                                        <td>{{ date_create_from_format('Y-m-d H:i:s',
                                                            $leave->from_date)->format('d/m/Y H:i') }} - {{
                                                            date_create_from_format('Y-m-d H:i:s',
                                                            $leave->to_date)->format('d/m/Y H:i') }}</td>
                                                        <td>
                                                            {{$approver->name}}
                                                            <br>
                                                            <span class="ml-3">-{{$approver->user->name}}
                                                                {{$approver->user->lastname}} (ผู้จัดการ)</span>
                                                            @foreach ($approver->authorizedUsers as $user)
                                                            <br>
                                                            <span class="ml-3">-{{$user->name}}
                                                                {{$user->lastname}}</span>

                                                            @endforeach
                                                        </td>
                                                        <td>@if ($leave->status === null)
                                                            <span class="badge bg-primary rounded-3" style="padding: 8px 12px">รออนุมัติ</span>
                                                            @elseif ($leave->status === '1')
                                                            <span class="badge bg-success rounded-3" style="padding: 8px 12px">อนุมัติแล้ว</span>
                                                            @elseif ($leave->status === '2')
                                                            <span class="badge bg-danger rounded-3" style="padding: 8px 12px">ไม่อนุมัติ</span>
                                                            @endif
                                                        </td>

                                                        <td class="text-end">

                                                            @if (!empty($leave->attachment))
                                                            <a class="btn btn-action btn-links btn-sm show-attachment"
                                                                data-id="{{$leave->id}}">
                                                                <i class="fas fa-link"></i>
                                                            </a>
                                                            @endif
                                                            @if ($leave->status === null)
                                                            <a class="btn btn-action btn-edit btn-sm"
                                                                href="{{route('groups.document-system.leave.document.view',['id' => $leave->id])}}">
                                                                <i class="fas fa-pencil-alt"></i>
                                                            </a>
                                                            <a class="btn btn-action btn-delete btn-sm"
                                                                data-confirm='ลบรายการลา "{{$leave->user->name}} {{$leave->user->lastname}}" หรือไม่?'
                                                                href="#" data-id="{{$leave->id}}"
                                                                data-delete-route="{{ route('groups.document-system.leave.document.delete', ['id' => '__id__']) }}"
                                                                data-message="รายการลา">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                            @endif

                                                        </td>
                                                    </tr>
@endif                      
                              @endforeach
                                                </tbody>
                                            </table>
                                            {{$leaves->links()}}
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
    <div class="modal fade" id="modal-attachment">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <img src="" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script type="module" src="{{asset('assets/js/helpers/document-system/leave/document.js?v=1')}}"></script>
<script src="{{asset('assets/js/helpers/helper.js?v=1')}}"></script>
<script>
    $('.select2').select2()
    window.params = {
        searchRoute: '{{ route('groups.document-system.leave.document.search') }}',
        getAttachmentRoute: '{{ route('groups.document-system.leave.document.get-attachment') }}',
        url: '{{ url('/') }}',
        token: $('meta[name="csrf-token"]').attr('content')
    };
</script>
@endpush
@endsection
