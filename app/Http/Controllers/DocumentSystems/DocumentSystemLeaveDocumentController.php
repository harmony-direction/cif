<?php

namespace App\Http\Controllers\DocumentSystems;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Leave;
use App\Models\Month;
use App\Models\Shift;
use App\Models\Approver;
use App\Models\LeaveType;
use App\Models\UserLeave;
use App\Models\LeaveDetail;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use App\Models\CompanyDepartment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Services\UpdatedRoleGroupCollectionService;

class DocumentSystemLeaveDocumentController extends Controller
{
    private $updatedRoleGroupCollectionService;
    private $activityLogger;

    public function __construct(UpdatedRoleGroupCollectionService $updatedRoleGroupCollectionService,ActivityLogger $activityLogger) 
    {
        $this->updatedRoleGroupCollectionService = $updatedRoleGroupCollectionService;
        $this->activityLogger = $activityLogger;
    }
    public function index()
    {
        // กำหนดค่าตัวแปร $action ให้เป็น 'show'
        $action = 'show';
        // ดึงค่า 'groupUrl' จาก session และแปลงเป็นข้อความ
        $groupUrl = strval(session('groupUrl'));

        // เรียกใช้งานเซอร์วิส updatedRoleGroupCollectionService เพื่อดึงข้อมูล updatedRoleGroupCollection, permission โดยใช้ค่า $action
        $roleGroupCollection = $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);
        $updatedRoleGroupCollection = $roleGroupCollection['updatedRoleGroupCollection'];
        $permission = $roleGroupCollection['permission'];
        $viewName = $roleGroupCollection['viewName'];
        $companyDepartments = CompanyDepartment::all();
        
        // Get the current date
        $currentDate = Carbon::today()->format('Y-m-d');

        // Retrieve Leave records with from_date equal to or greater than today
        // $leaves = Leave::where('from_date', '>=', $currentDate)->get();
        $leaves = Leave::paginate(5000);
        $months = Month::all();
        $currentYear = Carbon::now()->year;
        $nextYear = $currentYear + 1;
        $years = collect([$currentYear, $nextYear]);

