<?php

namespace App\Http\Controllers\TimeRecordingSystems;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserAttachment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class UserManagementSystemSettingUserInfAttachmentController extends Controller
{
    public function view($file) {
        $path = storage_path('app/uploads/attachments/' . $file);
        $file = \File::get($path);
        $type = \File::mimeType($path);
        $response = \Response::make($file, 200);
        $response->header('Content-Type', $type);
        return $response;
    }

    public function store(Request $request)
    {
        $file = $request->file('file');
        $userId = $request->userId;
        $name = $request->name;
        $type = $request->type;
        $link = $request->link;

        $filePath = $link;
        if ($type == 1){
            $filePath = $file->store('', 'attachments');
        }

        UserAttachment::create([
            'user_id' => $userId,
            'name' => $name,
            'file' => $filePath,
            'type' => $type
        ]);
        $user = User::find($userId);
        return view('groups.user-management-system.setting.userinfo.table-render.attachment-table-render',[
            'user' => $user
            ])->render();
    }

    public function delete(Request $request)
    {
        $userId = $request->data['userId'];
        $attachmentId = $request->data['attachmentId'];
        $attachment = UserAttachment::find($attachmentId);

        $filePath = $attachment->file;
        Storage::disk('attachments')->delete($filePath);
        $attachment->delete();
        $user = User::find($userId);
        return view('groups.user-management-system.setting.userinfo.table-render.attachment-table-render',[
            'user' => $user
            ])->render();
    }
}
