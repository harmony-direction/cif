@extends('layouts.dashboard')

@section('content')
<div>
    <div>
        <div class="container-fluid">
            <div class="title-header">
                <div>
                    <h3 class="m-0">ตารางทำงาน: {{$workSchedule->name}} ปี{{$workSchedule->year}}</h3>
                </div>
                <div aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route($viewRoute)}}">ตารางทำงาน</a></li>
                        <li class="breadcrumb-item active">{{$workSchedule->name}}</li>
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
                        <div class="card-header">
                            <h4 class="card-title">ตารางทำงาน</h4>
                        </div>
                        <div class="table-responsive">

                            <table class="table table-borderless text-nowrap">
                                <thead class="border-bottom">
                                    <tr>
                                        <th>#</th>
                                        <th>ตารางทำงาน</th>
                                        <th>เดือน</th>
                                        <th>มอบหมายผู้ใช้</th>
                                        <th>เพิ่มกะทำงาน</th>
                                        <th class="text-end">เพิ่มเติม</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($months as $key=> $month)
                                    @php
                                    $shiftsAdded =
                                    $workSchedule->isAllShiftsAdded($month->id,$workSchedule->year);

                                    $isExpired = $shiftsAdded === 'หมดเวลา';
                                    @endphp

                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td>{{$workSchedule->name}}</td>
                                        <td>{{$month->name}} {{$workSchedule->year}}</td>
                                        <td>
                                            {{$workSchedule->getUsersByWorkScheduleAssignment($month->id,$workSchedule->year)->count()}}
                                        </td>
                                        <td>
                                            @if (isset($shiftsAdded) && $shiftsAdded->original['expired'])
                                            <span class="badge bg-danger" style="padding: 8px 12px;"">หมดเวลา</span>
                                            @else
                                            @if(isset($shiftsAdded) && $shiftsAdded->original['assigned'] )
                                            <span class="badge bg-success" style="padding: 8px 12px;"">เพิ่มแล้ว</span>
                                            @else
                                            <span class="badge bg-warning" style="padding: 8px 12px;"">ยังไม่ได้เพิ่ม</span>
                                            @endif
                                            {{-- @elseif ($shiftsAdded)
                                            <span class="badge bg-success">เพิ่มแล้ว</span>
                                            @else
                                            <span class="badge bg-danger">ยังไม่ได้เพิ่ม</span> --}}
                                            @endif
                                        </td>
                                        <td class="text-end">

                                            @if ($permission->create || $permission->update)

                                            @if(isset($shiftsAdded) && $shiftsAdded->original['assigned'] )

                                            <a class="btn btn-action btn-edit btn-sm"
                                                href="{{ route('groups.time-recording-system.schedulework.schedule.assignment.user', ['workScheduleId' => $workSchedule->id, 'year' => $workSchedule->year, 'month' => $month->id]) }}">
                                                <i class="fas fa-users"></i>
                                            </a>
                                            @endif

                                            <a class="btn btn-action btn-links btn-sm"
                                                href="{{ route('groups.time-recording-system.schedulework.schedule.assignment.work-schedule', ['workScheduleId' => $workSchedule->id, 'year' => $workSchedule->year, 'month' => $month->id]) }}">
                                                <i class="fas fa-link"></i>
                                            </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@push('scripts')
<script src="{{asset('assets/js/helpers/helper.js?v=1')}}"></script>

@endpush
@endsection