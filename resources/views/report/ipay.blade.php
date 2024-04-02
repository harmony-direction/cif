@extends('layouts.dashboard')

@section('content')
    <div>
        <div>
            <div class="container-fluid">
                <div class="title-header">
                    <div>
                        <h3 class="m-0">{{ $typeData }}</h3>
                    </div>
                    <div aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">หน้าหลัก</a></li>
                            <li class="breadcrumb-item active">เลือกเดือน/ปี ที่ต้องการตรวจสอบรายได้</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                    @if ($permission->show)
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>เดือน</label>
                                            <select name="month" id="month"
                                                class="form-control select2 @error('month') is-invalid @enderror"
                                                style="width: 100%;">
                                                @foreach ($months as $month)
                                                <option value="{{$month->id}}" {{ $month->id == date('m') ? 'selected' : ''
                                                    }}>{{$month->name}}
                                                </option>
                                                @endforeach
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
                    @endif
            </div>
        </div>
    </div>
    @push('scripts')
        <script type="module" src="{{ asset('assets/js/helpers/user-management-system/setting/userinfo.js?v=1') }}"></script>
        <script src="{{ asset('assets/js/helpers/helper.js?v=1') }}"></script>
        <script>
            $(document).ready(function(){
                    $('#search_work_schedule').click(function(){
                        var year = $('#year').val();
                        var month = $('#month').val();
                        var url = "{{ route('getPage', ['year' => ':year', 'month' => ':month']) }}".replace(':year', year).replace(':month', month);

                        window.location.href = url;
                    });
                });
        </script>
    @endpush
@endsection
