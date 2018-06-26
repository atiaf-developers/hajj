<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Pilgrim;

class LoginController extends ApiController {

    private $rules = array(
        'username' => 'required',
        'password' => 'required',
        'device_token' => 'required',
        'device_type' => 'required',
        'type' => 'required|in:1,2,3'
    );

    public function login(Request $request) {

        if ($request->type == 3) {
            unset($this->rules['password']);
            $this->rules['mobile'] = 'required';
            $this->rules['step'] = 'required';
        }
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        }

        if ($request->type == 3) {

            $credentials = $request->only('username', 'mobile');
            if ($request->step == 1) {
                $pilgrim = $this->pilgrim_auth_check($credentials);
                if (!$pilgrim) {
                    return _api_json(new \stdClass(), ['message' => _lang('app.invalid_credentials')], 400);
                }
                $code = Random(4);
                $code = 1234;
                return _api_json(new \stdClass(), ['code' => $code]);
            } else if ($request->step == 2) {

                $pilgrim = $this->pilgrim_auth_check($credentials);
                //dd($pilgrim);
                if ($pilgrim) {
                    $pilgrim->mobile = $credentials['mobile'];
                    $pilgrim->save();

                    $token = new \stdClass();
                    $token->id = $pilgrim->id;
                    $token->type = $request->type;
                    $token->expire = strtotime('+' . $this->expire_no . $this->expire_type);
                    $expire_in_seconds = $token->expire;
                    //$pilgrim = Pilgrim::transform($this->getPilgrim($pilgrim->id));
                    $pilgrim = Pilgrim::getAll(['pilgrims.id' => $pilgrim->id]);
                    $this->update_token($request->input('device_token'), $request->input('device_type'), $pilgrim->id, $request->type);
                    return _api_json($pilgrim, ['message' => _lang('app.login_done_successfully'), 'token' => AUTHORIZATION::generateToken($token), 'expire' => $expire_in_seconds]);
                }else{
                    return _api_json(new \stdClass(), ['message' => _lang('app.invalid_credentials')], 400);
                }
            } else {
                _api_json(new \stdClass(), ['message' => _lang('app.error_is_occures')], 400);
            }
        } else {
            $credentials = $request->only('username', 'password', 'type');
            if ($user = $this->auth_check($credentials)) {
                //dd($user);
                $token = new \stdClass();
                $token->id = $user->id;
                $token->type = $request->type;
                $token->expire = strtotime('+' . $this->expire_no . $this->expire_type);
                $expire_in_seconds = $token->expire;

                $this->update_token($request->input('device_token'), $request->input('device_type'), $user->id, $request->type);
                return _api_json(User::transform($user), ['message' => _lang('app.login_done_successfully'), 'token' => AUTHORIZATION::generateToken($token), 'expire' => $expire_in_seconds]);
            }
            return _api_json(new \stdClass(), ['message' => _lang('app.invalid_credentials')], 400);
        }
    }

    private function auth_check($credentials) {

        $find = User::where(function ($query) use($credentials) {
                    $query->where('username', $credentials['username']);
                })
                ->where('type', $credentials['type'])
                ->where('active', 1)
                ->first();
        if ($find) {
            if (password_verify($credentials['password'], $find->password)) {
                return $find;
            }
        }
        return false;
    }

    private function pilgrim_auth_check($credentials) {

        $find = Pilgrim::where(function ($query) use($credentials) {
                    $query->where('pilgrims.ssn', $credentials['username']);
                    $query->orWhere('pilgrims.reservation_no', $credentials['username']);
                })
                ->where('pilgrims.active', 1)
                ->first();
        if ($find) {
            return $find;
        }
        return false;
    }

}
