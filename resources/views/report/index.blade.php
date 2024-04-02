@extends('layouts.dashboard')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/loading.css?v=1.0') }}">
    <style>
        a{
            text-decoration: none;
            color: #1d1d34;
        }
    </style>
@endpush
<div>
    @include('layouts.partial.loading')
    <div>
        <div class="container-fluid">
            <div class="title-header">
                <div>
                    <h3 class="m-0">สรรพกร

                        {{-- {{$month->name}} --}}
                    </h3>
                </div>
                <div aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{route('home')}}">หน้าหลัก</a>
                        </li>
                        <li class="breadcrumb-item active">รายงาน</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 col-sm-6 col-12 px-2 mb-3">
                    <a href="{{ route('bis50.list') }}">
                        <div class="d-flex gap-4 p-4 bg-white rounded-4">
                            <span class="material-symbols-outlined text-white rounded-circle d-flex justify-content-center align-items-center" style="background: #35cdaa; width: 4rem; height: 4rem; font-size: 36px;">
                                bar_chart
                            </span>
                            <div class="d-flex flex-column justify-content-between">
                                <span>หนังสือรับรองการหักภาษี ณ ที่จ่าย</span>
                                <h2 class="m-0">ทวิ 50</h2>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 col-12 px-2 mb-3">
                    <a href="{{ route('rd1.list') }}">
                        <div class="d-flex gap-4 p-4 bg-white rounded-4">
                            <span class="material-symbols-outlined text-white rounded-circle d-flex justify-content-center align-items-center" style="background: #35cdaa; width: 4rem; height: 4rem; font-size: 36px;">
                                calendar_month
                            </span>
                            <div class="d-flex flex-column justify-content-between">
                                <span>รายชื่อพนักงานผู้เสียภาษีเงินได้</span>
                                <h5 class="m-0">ภ.ง.ด.1 (สรุปรายเดือน)</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 col-12 px-2 mb-3">
                    <a href="{{ route('rd1.list.year') }}">
                        <div class="d-flex gap-4 p-4 bg-white rounded-4">
                            <span class="material-symbols-outlined text-white rounded-circle d-flex justify-content-center align-items-center" style="background: #35cdaa; width: 4rem; height: 4rem; font-size: 36px;">
                                calendar_today
                            </span>
                            <div class="d-flex flex-column justify-content-between">
                                <span>รายชื่อพนักงานผู้เสียภาษีเงินได้ </span>
                                <h5 class="m-0">ภ.ง.ด.1 (สรุปรายปี)</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 col-12 px-2 mb-3">
                    <a href="{{ route('rd2.list') }}">
                        <div class="d-flex gap-4 p-4 bg-white rounded-4">
                            <span class="material-symbols-outlined text-white rounded-circle d-flex justify-content-center align-items-center" style="background: #35cdaa; width: 4rem; height: 4rem; font-size: 36px;">
                                calendar_month
                            </span>
                            <div class="d-flex flex-column justify-content-between">
                                <span>ใบปะแบบยื่นรายการภาษีเงินได้ </span>
                                <h5 class="m-0">ภ.ง.ด.1 (รายเดือน)</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 col-12 px-2 mb-3">
                    <a href="{{ route('rd2.list.year') }}">
                        <div class="d-flex gap-4 p-4 bg-white rounded-4">
                            <span class="material-symbols-outlined text-white rounded-circle d-flex justify-content-center align-items-center" style="background: #35cdaa; width: 4rem; height: 4rem; font-size: 36px;">
                                calendar_today
                            </span>
                            <div class="d-flex flex-column justify-content-between">
                                <span>ใบปะแบบยื่นรายการภาษีเงินได้ </span>
                                <h5 class="m-0">ภ.ง.ด.1 (รายปี)</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 col-12 px-2 mb-3">
                    <a href="{{ route('pnd.list') }}">
                        <div class="d-flex gap-4 p-4 bg-white rounded-4">
                            <span class="material-symbols-outlined text-white rounded-circle d-flex justify-content-center align-items-center" style="background: #35cdaa; width: 4rem; height: 4rem; font-size: 36px;">
                                description
                            </span>
                            <div class="d-flex flex-column justify-content-between">
                                <span>ไฟล์แนบการยื่นภาษีสรรพกร </span>
                                <h5 class="m-0">PND (รายเดือน)</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 col-12 px-2 mb-3">
                    <a href="{{ route('pnd.list.year') }}">
                        <div class="d-flex gap-4 p-4 bg-white rounded-4">
                            <span class="material-symbols-outlined text-white rounded-circle d-flex justify-content-center align-items-center" style="background: #35cdaa; width: 4rem; height: 4rem; font-size: 36px;">
                                description
                            </span>
                            <div class="d-flex flex-column justify-content-between">
                                <span>ไฟล์แนบการยื่นภาษีสรรพกร </span>
                                <h5 class="m-0">PND (รายปี)</h5>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <hr>
            <div>
                <div class="container-fluid">
                    <div class="title-header">
                        <div>
                            <h3 class="m-0">ประกันสังคม
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 col-sm-6 col-12 px-2 mb-3">
                    <a href="{{ route('ssoPayment.index', ['list' => 'index','type' => 'day']) }}">
                    <div class="d-flex gap-4 p-4 bg-white rounded-4">
                        <span class="material-symbols-outlined text-white rounded-circle d-flex justify-content-center align-items-center" style="background: #1a1c7f; width: 4rem; height: 4rem; font-size: 36px;">
                            admin_panel_settings
                        </span>
                        <div class="d-flex flex-column justify-content-between">
                            <span>แบบรายการแสดงการส่งเงินสมทบ </span>
                            <h5 class="m-0">สปส 1-10 (รายวัน)</h5>
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 col-12 px-2 mb-3">
                    <a href="{{ route('ssoPayment.index', ['list' => 'index','type' => 'month']) }}">
                    <div class="d-flex gap-4 p-4 bg-white rounded-4">
                        <span class="material-symbols-outlined text-white rounded-circle d-flex justify-content-center align-items-center" style="background: #1a1c7f; width: 4rem; height: 4rem; font-size: 36px;">
                            shield_person
                        </span>
                        <div class="d-flex flex-column justify-content-between">
                            <span>แบบรายการแสดงการส่งเงินสมทบ </span>
                            <h5 class="m-0">สปส 1-10 (รายเดือน)</h5>
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 col-12 px-2 mb-3">
                    <a href="{{ route('ssoPayment.index', ['list' => 'index','type' => 'all']) }}">
                    <div class="d-flex gap-4 p-4 bg-white rounded-4">
                        <span class="material-symbols-outlined text-white rounded-circle d-flex justify-content-center align-items-center" style="background: #1a1c7f; width: 4rem; height: 4rem; font-size: 36px;">
                            supervised_user_circle
                        </span>
                        <div class="d-flex flex-column justify-content-between">
                            <span>แบบรายการแสดงการส่งเงินสมทบ </span>
                            <h5 class="m-0">สปส 1-10 (ใบรวม)</h5>
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 col-12 px-2 mb-3">
                    <a href="{{ route('ssoPayment.index', ['list' => 'list','type' => 'day']) }}">
                    <div class="d-flex gap-4 p-4 bg-white rounded-4">
                        <span class="material-symbols-outlined text-white rounded-circle d-flex justify-content-center align-items-center" style="background: #1a1c7f; width: 4rem; height: 4rem; font-size: 36px;">
                            admin_panel_settings
                        </span>
                        <div class="d-flex flex-column justify-content-between">
                            <span>รายชื่อพนักงานแสดงการส่งเงินสมทบ </span>
                            <h5 class="m-0">สปส 1-10 (รายวัน)</h5>
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 col-12 px-2 mb-3">
                    <a href="{{ route('ssoPayment.index', ['list' => 'list','type' => 'month']) }}">
                    <div class="d-flex gap-4 p-4 bg-white rounded-4">
                        <span class="material-symbols-outlined text-white rounded-circle d-flex justify-content-center align-items-center" style="background: #1a1c7f; width: 4rem; height: 4rem; font-size: 36px;">
                            shield_person
                        </span>
                        <div class="d-flex flex-column justify-content-between">
                            <span>รายชื่อพนักงานแสดงการส่งเงินสมทบ </span>
                            <h5 class="m-0">สปส 1-10 (รายเดือน)</h5>
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 col-12 px-2 mb-3">
                    <a href="{{ route('ssoPayment.index', ['list' => 'list','type' => 'all']) }}">
                    <div class="d-flex gap-4 p-4 bg-white rounded-4">
                        <span class="material-symbols-outlined text-white rounded-circle d-flex justify-content-center align-items-center" style="background: #1a1c7f; width: 4rem; height: 4rem; font-size: 36px;">
                            supervised_user_circle
                        </span>
                        <div class="d-flex flex-column justify-content-between">
                            <span>รายชื่อพนักงานแสดงการส่งเงินสมทบ </span>
                            <h5 class="m-0">สปส 1-10 (ใบรวม)</h5>
                        </div>
                    </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 col-12 px-2 mb-3">
                    <a href="{{ route('ssoPayment.index', ['list' => 'file', 'type' => 'month']) }}">
                    <div class="d-flex gap-4 p-4 bg-white rounded-4">
                        <span class="material-symbols-outlined text-white rounded-circle d-flex justify-content-center align-items-center" style="background: #1a1c7f; width: 4rem; height: 4rem; font-size: 36px;">
                            bar_chart
                        </span>
                        <div class="d-flex flex-column justify-content-between">
                            <span>ไฟล์นำส่งข้อมูลเงินสมทบพนักงาน</span>
                            <h5 class="m-0">DAT FILE (CSV)</h5>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3 col-sm-6 col-12 px-2 mb-3">
                    <a href="{{ route('getReport.index', ['list' => 'ipay', 'type' => 'ipay']) }}">
                        <div class="d-flex gap-4 p-4 bg-white rounded-4">

                            <img src="https://play-lh.googleusercontent.com/gnZXRi9diY-yjEo424IBqPsPYtne9pF6ho6cniBKNWAYCQDVU4LkFjbbKxXvn69PzF4" style=" width: 4rem; height: 4rem; font-size: 36px;">
                            {{-- <span class="material-symbols-outlined text-white rounded-circle d-flex justify-content-center align-items-center" style="background: #ffc73a; width: 4rem; height: 4rem; font-size: 36px;">

                            </span> --}}
                            <div class="d-flex flex-column justify-content-between">
                                <span></span>
                                <h2 class="m-0">IPAY</h2>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 col-12 px-2 mb-3">
                    <a href="{{ route('getReport.index', ['list' => 'cashBank', 'type' => 'cashBank']) }}">
                        <div class="d-flex gap-4 p-4 bg-white rounded-4">
                            <span class="material-symbols-outlined text-white rounded-circle d-flex justify-content-center align-items-center" style="background: #56d5ff; width: 4rem; height: 4rem; font-size: 36px;">
                                account_balance
                            </span>
                            <div class="d-flex flex-column justify-content-between">
                                <span>รายงานโอนเงินเข้าธนาคาร</span>
                                <h2 class="m-0">BANK</h2>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

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
