<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Supervisor;
use App\Models\News;
use App\Models\Location;
use App\Models\OurLocation;
use App\Models\ContactMessage;
use App\Models\CommonQuestion;
use App\Models\RateQuestion;
use App\Models\RateQuestionAnswer;
use App\Models\CommunicationGuide;
use App\Models\AdminNotification;
use App\Models\Rating;
use App\Helpers\Fcm;
use Carbon\Carbon;
use App\Models\SettingTranslation;
use DB;

class BasicController extends ApiController {

    private $contact_rules = array(
        'mobile' => 'required',
        'type' => 'required',
        'message' => 'required',
        'name' => 'required'
    );
    private $rate_rules = array(
        'question_id' => 'required',
        'answer_id' => 'required',
    );

    public function getToken(Request $request) {
        $token = $request->header('authorization');
        if ($token != null) {
            $token = Authorization::validateToken($token);
            if ($token) {
                $new_token = new \stdClass();
                if ($token->type == 3) {
                    $find = Pilgrim::where('id', $user_id)->where('active', true)->first();
                } else {
                    $find = User::where('id', $user_id)->where('active', true)->first();
                }
                if ($find != null) {
                    $new_token->id = $find->id;
                    $new_token->type = $token->type;
                    $new_token->expire = strtotime('+ ' . $this->expire_no . $this->expire_type);
                    $expire_in_seconds = $new_token->expire;
                    return _api_json('', ['token' => AUTHORIZATION::generateToken($new_token), 'expire' => $expire_in_seconds]);
                } else {
                    return _api_json('', ['message' => 'user not found'], 401);
                }
            } else {
                return _api_json('', ['message' => 'invalid token'], 401);
            }
        } else {
            return _api_json('', ['message' => 'token not provided'], 401);
        }
    }

    public function getSettings() {
        try {
            $settings = Setting::select('name', 'value')->get()->keyBy('name');
            $settings['social_media'] = json_decode($settings['social_media']->value);
            $info = json_decode($settings['info']->value);
            unset($settings['info']->name);
            unset($settings['info']->value);
            //dd($info);
            $settings['info']['about_text'] = $info->about->{$this->lang_code};
           

//            $locations_supervisors[] = Supervisor::transformSetting(json_decode($settings['mena_supervisor']->value));
//            $locations_supervisors[] = Supervisor::transformSetting(json_decode($settings['arafat_supervisor']->value));
//            $locations_supervisors[] = Supervisor::transformSetting(json_decode($settings['muzdalifah_supervisor']->value));
//
//
//            $settings['locations_supervisors'] = $locations_supervisors;
//
//
//            if ($settings['video_type']->value == '1') {
//                $settings['about_video_url']->value = "https://www.youtube.com/embed" . "/" . $settings['youtube_url']->value;
//            } elseif ($settings['video_type']->value == '2') {
//                $settings['about_video_url']->value = url('public/uploads/videos') . '/' . $settings['about_video_url']->value;
//            }
//           
//         
//            if ($settings['declarative_video_type']->value == '1') {
//                $settings['declarative_video_url']->value = "https://www.youtube.com/embed" . "/" . $settings['declarative_video_youtube_url']->value;
//            } elseif ($settings['declarative_video_type']->value == '2') {
//                $settings['declarative_video_url']->value = url('public/uploads/videos') . '/' . $settings['declarative_video_url']->value;
//            }
//
//            $settings['info'] = SettingTranslation::where('locale', $this->lang_code)->first();

            return _api_json( $settings);
        } catch (\Exception $e) {
            return _api_json(new \stdClass(), ['message' => $e->getMessage().$e->getLine()], 400);
        }
    }

    public function sendContactMessage(Request $request) {
        $validator = Validator::make($request->all(), $this->contact_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json('', ['errors' => $errors], 400);
        } else {
            try {
                $ContactMessage = new ContactMessage;
                $ContactMessage->mobile = $request->input('mobile');
                $ContactMessage->type = $request->input('type');
                $ContactMessage->message = $request->input('message');
                $ContactMessage->name = $request->input('name');
                $ContactMessage->save();
                return _api_json('', ['message' => _lang('app.message_is_sent_successfully')]);
            } catch (\Exception $ex) {
                return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
            }
        }
    }

