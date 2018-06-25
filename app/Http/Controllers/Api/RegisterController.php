<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Client;
use App\Models\Designer;
use App\Models\DesignerCategory;
use DB;

class RegisterController extends ApiController {

    private $client_rules = array(
        //'name' => 'required',
        'fname' => 'required',
        'lname' => 'required',
        'email' => 'required|email|unique:users',
        'mobile' => 'required|unique:users',
        'password' => 'required',
        'confirm_password' => 'required|same:password',
        'device_token' => 'required',
        'device_type' => 'required',
        'type' => 'required|in:2,3'
    );
    private $designer_rules = array(
        //'name' => 'required',
        'responsible_person_name' => 'required',
        'trade_name' => 'required',
        'about' => 'required',
        'lat' => 'required',
        'lng' => 'required',
        'email' => 'required|email|unique:users',
        'mobile' => 'required|unique:users',
        'password' => 'required',
        'confirm_password' => 'required|same:password',
        'device_token' => 'required',
        'device_type' => 'required',
        'categories' => 'required|onefromjsonarray',
        'type' => 'required|in:2,3',
        'image' => 'required|is_base64image'
    );

    public function __construct() {
        parent::__construct();
    }

    protected function register(Request $request) {
        $type = $request->input('type');
        $categories = json_decode($request->input('categories'));
        if ($type == 2) {
            $rules = $this->client_rules;
        } else if ($type == 3) {
            $rules = $this->designer_rules;
        }
        $validator = Validator::make($request->all(), $rules, $this->getGeneralMessages());
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            //dd($errors);

            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        } else {
   
            DB::beginTransaction();
            try {
                $user = $this->create_user($request);
                DB::commit();
                if ($type == 2) {
                    $token = new \stdClass();
                    $token->id = $user->id;
                    $token->expire = strtotime('+' . $this->expire_no . $this->expire_type);
                    $expire_in_seconds = $token->expire;
                    return _api_json($user, ['token' => AUTHORIZATION::generateToken($token), 'expire' => $expire_in_seconds], 201);
                } else if ($type == 3) {
                    return _api_json(new \stdClass(), [], 201);
                }
            } catch (\Exception $e) {
                DB::rollback();
                $message = $e->getMessage();
                //dd($message);
                return _api_json(new \stdClass(), ['message' => $message],400);
            }
        }
    }

    private function create_user($request) {
        $type = $request->input('type');
        $User = new User;
        if ($type == 2) {
            $User->username = $request->input('fname') . ' ' . $request->input('lname');
        }
        if ($type == 3) {
            $User->username = $request->input('trade_name');
        }
        $User->mobile = $request->input('mobile');
        $User->email = $request->input('email');
        $User->password = bcrypt($request->input('password'));
        $User->device_token = $request->input('device_token');
        $User->device_type = $request->input('device_type');
        $User->type = $type;
        $User->active = ($type == 2) ? 1 : 0;
        $User->save();
        if ($type == 2) {
            $Client = new Client;
            $Client->fname = $request->input('fname');
            $Client->lname = $request->input('lname');
            $Client->user_id = $User->id;
            $Client->image ='default.png';
            $Client->save();
            $User = User::transformClient($User);
        }
        if ($type == 3) {
            $Designer = new Designer;
            $Designer->responsible_person_name = $request->input('responsible_person_name');
            $Designer->trade_name = $request->input('trade_name');
            $Designer->about = $request->input('about');
            $Designer->lat = $request->input('lat');
            $Designer->lng = $request->input('lng');
            $Designer->address_ar = getAddress( $Designer->lat, $Designer->lng,'AR');
            $Designer->address_en = getAddress( $Designer->lat, $Designer->lng,'EN');
            $Designer->image = $this->_upload($request->input('image'), 'users', true, '\App\Models\User', false, true);
            $Designer->user_id = $User->id;
            $Designer->save();
            $categories = json_decode($request->input('categories'));
            $data = array();
            foreach ($categories as $category) {
                $data[] = [
                    'category_id' => $category,
                    'designer_id' => $Designer->id,
                ];
            }

            DesignerCategory::insert($data);
            $User = User::transformDesigner($User);
        }
        return $User;
    }

}