        return view($viewName, [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'companyDepartments' => $companyDepartments,
            'leaves' => $leaves,
            'months' => $months,
            'years' => $years
        ]);
    }

    public function create()
    {

        // กำหนดค่าตัวแปร $action ให้เป็น 'create'
        $action = 'create';
        // ดึงค่า 'groupUrl' จาก session และแปลงเป็นข้อความ
        $groupUrl = strval(session('groupUrl'));

        // เรียกใช้งานเซอร์วิส updatedRoleGroupCollectionService เพื่อดึงข้อมูล updatedRoleGroupCollection, permission โดยใช้ค่า $action
        $roleGroupCollection = $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);
        $updatedRoleGroupCollection = $roleGroupCollection['updatedRoleGroupCollection'];
        $permission = $roleGroupCollection['permission'];
        $users = User::all();
        $leaveTypes = LeaveType::all();

        return view('groups.document-system.leave.document.create', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'users' => $users,
            'leaveTypes' => $leaveTypes
    
        ]);

    }

    public function view($id)
    {
        // กำหนดค่าตัวแปร $action ให้เป็น 'create'
        $action = 'create';
        // ดึงค่า 'groupUrl' จาก session และแปลงเป็นข้อความ
        $groupUrl = strval(session('groupUrl'));

        // เรียกใช้งานเซอร์วิส updatedRoleGroupCollectionService เพื่อดึงข้อมูล updatedRoleGroupCollection, permission โดยใช้ค่า $action
        $roleGroupCollection = $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);
        $updatedRoleGroupCollection = $roleGroupCollection['updatedRoleGroupCollection'];
        $permission = $roleGroupCollection['permission'];
        $users = User::all();
        $leaveTypes = LeaveType::all();

        $leave = Leave::find($id);

        return view('groups.document-system.leave.document.view', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'users' => $users,
            'leaveTypes' => $leaveTypes,
            'leave' => $leave
    
        ]);
    }

    public function delete($id)
    {
        $action = 'delete';
        $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);

        $leave = Leave::findOrFail($id);
        $this->activityLogger->log('ลบ', $leave);

        $leave->delete();

        return response()->json(['message' => 'รายการลาได้ถูกลบออกเรียบร้อยแล้ว']);
    }

    public function checkLeave(Request $request)
    {
        
        $leaveType = LeaveType::find($request->data['leaveType']);
        $userId = $request->data['userId'];
       
        $startDate = Carbon::createFromFormat('d/m/Y', $request->data['startDate'])->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d/m/Y', $request->data['endDate'])->format('Y-m-d');

        $startTime = $request->data['startTime'];
        $endTime = $request->data['endTime'];

        $user = User::find($userId);

        $startDateTime = Carbon::createFromFormat('d/m/Y H:i', $request->data['startDate'] . ' ' . $startTime);
        $endDateTime = Carbon::createFromFormat('d/m/Y H:i', $request->data['endDate'] . ' ' . $endTime);

        $dateLists = $this->generateDateList($startDateTime, $endDateTime);


        $user = User::find($userId);

        // $shiftId = $user->isShiftAssignment($startDate)->first();
     
        // $shift = Shift::find($shiftId);

        $holidays = $user->getHolidayDates($startDate, $endDate)->toArray();

        $formattedDates = array_map(function($date) {
            return date_create_from_format('Y-m-d', $date)->format('d/m/Y');
        }, $holidays);

        $takeLeaveDates = array_diff($dateLists, $formattedDates);
        $dayCount = count($takeLeaveDates);

        $workShifts = [];
        $notFoundShiftAssignments = [];
        foreach ($takeLeaveDates as $takeLeaveDate){
            $date = Carbon::createFromFormat('d/m/Y', $takeLeaveDate)->format('Y-m-d');
            $shiftIds = $user->isShiftAssignment($date);
            if (count($shiftIds) != 0){
                $shift = Shift::find($shiftIds->first());
                $workShifts[] = $shift->name;
            }else{
                $notFoundShiftAssignments[] = 1;
            }
        }

        $approver = null;
        $approvers = $user->approvers;
        if ($approvers->isNotEmpty()) {
            $approver = $approvers->first();
        } 
        $userLeave = UserLeave::where('user_id',$userId)->first();
        return view('groups.document-system.leave.document.modal-render.leave-info-modal',[
            'dayCount' => $dayCount,
            'startDate' => $request->data['startDate'],
            'startTime' => $startTime,
            'endDate' => $request->data['endDate'],
            'endTime' => $endTime,
            'user' => $user,
            'leaveType' => $leaveType,
            // 'holidays' => $formattedDates,
            'takeLeaveDates' => $takeLeaveDates,
            'approver' => $approver,
            'workShifts' => $workShifts,
            'notFoundShiftAssignments' => $notFoundShiftAssignments,
            'userLeave' =>$userLeave
            ])->render();

    }

    public function store(Request $request)
    {
        $leaveId = $request->leaveId;
        $leaveType = $request->leaveType;
        $userId = $request->userId;
       
        $startDate = Carbon::createFromFormat('d/m/Y', $request->startDate)->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d/m/Y', $request->endDate)->format('Y-m-d');

        $startTime = $request->startTime;
        $endTime = $request->endTime;

        $startDateTime = Carbon::createFromFormat('d/m/Y H:i', $request->startDate . ' ' . $startTime);
        $endDateTime = Carbon::createFromFormat('d/m/Y H:i', $request->endDate . ' ' . $endTime);

        $dateLists = $this->generateDateList($startDateTime, $endDateTime);

        $user = User::find($userId);

        $holidays = $user->getHolidayDates($startDate, $endDate)->toArray();

        $formattedDates = array_map(function($date) {
            return date_create_from_format('Y-m-d', $date)->format('d/m/Y');
        }, $holidays);

        $takeLeaveDates = array_diff($dateLists, $formattedDates);

        $duration = $this->getLeaveDuration($takeLeaveDates,$startDate,$startTime,$endDate,$endTime,$user);
        $userLeave = UserLeave::where('user_id',$userId)->where('leave_type_id',$leaveType)->first();

        if($userLeave->count < $duration){
            return response()->json(['error' => 'วันลาคงเหลือไม่เพียงพอ']);
        }
        if (!isset($leaveId)) {
            $approver = User::find($userId)->approvers->first();
            $authorizedUserIds = $approver->authorizedUsers->pluck('id')->toArray();

            $approvedList = collect($authorizedUserIds)->map(function ($userId) {
                return ['user_id' => $userId, 'status' => 0];
            });
            $filePath = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filePath = $file->store('', 'attachments');
            }
            $leave = Leave::create([
                'user_id' => $userId,
                'leave_type_id' => $leaveType,
                'from_date' => $startDate . ' ' . $startTime,
                'to_date' => $endDate . ' ' . $endTime,
                'duration' => $duration,
                'attachment' => $filePath,
                'approved_list' => $approvedList->toJson(),
            ]);
            if (count($takeLeaveDates) == 1){
                LeaveDetail::firstOrCreate([
                    'leave_id' => $leave->id,
                    'from_date' => $startDate . ' ' . $startTime,
                    'to_date' => $endDate . ' ' . $endTime,
                ]);
                
            }else if(count($takeLeaveDates) == 2 ) {
                $startDate = Carbon::createFromFormat('d/m/Y', $takeLeaveDates[0])->format('Y-m-d');
                $endDate = Carbon::createFromFormat('d/m/Y', $takeLeaveDates[1])->format('Y-m-d');
                $shiftId = $user->isShiftAssignment($startDate)->first();
                $shift = Shift::find($shiftId);
                LeaveDetail::Create([
                    'leave_id' => $leave->id,
                    'from_date' => $startDate . ' ' . $startTime,
                    'to_date' => $startDate . ' ' . $shift->end,
                ]);
                LeaveDetail::Create([
                    'leave_id' => $leave->id,
                    'from_date' => $endDate . ' ' . $shift->start,
                    'to_date' => $endDate . ' ' . $endTime,
                ]);
            }else if(count($takeLeaveDates) > 2 ) {
                $firstItem = reset($takeLeaveDates);
                $lastItem = end($takeLeaveDates);
                $datesWithoutFirstLasts = array_slice($takeLeaveDates, 1, -1);

                $date = Carbon::createFromFormat('d/m/Y', $firstItem)->format('Y-m-d');
                $shiftId = $user->isShiftAssignment($date)->first();
                $shift = Shift::find($shiftId);
                LeaveDetail::Create([
                    'leave_id' => $leave->id,
                    'from_date' => $date . ' ' . $startTime,
                    'to_date' => $date . ' ' . $shift->end,
                ]);
                
                foreach($datesWithoutFirstLasts as $datesWithoutFirstLast)
                {
                    $date = Carbon::createFromFormat('d/m/Y', $datesWithoutFirstLast)->format('Y-m-d');
                    $shiftId = $user->isShiftAssignment($date)->first();
                    $shift = Shift::find($shiftId);
                    LeaveDetail::firstOrCreate([
                        'leave_id' => $leave->id,
                        'from_date' => $date . ' ' . $shift->start,
                        'to_date' => $date . ' ' . $shift->end,
                    ]);
                }
                $date = Carbon::createFromFormat('d/m/Y', $lastItem)->format('Y-m-d');
                $shiftId = $user->isShiftAssignment($date)->first();
                $shift = Shift::find($shiftId);
                LeaveDetail::Create([
                    'leave_id' => $leave->id,
                    'from_date' => $date . ' ' . $shift->start,
                    'to_date' => $date . ' ' . $endTime,
                ]);
            }

        } else {
            
            LeaveDetail::where('leave_id',$leaveId)->delete();
            $approver = User::find($userId)->approvers->first();
            $authorizedUserIds = $approver->authorizedUsers->pluck('id')->toArray();

            $approvedList = collect($authorizedUserIds)->map(function ($userId) {
                return ['user_id' => $userId, 'status' => 0];
            });

            
            $leave = Leave::find($leaveId);
            $filePath = $leave->attachment;
             if ($request->hasFile('file')) {
                if ($filePath !== null) {
                    Storage::disk('attachments')->delete($filePath);
                }
                $file = $request->file('file');
                $filePath = $file->store('', 'attachments');
            }
            Leave::find($leaveId)->update([
                'user_id' => $userId,
                'leave_type_id' => $leaveType,
                'from_date' => $startDate . ' ' . $startTime,
                'to_date' => $endDate . ' ' . $endTime,
                'duration' => $duration,
                'attachment' => $filePath,
                'approved_list' => $approvedList->toJson(),
            ]);
            $leave = Leave::find($leaveId);

            if (count($takeLeaveDates) == 1){
                LeaveDetail::firstOrCreate([
                    'leave_id' => $leave->id,
                    'from_date' => $startDate . ' ' . $startTime,
                    'to_date' => $endDate . ' ' . $endTime,
                ]);
            }else if(count($takeLeaveDates) == 2 ) {
                $startDate = Carbon::createFromFormat('d/m/Y', $takeLeaveDates[0])->format('Y-m-d');
                $endDate = Carbon::createFromFormat('d/m/Y', $takeLeaveDates[1])->format('Y-m-d');
                $shiftId = $user->isShiftAssignment($startDate)->first();
                $shift = Shift::find($shiftId);
                LeaveDetail::firstOrCreate([
                    'leave_id' => $leave->id,
                    'from_date' => $startDate . ' ' . $startTime,
                    'to_date' => $startDate . ' ' . $shift->end,
                ]);
                LeaveDetail::firstOrCreate([
                    'leave_id' => $leave->id,
                    'from_date' => $endDate . ' ' . $shift->start,
                    'to_date' => $endDate . ' ' . $endTime,
                ]);
            }else if(count($takeLeaveDates) > 2 ) {
                $firstItem = reset($takeLeaveDates);
                $lastItem = end($takeLeaveDates);
                $datesWithoutFirstLasts = array_slice($takeLeaveDates, 1, -1);

                $date = Carbon::createFromFormat('d/m/Y', $firstItem)->format('Y-m-d');
                $shiftId = $user->isShiftAssignment($date)->first();
                $shift = Shift::find($shiftId);
                LeaveDetail::firstOrCreate([
                    'leave_id' => $leave->id,
                    'from_date' => $date . ' ' . $startTime,
                    'to_date' => $date . ' ' . $shift->end,
                ]);
                
                foreach($datesWithoutFirstLasts as $datesWithoutFirstLast)
                {
                    $date = Carbon::createFromFormat('d/m/Y', $datesWithoutFirstLast)->format('Y-m-d');
                    $shiftId = $user->isShiftAssignment($date)->first();
                    $shift = Shift::find($shiftId);
                    LeaveDetail::firstOrCreate([
                        'leave_id' => $leave->id,
                        'from_date' => $date . ' ' . $shift->start,
                        'to_date' => $date . ' ' . $shift->end,
                    ]);
                }
                $date = Carbon::createFromFormat('d/m/Y', $lastItem)->format('Y-m-d');
                $shiftId = $user->isShiftAssignment($date)->first();
                $shift = Shift::find($shiftId);
                LeaveDetail::firstOrCreate([
                    'leave_id' => $leave->id,
                    'from_date' => $date . ' ' . $shift->start,
                    'to_date' => $date . ' ' . $endTime,
                ]);
            }
        }
        return;
    }

    public function getLeaveDuration($takeLeaveDates,$startDate,$startTime,$endDate,$endTime,$user)
    { 
        $collections = collect();
        if (count($takeLeaveDates) == 1)
        {
             $collections->push([
                'from_date' => $startDate . ' ' . $startTime,
                'to_date' => $endDate . ' ' . $endTime
            ]);
        }else if(count($takeLeaveDates) == 2)
        {
            $startDate = Carbon::createFromFormat('d/m/Y', $takeLeaveDates[0])->format('Y-m-d');
            $endDate = Carbon::createFromFormat('d/m/Y', $takeLeaveDates[1])->format('Y-m-d');
            $shiftId = $user->isShiftAssignment($startDate)->first();
            $shift = Shift::find($shiftId);
            $collections->push([
                'from_date' => $startDate . ' ' . $startTime,
                'to_date' => $startDate . ' ' . $shift->end
            ]);
            $collections->push([
                'from_date' => $endDate . ' ' . $shift->start,
                'to_date' => $endDate . ' ' . $endTime
            ]);
        }else if(count($takeLeaveDates) > 2)
        {
            $firstItem = reset($takeLeaveDates);
            $lastItem = end($takeLeaveDates);
            $datesWithoutFirstLasts = array_slice($takeLeaveDates, 1, -1);

            $date = Carbon::createFromFormat('d/m/Y', $firstItem)->format('Y-m-d');
            $shiftId = $user->isShiftAssignment($date)->first();
            $shift = Shift::find($shiftId);

            $collections->push([
                'from_date' => $date . ' ' . $startTime,
                'to_date' => $date . ' ' . $shift->end
            ]);
            
            foreach($datesWithoutFirstLasts as $datesWithoutFirstLast)
            {
                $date = Carbon::createFromFormat('d/m/Y', $datesWithoutFirstLast)->format('Y-m-d');
                $shiftId = $user->isShiftAssignment($date)->first();
                $shift = Shift::find($shiftId);
                $collections->push([
                    'from_date' => $date . ' ' . $shift->start,
                    'to_date' => $date . ' ' . $shift->end
                ]);
            }
            $date = Carbon::createFromFormat('d/m/Y', $lastItem)->format('Y-m-d');
            $shiftId = $user->isShiftAssignment($date)->first();
            $shift = Shift::find($shiftId);

            $collections->push([
                    'from_date' => $date . ' ' . $shift->start,
                    'to_date' => $date . ' ' . $endTime
                ]);
        }

       $diffInHour = [];
        foreach ($collections as $collection) {
            $fromDate = Carbon::parse($collection['from_date']);
            $toDate = Carbon::parse($collection['to_date']);
            
            $diffInHours = $fromDate->diffInHours($toDate) - 1;
        
            $diffInHour[] = $diffInHours;
        }
        $duration = intval(array_sum($diffInHour)/8) + (array_sum($diffInHour) % 8)/8;
        return $duration;
    }

    public function getAttachment(Request $request)
    {
        $leaveId = $request->data['leaveId'];
        $leave = Leave::find($leaveId);
        return response()->json($leave);
    }

    public function generateDateList($startDateTime, $endDateTime)
    {
        $dateList = [];
        if ($startDateTime->format('Y-m-d') === $endDateTime->format('Y-m-d')) {
            $dateList[] = $startDateTime->format('d/m/Y');
        } else {
            // Generate and add intermediate dates to the list
            while ($startDateTime->lte($endDateTime)) {
                $dateList[] = $startDateTime->format('d/m/Y');
                $startDateTime->addDay();
            }
        }
        return $dateList;
    }

    public function search(Request $request)
    {

        $searchString =$request->data;
        

        $leaves = Leave::whereHas('user', function ($query) use ($searchString) {
            $query->where('employee_no', 'like', '%' . $searchString . '%')
                ->orWhere('name', 'like', '%' . $searchString . '%')
                ->orWhere('lastname', 'like', '%' . $searchString . '%')
                ->orWhereHas('company_department', function ($subQuery) use ($searchString) {
                    $subQuery->where('name', 'like', '%' . $searchString . '%');
                })
                ->orWhereHas('approvers', function ($subQuery) use ($searchString) {
                    $subQuery->where('name', 'like', '%' . $searchString . '%')
                    ->orWhere('code', 'like', '%' . $searchString . '%');
                });
        })
        ->paginate(5000);

        return view('groups.document-system.leave.document.table-render.leave-table-render',[
            'leaves' => $leaves
            ])->render();
    }
}
