@extends('layouts.dashboard')

@section('content')
    {{-- <div>
        <div>
            <div class="container-fluid">
                <div class="title-header">
                    <div>
                        <h3 class="m-0">ข้อมูลพนักงาน รายชื่อพนักงานผู้เสียเงินได้ ภ.ง.ด. 1 (ประจำเดือน)</h3>
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
                        var url = "{{ route('rd1', ['year' => ':year', 'month' => ':month']) }}".replace(':year', year).replace(':month', month);

                        window.location.href = url;
                    });
                });
        </script>
    @endpush --}}

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/loading.css?v=1.0') }}">
    @endpush
    <div>
        @include('layouts.partial.loading')
        <div class="content-header">
            <div class="container-fluid">
                <div class="title-header">
                    <div>
                        <h3 class="m-0">ข้อมูลพนักงาน พนักงานผู้เสียเงินได้ ภ.ง.ด. 1 (ประจำเดือน)</h3>
                    </div>
                    <div aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">หน้าหลัก</a>
                            </li>
                            <li class="breadcrumb-item active">รอบเงินเดือนงวดปกติ</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                @if ($permission->show)
                    <div class="row">
                        <div class="col-12" id="content_wrapper">
                            <div class="card card-tabs pe-2">
                                <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                                    @foreach ($paydays->where('type', 1) as $key => $payday)
                                        <li class="nav-item">
                                            <a class="nav-link {{ $key === 0 ? 'active' : '' }}"
                                                id="custom-tabs-{{ $payday->id }}-tab" data-bs-toggle="tab"
                                                href="#custom-tabs-{{ $payday->id }}" role="tab"
                                                aria-controls="custom-tabs-{{ $payday->id }}"
                                                aria-selected="true">{{ $payday->name }}</a>
                                        </li>
                                    @endforeach
                                    <li class="ms-auto my-2">
                                        <div class="card-tools">
                                            <div class="input-group input-group-sm" style="width: 150px;">
                                                <select name="year" id="year" class="form-control select2"
                                                    style="width: 100%;">
                                                    @if (count($years) >= 1)
                                                        @foreach ($years as $year)
                                                            <option value="{{ $year }}"
                                                                {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option value="" disabled selected>ยังไม่มีข้อมูล</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <div>
                                    <div class="tab-content" id="custom-tabs-three-tabContent">
                                        @foreach ($paydays->where('type', 1) as $key => $payday)
                                            <div class="tab-pane fade show {{ $key === 0 ? 'active' : '' }} table-responsive"
                                                id="custom-tabs-{{ $payday->id }}" role="tabpanel"
                                                aria-labelledby="custom-tabs-{{ $payday->id }}-tab">
                                                <table class="table table-borderless text-nowrap">
                                                    <thead class="border-bottom">
                                                        <tr>
                                                            <th>ต้นงวด</th>
                                                            <th>ปลายงวด</th>
                                                            <th>การคำนวน</th>
                                                            <th>สถานะ</th>
                                                            <th style="width: 150px">เพิ่มเติม</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($payday->paydayDetails as $key => $paydayDetail)
                                                            @php

                                                                $startDate = \Carbon\Carbon::parse(
                                                                    $paydayDetail->start_date,
                                                                );
                                                                $endDate = \Carbon\Carbon::parse(
                                                                    $paydayDetail->end_date,
                                                                );
                                                                $currentMonth = \Carbon\Carbon::now()->month;
                                                                $paymentDate = \Carbon\Carbon::parse(
                                                                    $paydayDetail->payment_date,
                                                                );
                                                                $currentDate = \Carbon\Carbon::now();
                                                            @endphp

                                                            @if ($startDate->month <= $currentMonth)
                                                                <tr>
                                                                    <td>

                                                                        {{ $startDate->format('d/m/Y') }}
                                                                        @if ($paymentDate->gte($currentDate) && $currentDate->gte($endDate))
                                                                            <i class="fas fa-fire-alt text-danger"></i>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $endDate->format('d/m/Y') }}</td>
                                                                    <td>
                                                                        @if ($currentDate->gte($paymentDate))
                                                                            <span class="badge rounded-3"
                                                                                style="padding: 8px 12px; background: #F9FAFB;">หมดเวลา</span>
                                                                        @elseif($paymentDate->gte($currentDate) && $currentDate->gte($endDate))
                                                                            <span class="badge bg-success rounded-3"
                                                                                style="padding: 8px 12px">อยู่ในช่วงทำรายการ</span>
                                                                        @else
                                                                            <span
                                                                                class="badge bg-warning rounded-3 text-primary"
                                                                                style="padding: 8px 12px">รอบต่อไป</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if (count($paydayDetail->SalarySummary()->get()) != 0)
                                                                            <span class="badge rounded-3 text-primary"
                                                                                style="padding: 8px 12px; background: #D1FADF">ปิดงวด</span>
                                                                        @else
                                                                            <span
                                                                                class="badge rounded-3 text-primary bg-warning"
                                                                                style="padding: 8px 12px;">ปกติ</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <a href="{{ route('rd1', ['year' => $year, 'month' => $startDate->month, 'id' => $paydayDetail->id]) }}">
                                                                        <button class="btn btn-primary d-flex gap-2 align-items-center" id="search_work_schedule">
                                                                            <i class="fas fa-search"></i>ค้นหา</button>
                                                                        </a>
                                                                        {{-- <a href="{{ route('groups.salary-system.salary.calculation-list.calculation', ['id' => $paydayDetail->id]) }}"
                                                                            class="btn btn-calculator btn-action">
                                                                            <i class="fas fa-calculator"></i>
                                                                        </a> --}}
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endforeach
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
        <script type="module" src="{{ asset('assets/js/helpers/salary-system/salary/calculation-list/index.js?v=1') }}">
        </script>
        <script src="{{ asset('assets/js/helpers/helper.js?v=1') }}"></script>
        <script>
            window.params = {
                searchRoute: '{{ route('groups.salary-system.salary.calculation-list.search') }}',
                url: '{{ url('/') }}',
                token: $('meta[name="csrf-token"]').attr('content')
            };
            $('.select2').select2()
        </script>
    @endpush
@endsection
