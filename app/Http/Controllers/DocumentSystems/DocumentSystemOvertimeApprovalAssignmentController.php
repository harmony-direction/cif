<?php

namespace App\Http\Controllers\DocumentSystems;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Approver;
use App\Models\OverTime;
use App\Models\SearchField;
use Illuminate\Http\Request;
use App\Models\OverTimeDetail;
use PhpOffice\PhpWord\PhpWord;
use App\Helpers\ActivityLogger;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Tab;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Shared\ZipArchive;
use PhpOffice\PhpWord\SimpleType\JcTable;
use App\Services\UpdatedRoleGroupCollectionService;

class DocumentSystemOvertimeApprovalAssignmentController extends Controller
{
    private $updatedRoleGroupCollectionService;
    private $activityLogger;

    public function __construct(UpdatedRoleGroupCollectionService $updatedRoleGroupCollectionService,ActivityLogger $activityLogger) 
    {
        $this->updatedRoleGroupCollectionService = $updatedRoleGroupCollectionService;
        $this->activityLogger = $activityLogger;
    }
    public function index($id)
    {
        // กำหนดค่าตัวแปร $action ให้เป็น 'show'
        $action = 'show';
        // ดึงค่า 'groupUrl' จาก session และแปลงเป็นข้อความ
        $groupUrl = strval(session('groupUrl'));

        // เรียกใช้งานเซอร์วิส updatedRoleGroupCollectionService เพื่อดึงข้อมูล updatedRoleGroupCollection, permission โดยใช้ค่า $action
        $roleGroupCollection = $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);
        $updatedRoleGroupCollection = $roleGroupCollection['updatedRoleGroupCollection'];
        $permission = $roleGroupCollection['permission'];

        $overtime = OverTime::find($id);
        // $users = OverTimeDetail::where('over_time_id',$id)->get();

        $users = $overtime->overtimeDetails()->with('user')->get()->pluck('user')->unique();
        $approvers = Approver::all();
        

