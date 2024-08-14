<?php

namespace App\Http\Controllers\TimeRecordingSystems;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Month;
use App\Models\Shift;
use App\Models\SearchField;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use App\Models\YearlyHoliday;
use App\Models\WorkScheduleEvent;
use App\Http\Controllers\Controller;
use App\Models\WorkScheduleAssignment;
use App\Helpers\AddDefaultWorkScheduleAssignment;
use App\Services\UpdatedRoleGroupCollectionService;

class TimeRecordingSystemScheduleWorkScheduleAssignmentController extends Controller
{
    private $updatedRoleGroupCollectionService;
    private $addDefaultWorkScheduleAssignment;

    public function __construct(UpdatedRoleGroupCollectionService $updatedRoleGroupCollectionService, AddDefaultWorkScheduleAssignment $addDefaultWorkScheduleAssignment) 
    {
        $this->updatedRoleGroupCollectionService = $updatedRoleGroupCollectionService;
        $this->addDefaultWorkScheduleAssignment = $addDefaultWorkScheduleAssignment;
    }
    public function view($id)
    {
        
        // กำหนดตัวแปร $action ให้มีค่าเป็น 'update'
        $action = 'update';
        // ดึงค่า 'groupUrl' จาก session และแปลงเป็นข้อความ
        $groupUrl = strval(session('groupUrl'));

        // เรียกใช้งานเซอร์วิส updatedRoleGroupCollectionService เพื่อดึงข้อมูล updatedRoleGroupCollection, permission, viewRoute โดยใช้ค่า $action
        $roleGroupCollection = $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);
        $updatedRoleGroupCollection = $roleGroupCollection['updatedRoleGroupCollection'];
        $permission = $roleGroupCollection['permission'];
        $viewRoute = $roleGroupCollection['viewRoute'];

        // ค้นหาข้อมูล WorkSchedule จาก id ที่ระบุและเก็บในตัวแปร $workSchedule
        $workSchedule = WorkSchedule::findOrFail($id);

        // กำหนดค่าตัวแปร $year เท่ากับปีของ $workSchedule
        $year = $workSchedule->year;

        // กำหนดค่าตัวแปร $currentMonth เท่ากับเดือนปัจจุบัน
        $currentMonth = date('n');

        // ค้นหาเดือนที่ไม่ซ้ำกันจากการมอบหมายงานในปีเดียวกันและเก็บในตัวแปร $uniqueMonths
        // $uniqueMonths = $workSchedule->assignments()
        //     ->where('year', $year)
        //     ->distinct('month_id')
        //     ->pluck('month_id')
        //     ->filter(function ($month) use ($currentMonth) {
        //         return $month >= $currentMonth;
        //     });

        $uniqueMonths = $workSchedule->assignments()
            ->where('year', $year)
            ->distinct('month_id')
            ->pluck('month_id');    

        // ค้นหาข้อมูลเดือนจากตาราง Month โดยเลือกเฉพาะเดือนที่อยู่ใน $uniqueMonths และเก็บในตัวแปร $months
        $months = Month::whereIn('id', $uniqueMonths)->get();   

        // ค้นหาข้อมูล WorkSchedule จาก id ที่ระบุและเก็บในตัวแปร $workSchedule
        $workSchedule = WorkSchedule::find($id);

