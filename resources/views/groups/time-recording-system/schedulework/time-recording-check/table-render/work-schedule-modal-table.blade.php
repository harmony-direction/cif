<table class="table table-borderless text-nowrap">
    <thead class="border-bottom">
        <tr>
            <th>วันที่เข้า</th>
            <th style="width:20%">เวลาเข้า</th>
            <th>วันที่ออก</th>
            <th style="width:20%">เวลาออก</th>
            @if (count($overtimeDetails) != 0)
            <th style="width:15%">O.T.</th>
            @endif

            <th class="text-end">บันทึก</th>
        </tr>
    </thead>
    <!-- Add a unique class to the <a> elements instead of using id -->
    <tbody>
        @foreach ($workScheduleAssignmentUsers as $key => $workScheduleAssignmentUser)
        <tr data-id="{{ $workScheduleAssignmentUser->id }}">
            <td>
                @php
                $moreThanOneHourLate =
                $workScheduleAssignmentUser->moreThanOneHourLate();
                @endphp
                {{ date('d/m/Y', strtotime($workScheduleAssignmentUser->date_in)) }}
                {!! (strpos($workScheduleAssignmentUser->workScheduleAssignment->shift->code, '_H') !== false ||
                strpos($workScheduleAssignmentUser->workScheduleAssignment->shift->code, '_TH') !== false) ? '<span
                    class="badge bg-success">วันหยุด</span>' : '' !!}

                @if (strpos($workScheduleAssignmentUser->workScheduleAssignment->shift->code, '_H') === false &&
                strpos($workScheduleAssignmentUser->workScheduleAssignment->shift->code, '_TH') === false)
                @if (empty($workScheduleAssignmentUser->time_in) || empty($workScheduleAssignmentUser->time_out))
                @php
                $checkDate = $workScheduleAssignmentUser->date_in; // Assuming the date is available as
                $leaveStatus = $workScheduleAssignmentUser->checkLeaveStatus($checkDate);
                @endphp

                @if ($leaveStatus['leave'] && $leaveStatus['approved'] && !$leaveStatus['rejected'])
                <span class="badge bg-success">L</span>
                @elseif ($leaveStatus['leave'] && !$leaveStatus['approved'] && !$leaveStatus['rejected'])
                <span class="badge bg-warning">L</span>
                @elseif ($leaveStatus['leave'] && !$leaveStatus['approved'] && $leaveStatus['rejected'])
                <span class="badge bg-danger">R</span>
                @else
                <span class="badge bg-danger" id="error_{{$workScheduleAssignmentUser->id}}">M</span>
                @endif
                @endif
                @endif

                @if (strpos($workScheduleAssignmentUser->code, 'E') !== false)
                <span class="badge bg-warning">E</span>
                @endif
                @if ($moreThanOneHourLate > 55)
                <i class="fas fa-exclamation-circle text-warning"></i>
                @endif

            </td>
            <td style="width: 25%">
                <input type="text" id="time_in[{{ $workScheduleAssignmentUser->id }}]"
                    class="form-control input-time-format" value="{{ $workScheduleAssignmentUser->time_in }}">
            </td>
            <td>{{ date('d/m/Y', strtotime($workScheduleAssignmentUser->date_out)) }}</td>
            <td style="width: 25%">
                <input type="text" id="time_out[{{ $workScheduleAssignmentUser->id }}]"
                    class="form-control input-time-format" value="{{ $workScheduleAssignmentUser->time_out }}">
            </td>
            @if (count($overtimeDetails) != 0)
            <td>
                @php
                $hour = '';
                $overtimeDetail = $workScheduleAssignmentUser->getOvertimeHourFromDate();
                if($overtimeDetail != null){
                $hour = $overtimeDetail->hour;
                }
                @endphp
                @if ($overtimeDetail != null)
                <input type="text" name="hour" id="hour" class="form-control integer" value="{{$hour}}"
                    data-overtimeid="{{$overtimeDetail->over_time_id}}"
                    data-userid="{{$workScheduleAssignmentUser->user_id}}">
                @endif
            </td>
            @endif

            <td class="text-end">
                @if (strpos($workScheduleAssignmentUser->workScheduleAssignment->shift->code, '_H') === false &&
                    strpos($workScheduleAssignmentUser->workScheduleAssignment->shift->code, '_TH') === false)
                    @if (empty($workScheduleAssignmentUser->time_in) || empty($workScheduleAssignmentUser->time_out))
                    <a class="btn btn-delete btn-action btn-sm btnAttachment" data-id="{{$workScheduleAssignmentUser->id}}">
                        <i class="fas fa-link"></i>
                    </a>
                    @endif
                    @endif
                    @if (!empty($workScheduleAssignmentUser->getAttachmentForDate()))
                    <a class="btn btn-warning btn-sm show-leave-attachment" data-id="{{$workScheduleAssignmentUser->id}}">
                        <i class="fas fa-leaf"></i>
                    </a>
                @endif
                <a class="btn btn-links btn-action btn-sm btnSaveBtn">
                    <i class="far fa-save"></i>
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>