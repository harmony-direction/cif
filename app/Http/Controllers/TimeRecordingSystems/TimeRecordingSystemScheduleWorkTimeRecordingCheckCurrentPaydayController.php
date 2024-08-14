<?php

namespace App\Http\Controllers\TimeRecordingSystems;

use Carbon\Carbon;
use App\Models\User;
use App\Models\PaydayDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\WorkScheduleAssignmentUser;
use App\Models\WorkScheduleAssignmentUserFile;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\UpdatedRoleGroupCollectionService;

class TimeRecordingSystemScheduleWorkTimeRecordingCheckCurrentPaydayController extends Controller
{
    private $updatedRoleGroupCollectionService;

    public function __construct(UpdatedRoleGroupCollectionService $updatedRoleGroupCollectionService) 
    {
        $this->updatedRoleGroupCollectionService = $updatedRoleGroupCollectionService;
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

        $date = Carbon::now();
        $monthId = intval(Carbon::now()->month);
        $currentDate = $date->format('Y-m-d');
        // $paydayDetails = PaydayDetail::whereDate('end_date', '<=', Carbon::parse($currentDate))
        //     ->whereDate('payment_date', '>=', Carbon::parse($currentDate))
        //     ->get();
        $type =1;
        $paydayDetails = PaydayDetail::whereDate('end_date', '<=', Carbon::parse($currentDate))
            ->whereHas('payday', function ($query) use ($type) {
                $query->where('type', '=', $type);
            })
            ->whereDate('payment_date', '>=', Carbon::parse($currentDate))
            ->get();

        if (count($paydayDetails) === 0)   
        {
            return redirect()->route('groups.time-recording-system.schedulework.time-recording')
            ->withErrors(['error_out_payday_range' => __('validation.error_specific')]);
        }

        $userIds = [];
        foreach($paydayDetails as $paydayDetail)
        {
            $startDate = $paydayDetail->start_date;
            $endDate = $paydayDetail->end_date;
            $ids = $this->getUsersByWorkScheduleAssignment($startDate, $endDate)->pluck('id')->toArray();
            $userIds = array_merge($userIds, $ids);
        }  
        $userIds = array_unique($userIds);

        $users = User::whereIn('id', $userIds)->paginate(5000);

        return view('groups.time-recording-system.schedulework.time-recording-check-current-payday.index', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'users' => $users,
            'paydayDetails' => $paydayDetails
        ]);

    }
    public function getUsersByWorkScheduleAssignment($startDate,$endDate)
    {
        // Convert the start and end date to the correct format
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate = date('Y-m-d', strtotime($endDate));

        // ค้นหาผู้ใช้ที่มีการกำหนดงานเรียกงานใน workScheduleId และ date_in อยู่ในช่วง startDate ถึง endDate
        $users = User::whereHas('workScheduleAssignmentUsers', function ($query) use ($startDate, $endDate) {
            $query->whereNotNull('date_in')
                ->whereBetween('date_in', [$startDate, $endDate]);
        })->get();

        return $users;

    }
    public function timeRecordCheck(Request $request)
    {
        $startDate = Carbon::createFromFormat('d/m/Y', $request->data['startDate'])->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d/m/Y', $request->data['endDate'])->format('Y-m-d');
        $monthId = $request->data['monthId'];
        $year = $request->data['year'];
        $workScheduleId = $request->data['workScheduleId'];
        $page = optional($request->data)['page'];
        $filter = optional($request->data)['filter'];

        $users = $this->getUsersByWorkScheduleAssignment($startDate, $endDate);
        
        $usersWithWorkScheduleAssignments = [];
        foreach($users as $user)
        {
            $workScheduleAssignmentUsers = $user->getWorkScheduleAssignmentUsersInformation($startDate, $endDate, $year);
            $dateInList = $workScheduleAssignmentUsers->pluck('date_in')->toArray();
            
            // Apply filtering based on the selected filter option or show all if filter is not set
            if (!$filter || ($filter == 0) || ($filter == 1 && count($dateInList) == 0) || ($filter == 2 && count($dateInList) > 0)) {
                $usersWithWorkScheduleAssignments[$user->id] = [
                    'user' => $user,
                    'date_in_list' => $dateInList
                ]; 
            }
        }

        $usersCollection = new Collection($usersWithWorkScheduleAssignments);

        // Set up pagination parameters
        $perPage = 20; // Number of items per page
        $currentPage = $request->query('page', $page); // Get the current page from the request query

        // Calculate the items for the current page
        $currentPageItems = $usersCollection->slice(($currentPage - 1) * $perPage, $perPage);

        // Create a LengthAwarePaginator instance for pagination
        $paginatedUsers = new LengthAwarePaginator(
            $currentPageItems,
            $usersCollection->count(),
            $perPage,
            $currentPage,
            ['path' => route('groups.time-recording-system.schedulework.time-recording-check')] // Replace with the correct route name
        );
        
        return view('groups.time-recording-system.schedulework.time-recording-check.table-render.work-schedule-check-table', [
            'usersWithWorkScheduleAssignments' => $paginatedUsers,
        ])->render();       

    }

    public function search(Request $request)
    {
        $searchInput = $request->data['searchInput'];
        
        $filter = $request->data['filter'];
        $date = Carbon::now();
        $monthId = intval(Carbon::now()->month);
        $currentDate = $date->format('Y-m-d');
        $paydayDetails = PaydayDetail::whereDate('end_date', '<=', Carbon::parse($currentDate))
            ->whereDate('payment_date', '>=', Carbon::parse($currentDate))
            ->get();
        $paydayUserIds = [];

        foreach ($paydayDetails as $paydayDetail) {
            $startDate = $paydayDetail->start_date;
            $endDate = $paydayDetail->end_date;
            $ids = $this->getUsersByWorkScheduleAssignment($startDate, $endDate)->pluck('id')->toArray();
            $paydayUserIds = array_merge($paydayUserIds, $ids);
        }

        $paydayUserIds = array_unique($paydayUserIds);

        $userIds = User::whereIn('id', $paydayUserIds)
            ->where(function ($query) use ($searchInput) {
                $query->where('employee_no', 'like', '%' . $searchInput . '%')
                    ->orWhere('name', 'like', '%' . $searchInput . '%')
                    ->orWhere('lastname', 'like', '%' . $searchInput . '%')
                    ->orWhere('passport', 'like', '%' . $searchInput . '%')
                    ->orWhere('hid', 'like', '%' . $searchInput . '%')
                    ->orWhereHas('user_position', function ($query) use ($searchInput) {
                        $query->where('name', 'like', '%' . $searchInput . '%');
                    })
                    ->orWhereHas('ethnicity', function ($query) use ($searchInput) {
                        $query->where('name', 'like', '%' . $searchInput . '%');
                    })
                    ->orWhereHas('nationality', function ($query) use ($searchInput) {
                        $query->where('name', 'like', '%' . $searchInput . '%');
                    })
                    ->orWhereHas('company_department', function ($query) use ($searchInput) {
                        $query->where('name', 'like', '%' . $searchInput . '%');
                    });
            })
            // ->paginate(5000);
            ->pluck('id')->toArray();

            $userList = User::whereIn('id', $userIds)->get();
            $users = User::whereIn('id', $userIds)->paginate(5000);
            if($filter == '1')
            {
                $filteredUsers = $userList->filter(function ($user) {
                    return $user->getErrorDate() == null;
                });

                $filteredUserIds = $filteredUsers->pluck('id')->toArray();

                $users = User::whereIn('id', $filteredUserIds)->paginate(5000);
            }else if($filter == '2')
            {
                $filteredUsers = $userList->filter(function ($user) {
                    return $user->getErrorDate() != null;
                });

                $filteredUserIds = $filteredUsers->pluck('id')->toArray();
                $users = User::whereIn('id', $filteredUserIds)->paginate(5000);

            }
        return view('groups.time-recording-system.schedulework.time-recording-check-current-payday.table-render.time-recording-check-current-payday-table',[
            'users' => $users
            ])->render();
    }

    public function viewUser(Request $request)
    {
        // dd($request->data['startDate']);
        $startDate = $request->data['startDate'] ;//Carbon::createFromFormat('d/m/Y', $request->data['startDate'])->format('Y-m-d');
        $endDate = $request->data['endDate'] ; //Carbon::createFromFormat('d/m/Y', $request->data['endDate'])->format('Y-m-d');
        $year = Carbon::now()->year;
        $userId = $request->data['userId'];
        
        $user = User::find($userId);
        $workScheduleAssignmentUsers = $user->getWorkScheduleAssignmentUsersByConditions($startDate,$endDate, $year);
        return view('groups.time-recording-system.schedulework.time-recording-check-current-payday.table-render.work-schedule-modal-table',[
            'workScheduleAssignmentUsers' => $workScheduleAssignmentUsers
            ])->render();

    }
    
    public function update(Request $request)
    {
        $timeInValue = $request->data['timeInValue'];
        $timeOutValue = $request->data['timeOutValue'];
        $workScheduleAssignmentUserId = $request->data['workScheduleAssignmentUserId'];

        $searchInput = $request->data['searchInput'];
        $filter = $request->data['filter'];

        // if ($timeInValue == null && $timeOutValue == null) {
        //     $timeInValue = $timeOutValue = '00:00:00';
        // }

        WorkScheduleAssignmentUser::find($workScheduleAssignmentUserId)->update([
            'time_in' => $timeInValue,
            'time_out' => $timeOutValue,
            'code' => 'E'
        ]);

        $date = Carbon::now();
        $monthId = intval(Carbon::now()->month);
        $currentDate = $date->format('Y-m-d');
        $paydayDetails = PaydayDetail::whereDate('end_date', '<=', Carbon::parse($currentDate))
            ->whereDate('payment_date', '>=', Carbon::parse($currentDate))
            ->get();
        $paydayUserIds = [];

        foreach ($paydayDetails as $paydayDetail) {
            $startDate = $paydayDetail->start_date;
            $endDate = $paydayDetail->end_date;
            $ids = $this->getUsersByWorkScheduleAssignment($startDate, $endDate)->pluck('id')->toArray();
            $paydayUserIds = array_merge($paydayUserIds, $ids);
        }

        $paydayUserIds = array_unique($paydayUserIds);

        $userIds = User::whereIn('id', $paydayUserIds)
            ->where(function ($query) use ($searchInput) {
                $query->where('employee_no', 'like', '%' . $searchInput . '%')
                    ->orWhere('name', 'like', '%' . $searchInput . '%')
                    ->orWhere('lastname', 'like', '%' . $searchInput . '%')
                    ->orWhere('passport', 'like', '%' . $searchInput . '%')
                    ->orWhere('hid', 'like', '%' . $searchInput . '%')
                    ->orWhereHas('user_position', function ($query) use ($searchInput) {
                        $query->where('name', 'like', '%' . $searchInput . '%');
                    })
                    ->orWhereHas('ethnicity', function ($query) use ($searchInput) {
                        $query->where('name', 'like', '%' . $searchInput . '%');
                    })
                    ->orWhereHas('nationality', function ($query) use ($searchInput) {
                        $query->where('name', 'like', '%' . $searchInput . '%');
                    })
                    ->orWhereHas('company_department', function ($query) use ($searchInput) {
                        $query->where('name', 'like', '%' . $searchInput . '%');
                    });
            })
            // ->paginate(5000);
            ->pluck('id')->toArray();

            $userList = User::whereIn('id', $userIds)->get();
            $users = User::whereIn('id', $userIds)->paginate(5000);
            if($filter == '1')
            {
                $filteredUsers = $userList->filter(function ($user) {
                    return $user->getErrorDate() == null;
                });

                $filteredUserIds = $filteredUsers->pluck('id')->toArray();

                $users = User::whereIn('id', $filteredUserIds)->paginate(5000);
            }else if($filter == '2')
            {
                $filteredUsers = $userList->filter(function ($user) {
                    return $user->getErrorDate() != null;
                });

                $filteredUserIds = $filteredUsers->pluck('id')->toArray();
                $users = User::whereIn('id', $filteredUserIds)->paginate(5000);

            }

        return view('groups.time-recording-system.schedulework.time-recording-check-current-payday.table-render.time-recording-check-current-payday-table',[
            'users' => $users
            ])->render();

    }
    public function getImage(Request $request)
    {
        $workScheduleAssignmentId = $request->data['workScheduleAssignmentId'];
        $workScheduleAssignment = WorkScheduleAssignmentUserFile::where('work_schedule_assignment_user_id', $workScheduleAssignmentId)
                            ->latest()
                            ->first();
        return response()->json($workScheduleAssignment);
    }

    public function uploadImage(Request $request){
        
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $workScheduleAssignmentId = $request->input('workScheduleAssignmentId');

            // Store the file in the 'attachments' disk
            $filePath = $file->store('', 'attachments');
            

            // Create a record in the work_schedule_assignment_user_files table
            WorkScheduleAssignmentUserFile::create([
                'work_schedule_assignment_user_id' => $workScheduleAssignmentId,
                'file' => $filePath,
            ]);

            $workScheduleAssignment = WorkScheduleAssignmentUserFile::where('work_schedule_assignment_user_id', $workScheduleAssignmentId)
                                    ->latest()
                                    ->first();
            return response()->json($workScheduleAssignment);
        }
        
    }
    public function deleteImage(Request $request)
    {
        $workScheduleAssignmentUserFileId = $request->data['workScheduleAssignmentUserFileId'];
    
        // Find the file record by its ID
        $workScheduleAssignmentUserFile = WorkScheduleAssignmentUserFile::find($workScheduleAssignmentUserFileId);
        
        if ($workScheduleAssignmentUserFile) {
            // Delete the file from storage
            Storage::disk('attachments')->delete($workScheduleAssignmentUserFile->file);
            
            // Delete the record from the database
            $workScheduleAssignmentUserFile->delete();

            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'File not found.']);
        }

    }

    public function getLeaveAttachment(Request $request)
    {
        $workScheduleAssignmentUserId = $request->data['workScheduleAssignmentUserId'];
        $leaveAttachment = WorkScheduleAssignmentUser::find($workScheduleAssignmentUserId)->getAttachmentForDate();
        // dd($leaveAttachment);
        return response()->json($leaveAttachment);
    }
}
