@extends('layouts.setting-dashboard')

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
                        <li class="breadcrumb-item"><a href="{{route('home')}}">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active">นำเข้าพนักงาน</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                เกิดข้อผิดพลาดการนำเข้า โปรดตรวจสอบไฟล์นำเข้าให้ถูกต้อง
                @if ($errors->get('error_users'))
                <ul>
                    @foreach ($errors->get('error_users') as $key => $user)
                        <li>{{ $user }}</li>
                    @endforeach
                </ul>
            @endif
            </div>
            @endif
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">วิธีการนำเข้าพนักงาน</h4>
                        </div>
                        <div class="card-body">
                            <p>การนำเข้าพนักงานโดยใช้ไฟล์เทมเพลต จะต้องตรวสอบข้อมูลให้ถูกต้องและห้ามลบแถวแรกของตาราง
                                ถ้าช่องคอลัมน์ที่ไฮไลต์สีแดงจะต้องใส่ให้ครบ</p>
                            <strong>ดาวน์โหลดเทมเพลต</strong>
                            <div>
                                <a href="https://fontawesome.com/">เทมเพลตนำเข้าพนักงาน</a><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <form action="{{route('setting.organization.employee.import.store')}}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        {{-- <input type="file" name="file" accept=".xlsx, .xls, .csv"> --}}
                        <div class="form-group">

                            <div class="custom-file">
                                <input type="file" name="file" class="custom-file-input" accept=".xlsx, .xls, .csv"
                                    id="customFile">
                                {{-- <label class="custom-file-label" for="customFile">เลือกไฟล์</label> --}}
                                <label class="custom-file-label" for="customFile">เลือกไฟล์ exel</label>
                            </div>

                        </div>
                        <button type="submit" class="btn btn-primary mb-2">นำเข้าพนักงาน</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function () {
        bsCustomFileInput.init();
        });
</script>
@endpush
@endsection