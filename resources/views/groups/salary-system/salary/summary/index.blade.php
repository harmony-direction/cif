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
                    <h3 class="m-0">รายการเงินเดือน</h3>
                    <ul class="mt-2">
                        @foreach ($paydayDetails as $paydayDetail)
                        <li>
                            <h4>{{$paydayDetail->payday->name}} (รอบเงินเดือน {{date('d/m/Y',
                                strtotime($paydayDetail->start_date))}}
                                -
                                {{date('d/m/Y', strtotime($paydayDetail->end_date))}})</h4>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a
                                href="{{route('groups.time-recording-system.schedulework.time-recording')}}">ตารางทำงาน</a>
                        </li>
                        <li class="breadcrumb-item active">นำเข้าไฟล์เวลา ddd</li>
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
                        <div class="card-header d-flex justify-content-between">
                            <h4 class="card-title">พนักงาน</h4>
                            @if (count($users) !=0)
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 150px;">
                                    <input type="text" name="search_query" id="search_query"
                                        class="form-control" placeholder="ค้นหา">
                                </div>
                            </div>
                            @endif

                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12" id="table_container">
                                    <div class="table-responsive">
                                        <table class="table table-borderless text-nowrap dataTable dtr-inline">
                                            <thead class="border-bottom">
                                                <tr>
                                                    <th style="width: 8%">รหัสพนักงาน</th>
                                                    <th style="width: 15%">ชื่อ-สกุล</th>
                                                    <th class="text-center" style="width: 13%">เงินเดือน</th>
                                                    <th class="text-center" style="width: 13%">ล่วงเวลา</th>
                                                    <th class="text-center" style="width: 13%">เบี้ยขยัน</th>
                                                    <th class="text-center" style="width: 13%">เงินได้</th>
                                                    <th class="text-center" style="width: 13%">เงินหัก</th>
                                                    <th class="text-center" style="width: 10%">สุทธิ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($users as $user)
                                                @php
                                                // $paydayDetailWithToday = $user->getPaydayDetailWithToday();
                                                $userSummary = $user->userSummary();
                                                @endphp
                                                <tr>

                                                    <td>
                                                        {{-- @if (count($user->getErrorDate()) > 0)
                                                        <i class="fas fa-times-circle text-danger"></i>
                                                        @else
                                                        <i class="fas fa-check-circle text-success"></i>
                                                        @endif --}}
                                                        {{ $user->employee_no }}

                                                    </td>
                                                    <td>{{ $user->prefix->name }}{{
                                                        $user->name }} {{
                                                        $user->lastname }}</td>
                                                    <td class="text-center">{{$userSummary['salary']}}</td>
                                                    <td class="text-center">{{$userSummary['overTimeCost']}}</td>
                                                    <td class="text-center">{{$userSummary['deligenceAllowance']}}
                                                    </td>

                                                    <td class="text-start ">

                                                        @foreach ($user->getSummaryIncomeDeductByUsers(1)
                                                        as $getIncomeDeductByUser)
                                                        <li>{{$getIncomeDeductByUser->incomeDeduct->name}}
                                                            ({{$getIncomeDeductByUser->value}})</li>
                                                        @endforeach
                                                    </td>
                                                    <td class="text-start">
                                                        @foreach ($user->getSummaryIncomeDeductByUsers(2)
                                                        as $getIncomeDeductByUser)
                                                        <li>{{$getIncomeDeductByUser->incomeDeduct->name}}
                                                            ({{$getIncomeDeductByUser->value}})</li>
                                                        @endforeach
                                                    </td>
                                                    <td class="text-center">xx</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        {{$users->links()}}
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
<div class="modal-footer justify-content-between">
    {{-- <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button> --}}
    <button type="button" class="btn btn-primary" id="bntUpdateReportField">ต่อไป</button>
</div>
@push('scripts')

<script type="module" src="{{ asset('assets/js/helpers/salary-system/salary/calculation/index.js?v=1') }}">
</script>
<script src="{{asset('assets/js/helpers/helper.js?v=1')}}"></script>
<script>
    window.params = {
        searchRoute: '{{ route('groups.salary-system.salary.calculation.search') }}',        
        url: '{{ url('/') }}',
        token: $('meta[name="csrf-token"]').attr('content')
    };
</script>

@endpush
@endsection