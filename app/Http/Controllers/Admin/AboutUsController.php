<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\BackendController;
use App\Models\Setting;
use App\Models\Supervisor;
use App\Models\SettingTranslation;
use DB;

class AboutUsController extends BackendController {

    private $rules2 = array(
//        'setting.email' => 'required|email',
//        'setting.phone' => 'required',
//        'setting.phone_2' => 'required',
//        'setting.state_of_accommodation' => 'required',
//        'setting.upload_profile_image' => 'required',
//        //'setting.declarative_video_url' => 'required',
//        'setting.declarative_video_type' => 'required',
//        'setting.social_media.facebook' => 'required',
//        'setting.social_media.twitter' => 'required',
//        'setting.social_media.instagram' => 'required',
//        'setting.social_media.google' => 'required',
//        'setting.social_media.linkedin' => 'required',
//        'setting.mena_supervisor.name' => 'required',
////        'setting.mena_supervisor.image' => 'required',
//        'setting.mena_supervisor.contact_numbers' => 'required',
//        'setting.muzdalifah_supervisor.name' => 'required',
////        'setting.muzdalifah_supervisor.image' => 'required',
//        'setting.muzdalifah_supervisor.contact_numbers' => 'required',
//        'setting.arafat_supervisor.name' => 'required',
////        'setting.arafat_supervisor.image' => 'required',
//        'setting.arafat_supervisor.contact_numbers' => 'required',
//        'setting.about_type' => 'required',
    );
    private $rules = array(
    );

    public function index() {
        //dd('here');
        $this->data['settings'] = Setting::get()->keyBy('name');
        $this->data['settings']['info'] = json_decode($this->data['settings']['info']->value);
//        dd($this->data['settings']['info']->value->about->$this->lang_code);
        return $this->_view('about_us/index', 'backend');
    }

    public function store(Request $request) {

//        if ($request->file('setting.about_video_url')) {
//            $this->rule['setting.about_video_url'] = 'required|mimes:mp4';
//        }
//        if ($request->file('setting.declarative_video_url')) {
//            $this->rule['setting.declarative_video_url'] = 'required|mimes:mp4';
//        }
        $validator = Validator::make($request->all(), array_merge($this->rules, $this->lang_rules(['setting.info.about_text' => 'required'])));


        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        //dd($request->all());

        DB::beginTransaction();
        try {


            $setting = $request->input('setting');
            foreach ($setting as $key => $value) {
                if ($key == 'info') {
                    $value = json_encode($value);
                }
                Setting::updateOrCreate(
                        ['name' => $key], ['value' => $value]);
            }
            //dd($request->file('about_video_url'));
            if ($request->file('about_video_url')) {
                $value = Setting::upload_simple($request->file('about_video_url'), 'videos');
                Setting::updateOrCreate(
                        ['name' => 'about_video_url'], ['value' => $value]);
            }
            if ($request->file('declarative_video_url')) {
                $value = Setting::upload_simple($request->file('declarative_video_url'), 'videos');
                Setting::updateOrCreate(
                        ['name' => 'declarative_video_url'], ['value' => $value]);
            }
              //dd('here2');
            DB::commit();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            dd($ex);
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

}
