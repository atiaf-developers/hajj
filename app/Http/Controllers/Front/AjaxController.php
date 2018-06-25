<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use App\Models\City;
use App\Models\Resturant;
use App\Models\Topping;
use App\Models\Size;
use App\Models\User;
use App\Models\MenuSection;
use App\Models\Address;
use App\Notifications\GeneralNotification;
use App\Helpers\Fcm;
use App\Mail\GeneralMail;
use Mail;
use Validator;
use Notification;

class AjaxController extends FrontController {



    public function search(Request $request) {
        //dd($request->all());
        $city_id = $request->input('city');
        $area_id = $request->input('region');
        $long = 7 * 60 * 24;
        return response()->json([
                    'type' => 'success',
                    'message' => _url('resturantes')
                ])->cookie('city_id', $city_id, $long)->cookie('area_id', $area_id, $long);
    }
    public function getRegionByCity($city_id) {
      
        $regions = City::where('parent_id', $city_id)
                       ->where('active', 1)
                       ->select('id', 'title_' . $this->lang_code . ' as title')
                       ->get();

        return _json('success', $regions->toArray());
    }
    public function getAddress($address_id) {
        $Address = Address::find($address_id);
       // dd($address_id);
        if($Address){
            return _json('success', Address::transform($Address));
        }else{
            return _json('error', _lang('app.error_is_occured'),400);
        }

        
    }



}