        // ส่งค่าตัวแปรไปยัง view 'groups.time-recording-system.schedulework.schedule.assignment.index'
        return view('groups.time-recording-system.schedulework.schedule.assignment.index', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'months' => $months,
            'workSchedule' => $workSchedule,
            'viewRoute' => $viewRoute
        ]);

    }
    public function createWorkSchedule($scheduleId,$year,$monthId)
    {
        // กำหนดตัวแปร $action ให้มีค่าเป็น 'create'
        $action = 'create';
        // ดึงค่า 'groupUrl' จาก session และแปลงเป็นข้อความ
        $groupUrl = strval(session('groupUrl'));

        // เรียกใช้งานเซอร์วิส updatedRoleGroupCollectionService เพื่อดึงข้อมูล updatedRoleGroupCollection, permission, viewRoute โดยใช้ค่า $action
        $roleGroupCollection = $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);
        $updatedRoleGroupCollection = $roleGroupCollection['updatedRoleGroupCollection'];
        $permission = $roleGroupCollection['permission'];
        $viewRoute = $roleGroupCollection['viewRoute'];

        // ค้นหาข้อมูลเดือนจากตาราง Month โดยใช้ค่า $monthId และเก็บในตัวแปร $month
        $month = Month::find($monthId);

        // ค้นหาข้อมูล WorkSchedule จาก scheduleId และเก็บในตัวแปร $workSchedule
        $workSchedule = WorkSchedule::find($scheduleId);

        // ค้นหาข้อมูล YearlyHoliday ที่ตรงกับปีและเดือนที่กำหนด และเก็บในตัวแปร $yearlyHolidays
        $yearlyHolidays = YearlyHoliday::where('year', $year)->where('month', $month->id)->get();

        // ค้นหาข้อมูล WorkSchedule จาก scheduleId และเก็บในตัวแปร $workSchedule
        $workSchedule = WorkSchedule::find($scheduleId);

        // ค้นหาข้อมูล WorkSchedule จาก scheduleId และเก็บในตัวแปร $workSchedule
        $workSchedule = WorkSchedule::find($scheduleId);

        // ค้นหาข้อมูล Shift ที่เกี่ยวข้องกับ $workSchedule และเก็บในตัวแปร $shifts
        $shifts = $workSchedule->shifts;

        // ค้นหาข้อมูล WorkScheduleEvent ที่ตรงกับเดือนและปีที่กำหนด และเก็บในตัวแปร $events
        $events = WorkScheduleEvent::where('month_id', $month->id)
            ->where('year', $year)
            ->where('work_schedule_id', $scheduleId)
            ->get()
            ->map(function ($event) {
                $shift = Shift::find($event->event_id);
                $mappedEvent = [
                    'id' => $event->event_id,
                    'title' => $event->event_name,
                    'start' => Carbon::parse($event->event_start_date)->format('Y-m-d'),
                    'backgroundColor' => $shift->color,
                    'borderColor' => $shift->color,
                    'allDay' => $event->long_event,
                ];

                if ($event->event_end_date !== null) {
                    $mappedEvent['end'] = Carbon::parse($event->event_end_date)
                        ->addDay()
                        ->format('Y-m-d');
                }

                return $mappedEvent;
            });

        // ส่งค่าตัวแปรไปยัง view 'groups.time-recording-system.schedulework.schedule.assignment.create'
        return view('groups.time-recording-system.schedulework.schedule.assignment.create', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'viewRoute' => $viewRoute,
            'month' => $month,
            'workSchedule' => $workSchedule,
            'year' => $year,
            'shifts' => $shifts,
            'events' => $events,
            'yearlyHolidays' => $yearlyHolidays
        ]);

    }
    public function storeCalendar(Request $request)
    {
        // รับค่า events, daySchedules, month, year, workScheduleId จาก request
        $events = $request->data['allEvents'];
        $daySchedules = $request->data['daySchedules'];
        $month = $request->data['month'];
        $year = $request->data['year'];
        $workScheduleId = $request->data['workScheduleId'];

        // ลบข้อมูล WorkScheduleEvent ที่ตรงกับ workScheduleId, month, year
        WorkScheduleEvent::where([
            'work_schedule_id' => $workScheduleId,
            'month_id' => $month,
            'year' => $year
        ])->delete();

        // เพิ่มข้อมูล WorkScheduleEvent จาก events
        foreach($events as $event)
        {
            $workScheduleEvent = new WorkScheduleEvent();
            $workScheduleEvent->work_schedule_id = $workScheduleId;
            $workScheduleEvent->month_id = $month;
            $workScheduleEvent->year = $year;
            $workScheduleEvent->event_id = intval($event['eventId']);
            $workScheduleEvent->event_name = $event['eventName'];
            $workScheduleEvent->event_start_date = $event['eventStartDate'];
            $workScheduleEvent->event_end_date = $event['eventEndDate'] ??  null;
            $workScheduleEvent->long_event = $event['longEvent'];
            $workScheduleEvent->save();
        }

        // อัปเดตข้อมูล WorkScheduleAssignment จาก daySchedules
        foreach($daySchedules as $daySchedule)
        {
            $eventDate = $daySchedule['eventDate'];
            $shiftId = $daySchedule['eventId'];
            $carbonDate = Carbon::createFromFormat('Y-m-d', $eventDate);
            $day = intval($carbonDate->day);
            $month = intval($carbonDate->month);
            $year = $carbonDate->year;
            WorkScheduleAssignment::where('work_schedule_id',$workScheduleId)->where('year',$year)->where('month_id',$month)->where('day',$day)->update([
                'shift_id' => $shiftId
            ]);
        }

        // ส่งค่า workScheduleId กลับเป็น JSON response
        return response()->json(['workScheduleId' => $workScheduleId]);

    }

}