        return view('groups.document-system.overtime.document.assignment.index', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'overtime' => $overtime,
            'users' => $users,
            'approvers' => $approvers
        ]);
    }

    public function create($id)
    {

        // กำหนดค่าตัวแปร $action ให้เป็น 'show'
        $action = 'create';
        // ดึงค่า 'groupUrl' จาก session และแปลงเป็นข้อความ
        $groupUrl = strval(session('groupUrl'));

        // เรียกใช้งานเซอร์วิส updatedRoleGroupCollectionService เพื่อดึงข้อมูล updatedRoleGroupCollection, permission โดยใช้ค่า $action
        $roleGroupCollection = $this->updatedRoleGroupCollectionService->getUpdatedRoleGroupCollection($action);
        $updatedRoleGroupCollection = $roleGroupCollection['updatedRoleGroupCollection'];
        $permission = $roleGroupCollection['permission'];

        $overtime = OverTime::find($id);

        $users = User::whereDoesntHave('overTimeDetails', function ($query) use ($id) {
            $query->where('over_time_id', $id);
        })->paginate(5000);

        return view('groups.document-system.overtime.document.assignment.create', [
            'groupUrl' => $groupUrl,
            'modules' => $updatedRoleGroupCollection,
            'permission' => $permission,
            'overtime' => $overtime,
            'users' => $users
        ]);
    }
    public function search(Request $request)
    {
        $queryInput = $request->data['searchInput'];
        $overtimeId = $request->data['overtimeId'];

        // dd($queryInput,$overtimeId);
        
        $searchFields = SearchField::where('table', 'users')->where('status', 1)->get();

        $query = User::query();

        foreach ($searchFields as $field) {
            $fieldName = $field['field'];
            $fieldType = $field['type'];

            if ($fieldType === 'foreign') {
                $query->orWhereHas($fieldName, function ($query) use ($fieldName, $queryInput) {
                    $query->where('name', 'like', "%{$queryInput}%");
                });
            } else {
                $query->orWhere($fieldName, 'like', "%{$queryInput}%");
            }
        }

        $userIds = $query->pluck('id');
        $overtimeDetailUserIds = OverTimeDetail::where('over_time_id', $overtimeId)->pluck('user_id');

        $filteredUserIds = $userIds->diff($overtimeDetailUserIds);

        $users = User::whereIn('id', $filteredUserIds)->paginate(5000);

        return view('groups.document-system.overtime.document.assignment.table-render.employee-table', ['users' => $users])->render();
    }

    public function store(Request $request)
    {
        if ($request->users === null){
             return redirect()->back()->withErrors('กรุณาเลือกรายการ');
        }
        $selectedUsers = $request->users;
        $overtimeId = $request->overtimeId;
        $overtime = OverTime::find($overtimeId);
        $fromDate = Carbon::createFromFormat('Y-m-d', $overtime->from_date);
        $toDate = Carbon::createFromFormat('Y-m-d', $overtime->to_date)->subDay(); // Subtract one day here
        $startTime = $overtime->start_time;
        $endTime = $overtime->end_time;
        $dateRange = Carbon::parse($fromDate)->daysUntil($toDate->addDay()); // Add the day back for the iteration
        $errorCollection = new Collection();
        foreach ($selectedUsers as $userId) {
            $user = User::find($userId);
            $approvers = $user->approvers->where('document_type_id',2);
            if ($approvers->isNotEmpty()) {
                $authorizedUserIds =$approvers->first()->authorizedUsers->pluck('id')->toArray();
                $approvedList = collect($authorizedUserIds)->map(function ($userId) {
                    return ['user_id' => $userId, 'status' => 0];
                });
                foreach ($dateRange as $date) {
                    $user->overTimeDetails()->create([
                        // 'over_time_id' => $overtimeId,
                        // 'from_date' => $date->format('Y-m-d'),
                        // 'to_date' => $date->format('Y-m-d'),
                        // 'start_time' => $startTime,
                        // 'end_time' => $endTime,
                        // 'approved_list' => $approvedList->toJson()
                        'over_time_id' => $overtimeId,
                        'from_date' => $date->format('Y-m-d'),
                        'to_date' => $date->format('Y-m-d'),
                        'start_time' => $startTime,
                        'end_time' => $endTime
                    ]);
                }
            } else {
                $errorCollection->push($user->name . ' ' . $user->lastname); // Add the name and last name to the error collection if no approvers found for the user
            }
        }

        if ($errorCollection->isNotEmpty()) {
            $errorNames = implode(', ', $errorCollection->all());
            return redirect()->back()->withErrors('ไม่พบผู้อนุมัติล่วงเวลา สำหรับ: ' . $errorNames);
        }

        return redirect()->to('groups/document-system/overtime/document/assignment/' . $overtimeId);
    }

    public function importUserGroup(Request $request)
    {
        $overtimeId = $request->overtimeId;
        $approverId = $request->approverId;
        $approver = Approver::findOrFail($approverId);
        $users = $approver->users;

        $overtime = OverTime::find($overtimeId);
        $fromDate = Carbon::createFromFormat('Y-m-d', $overtime->from_date);
        $toDate = Carbon::createFromFormat('Y-m-d', $overtime->to_date)->subDay(); // Subtract one day here
        $startTime = $overtime->start_time;
        $endTime = $overtime->end_time;

        $dateRange = Carbon::parse($fromDate)->daysUntil($toDate->addDay()); // Add the day back for the iteration

        foreach ($users as $user) {
            foreach ($dateRange as $date) {
                $user->overTimeDetails()->create([
                    'over_time_id' => $overtimeId,
                    'from_date' => $date->format('Y-m-d'),
                    'to_date' => $date->format('Y-m-d'),
                    'start_time' => $startTime,
                    'end_time' => $endTime
                ]);
            }
        }
    }
    public function delete($overtimeId,$userId)
    {
        // ค้นหาข้อมูลผู้ใช้จาก userId
        $user = User::find($userId);
        // ถ้าพบข้อมูลผู้ใช้
        if ($user) {
            // ลบการกำหนดงานใน WorkScheduleAssignment ของผู้ใช้ใน workScheduleId, monthId, year
            $user->overTimeDetails()
                ->where('over_time_id', $overtimeId)
                ->delete();
        }

        // กำหนด URL สำหรับ redirect
        $url = "groups/document-system/overtime/document/assignment/{$overtimeId}";

        // ทำการ redirect ไปยัง URL ที่กำหนด
        return redirect()->to($url);

    }

    public function importEmployeeNo(Request $request)
    {
        $employeeNos = $request->data['employeeNos'];
        $overtimeId = $request->data['overtimeId'];

        // dd($employeeNos,$overtimeId);
        // $approver = Approver::findOrFail($approverId);
        $users = User::whereIn('employee_no',$employeeNos)->get();

        $overtime = OverTime::find($overtimeId);
        $fromDate = Carbon::createFromFormat('Y-m-d', $overtime->from_date);
        $toDate = Carbon::createFromFormat('Y-m-d', $overtime->to_date)->subDay(); // Subtract one day here
        $startTime = $overtime->start_time;
        $endTime = $overtime->end_time;

        $dateRange = Carbon::parse($fromDate)->daysUntil($toDate->addDay()); // Add the day back for the iteration

        foreach ($users as $user) {
            foreach ($dateRange as $date) {
                $user->overTimeDetails()->create([
                    'over_time_id' => $overtimeId,
                    'from_date' => $date->format('Y-m-d'),
                    'to_date' => $date->format('Y-m-d'),
                    'start_time' => $startTime,
                    'end_time' => $endTime
                ]);
            }
        }
    }
    public function download($id){
        $overtime = OverTime::find($id);
        
        $date = Carbon::createFromFormat('Y-m-d', $overtime->from_date)->format('d/m/Y'); ;
        $startTime = substr($overtime->start_time, 0, -3);
        $endTime = substr($overtime->end_time, 0, -3);
        $department = $overtime->approver->company_department->name;

        if($startTime == "00:00" && $endTime="00:00"){
            $startTime = '';
            $endTime = '';
        }

        $userList = $overtime->overtimeDetails()->with('user')->get()->pluck('user')->unique();
        if(count($userList) == 0){
            return redirect()->back();
        }
        
        $businessName = 'บริษัท ฉวีวรรณ อินเตอร์เนชั่นแนล ฟู๊ดส์ จำกัด';
        $subtitleLine1 = 'ใบขอค่าล่วงเวลาพนักงาน (PS-02/1)';
        $subtitleLine2 = "ประจำวันที่.......{$date}........";

        $maxItemsPerDocument = 18; // Number of items to include in each document
            // Create a zip file to store the documents
        $zipPath = storage_path('app/exportedfiles.zip');
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            return response()->json(['error' => 'Failed to create a zip file.']);
        }

        $userChunk = array_chunk($userList->toArray(), $maxItemsPerDocument);
        // dd( $userChunk);

        foreach ($userChunk as $index => $users) {

            $pathToFile = storage_path('app/exportedfile_'.($index+1).'.docx');
            $phpWord = new PhpWord();
            $properties = $phpWord->getDocInfo();
            $properties->setCreator('Paul');
            $properties->setCompany('Orange Juice Factory');
            $properties->setTitle('Purchase invoice');
            $phpWord->setDefaultFontName('Cordia New');
            $title = array('size' => 22, 'bold' => true);
            $subtitle = array('size' => 18, 'bold' => true);
            $subtitle2 = array('size' => 16, 'bold' => false, 'valign' => 'center');
            $normalNoSpace = array('bold' => false,
            'spaceBefore' => 0, 
            'spaceAfter' => 0, 'valign' => 'center');

            $itemsTableStyle = array(
                'borderSize' => 0,
                'borderColor' => '85837f',
                'cellMargin' => 0,  // Set cell margin to 0
                'alignment' => JcTable::START,
                'spaceBefore' => 0,
                'spaceAfter' => 0,
                'spacing' => 0,
            );
            //start building document
            $section = $phpWord->addSection();
            $phpWord->addParagraphStyle('pStyleCenter', array(
                'align' => Jc::CENTER,
                'spaceBefore' => 0, 
                'spaceAfter' => 0,
                'spacing' => 0,
                'tabs' => array(
                    new Tab('left', 1550),
                    new Tab('center', 3200),
                    new Tab('right', 5300),
                )
            ));
            $section->addText($businessName, $title, 'pStyleCenter');
            $section->addText($subtitleLine1, $subtitle, 'pStyleCenter');
            $section->addText($subtitleLine2, $subtitle2, 'pStyleCenter');
            $phpWord->addParagraphStyle(
                'rightTab',
                array('tabs' => array(new Tab('right', 9090)))
            );
            $leftText = "แผนก.....{$department}......";
            $rightText = "เลขที่...............";

            $section->addText(htmlspecialchars($leftText . "\t" . $rightText), $subtitle2, 'rightTab');

            $section->addText(" ขออนุมัติให้ค่าล่วงเวลาแก่พนักงาน ดังมีรายชื่อดังต่อไปนี้", $subtitle2, 'pStyleCenter');


            $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center','align' => 'center');
            $cellRowContinue = array('vMerge' => 'continue');
            $cellColSpan = array('gridSpan' => 2, 'valign' => 'center');
            $cellColSpan3 = array('gridSpan' => 3, 'valign' => 'center');
            $cellColSpan4 = array('gridSpan' => 4, 'valign' => 'center');
            $cellHCentered = array('align' => 'center','spaceBefore' => 0, 'spaceAfter' => 0);
            $cellVCentered = array('valign' => 'center','align' => 'center');
            $tableBottom = array('align' => 'left','spaceBefore' => 0, 'spaceAfter' => 0);

            $phpWord->addTableStyle('Colspan Rowspan', $itemsTableStyle);

            $table = $section->addTable('Colspan Rowspan');

            $table->addRow();

            $cell1 = $table->addCell(1500, $cellRowSpan);
            $textrun1 = $cell1->addTextRun($cellHCentered);
            $textrun1->addText('รหัส', $subtitle2,$normalNoSpace);

            $cell1 = $table->addCell(3000, $cellRowSpan);
            $textrun1 = $cell1->addTextRun($cellHCentered);
            $textrun1->addText('ชื่อ-สกุล', $subtitle2,$normalNoSpace);

            $cell2 = $table->addCell(2400, $cellColSpan);
            $textrun2 = $cell2->addTextRun($cellHCentered);
            $textrun2->addText('เวลาทำ O.T.', $subtitle2,$normalNoSpace);

            $table->addCell(700, $cellRowSpan)->addText('จำนวน' . "\nชั่วโมง", $subtitle2, $cellHCentered);
            $table->addCell(2500, $cellRowSpan)->addText('ลายเซ็น', $subtitle2, $cellHCentered);
            $table->addCell(2500, $cellRowSpan)->addText('หมายเหตุ', $subtitle2, $cellHCentered);

            $table->addRow();
            $table->addCell(null, $cellRowContinue);
            $table->addCell(null, $cellRowContinue);
            $table->addCell(1200, $cellVCentered)->addText('เริ่ม', $subtitle2,$cellHCentered);
            $table->addCell(1200, $cellVCentered)->addText('เสร็จ', $subtitle2,$cellHCentered);
            $table->addCell(null, $cellRowContinue);
            $table->addCell(null, $cellRowContinue);
            $table->addCell(null, $cellRowContinue);


            foreach ($users as $user) {
                $table->addRow(500);
                $table->addCell(1000)->addText($user['employee_no'], $subtitle2, $normalNoSpace);
                $table->addCell(1000)->addText($user['name'] . ' ' . $user['lastname'], $subtitle2, $normalNoSpace);
                $table->addCell(1000)->addText($startTime, $subtitle2, $normalNoSpace);
                $table->addCell(1000)->addText($endTime, $subtitle2, $normalNoSpace);
                $table->addCell(1000)->addText('', $subtitle2, $normalNoSpace);
                $table->addCell(1000)->addText('', $subtitle2, $normalNoSpace);
                $table->addCell(1000)->addText('', $subtitle2, $normalNoSpace);
            }

            $remainingCells = 18 - count($users);
            for ($i = 0; $i < $remainingCells; $i++) {
                $table->addRow(500);
                for ($j = 0; $j < 7; $j++) {
                    $table->addCell(1000)->addText('', $subtitle2, $normalNoSpace);
                }
            }

            $table->addRow();
            $cell1 = $table->addCell(5000, $cellColSpan3);
            $textrun1 = $cell1->addTextRun($tableBottom);
            $textrun1->addText('ผู้ขออนุมัติ', array('size' => 14, 'bold' => false));
            $textrun1->addTextBreak(); // Add a line break (blank line)

            $cell2 = $table->addCell(5000, $cellColSpan4);
            $textrun2 = $cell2->addTextRun($tableBottom);
            $textrun2->addText('ผู้อนุมัติ', array('size' => 14, 'bold' => false));
            $textrun2->addTextBreak();

            $footer = $section->addFooter();
            $footer->addPreserveText('PER-02/2/0-10\05\01', null);
            // $footer->addLink('<https://paulreaney.medium.com/>', 'PHPWord demo');
            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($pathToFile);
            $zip->addFile($pathToFile, "exportedfile_{$index}.docx");
            if(count($userChunk) == 1){
                return response()->download($pathToFile);
            }
        }
      $zip->close();
      return response()->download($zipPath)->deleteFileAfterSend(true); 

    }

    public function updateHour(Request $request){
        $overtimeId = $request->data['overtimeId'];
        $userId = $request->data['userId'];
        $val = $request->data['val'];
        // dd($overtimeId,$userId,$val);
        OvertimeDetail::where('over_time_id',$overtimeId)->where('user_id',$userId)->first()->update(['hour' => $val]);
    }
}
