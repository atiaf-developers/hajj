<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\BackendController;
use App\Models\Setting;
use App\Models\Supervisor;
use App\Models\SettingTranslation;
use DB;

class SettingsController extends BackendController {

    private $rules = array(
        'setting.email' => 'required|email',
        'setting.phone' => 'required',
        'setting.phone_2' => 'required',
        'setting.state_of_accommodation' => 'required',
        'setting.upload_profile_image' => 'required',
        //'setting.declarative_video_url' => 'required',
        'setting.declarative_video_type' => 'required',
        'setting.social_media.facebook' => 'required',
        'setting.social_media.twitter' => 'required',
        'setting.social_media.instagram' => 'required',
        'setting.social_media.google' => 'required',
        'setting.social_media.linkedin' => 'required',

        'setting.mena_supervisor.name' => 'required',
//        'setting.mena_supervisor.image' => 'required',
        'setting.mena_supervisor.contact_numbers' => 'required',

        'setting.muzdalifah_supervisor.name' => 'required',
//        'setting.muzdalifah_supervisor.image' => 'required',
        'setting.muzdalifah_supervisor.contact_numbers' => 'required',

        'setting.arafat_supervisor.name' => 'required',
//        'setting.arafat_supervisor.image' => 'required',
        'setting.arafat_supervisor.contact_numbers' => 'required',


        'setting.about_type' => 'required',
    );

    public function index() {
        $this->data['settings'] = Setting::get()->keyBy('name');
        if($this->data['settings']){
            if (isset($this->data['settings']['social_media'])) {
                $this->data['settings']['social_media']=json_decode($this->data['settings']['social_media']->value);
                $this->data['settings']['mena_supervisor']=json_decode($this->data['settings']['mena_supervisor']->value);
                $this->data['settings']['muzdalifah_supervisor']=json_decode($this->data['settings']['muzdalifah_supervisor']->value);
                $this->data['settings']['arafat_supervisor']=json_decode($this->data['settings']['arafat_supervisor']->value);
            }
        }
      
        return $this->_view('settings/index', 'backend');
    }

    public function store(Request $request) {
      
        if ($request->file('setting.about_video_url')) {
            $this->rule['setting.about_video_url'] = 'required|mimes:mp4';
        }
        if ($request->file('setting.declarative_video_url')) {
            $this->rule['setting.declarative_video_url'] = 'required|mimes:mp4';
        }
        $columns_arr = array(
            'about_text' => 'required',
        );
        $this->rules = array_merge($this->rules, $this->lang_rules($columns_arr));
        $validator = Validator::make($request->all(), $this->rules);
        

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } 
        //dd($request->all());

            DB::beginTransaction();
            try {
                
                $setting = $request->input('setting');
                $old_settings = Setting::get()->keyBy('name');

                if ($request->file('setting.about_video_url')) {
                    $name = Setting::upload_simple($request->file('setting.about_video_url'), 'videos');
                    $setting['about_video_url'] = $name;
                }
                else if (!$request->input('setting.about_video_url')){
                   $setting['about_video_url'] = $old_settings['about_video_url']->value;
                   $setting['youtube_url'] = $old_settings['youtube_url']->value;
                }

                if ($request->file('setting.declarative_video_url')) {
                    $name = Setting::upload_simple($request->file('setting.declarative_video_url'), 'videos');
                    $setting['declarative_video_url'] = $name;
                }
                else if (!$request->input('setting.declarative_video_url')){
                   $setting['declarative_video_url'] = $old_settings['declarative_video_url']->value;
                   $setting['declarative_video_youtube_url'] = $old_settings['declarative_video_youtube_url']->value;
                }

                if ($request->file('setting.muzdalifah_supervisor.image')) {
                    $name = Supervisor::upload($request->file('setting.muzdalifah_supervisor.image'), 'supervisors');
                    $setting['muzdalifah_supervisor']['image'] = $name;
                }

                if ($request->file('setting.mena_supervisor.image')) {
                    $name = Supervisor::upload($request->file('setting.mena_supervisor.image'), 'supervisors');
                    $setting['mena_supervisor']['image'] = $name;
                }

                if ($request->file('setting.arafat_supervisor.image')) {
                    $name = Supervisor::upload($request->file('setting.arafat_supervisor.image'), 'supervisors');
                    $setting['arafat_supervisor']['image'] = $name;
                }
                foreach($setting as $key=>$value){
                    
                    if(in_array($key, ['social_media','mena_supervisor','muzdalifah_supervisor','arafat_supervisor'])){
                        Setting::updateOrCreate(
                        ['name' => $key], ['value' => json_encode($value)]);
                    }
                   else{
                        Setting::updateOrCreate(
                            ['name' => $key], ['value' => $value]);
                    }
                }
                $about = $request->input('about_text');
                foreach ($about as $key => $value) {
                    SettingTranslation::updateOrCreate(
                            ['locale' => $key], [
                                'locale' => $key,'about_text' => $about[$key]
                            ]);
                }
                DB::commit();
                return _json('success', _lang('app.updated_successfully'));
            } catch (\Exception $ex) {
                DB::rollback();
                dd($ex);
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        
    }



}
