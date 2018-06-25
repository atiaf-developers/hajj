<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Helpers\AUTHORIZATION;

use App\Models\User;
use App\Models\Pilgrim;
use App\Models\Supervisor;

use Validator;
use DB;

class UserController extends ApiController {

    public function __construct() {
        parent::__construct();
    }

    public function show() {
        $User = $this->auth_user();
        return _api_json(true, User::transform($User));
    }

    protected function update(Request $request) {
        $User = $this->auth_user();
        $rules = array();
        if ($request->input('mobile')) {
            $rules['step'] = "required";
            $rules['mobile'] = "required";
        }
        if ($request->input('image')) {
            $rules['image'] = "required|is_base64image";
        }
      
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors]);
        } 
        if ($request->input('step') && $request->input('step') == 1) {
            $verification_code = Random(4);
            return _api_json(new \stdClass(), ['code' => $verification_code]);
        }

            DB::beginTransaction();
            try {
               
                if ($request->input('mobile') && $request->input('step') == 2) {
                    $User->mobile = $request->input('mobile');
                }
                if ($request->input('image')) {
                    if ($User->image) {
                       Pilgrim::deleteUploaded('pilgrims', $User->image);
                    }
                    $User->image = Pilgrim::upload($request->input('image'), 'pilgrims', false, false, true);
                }
                
                $User->save();
               
                DB::commit();
                $pilgrim = Pilgrim::getAll(['pilgrims.id'=>$User->id]);
                return _api_json($pilgrim, ['message' => _lang('app.updated_successfully')]);
            } catch (\Exception $e) {
                DB::rollback();
                $message = _lang('app.error_is_occured');
                return _api_json(new \stdClass(), ['message' => $message]);
            }
        
    }
    
    public function logout() {
        //dd(Request::header('authorization'));
        $token = \Request::header('authorization');
        $token = Authorization::validateToken($token);
        $user = null;
        if ($token) {
            if ($token->type == 3) {

                $find = $this->getPilgrim($token->id);
                //$user = Pilgrim::find($token->id);
            } else if($token->type == 2)  {
                $find = Supervisor::join('pilgrims_buses','supervisors.id','=','pilgrims_buses.supervisor_id')->where('pilgrims_buses.user_id', $token->id)->select('supervisors.*')->first();
            }else if($token->type == 1)  {
                $find = User::find($token->id);
            }
        }
   
        $find->device_token='';
        $find->save();
        return _api_json(new \stdClass(), array(), 201);
    }

    


}