    public function getNews() {
        try {

            $news = News::Join('news_translations', 'news.id', '=', 'news_translations.news_id')
                    ->where('news_translations.locale', $this->lang_code)
                    ->where('news.active', true)
                    ->orderBy('news.this_order')
                    ->select('news.id', 'news.image', 'news.created_at', 'news_translations.description')
                    ->paginate($this->limit);


            return _api_json(News::transformCollection($news));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getCategories() {
        try {
            $categories = Category::Join('categories_translations', 'categories.id', '=', 'categories_translations.category_id')
                    ->where('categories_translations.locale', $this->lang_code)
                    ->where('categories.active', true)
                    ->where('categories.parent_id', 0)
                    ->orderBy('categories.this_order')
                    ->select("categories.id", "categories_translations.title", "categories.parent_id")
                    ->paginate($this->limit);
            return _api_json(Category::transformCollection($categories));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getOurLocations() {
        try {

            $our_locations = OurLocation::Join('our_locations_translations', 'our_locations.id', '=', 'our_locations_translations.our_location_id')
                    ->where('our_locations_translations.locale', $this->lang_code)
                    ->where('our_locations.active', true)
                    ->orderBy('our_locations.this_order')
                    ->select("our_locations.id", "our_locations.location_image", "our_locations_translations.title", "our_locations_translations.address", "our_locations.lat", "our_locations.lng", "our_locations.contact_numbers")
                    ->paginate($this->limit);

            return _api_json(OurLocation::transformCollection($our_locations));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getLocations() {
        try {

            $locations = Location::Join('locations_translations', 'locations.id', '=', 'locations_translations.location_id')
                    ->where('locations_translations.locale', $this->lang_code)
                    ->orderBy('locations.this_order')
                    ->select("locations.id", "locations_translations.title")
                    ->get();

            return _api_json(Location::transformCollection($locations));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getCommonQuestions() {
        try {

            $common_questions = CommonQuestion::Join('common_questions_translations', 'common_questions.id', '=', 'common_questions_translations.common_question_id')
                    ->where('common_questions_translations.locale', $this->lang_code)
                    ->where('common_questions.active', true)
                    ->orderBy('common_questions.this_order')
                    ->select("common_questions.id", "common_questions_translations.question", "common_questions_translations.answer")
                    ->paginate($this->limit);

            return _api_json(CommonQuestion::transformCollection($common_questions));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getRateQuestions() {
        try {

            $rate_questions = RateQuestion::Join('rate_questions_translations', 'rate_questions.id', '=', 'rate_questions_translations.rate_question_id')
                    ->where('rate_questions_translations.locale', $this->lang_code)
                    ->where('rate_questions.active', true)
                    ->orderBy('rate_questions.this_order')
                    ->select("rate_questions.id", "rate_questions_translations.title")
                    ->paginate($this->limit);

            return _api_json(RateQuestion::transformCollection($rate_questions));
        } catch (\Exception $e) {

            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getCommunicationGuides() {
        try {

            $communication_guides = CommunicationGuide::Join('communication_guides_translations', 'communication_guides.id', '=', 'communication_guides_translations.communication_guide_id')
                    ->where('communication_guides_translations.locale', $this->lang_code)
                    ->where('communication_guides.active', true)
                    ->orderBy('communication_guides.this_order')
                    ->select([
                        'communication_guides.id', "communication_guides_translations.title", "communication_guides_translations.description"
                    ])
                    ->paginate($this->limit);

            return _api_json(CommunicationGuide::transformCollection($communication_guides));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getNotifications(Request $request) {
        $type = $request->input('type');
        if (!in_array($type, [0, 3])) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
        try {

            $admin_notifications = AdminNotification::orderBy('admin_notifications.created_at', 'DESC');
          
            if ($type == 0) {
           
                $admin_notifications->where('admin_notifications.type', 0);
            }else if ($type == 3) {
                $admin_notifications->where('admin_notifications.type', 0);
                $admin_notifications->orWhere('admin_notifications.type', 3);
            }

            $admin_notifications->select("title", "body", 'created_at', "type");
            $admin_notifications=$admin_notifications->paginate($this->limit);
         
            return _api_json(AdminNotification::transformCollection($admin_notifications));
        } catch (\Exception $e) {

            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function rate(Request $request) {
        try {
            $validator = Validator::make($request->all(), $this->rate_rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json('', ['errors' => $errors], 400);
            }
            DB::beginTransaction();
            try {
                $check = Rating::where('question_id', $request->input('question_id'))
                        ->where('pilgrim_id', $this->auth_user()->id)
                        ->first();
                if ($check) {
                    $check->answer_id = $request->answer_id;
                    $check->save();
                    DB::table('rate_question_answers')->where('id', $check->answer_id)->decrement('count_of_raters', 1);
                } else {
                    $rate = new Rating;
                    $rate->question_id = $request->input('question_id');
                    $rate->answer_id = $request->input('answer_id');
                    $rate->pilgrim_id = $this->auth_user()->id;
                    $rate->save();
                }
                DB::table('rate_question_answers')->where('id', $request->answer_id)->increment('count_of_raters', 1);

                DB::commit();
                return _api_json('', ['message' => _lang('app.thank_you_for_your_answer')]);
            } catch (\Exception $ex) {
                DB::rollback();
                return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
            }
        } catch (\Exception $e) {

            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

}
