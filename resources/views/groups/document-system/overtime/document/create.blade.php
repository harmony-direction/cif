@extends('layouts.dashboard')

@section('content')
<div>
    <div>
        <div class="container-fluid">
            <div class="title-header">
                <div>
                    <h3 class="m-0">เพิ่มรายการล่วงเวลา</h3>
                </div>
                <div aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a
                                href="{{route('groups.document-system.overtime.document')}}">ล่วงเวลา</a>
                        </li>
                        <li class="breadcrumb-item active">เพิ่มรายการลา</li>
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
                            <h4 class="card-title">เพิ่มรายการล่วงเวลา</h4>
                        </div>
                        <form action="{{route('groups.document-system.overtime.document.store')}}" method="POST">
                            <div class="card-body">
                            @csrf
                                <div class="row gy-2">
                                    <input type="text" name="manual_time" id="manual_time" value="1" hidden>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>ชื่อรายการล่วงเวลา<span class="small text-danger">*</span></label>
                                            <input type="text" name="name" id="name" value="{{old('name')}}"
                                                class="form-control  @error('name') is-invalid @enderror">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>ประเภท<span class="small text-danger">*</span></label>
                                            <select name="type" id="type" class="form-control select2"
                                                style="width: 100%;">
                                                <option value="1">วันทำงานปกติ</option>
                                                <option value="2">วันหยุด</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>สายอนุมัติ<span class="small text-danger">*</span></label>
                                            <select name="approver" id="approver" class="form-control select2"
                                                style="width: 100%;">
                                                @foreach ($approvers as $approver)
                                                <option value="{{$approver->id}}">{{$approver->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-b">
                                        <hr>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <div class="form-group clearfix mt-2">
                                            <div class="icheck-primary d-inline me-2 ">
                                                <input type="radio" id="radFixHour" name="rad" checked>
                                                <label for="radFixHour">กำหนดชั่วโมง
                                                </label>
                                            </div>
                                            <div class="icheck-primary d-inline">
                                                <input type="radio" id="radManualHour" name="rad">
                                                <label for="radManualHour">กำหนดเวลาเอง
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>วันที่เริ่ม (วดป. คศ)<span class="small text-danger">*</span></label>
                                            <input type="text" name="startDate" id="startDate"
                                                value="{{old('startDate')}}"
                                                class="form-control input-date-format @error('startDate') is-invalid @enderror">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>ถึงวันที่ (วดป. คศ)<span class="small text-danger">*</span></label>
                                            <input type="text" name="endDate" id="endDate" value="{{old('endDate')}}"
                                                class="form-control input-date-format @error('endDate') is-invalid @enderror">
                                        </div>
                                    </div>
                                    <div class="col-lg-12" id="content_wrapper">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>จำนวนชั่วโมง<span class="small text-danger">*</span></label>
                                                    <input type="text" name="hour_duration" id="hour_duration" value="6"
                                                        class="form-control  @error('hour_duration') is-invalid @enderror">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="card-footer card-create">
                                <button class="btn btn-primary">บันทึก</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-users">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12" id="modal-user-wrapper">

                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-primary" id="save_authorized_user">เพิ่มรายการ</button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script type="module" src="{{asset('assets/js/helpers/document-system/overtime/document/create.js?v=1')}}">
</script>
<script src="{{ asset('assets/js/helpers/helper.js?v=1') }}"></script>
<script>
    $('.select2').select2()
    window.params = {
        getUsersRoute: '{{ route('groups.document-system.overtime.document.get-users') }}',
        url: '{{ url('/') }}',
        token: $('meta[name="csrf-token"]').attr('content')
    };

    $(document).on('click', '#radFixHour, #radManualHour', function () {
    var selection = 0;
    
    if ($('#radFixHour').is(':checked')) {
    selection = 1;
    $('#manual_time').val(1);
    // $('#content_wrapper_1').show();
    $('#content_wrapper').show();
    $('#startDate, #endDate').removeClass('input-datetime-format').addClass('input-date-format').inputmask('99/99/9999');
    } else if ($('#radManualHour').is(':checked')) {
    selection = 2;
    // $('#content_wrapper_1').show();
    $('#manual_time').val(2);
    $('#content_wrapper').hide();
    $('#startDate, #endDate').removeClass('input-date-format').addClass('input-datetime-format').inputmask('99/99/9999 99:99');
    }
    
    // console.log('Selection:', selection);
    });

</script>
@endpush
@endsection