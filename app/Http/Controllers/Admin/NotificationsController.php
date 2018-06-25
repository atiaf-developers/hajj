<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\BackendController;
use App\Models\AdminNotification;
use App\Notifications\GeneralNotification;
use App\Helpers\Fcm;
use App\Models\NotiObject;
use App\Models\Noti;
use Notification;
use DB;

class NotificationsController extends BackendController {

    private $rules = array(
        'title' => 'required|max:20', 'body' => 'required|max:100', 'type' => 'required'
    );

    public function index() {
        return $this->_view('notifications/index', 'backend');
    }

    public function store(Request $request) {

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } else {
            DB::beginTransaction();
            try {
                $type = $request->input('type');
                $title = $request->input('title');
                $body = $request->input('body');
                $AdminNotification = new AdminNotification;
                $AdminNotification->title = $title;
                $AdminNotification->body = $body;
                $AdminNotification->type = $type;
                $AdminNotification->save();
                $notification = array('title' => $title, 'body' => $body, 'type' => 1);
                if ($type == 1) {
                    $token_and = '/topics/hajj_managers_and';
                    $token_ios = '/topics/hajj_managers_ios';
                } else if ($type == 2) {
                    $token_and = '/topics/hajj_supervisors_and';
                    $token_ios = '/topics/hajj_supervisors_ios';
                } else if ($type == 3) {
                    $token_and = '/topics/hajj_pilgrims_and';
                    $token_ios = '/topics/hajj_pilgrims_ios';
                } else {
                    $token_and = '/topics/hajj_general_and';
                    $token_ios = '/topics/hajj_general_ios';
                }
                $Fcm = new Fcm;
                $Fcm->send($token_and, $notification, 'and');
                $Fcm->send($token_ios, $notification, 'ios');
                DB::commit();
                return _json('success', _lang('app.sending_successfully'));
            } catch (\Exception $ex) {
                DB::rollBack();
                return _json('error', $ex->getMessage(), 400);
            }
        }
    }

}
