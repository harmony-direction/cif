<?php

namespace App\Http\Controllers\settings;

use App\Models\User;
use App\Models\UserPosition;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SettingGeneralUserPositionController extends Controller
{
    private $activityLogger;

    public function __construct(ActivityLogger $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }
    
    public function index()
    {
        $userPositions = UserPosition::all();

        return view('setting.general.user-position.index', [
            'userPositions' => $userPositions
        ]);
    }

    public function create()
    {
        return view('setting.general.user-position.create');
    }

    public function store(Request $request)
    {

        $validator = $this->validateFormData($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $name = $request->name;

        $userPosition = new UserPosition();
        $userPosition->name = $name;
        $userPosition->save();

        $this->activityLogger->log('เพิ่ม', $userPosition);

        return redirect()->route('setting.general.user-position.index', [
            'message' => 'นำเข้าข้อมูลเรียบร้อยแล้ว'
        ]);
    }

    public function view($id)
    {
        $userPosition = UserPosition::findOrFail($id);

        return view('setting.general.user-position.view', [
            'userPosition' => $userPosition
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = $this->validateFormData($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $userPosition = UserPosition::findOrFail($id);

        $this->activityLogger->log('อัปเดต', $userPosition);

        $userPosition->update($validator->validated());

        return redirect()->route('setting.general.user-position.index', [
            'success' => 'อัปเดตตำแหน่งเรียบร้อยแล้ว'
        ]);
    }

    public function delete($id)
    {
        $userPosition = UserPosition::findOrFail($id);

        $check = User::where('user_position_id', $id)->exists();
        if ($check) {
            return response()->json(['error' => 'ตำแหน่งนี้ถูกใช้งานอยู่ในปัจจุบันและไม่สามารถลบได้'], 422);
        }

        $this->activityLogger->log('ลบ', $userPosition);
        $userPosition->delete();

        return response()->json(['message' => 'ตำแหน่งได้ถูกลบออกเรียบร้อยแล้ว']);
    }


    public function validateFormData($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);

        return $validator;
    }

}
