<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Pilgrim;
use App\Models\Supervisor;
use App\Models\AdminNotification;
use App\Models\Notification;
use App\Models\Setting;
use App\Traits\Basic;
use Request;

class ApiController extends Controller {

    use Basic;

    protected $lang_code;
    protected $User;
    protected $data;
    protected $limit = 10;
    protected $expire_no = 1;
    protected $expire_type = 'day';

    public function __construct() {
        $this->getLangAndSetLocale();
        $this->slugsCreate();
    }

    private function getLangAndSetLocale() {
        $languages = array('ar', 'en', 'ur');
        $lang = Request::header('lang');
        if ($lang == null || !in_array($lang, $languages)) {
            $lang = 'ar';
        }
        $this->currency_sign = $lang == 'ar' ? 'ريال' : 'SAR';
        //return _api_json(false,'ssss');
        $this->lang_code = $lang;
        app()->setLocale($lang);
    }

    protected function inputs_check($model, $inputs = array(), $id = false, $return_errors = true) {
        $errors = array();
        foreach ($inputs as $key => $value) {
            $where_array = array();
            $where_array[] = array($key, '=', $value);
            if ($id) {
                $where_array[] = array('id', '!=', $id);
            }

            $find = $model::where($where_array)->get();

            if (count($find)) {

                $errors[$key] = array(_lang('app.' . $key) . ' ' . _lang("app.added_before"));
            }
        }

        return $errors;
    }

    private function slugsCreate() {

        $this->title_slug = 'title_' . $this->lang_code;
        $this->data['title_slug'] = $this->title_slug;
    }

    protected function auth_user() {
        $token = Request::header('authorization');

        $token = Authorization::validateToken($token);
        $user = null;
        if ($token) {
            if ($token->type == 3) {

                $user = $this->getPilgrim($token->id);
                //$user = Pilgrim::find($token->id);
            } else  {
                $user = User::find($token->id);
            }
        }
        return $user;
    }

    protected function getPilgrim($pilgrim_id) {
        $find = Pilgrim::Join('locations', 'pilgrims.location_id', '=', 'locations.id')
                ->join('locations_translations', 'locations_translations.location_id', '=', 'locations.id')
                ->join('pilgrims_class', 'pilgrims.pilgrim_class_id', '=', 'pilgrims_class.id')
                ->join('pilgrims_class_translations', 'pilgrims_class_translations.pilgrims_class_id', '=', 'pilgrims_class.id')
                ->where('pilgrims.id', $pilgrim_id)
                ->where('locations_translations.locale', $this->lang_code)
                ->where('pilgrims_class_translations.locale', $this->lang_code)
                ->where('pilgrims.active', 1)
                ->select('pilgrims.id', 'pilgrims.name', 'pilgrims.nationality', 'pilgrims.ssn', 'pilgrims.mobile', 'pilgrims.image', 'pilgrims.reservation_no', 'locations_translations.title as city', 'pilgrims_class_translations.title as class')
                ->first();

        return $find;
    }

    protected function update_token($device_token, $device_type, $id, $type) {
        $find = null;
        if ($type == 1) {
            $find = User::find($id);
        } else if ($type == 2) {
            $find = Supervisor::join('pilgrims_buses','supervisors.id','=','pilgrims_buses.supervisor_id')->where('pilgrims_buses.user_id', $id)->select('supervisors.*')->first();
        } else if ($type == 3) {
            $find = Pilgrim::where('id', $id)->first();
        }
        if ($find) {
            $find->device_token = $device_token;
            $find->device_type = $device_type;
            $find->save();
        }
    }

    protected function getGeneralMessages() {
        return [
            'required' => _lang('app.this_field_is_required'),
            'is_base64image' => _lang('app.this_field_must_be_base64_as_image'),
            'onefromjsonarray' => _lang('app.you_should_select_one_at_least'),
        ];
    }

}
